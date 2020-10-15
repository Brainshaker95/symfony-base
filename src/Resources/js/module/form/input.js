import $ from 'jquery';

import device from '../../util/device';
import keyCode from '../../util/keycode';
import { validate } from './validate';

export const setFormRowClass = ($input) => {
  const type = $input.attr('type');

  if (type === 'checkbox' || type === 'radio') {
    return false;
  }

  const $formRow = $input.closest('.form__row');
  const value = $input.val();

  if (value && value.length) {
    $formRow.addClass('form__row--has-value');

    return true;
  }

  $formRow.removeClass('form__row--has-value');

  return false;
};

export default () => {
  $('input, select, textarea').each((index, input) => {
    const $input = $(input);
    const $label = $input
      .closest('.form__row')
      .find('.form__label:not(.form__label--is-static)');

    setFormRowClass($input);

    $input.on('change', () => {
      setFormRowClass($input);

      if ($input.is(':required') || $input.data('required')) {
        validate($input);
      }
    });

    if ($input.attr('type') === 'number') {
      $input.on('keydown', ({ code }) => code !== keyCode.comma
        && code !== keyCode.e
        && code !== keyCode.minus
        && code !== keyCode.period
        && code !== keyCode.plus);
    }

    if (device('ie') && $label.length) {
      $input.on('input', () => {
        if ($input.val()) {
          $label.hide();
        } else {
          $label.show();
        }
      });
    }
  });
};
