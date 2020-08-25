import $ from 'jquery';

import appendItems from '../util/template-helper';
import initModal from '../module/modal';
import initPaginations from '../module/pagination';
import scrollTo from '../util/scroll-to';

$(() => {
  const modal = initModal($('.upload-images-modal'));
  const $galleryGrid = $('.gallery-grid');
  const $loadingIndicator = $galleryGrid.next('.loading');

  $('.upload-images').on('click', () => modal.open());

  initPaginations({
    $paginations: $('.gallery-pagination'),
    onLoading: () => {
      $galleryGrid.empty();
      $loadingIndicator.removeClass('hide');
    },
    done: ({ images }) => {
      appendItems(images, $('.gallery-grid'), $('.gallery-grid-template'));
      scrollTo();
    },
    always: () => {
      $loadingIndicator.addClass('hide');
    },
  });
});
