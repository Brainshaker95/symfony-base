import $ from 'jquery';

import notify from '../util/notify';
import scrollTo from '../util/scroll-to';
import translate from '../util/translate';
import { validate } from './input';

let loading;

const validateForm = ($form) => {
  let errorCount = 0;

  $form.find('[required]').each((index, input) => {
    errorCount += validate($(input)) ? 0 : 1;
  });

  return errorCount === 0;
};

export default (opts = {}) => {
  const $firstForm = $('.form').eq(0);
  const options = {
    $form: $firstForm,
    $button: $firstForm.find('[type="submit"]'),
    errorMessage: translate('error.general'),
    errorType: 'danger',
    errorTime: 3000,
    done: () => {},
    fail: () => {},
    always: () => {},
    ...opts,
  };

  const { $button, $form } = options;

  $form.on('submit', (event) => {
    event.preventDefault();

    if (!validateForm($form)) {
      const $firstError = $form.find('.form__error').first();

      scrollTo(
        $firstError,
        $firstError.height() + $firstError.prev('input').height(),
      );

      return;
    }

    $button
      .addClass('button--is-loading')
      .data('text', $button.text())
      .text('')
      .blur();

    if (loading) {
      loading.abort();
    }

    loading = $.ajax({
      method: $form.attr('method') || 'POST',
      url: $form.attr('action') || window.location.href,
      data: $form.serialize(),
    }).done((response) => options.done(response))
      .fail((response) => {
        if (response.statusText === 'abort') {
          return;
        }

        options.fail(response);

        notify({
          type: options.errorType,
          text: options.errorMessage,
          time: options.errorTime,
        });
      }).always(() => {
        $button
          .text($button.data('text'))
          .removeClass('button--is-loading');

        options.always();
      });
  });
};
