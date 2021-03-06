import $ from 'jquery';

import { setFormRowClass } from './input';
import keycode from '../../util/keycode';
import translate from '../../util/translate';

const setSelectionVisibility = ($select = []) => {
  if (!$select.length) {
    return;
  }

  const $selection = $select.find('.select__selection');
  const hasValue = setFormRowClass($select.find('select'));

  if ($select.hasClass('select--is-expanded')) {
    if (hasValue) {
      $selection.addClass('select__selection--is-visible');
    } else {
      $selection.removeClass('select__selection--is-visible');
    }
  } else {
    $selection.addClass('select__selection--is-visible');
  }
};

const closeSelects = () => {
  setSelectionVisibility($('.select--is-expanded').first());

  $('.select')
    .removeClass('select--is-expanded')
    .find('.select__options')
    .stop()
    .slideUp('fast');
};

const clear = ($select) => {
  const $input = $select.find('.select__input');

  if ($select.hasClass('select--is-expanded')) {
    $select
      .removeClass('select--is-expanded')
      .find('.select__options')
      .stop()
      .slideUp('fast');
  }

  $select
    .find('option')
    .removeAttr('selected');

  $select
    .find('.select__option')
    .removeClass('select__option--is-selected')
    .removeClass('select__option--is-anker');

  $select
    .closest('.form__row')
    .removeClass('form__row--has-value');

  $input
    .val(null)
    .trigger('change');

  $select
    .find('.select__selection')
    .text(`${$input.data('placeholder') || '...'}`)
    .removeClass('select__selection--is-visible');
};

const toggle = ($target) => {
  const $select = $target.closest('.select');
  const $selection = $select.find('.select__selection');
  const $options = $select.find('.select__options');

  if ($select.hasClass('select--is-expanded')) {
    $select.removeClass('select--is-expanded');
    $options.stop().slideUp('fast');
    $selection.trigger('blur');
  } else {
    $select.addClass('select--is-expanded');
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
  const selectedOptions = () => $optionTags.filter(':selected:not(:disabled), [selected]:not([disabled])');
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
    $selection.addClass('select__selection--is-visible');
  };

  const deselectOptions = () => {
    $optionTags.removeAttr('selected');
    $options.removeClass('select__option--is-selected');
    $selection.removeClass('select__selection--is-visible');
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

    const $allOptionTags = $select.find('option');

    $options.each((index, option) => {
      if (index >= lowerBound && index <= upperBound) {
        const $optionTag = $allOptionTags.eq(index + 1);

        if (!$optionTag.prop('disabled')) {
          selectOption($optionTag, $(option));
        }
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

  if (singleSelection) {
    closeSelects();
  }
};

const generateMarkup = ($input) => {
  const $parent = $input.parent();
  const $label = $parent.find('.form__label');
  const $error = $parent.find('.form__error');
  const $optionTags = $input.find('option');
  const $select = $('<div class="select form__input" />');
  const $selectOptions = $('<div class="select__options" />');
  const $clearButton = $(`<button
    type="button"
    class="button button--close button--clear button--is-hidden"
    title="${translate('remove')}"
    aria-label="${translate('remove')}"
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
    selection = `${$input.data('placeholder') || '...'}`;
  }

  const $selection = $(`<div class="select__selection" tabindex="0">${selection}</div>`);

  if ($input.prop('disabled')) {
    $select.addClass('select--is-disabled');
    $selection.attr('tabindex', -1);
    $clearButton.attr('tabindex', -1);
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
    .append($label)
    .append($input)
    .append($selection);

  if (!$input.hasClass('select--no-clear')) {
    $select.append($clearButton);
  }

  if (!selectedOptionCount) {
    $select.closest('.form__row').removeClass('form__row--has-value');
  } else {
    $selection.addClass('select__selection--is-visible');
  }

  $select.append($selectOptions);
};

const attachHandlers = ($input) => {
  const $select = $input.closest('.select');
  const $options = $select.find('.select__option');
  const $selection = $select.find('.select__selection');
  const $clearButton = $select.find('.button--clear');

  $selection.on('click', ({ currentTarget }) => {
    setTimeout(() => toggle($(currentTarget)), 0);
  });

  $select.on('keydown', (event) => {
    const { key } = event;
    const $focusedElement = $(':focus');

    if (key === keycode.escape) {
      closeSelects();
      $selection.trigger('focus');
    } else if (key === keycode.arrowUp) {
      event.preventDefault();

      $focusedElement.prev().trigger('focus');
    } else if (key === keycode.arrowDown) {
      event.preventDefault();

      if ($(event.target).hasClass('select__selection')) {
        $select.find('.select__option').first().trigger('focus');
      } else {
        $focusedElement.next().trigger('focus');
      }
    } else if (key === keycode.enter) {
      if ($focusedElement.hasClass('select__option')) {
        select(event, true);

        if (!$select.prop('multiple') && !event.ctrlKey && !event.shiftKey) {
          $selection.trigger('focus');
        }
      } else if ($focusedElement.hasClass('button--clear')) {
        if ($input.prop('disabled')) {
          return;
        }

        clear($select);
      } else {
        toggle($(event.currentTarget));
        $selection.trigger('focus');
      }
    }
  });

  $selection
    .add($options)
    .add($clearButton)
    .on('focus', () => {
      $selection.addClass('select__selection--is-visible');
    })
    .on('focusout', () => {
      setTimeout(() => {
        if (!$select.find($(':focus')).length) {
          const value = ($input.val() || []);

          if (!value.length) {
            $selection.removeClass('select__selection--is-visible');
          }

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
    const selectedOptionCount = $input
      .find('option')
      .filter(':selected:not(:disabled), [selected]:not([disabled])')
      .length;

    if (selectedOptionCount) {
      $clearButton.removeClass('button--is-hidden');
    } else {
      $clearButton.addClass('button--is-hidden');
    }
  });

  $clearButton.on('click', () => {
    if ($input.prop('disabled')) {
      return;
    }

    clear($select);
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
