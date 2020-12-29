import $ from 'jquery';

import appendItems from '../util/template-helper';
import { initAccordion } from '../module/accordion';
import initPaginations from '../module/pagination';
import scrollTo from '../util/scroll-to';

$(() => {
  const $news = $('.news');
  const $loadingIndicator = $news.next('.loading');
  const $pagination = $('.news-pagination');

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
