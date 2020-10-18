import $ from 'jquery';

import translate from '../util/translate';
import scssVars from '../util/scss-vars';

const appendNotification = (options) => {
  const $notification = $(`<div class="notification notification--${options.type}">${options.text}</div>`);

  $('body').append($notification);

  setTimeout(() => {
    $notification.addClass('notification--is-visible');
  }, 100);

  setTimeout(() => {
    $notification.removeClass('notification--is-visible');

    setTimeout(() => {
      $notification.remove();
    }, scssVars.notificationAnimationTime);
  }, options.time);
};

export default (opts = {}) => {
  const options = {
    text: translate('error.general'),
    time: 3000,
    type: 'error',
    ...opts,
  };

  const $notifications = $('.notification');

  if ($notifications.length >= 1) {
    $notifications.removeClass('notification--is-visible');

    setTimeout(() => {
      $notifications.remove();
      appendNotification(options);
    }, scssVars.notificationAnimationTime);
  } else {
    appendNotification(options);
  }
};
