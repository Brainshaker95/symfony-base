import $ from 'jquery';

import keycode from '../../util/keycode';
import translate from '../../util/translate';

const closeSelects = ($select = []) => {
  if ($select.length && $select.hasClass('select--is-disabled')) {
    return;
  }

  $('.select')
    .not($select)
    .removeClass('select--is-open')
    .find('.select__options')
    .stop()
    .slideUp('fast');
};

const clear = ($select) => {
  const $input = $select.find('.select__input');

  $select.find('.button--clear').addClass('button--is-hidden');

  $select
    .find('.select__option')
    .removeClass('select__option--is-selected')
    .removeClass('select__option--is-anker');

  $select
    .find('option')
    .removeAttr('selected');

  $select
    .find('.select__selection')
    .removeClass('select__selection--has-value')
    .text($input.data('placeholder') || '...');

  if ($select.hasClass('select--is-open')) {
    $select
      .removeClass('select--is-open')
      .find('.select__options')
      .stop()
      .slideUp('fast');
  }

  $input
    .val(null)
    .trigger('change');
};

const toggle = (event) => {
  const $select = $(event.currentTarget).closest('.select');
  const $options = $select.find('.select__options');

  closeSelects($select);

  if ($select.hasClass('select--is-open')) {
    $select.removeClass('select--is-open');
    $options.stop().slideUp('fast');
  } else {
    $select.addClass('select--is-open');
    $options.stop().slideDown('fast');
  }
};

const select = (event, isKeyboardSelection) => {
  event.stopPropagation();

  const $target = isKeyboardSelection ? $(event.target) : $(event.currentTarget);
  const $select = $target.closest('.select');
  const $input = $select.find('.select__input');
  const $selection = $select.find('.select__selection');
  const isMultiple = $input.prop('multiple');
  const selectedValue = $target.text();

  if ((selectedValue === $selection.text() && !isMultiple)
    || $target.hasClass('select__option--is-disabled')) {
    return;
  }

  const $options = $select.find('.select__option');
  const $optionTags = $select.find('option').not(':disabled');
  const $ankers = $select.find('.select__option--is-anker');
  const isSelected = $target.hasClass('select__option--is-selected');
  const selectedOptions = () => $optionTags.filter(':selected:not(:disabled)');
  const $targetOption = $optionTags.filter((_, optionTag) => $(optionTag).text() === selectedValue);
  const singleSelection = !isMultiple || (isMultiple && !event.ctrlKey && !event.shiftKey);
  let targetValue = $targetOption.text();

  const addSelectionAnker = () => {
    $ankers.removeClass('select__option--is-anker');
    $target.addClass('select__option--is-anker');
  };

  const selectOption = ($optionTag, $option) => {
    $optionTag.attr('selected', 'selected');
    $option.addClass('select__option--is-selected');
    $selection.addClass('select__selection--has-value');
  };

  const deselectOptions = () => {
    $optionTags.removeAttr('selected');
    $options.removeClass('select__option--is-selected');
    $selection.removeClass('select__selection--has-value');
  };

  const selectOptions = () => {
    let lowerBound;
    let upperBound;
    let targetIndex;
    let ankerIndex;

    if (!$ankers.length) {
      addSelectionAnker();
    }

    $options.each((index, option) => {
      const $option = $(option);

      if ($option.text() === selectedValue) {
        targetIndex = index;
      }

      if ($option.hasClass('select__option--is-anker')) {
        ankerIndex = index;
      }
    });

    if (ankerIndex < targetIndex) {
      lowerBound = ankerIndex;
      upperBound = targetIndex;
    } else if (ankerIndex > targetIndex) {
      lowerBound = targetIndex;
      upperBound = ankerIndex;
    } else {
      lowerBound = targetIndex;
      upperBound = targetIndex;
    }

    $options.each((index, option) => {
      if (index >= lowerBound && index <= upperBound) {
        selectOption($optionTags.eq(index), $(option));
      }
    });
  };

  if (singleSelection) {
    deselectOptions();
  }

  if (isMultiple && isSelected && event.ctrlKey) {
    $target.removeClass('select__option--is-selected');
    $targetOption.removeAttr('selected');

    const $selectedOptions = selectedOptions();

    targetValue = $selectedOptions.length ? $selectedOptions.eq(0).text() : $input.data('placeholder') || '...';

    addSelectionAnker();
  } else if (isMultiple && event.shiftKey) {
    deselectOptions();
    selectOptions();
  } else {
    if (!isMultiple) {
      $input.val($targetOption.val());
    }

    selectOption($targetOption, $target);
    addSelectionAnker();
  }

  $input.trigger('change');

  if (isMultiple && selectedOptions().length > 1) {
    targetValue += '...';
  }

  $selection.text(targetValue);
  $select
    .find('.button--clear')
    .removeClass('button--is-hidden');

  if (singleSelection) {
    closeSelects();
  }
};

const generateMarkup = ($input) => {
  const $parent = $input.parent();
  const $error = $parent.find('.form__error');
  const $optionTags = $input.find('option');
  const $select = $('<div class="select form__input" />');
  const $selectOptions = $('<div class="select__options" />');
  const $clearButton = $(`<button
    type="button"
    class="button button--close button--clear button--is-hidden"
    title="${translate('remove')}"
  />`);

  $input.prepend('<option value="" selected disabled></a>');

  const $selectedOptions = $optionTags.filter('[selected]');
  const selectedOptionCount = $selectedOptions.length;
  const isMultiple = $input.prop('multiple');
  let selection;

  if (selectedOptionCount) {
    selection = $selectedOptions.eq(0).text();
    $clearButton.removeClass('button--is-hidden');
  }

  if (isMultiple) {
    $select.addClass('select--is-multiple');

    if (selectedOptionCount > 1) {
      selection += '...';
    }
  }

  if (!selection) {
    selection = $input.data('placeholder') || '...';
  }

  const $selection = $(`<div class="select__selection" tabindex="0">${selection}</div>`);

  if ($input.prop('disabled')) {
    $select.addClass('select--is-disabled');
    $selection.attr('tabindex', -1);
  }

  if (selectedOptionCount) {
    $selection.addClass('select__selection--has-value');
  }

  $optionTags.each((index, optionTag) => {
    const $optionTag = $(optionTag);
    const $option = $(`<div class="select__option" tabindex="0">${$optionTag.text()}</div>`);

    if ($optionTag.attr('selected')) {
      if (!isMultiple) {
        $input.val($optionTag.val());
      }

      $option.addClass('select__option--is-selected');
    }

    if ($optionTag.prop('disabled')) {
      $option.addClass('select__option--is-disabled');
      $option.attr('tabindex', -1);
    }

    $selectOptions.append($option);
  });

  if ($error.length) {
    $select.insertBefore($error.eq(0));
  } else {
    $parent.append($select);
  }

  $select
    .append($parent.find('.form__label'))
    .append($input)
    .append($selection)
    .append($selectOptions)
    .append($clearButton);
};

const attachHandlers = ($input) => {
  const $select = $input.closest('.select');
  const $options = $select.find('.select__option');
  const $selection = $select.find('.select__selection');
  const $clearButton = $select.find('.button--clear');

  $selection.on('click', toggle);

  $select.on('keydown', (event) => {
    const { which } = event;
    const $focusedElement = $(':focus');

    if (which === keycode.escape) {
      closeSelects();
      $selection.focus();
    } else if (which === keycode.arrowUp) {
      event.preventDefault();

      $focusedElement.prev().focus();
    } else if (which === keycode.arrowDown) {
      event.preventDefault();

      if ($(event.target).hasClass('select__selection')) {
        $select.find('.select__option').first().focus();
      } else {
        $focusedElement.next().focus();
      }
    } else if (which === keycode.enter) {
      if ($focusedElement.hasClass('select__option')) {
        select(event, true);

        if (!$select.prop('multiple') && !event.ctrlKey && !event.shiftKey) {
          $selection.focus();
        }
      } else {
        toggle(event);
      }
    }
  });

  $selection
    .add($options)
    .add($clearButton)
    .on('focusout', () => {
      setTimeout(() => {
        if (!$select.find($(':focus')).length) {
          closeSelects();
        }
      }, 0);
    });

  $options.on('click', (event) => {
    if ($(event.currentTarget).prop('disabled')) {
      return;
    }

    select(event);
  });

  $input.on('change', () => {
    const value = $input.val();

    if (value && value.length) {
      $clearButton.removeClass('button--is-hidden');
    } else {
      $clearButton.addClass('button--is-hidden');
    }
  });

  $clearButton.on('click keydown', (event) => {
    if ($input.prop('disabled')
      || (event.type === 'keydown' && event.which !== keycode.enter)) {
      return;
    }

    clear($select);
  });

  $(document).on('click', ({ target }) => {
    const $target = $(target);

    if (!$target.closest('.select').length) {
      closeSelects();
    }
  });
};

export default () => {
  const $inputs = $('.select__input');

  $inputs.each((index, input) => {
    const $input = $(input);

    generateMarkup($input);
    attachHandlers($input);
  });
};
