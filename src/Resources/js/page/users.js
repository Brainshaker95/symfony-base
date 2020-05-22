import $ from 'jquery';

import ajax from '../util/ajax';
import initModal from '../module/modal';
import notify from '../util/notify';
import translate from '../util/translate';

$(() => {
  const url = $('.users').data('path');
  const $deleteUserModal = $('.delete-user-modal');
  const $confirmButton = $deleteUserModal.find('.modal__confirm');
  const $declineButton = $deleteUserModal.find('.modal__decline');

  const modal = initModal($deleteUserModal, {
    onConfirm: (close) => {
      const $target = $deleteUserModal.data('target');

      $declineButton.prop('disabled', true);

      ajax({
        url,
        data: {
          id: $target.data('id'),
        },
        $button: $confirmButton,
        done: (response) => {
          $declineButton.prop('disabled', false);

          if (!response.success) {
            notify();

            return;
          }

          const $userRow = $target.closest('.users__row');

          notify({
            type: 'success',
            text: translate('success.user_deleted', {
              username: $userRow.find('.users__username').text(),
            }),
          });

          $userRow.slideUp('fast', () => {
            $userRow.remove();
          });
        },
        always: close,
      });
    },
  });

  $('.delete-user').on('click', (event) => {
    const $target = $(event.currentTarget);

    $deleteUserModal
      .find('.modal__body')
      .text(
        $target
          .closest('.users__row')
          .find('.users__username')
          .text(),
      );

    $deleteUserModal.data('target', $target);
    modal.open();
  });
});
