import $ from 'jquery';

import ajax from '../../util/ajax';
import keycode from '../../util/keycode';
import notify from '../notify';
import translate from '../../util/translate';

const updatePath = ($input, isClear) => {
  const $file = $input.closest('.file');
  const $path = $file.find('.file__path');
  const $clearButton = $file.find('.button--clear');
  const { files } = $input[0];
  const { length } = files;
  let path = '';

  if (isClear) {
    $input.trigger('change');
  }

  if (length) {
    Array.from(files).forEach(({ name }, index) => {
      path += index === length - 1 ? name : `${name}, `;
    });

    $file.attr('title', path);
    $clearButton.removeClass('button--is-hidden');
    $path.addClass('file__path--has-value');
  } else {
    $clearButton.addClass('button--is-hidden');
    $path.removeClass('file__path--has-value');
  }

  if (!path) {
    path = $input.data('placeholder') || translate('placeholder.upload_file');
    $file.removeAttr('title');
  }

  $path.text(path);
};

const generateMarkup = ($input, isDragAndDrop) => {
  const $parent = $input.parent();
  const $error = $parent.find('.form__error');
  const $file = $('<div class="file" />');
  let $path;

  if (isDragAndDrop) {
    $path = $('<button type="button" class="button file__button" />');

    $file.addClass('file--is-drag-and-drop');
  } else {
    $path = $('<div class="file__path" />');

    $file
      .addClass('button form__input')
      .attr('tabindex', 0);
  }

  if ($input.prop('multiple')) {
    $file.addClass('file--is-multiple');
  }

  if ($input.prop('disabled')) {
    $file
      .addClass('file--is-disabled')
      .attr('tabindex', -1);
  }

  if ($error.length) {
    $file.insertBefore($error.eq(0));
  } else {
    $parent.append($file);
  }

  $file
    .append($parent.find('.form__label'))
    .append($input)
    .append($path);

  if (!isDragAndDrop) {
    $file.append($(`<button
      type="button"
      class="button button--close button--clear button--is-hidden"
      title="${translate('remove')}"
      aria-label="${translate('remove')}"
    />`));
  } else {
    $file
      .append($('<div class="progress"><span><span></span></span></div>'))
      .append($('<ul class="file__list" />'));
  }

  $path.text($input.val() || $input.data('placeholder') || translate('placeholder.upload_file'));
};

const attachDefaultHandlers = ($input) => {
  const $file = $input.closest('.file');

  $file.on('click keydown', (event) => {
    if (event.target !== event.currentTarget
      || (event.type === 'keydown' && event.key !== keycode.enter)) {
      return;
    }

    if ($(event.target).hasClass('button--clear')
      || $file.hasClass('file--is-disabled')) {
      return;
    }

    $input.trigger('click');
  });

  $input.on('change', () => updatePath($input));

  $file.find('.button--clear').on('click', () => {
    $input.val(null);
    updatePath($input, true);
  });
};

const preventDefaults = (event) => {
  event.preventDefault();
  event.stopPropagation();
};

const attachDragAndDropHandlers = ($input) => {
  const $file = $input.closest('.file');
  const $progressBar = $file.find('.progress');
  const $body = $('body');

  const highlight = () => {
    $file.addClass('file--is-highlighted');
  };

  const unhighlight = () => {
    $file.removeClass('file--is-highlighted');
  };

  let uploadProgress = [];

  const initializeProgress = (fileCount) => {
    uploadProgress = [];

    for (let i = fileCount; i > 0; i -= 1) {
      uploadProgress.push(0);
    }
  };

  const appendFile = (file, filename) => {
    const { name } = file;
    const reader = new FileReader();
    const $fileList = $file.find('.file__list');
    let extension = name.split('.').pop();

    reader.readAsDataURL(file);

    if (extension.length > 4) {
      extension = 'file';
    }

    reader.onloadend = () => {
      $fileList.append($(`<li class="file__list-item">
          <i class="file-icon" data-extension="${extension}"></i>
          <div class="file__list-name">${name}</div>
          <button
            type="button"
            class="button button--close button--clear"
            title="${translate('remove')}"
            aria-label="${translate('remove')}"
          ></button>
            ${reader.result.indexOf('data:image/') === 0 ? `
              <img src="${reader.result}" class="file__image responsive-image" alt="${file.name}">
            `.trim() : ''}
        </li>`));

      const $lastListItem = $file.find('.file__list-item').last();

      $lastListItem
        .find('.button--clear')
        .on('click', () => {
          $input.val(null);

          ajax({
            url: $input.data('path') || '/',
            method: 'DELETE',
            data: {
              filename,
            },
            done: (response) => {
              if (!response.success) {
                notify();

                return;
              }

              $lastListItem.slideUp('fast', () => {
                $lastListItem.remove();
              });
            },
          });
        });
    };
  };

  const updateProgress = (fileCount, percent) => {
    uploadProgress[fileCount] = percent;

    const total = uploadProgress.reduce((progress, current) => progress + current, 0);
    const percentageFilled = total / uploadProgress.length;

    $progressBar
      .find('span')
      .first()
      .width(`${percentageFilled}%`);

    if (percentageFilled >= 100) {
      $progressBar.removeClass('progress--is-visible');
    }
  };

  const uploadFile = (file, index) => {
    const maxSize = $input.data('max-size') || Infinity;
    const types = $input.data('mime-types');
    const allowedTypes = types.split(', ');

    if (file.size > maxSize * 1048576) {
      notify({
        text: translate('error.form.max_size.exceeded', { limit: maxSize }),
      });

      return;
    }

    if (!allowedTypes.includes(file.type)) {
      notify({
        text: translate('error.form.mime_type.invalid', { types }),
      });

      return;
    }

    let alreadyAdded = false;

    $file.find('.file__list-name').each((i, name) => {
      if (!alreadyAdded && $(name).text() === file.name) {
        alreadyAdded = true;
      }
    });

    if (alreadyAdded) {
      notify({
        text: translate('error.form.file.already_added'),
      });

      return;
    }

    const formData = new FormData();

    formData.append('file', file);
    $progressBar.addClass('progress--is-visible');

    ajax({
      url: $input.data('path') || '/',
      data: formData,
      enctype: 'multipart/form-data',
      processData: false,
      contentType: false,
      allowParallelRequests: true,
      $button: $file.find('.file__button'),
      done: (response) => {
        if (!response.success) {
          notify();

          return;
        }

        $progressBar.removeClass('progress--is-visible');
        appendFile(file, response.filename);
      },
      xhr: () => {
        const xhr = new XMLHttpRequest();

        xhr.upload.addEventListener('progress', (event) => {
          updateProgress(index, ((event.loaded * 100.0) / event.total) || 100);
        });

        xhr.addEventListener('readystatechange', () => {
          if (xhr.readyState === 4 && xhr.status === 200) {
            updateProgress(index, 100);
          } else if (xhr.readyState === 4 && xhr.status !== 200) {
            notify();
          }
        });

        return xhr;
      },
    });
  };

  const handleFiles = ([...files]) => {
    unhighlight();

    $progressBar
      .removeClass('progress--is-filled')
      .find('span')
      .first()
      .width(0);

    if ($input.prop('multiple')) {
      initializeProgress(files.length);
      files.forEach(uploadFile);
    } else {
      initializeProgress(1);
      uploadFile(files[0], 0);
    }
  };

  ['dragenter', 'dragover', 'dragleave', 'drop'].forEach((eventName) => {
    $file.on(eventName, preventDefaults);
    $body.on(eventName, preventDefaults);
  });

  $file
    .on('dragenter', highlight)
    .on('dragover', highlight)
    .on('dragleave', unhighlight)
    .on('drop', (event) => handleFiles(event.originalEvent.dataTransfer.files));

  $file.on('change', () => handleFiles($input[0].files));

  $file.find('.file__button').on('click', (event) => {
    if (event.type === 'keydown' && event.key !== keycode.enter) {
      return;
    }

    if ($file.hasClass('file--is-disabled')) {
      return;
    }

    $input.trigger('click');
  });
};

export default () => {
  $('.file__input').each((index, input) => {
    const $input = $(input);
    const isDragAndDrop = $input.data('drag-and-drop');

    generateMarkup($input, isDragAndDrop);

    if (isDragAndDrop) {
      attachDragAndDropHandlers($input);
    } else {
      attachDefaultHandlers($input);
    }
  });
};
