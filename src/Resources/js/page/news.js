import $ from 'jquery';

import appendItems from '../util/template-helper';
import { firstFocusable } from '../module/anchor';
import { initAccordion } from '../module/accordion';
import initModal from '../module/modal';
import initPaginations from '../module/pagination';
import scrollTo from '../util/scroll-to';

$(() => {
  const $news = $('.news');
  const $loadingIndicator = $news.next('.loading');
  const $pagination = $('.news-pagination');
  const $addNewsArticleModal = $('.add-news-article-modal');
  const addNewsArticleModal = initModal($addNewsArticleModal, {
    onOpen: () => {
      const $firstFocusable = firstFocusable($addNewsArticleModal);

      if ($firstFocusable) {
        $firstFocusable.trigger('focus');
      }
    },
  });

  $('.add-news-article').on('click', () => addNewsArticleModal.open());

  initPaginations({
    $paginations: $pagination,
    onLoading: () => {
      $news.empty();
      $loadingIndicator.removeClass('hide');
    },
    done: ({ newsArticles }) => {
      appendItems(newsArticles, $news, $('.news-item-template'));
      initAccordion($news);
      scrollTo();
    },
    always: () => {
      $loadingIndicator.addClass('hide');
    },
  });
});
