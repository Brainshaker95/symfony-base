import $ from 'jquery';

import appendItems from '../util/template-helper';
import initModal from '../module/modal';
import initPaginations from '../module/pagination';
import scrollTo from '../util/scroll-to';
import translate from '../util/translate';

$(() => {
  const modal = initModal($('.upload-images-modal'));
  const $galleryGrid = $('.gallery-grid');
  const $loadingIndicator = $galleryGrid.next('.loading');
  const $pagination = $('.gallery-pagination');

  $('.upload-images').on('click', () => modal.open());

  const pagination = initPaginations({
    $paginations: $pagination,
    onLoading: () => {
      $galleryGrid.empty();
      $loadingIndicator.removeClass('hide');
    },
    done: ({ images }) => {
      appendItems(images, $galleryGrid, $('.gallery-grid-template'));
      scrollTo();
    },
    always: () => {
      $loadingIndicator.addClass('hide');
    },
  });

  const loadPageThroughFancybox = (currentPage, page) => {
    $.fancybox.close();

    pagination.update(currentPage + page);
    pagination.loadPage(currentPage + page, () => {
      $galleryGrid
        .find('[data-fancybox]')
        .first()
        .trigger('click');
    });
  };

  const beforeShow = (identifier, currentPage, page) => {
    const $fancyboxContainer = $('.fancybox-container');

    $fancyboxContainer.append(
      `<button class="arrow-button arrow-button--${identifier} fancybox-paging fancybox-${identifier}-page fancybox-button" title="${translate(`fancybox.${identifier}`)}" aria-label="${translate(`fancybox.${identifier}`)}"></button>`,
    );

    $fancyboxContainer
      .find(`.fancybox-${identifier === 'prev' ? 'next' : 'prev'}-page`)
      .remove();

    $fancyboxContainer
      .find(`.fancybox-${identifier}-page`)
      .on('click', () => loadPageThroughFancybox(currentPage, page));
  };

  $.fancybox.defaults = {
    ...$.fancybox.defaults,
    beforeShow: ({ currIndex, group }) => {
      const totalPages = $pagination.data('total-pages');
      const currentPage = $pagination.data('current-page');

      if (currIndex === group.length - 1 && currentPage < totalPages) {
        beforeShow('next', currentPage, 1);
      } else if (currIndex === 0 && currentPage > 1) {
        beforeShow('prev', currentPage, -1);
      } else {
        $('.fancybox-container')
          .find('.fancybox-paging')
          .remove();
      }
    },
  };
});
