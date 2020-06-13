import $ from 'jquery';

import keycode from '../../util/keycode';

const generateMarkup = ($input) => {
  const $parent = $input.parent();
  const $error = $parent.find('.form__error');
  const $radio = $('<div class="radio form__input" tabindex="0" />');

  if ($input.prop('disabled')) {
    $radio
      .addClass('radio--is-disabled')
      .attr('tabindex', -1);
  }

  if ($error.length) {
    $radio.insertBefore($error.eq(0));
  } else {
    $parent.append($radio);
  }

  if ($input.attr('id').endsWith('0')) {
    $radio.addClass('radio--is-checked');
    $input.prop('checked', true);
  }

  $radio
    .append($parent.find(`.form__label--is-static[for="${$input.attr('id')}"]`))
    .append($input);
};

const attachHandlers = ($input) => {
  const $radio = $input.closest('.radio');

  $radio.on('click keydown', (event) => {
    if ($input.prop('disabled')
      || $input.prop('checked')
      || (event.type === 'keydown' && event.which !== keycode.enter)) {
      return;
    }

    const $parent = $radio.parent();

    $parent
      .find('.radio')
      .removeClass('radio--is-checked');

    $parent
      .find(`[name="${$input.attr('name')}"]`)
      .prop('checked', false);

    $radio.addClass('radio--is-checked');
    $input.prop('checked', true);
    $input.trigger('change');
  });
};

export default () => {
  $('.radio__input').each((index, input) => {
    const $input = $(input);

    generateMarkup($input);
    attachHandlers($input);
  });
};
