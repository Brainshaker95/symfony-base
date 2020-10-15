import $ from 'jquery';

import ajax from '../util/ajax';
import initModal from '../module/modal';
import notify from '../module/notify';
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

const onModalConfirm = (close, selector, successMessage, successCallback) => {
  const $modal = $(`.${selector}-modal`);
  const $target = $modal.data('target');
  const $confirmButton = $modal.find('.modal__confirm');
  const $declineButton = $modal.find('.modal__decline');

  $declineButton.prop('disabled', true);

  ajax({
    url: $('.users').data(`${selector}-path`),
    data: {
      id: $target.data('id'),
    },
    $button: $confirmButton,
    done: (response) => {
      const $userRow = $target.closest('.users__row');

      $declineButton.prop('disabled', false);

      if (requestDone(
        $userRow,
        response.success,
        successMessage,
      )) {
        successCallback();
      }
    },
    always: close,
  });
};

$(() => {
  const $users = $('.users');
  const $deleteUserModal = $('.delete-user-modal');
  const $deleteProfileImageModal = $('.delete-profile-image-modal');

  const deleteUsermodal = initModal($deleteUserModal, {
    onConfirm: (close) => onModalConfirm(
      close,
      'delete-user',
      'success.user_deleted',
      () => {
        const $userRow = $deleteUserModal
          .data('target')
          .closest('.users__row');

        $userRow.slideUp('fast', () => {
          $userRow.remove();
        });
      },
    ),
  });

  const deleteProfileImageModal = initModal($deleteProfileImageModal, {
    onConfirm: (close) => onModalConfirm(
      close,
      'delete-profile-image',
      'success.profile_image_deleted',
      () => {
        const $target = $deleteProfileImageModal.data('target');
        const $userRow = $target.closest('.users__row');

        $target.remove();
        $userRow
          .find('.users__profile-image')
          .attr('src', `${$('[data-placeholder-image]')
            .data('placeholder-image')}`);
      },
    ),
  });

  const openModal = ($target, selector) => {
    const $modal = $(`.${selector}-modal`);

    $modal
      .find('.modal__body')
      .text($target
        .closest('.users__row')
        .find('.users__username')
        .text());

    $modal.data('target', $target);

    if (selector === 'delete-user') {
      deleteUsermodal.open();
    } else if (selector === 'delete-profile-image') {
      deleteProfileImageModal.open();
    }
  };

  $('.delete-user').on('click', ({ currentTarget }) => openModal(
    $(currentTarget),
    'delete-user',
  ));

  $('.delete-profile-image').on('click', ({ currentTarget }) => openModal(
    $(currentTarget),
    'delete-profile-image',
  ));

  $('.user-roles').on('change', ({ currentTarget }) => {
    $(currentTarget)
      .closest('.users__row')
      .find('.update-roles')
      .prop('disabled', false)
      .addClass('update-roles--is-visible');
  });

  $('.update-roles').on('click', ({ currentTarget }) => {
    const $target = $(currentTarget);

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
      always: () => {
        $target.removeClass('update-roles--is-visible');
      },
    });
  });
});
