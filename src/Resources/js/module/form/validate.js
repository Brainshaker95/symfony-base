import $ from 'jquery';

import scrollTo from '../../util/scroll-to';
import translate from '../../util/translate';

const inputNames = [
  'image',
  'password',
  'username',
];

const inputTypes = [
  'checkbox',
  'email',
  'text',
];

const emailInvalid = (value) => value.trim().indexOf(' ') > -1 || !value.match(/^[^@\s]+@[^@\s.]+\.[^@.\s]+$/);

export const validate = ($input) => {
  const tagName = $input.prop('tagName');
  const name = $input.attr('name');
  const type = $input.attr('type');
  const value = $input.val();
  let error;

  $input
    .closest('.form__row')
    .find('.form__error')
    .remove();

  if (!value || (tagName === 'SELECT' && !value.length)) {
    if (inputNames.includes(name)) {
      error = translate(`error.form.${name}.empty`);
    } else if (inputTypes.includes(type)) {
      error = translate(`error.form.${type}.empty`);
    } else {
      error = translate('error.form.text.empty');
    }
  } else if (type === 'checkbox' && !$input.is(':checked')) {
    if ($input.attr('id').indexOf('privacyAndTerms') > -1) {
      error = translate('error.form.privacy_and_terms.empty');
    } else if ($input.attr('id').indexOf('privacy') > -1) {
      error = translate('error.form.privacy.empty');
    } else {
      error = translate('error.form.checkbox.empty');
    }
  } else if (type === 'email' && emailInvalid(value)) {
    error = translate('error.form.email.invalid');
  } else if (tagName === 'TEXTAREA') {
    const maxLength = $input.attr('maxlength');

    if (maxLength && value.length > maxLength) {
      error = translate('error.form.textarea.max', {
        limit: maxLength,
      });
    }
  } else if (type === 'password') {
    const min = $input.attr('min');

    if (value.trim().length < min) {
      error = translate('error.form.password.min', {
        limit: min,
      });
    }
  } else if (type === 'file') {
    Array.from($input[0].files).forEach((file) => {
      const maxSize = $input.data('max-size') || Infinity;
      const mimeTypes = ($input.data('mime-types') || []).split(', ');

      if (file.size > maxSize * 1048576) {
        error = translate('error.form.max_size.exceeded', {
          limit: maxSize,
        });
      } else if (!mimeTypes.includes(file.type)) {
        error = translate('error.form.mime_type.invalid', {
          types: `"${mimeTypes.join('", "')}"`,
        });
      }
    });
  }

  if (error) {
    $input
      .closest('.form__row')
      .append(`<div class="form__error">${error}</div>`);

    return false;
  }

  return true;
};

export const validateForm = ($form) => {
  let errorCount = 0;

  $form.find('[required], [data-required]').each((index, input) => {
    errorCount += validate($(input)) ? 0 : 1;
  });

  const $firstError = $form.find('.form__error').first();

  if ($firstError.length) {
    scrollTo(
      $firstError,
      $firstError.height() + $firstError.parent().find('input, textarea, .select__selection').height(),
    );
  }

  return errorCount === 0;
};
