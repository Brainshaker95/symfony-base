import $ from 'jquery';

import translate from '../util/translate';

const names = [
  'password',
  'username',
];

const types = [
  'checkbox',
  'email',
  'text',
];

const emailInvalid = (value) => value.trim().indexOf(' ') > -1 || !value.match(/\w+@\w+\.\w+/g);

const checkValue = ($input) => {
  if ($input.attr('type') === 'checkbox') {
    return;
  }

  const $formRow = $input.closest('.form__row');

  if ($input.val()) {
    $formRow.addClass('form__row--has-value');
  } else {
    $formRow.removeClass('form__row--has-value');
  }
};

export const validate = ($input) => {
  const name = $input.attr('name');
  const type = $input.attr('type');
  const value = $input.val();
  let error;

  $input.next('.form__error').remove();

  if (!value) {
    if (names.includes(name)) {
      error = translate(`error.form.${name}.empty`);
    } else if (types.includes(type)) {
      error = translate(`error.form.${type}.empty`);
    } else {
      error = translate('error.form.text.empty');
    }
  } else if (type === 'checkbox' && !$input.is(':checked')) {
    error = translate('error.form.checkbox.empty');
  } else if (type === 'email' && emailInvalid(value)) {
    error = translate('error.form.email.invalid');
  } else if (type === 'password') {
    const min = $input.attr('min');

    if (value.trim().length < min) {
      error = translate('error.form.password.min', {
        limit: min,
      });
    }
  }

  if (error) {
    $(`<div class="form__error">${error}</div>`).insertAfter($input);

    return false;
  }

  return true;
};

export const initInputs = () => {
  $('input').each((index, input) => {
    const $input = $(input);

    checkValue($input);

    $input.on('change', () => {
      checkValue($input);

      if ($input.is(':required')) {
        validate($input);
      }
    });
  });
};
