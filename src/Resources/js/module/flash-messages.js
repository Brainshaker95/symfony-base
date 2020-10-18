import $ from 'jquery';

const flashMessagesVisibleTime = 3000;

export default () => {
  const $flashMessages = $('.flash-messages');

  if (!$flashMessages.length) {
    return;
  }

  setTimeout(() => {
    $flashMessages
      .find('.alert')
      .slideUp('fast');
  }, flashMessagesVisibleTime);
};
