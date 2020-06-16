import $ from 'jquery';

import ajax from '../util/ajax';
import initModal from '../module/modal';
import notify from '../util/notify';
import translate from '../util/translate';

const requestDone = ($userRow, success, successMessage) => {
  if (!success) {
    notify();

    return false;
  }

  notify({
    type: 'success',
    text: translate(successMessage, {
      username: $userRow.find('.users__username').text(),
    }),
  });

  return true;
};

$(() => {
  const $users = $('.users');
  const $deleteUserModal = $('.delete-user-modal');
  const $deleteProfileImageModal = $('.delete-profile-image-modal');
  const $deleteUserConfirmButton = $deleteUserModal.find('.modal__confirm');
  const $deleteUserDeclineButton = $deleteUserModal.find('.modal__decline');
  const $deleteProfileImageConfirmButton = $deleteProfileImageModal.find('.modal__confirm');
  const $deleteProfileImageDeclineButton = $deleteProfileImageModal.find('.modal__decline');

  const deleteUsermodal = initModal($deleteUserModal, {
    onConfirm: (close) => {
      const $target = $deleteUserModal.data('target');

      $deleteUserDeclineButton.prop('disabled', true);

      ajax({
        url: $users.data('delete-user-path'),
        data: {
          id: $target.data('id'),
        },
        $button: $deleteUserConfirmButton,
        done: (response) => {
          const $userRow = $target.closest('.users__row');

          $deleteUserDeclineButton.prop('disabled', false);

          if (requestDone(
            $userRow,
            response.success,
            'success.user_deleted',
          )) {
            $userRow.slideUp('fast', () => {
              $userRow.remove();
            });
          }
        },
        always: close,
      });
    },
  });

  const deleteProfileImageModal = initModal($deleteProfileImageModal, {
    onConfirm: (close) => {
      const $target = $deleteProfileImageModal.data('target');

      $deleteProfileImageDeclineButton.prop('disabled', true);

      ajax({
        url: $users.data('delete-profile-image-path'),
        data: {
          id: $target.data('id'),
        },
        $button: $deleteProfileImageConfirmButton,
        done: (response) => {
          const $userRow = $target.closest('.users__row');

          $deleteProfileImageDeclineButton.prop('disabled', false);

          if (requestDone(
            $userRow,
            response.success,
            'success.user_deleted',
          )) {
            $target.remove();
            $userRow
              .find('.users__profile-image')
              .attr('src', `${$('[data-placeholder-image]').data('placeholder-image')}`);
          }
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
    deleteUsermodal.open();
  });

  $('.delete-profile-image').on('click', (event) => {
    const $target = $(event.currentTarget);

    $deleteProfileImageModal
      .find('.modal__body')
      .text(
        $target
          .closest('.users__row')
          .find('.users__username')
          .text(),
      );

    $deleteProfileImageModal.data('target', $target);
    deleteProfileImageModal.open();
  });

  $('.user-roles').on('change', (event) => {
    $(event.currentTarget)
      .closest('.users__row')
      .find('.update-roles')
      .prop('disabled', false);
  });

  $('.update-roles').on('click', (event) => {
    const $target = $(event.currentTarget);

    ajax({
      url: $users.data('update-roles-path'),
      data: {
        roles: $target.parent().find('.user-roles').val(),
        id: $target.data('id'),
      },
      $button: $target,
      done: (response) => requestDone(
        $target.closest('.users__row'),
        response.success,
        'success.roles_updated',
      ),
    });
  });
});
