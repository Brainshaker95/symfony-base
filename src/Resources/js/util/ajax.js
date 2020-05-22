import $ from 'jquery';

import notify from './notify';
import translate from './translate';

let loading;

export default (opts) => {
  const options = {
    done: () => {},
    fail: () => {},
    always: () => {},
    $button: null,
    errorMessage: translate('error.general'),
    errorType: 'error',
    errorTime: 3000,
    ...opts,
  };

  const { $button } = options;

  if ($button) {
    $button
      .addClass('button--is-loading')
      .data('text', $button.text())
      .text('')
      .blur();
  }

  if (loading) {
    loading.abort();
  }

  loading = $.ajax({
    method: options.method || 'POST',
    url: options.url || window.location.href,
    data: options.data || {},
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
      if ($button) {
        $button
          .text($button.data('text'))
          .removeClass('button--is-loading');
      }

      options.always();
    });
};
