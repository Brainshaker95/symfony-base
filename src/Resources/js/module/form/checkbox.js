import $ from 'jquery';

import keycode from '../../util/keycode';

const uncheckChildren = ($target, isChecked) => {
  if (isChecked) {
    return;
  }

  const $childConditionals = $target.children('[data-conditional]');

  if ($childConditionals.length) {
    $childConditionals.each((index, childConditional) => {
      const $childInput = $(`[data-conditional-toggle^="${$(childConditional).data('conditional')}"]`);

      $childInput
        .closest('.checkbox')
        .removeClass('checkbox--is-checked');

      $childInput
        .prop('checked', false)
        .trigger('conditional-toggle', [false]);
    });
  }
};

const handleConditionalToggle = ({ currentTarget }, isChecked) => {
  const conditionalToggle = $(currentTarget).data('conditional-toggle');

  if (conditionalToggle) {
    const [id, type, cssClass] = conditionalToggle.split('-');
    const $target = $(`[data-conditional="${id}"]`);

    if (isChecked) {
      if (type === 'class') {
        $target.removeClass(cssClass);
      } else if (type === 'slide') {
        $target.stop().slideDown(300);
      }

      return;
    }

    if (type === 'class') {
      $target.addClass(cssClass);
    } else if (type === 'slide') {
      $target.stop().slideUp(300);
    }

    uncheckChildren($target, isChecked);
  }
};

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

  const $label = $parent.find('.form__label');

  if ($label.hasClass('form__label--is-required')) {
    $label.html(`${$label.html()}*`);
  }

  $input
    .on('conditional-toggle', handleConditionalToggle)
    .trigger('conditional-toggle', [isChecked]);

  $checkbox
    .append($label)
    .append($input);
};

const attachHandlers = ($input) => {
  const $checkbox = $input.closest('.checkbox');

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

    $input
      .trigger('conditional-toggle', [$input.prop('checked')])
      .trigger('change');
  });
};

export default () => {
  $('.checkbox__input').each((index, input) => {
    const $input = $(input);

    generateMarkup($input);
    attachHandlers($input);
  });
};
