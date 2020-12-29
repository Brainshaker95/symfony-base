import $ from 'jquery';

import ajax from '../util/ajax';
import { firstFocusable } from '../module/anchor';
import form from '../module/form';
import initModal from '../module/modal';
import notify from '../module/notify';
import translate from '../util/translate';
import { validateForm } from '../module/form/validate';

const requestDone = ($newsRow, success, successMessage, title) => {
  if (!success) {
    notify();

    return false;
  }

  notify({
    type: 'success',
    text: translate(successMessage, {
      title: title || $newsRow.find('.news__title').text(),
    }),
  });

  return true;
};

const onModalConfirm = (close, selector, successMessage, successCallback, method) => {
  const $modal = $(`.${selector}-modal`);
  const $target = $modal.data('target');
  const $confirmButton = $modal.find('.modal__confirm');
  const $declineButton = $modal.find('.modal__decline');

  $declineButton.prop('disabled', true);

  ajax({
    url: $('.news').data(`${selector}-path`),
    method: method || 'POST',
    data: {
      id: $target.data('id'),
      title: $modal.find('[name="title"]').val() || null,
      text: $modal.find('[name="text"]').val() || null,
    },
    $button: $confirmButton,
    done: (response) => {
      const $newsRow = $target.closest('.news__row');

      $declineButton.prop('disabled', false);

      if (requestDone(
        $newsRow,
        response.success,
        successMessage,
        response.title || null,
      )) {
        successCallback(response);
      }
    },
    always: close,
  });
};

$(() => {
  const $addNewsArticleModal = $('.add-news-article-modal');
  const $deleteNewsArticleModal = $('.delete-news-article-modal');
  const $editNewsArticleModal = $('.edit-news-article-modal');

  const addNewsArticleModal = initModal($addNewsArticleModal, {
    onOpen: () => {
      const $firstFocusable = firstFocusable($addNewsArticleModal);

      if ($firstFocusable) {
        $firstFocusable.trigger('focus');
      }
    },
  });

  const deleteNewsArticleModal = initModal($deleteNewsArticleModal, {
    onConfirm: (close) => onModalConfirm(
      close,
      'delete-news-article',
      'success.news_article_deleted',
      () => {
        const $newsRow = $deleteNewsArticleModal
          .data('target')
          .closest('.news__row');

        $newsRow.slideUp('fast', () => {
          $newsRow.remove();
        });
      },
      'DELETE',
    ),
  });

  const editNewsArticleModal = initModal($editNewsArticleModal, {
    onOpen: () => {
      const $form = $editNewsArticleModal.find('form');

      form($form);
      $form.on('submit', () => false);
    },
    onConfirm: (close) => {
      if (!validateForm($editNewsArticleModal.find('form'))) {
        return;
      }

      onModalConfirm(
        close,
        'edit-news-article',
        'success.news_article_edited',
        ({ title }) => {
          $editNewsArticleModal
            .data('target')
            .closest('.news__row')
            .find('.news__title')
            .text(title);
        },
        'POST',
      );
    },
  });

  const openModal = ($target, selector) => {
    const $modal = $(`.${selector}-modal`);

    $modal
      .find('.modal__body')
      .text($target
        .closest('.news__row')
        .find('.news__title')
        .text());

    $modal.data('target', $target);

    if (selector === 'delete-news-article') {
      deleteNewsArticleModal.open();
    } else if (selector === 'edit-news-article') {
      ajax({
        url: $('.news').data('edit-news-article-path'),
        method: 'GET',
        data: {
          id: $target.data('id'),
        },
        done: (formHtml) => {
          $editNewsArticleModal
            .find('.modal__body')
            .html(formHtml)
            .find('.form__row--submit')
            .remove();

          editNewsArticleModal.open();
        },
      });
    }
  };

  $('.delete-news-article').on('click', ({ currentTarget }) => openModal(
    $(currentTarget),
    'delete-news-article',
  ));

  $('.edit-news-article').on('click', ({ currentTarget }) => openModal(
    $(currentTarget),
    'edit-news-article',
  ));

  $('.add-news-article').on('click', () => addNewsArticleModal.open());
});
