import $ from 'jquery';

import keycode from '../../util/keycode';

const uncheckChildren = ($target, isChecked) => {
  if ($target.find('[data-conditional]').length && !isChecked) {
    $target
      .find('.checkbox')
      .removeClass('checkbox--is-checked')
      .find('.checkbox__input')
      .prop('checked', false);
  }
};

// TODO: Maybe generate markup in form theme instead of js for form elements?
const generateMarkup = ($input) => {
  const $parent = $input.parent();
  const $error = $parent.find('.form__error');
  const $checkbox = $('<div class="checkbox form__input" tabindex="0" />');
  const isChecked = $input.prop('checked');

  if (isChecked) {
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

  const $label = $parent.find('.form__label--is-static');

  if ($label.hasClass('form__label--is-required')) {
    $label.html(`${$label.html()}*`);
  }

  const conditionalToggle = $input.data('conditional-toggle');

  if (conditionalToggle && isChecked) {
    const [id, type, cssClass] = conditionalToggle.split('-');
    const $target = $(`[data-conditional="${id}"]`);

    if (type === 'class') {
      $target.removeClass(cssClass);
    } else if (type === 'slide') {
      $target.stop().slideDown(300);
    }

    uncheckChildren($target, isChecked);
  }

  $checkbox
    .append($label)
    .append($input);
};

const attachHandlers = ($input) => {
  const $checkbox = $input.closest('.checkbox');
  const conditionalToggle = $input.data('conditional-toggle');
  let $target = [];
  let cssClass;
  let type;
  let id;

  if (conditionalToggle) {
    [id, type, cssClass] = conditionalToggle.split('-');
    $target = $(`[data-conditional="${id}"]`);
  }

  $checkbox.on('click keydown', (event) => {
    if ($(event.target).prop('tagName') === 'A'
      || (event.type === 'keydown' && event.key === keycode.tab)) {
      return;
    }

    event.preventDefault();

    if ($input.prop('disabled')
      || (event.type === 'keydown' && event.key !== keycode.enter)) {
      return;
    }

    if ($input.prop('checked')) {
      $input.prop('checked', false);
      $checkbox.removeClass('checkbox--is-checked');
    } else {
      $input.prop('checked', true);
      $checkbox.addClass('checkbox--is-checked');
    }

    if ($target.length) {
      if (type === 'class') {
        $target.toggleClass(cssClass);
      } else if (type === 'slide') {
        $target.stop().slideToggle(300);
      }

      uncheckChildren($target, $input.prop('checked'));
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
