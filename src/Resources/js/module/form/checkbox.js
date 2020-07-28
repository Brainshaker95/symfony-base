import $ from 'jquery';

import keycode from '../../util/keycode';

const generateMarkup = ($input) => {
  const $parent = $input.parent();
  const $error = $parent.find('.form__error');
  const $checkbox = $('<div class="checkbox form__input" tabindex="0" />');

  if ($input.prop('checked')) {
    $checkbox.addClass('checkbox--is-checked');
  }

  if ($input.prop('disabled')) {
    $checkbox
      .addClass('checkbox--is-disabled')
      .attr('tabindex', -1);
  }

  if ($error.length) {
    $checkbox.insertBefore($error.eq(0));
  } else {
    $parent.append($checkbox);
  }

  // TODO: Add rewuired start back for checkbox label

  $checkbox
    .append($parent.find('.form__label--is-static'))
    .append($input);
};

const attachHandlers = ($input) => {
  const $checkbox = $input.closest('.checkbox');

  $checkbox.on('click keydown', (event) => {
    if ($input.prop('disabled')
      || (event.type === 'keydown' && event.which !== keycode.enter)) {
      return;
    }

    if ($input.prop('checked')) {
      $input.prop('checked', false);
      $checkbox.removeClass('checkbox--is-checked');
    } else {
      $input.prop('checked', true);
      $checkbox.addClass('checkbox--is-checked');
    }

    $input.trigger('change');
  });
};

export default () => {
  $('.checkbox__input').each((index, input) => {
    const $input = $(input);

    generateMarkup($input);
    attachHandlers($input);
  });
};
