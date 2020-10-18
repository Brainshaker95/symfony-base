import $ from 'jquery';
import 'jquery-ui/ui/widgets/datepicker';

import keycode from '../../util/keycode';
import translate from '../../util/translate';

const days = [
  'monday',
  'tuesday',
  'wednesday',
  'thursday',
  'friday',
  'saturday',
  'sunday',
];

const months = [
  'january',
  'february',
  'march',
  'april',
  'may',
  'june',
  'july',
  'august',
  'september',
  'october',
  'november',
  'december',
];

const dayNames = [];
const dayNamesMin = [];
const monthNames = [];

days.forEach((day) => {
  dayNames.push(translate(`datepicker.day.long.${day}`));
  dayNamesMin.push(translate(`datepicker.day.min.${day}`));
});

months.forEach((month) => {
  monthNames.push(translate(`datepicker.month.${month}`));
});

const generateMarkup = ($input) => {
  const $parent = $input.parent();
  const $error = $parent.find('.form__error');
  const $date = $('<div class="date" />');
  const $clearButton = $(`<button
    type="button"
    class="button button--close button--clear button--is-hidden"
    title="${translate('remove')}"
  />`);

  if ($input.prop('disabled')) {
    $date.addClass('date--is-disabled');
  }

  if ($error.length) {
    $date.insertBefore($error.eq(0));
  } else {
    $parent.append($date);
  }

  $date
    .append($parent.find('.form__label'))
    .append($input)
    .append($clearButton);
};

const attachHandlers = ($input) => {
  const $clearButton = $input.closest('.date').find('.button--clear');

  $input.on('change', () => {
    if ($input.val()) {
      $clearButton.removeClass('button--is-hidden');
    } else {
      $clearButton.addClass('button--is-hidden');
    }
  });

  $clearButton.on('click keydown', (event) => {
    if ($input.prop('disabled')
      || (event.type === 'keydown' && event.key !== keycode.enter)) {
      return;
    }

    $input.val(null);
    $input.trigger('change');
  });
};

export default () => {
  const $inputs = $('.date__input');

  $inputs.each((index, theInput) => {
    const $input = $(theInput);

    if ($input.attr('type') === 'date') {
      $input.datepicker({
        monthNames,
        dayNames,
        dayNamesMin,
        firstDay: 1,
        dateFormat: 'yy-mm-dd',
        prevText: translate('datepicker.prev_month'),
        nextText: translate('datepicker.next_month'),
        beforeShow: (input) => {
          $(input)
            .prev('.form__label')
            .addClass('form__label--is-static');
        },
        onClose: (_, { input }) => {
          $(input)
            .prev('.form__label')
            .removeClass('form__label--is-static');
        },
      });
    }

    generateMarkup($input);
    attachHandlers($input);
  });
};
