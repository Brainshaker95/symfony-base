import $ from 'jquery';

import ajax from '../util/ajax';
import notify from './notify';

export default (opts) => {
  const options = {
    $paginations: null,
    onLoading: $.noop,
    done: $.noop,
    data: {},
    ...opts,
  };

  const { $paginations } = options;

  if (!$paginations) {
    return {};
  }

  let url;

  $paginations.each((index, pagination) => {
    if (!url) {
      url = $(pagination).data('path');
    }
  });

  if (!url) {
    return {};
  }

  const loadPage = (page, callback = $.noop) => {
    options.onLoading();

    ajax({
      url,
      ...options,
      data: {
        page,
        ...options.data,
      },
      done: (response) => {
        if (!response.success) {
          notify();

          return;
        }

        options.done(response);
        callback();
      },
    });
  };

  const updatePagination = (page) => {
    if (window.history.replaceState) {
      const params = [];

      if (options.method === 'GET') {
        Object.entries(options.data).forEach(([key, value]) => {
          const keyValue = `${key}=${value}`;

          if (!params.length) {
            params.push(`?${keyValue}`);
          } else {
            params.push(`&${keyValue}`);
          }
        });
      }

      window.history.replaceState(
        null,
        null,
        `${window.location.pathname}${params.join('')}${params.length ? '&' : '?'}page=${page}`,
      );
    }

    $paginations.each((index, pagination) => {
      const $pagination = $(pagination);
      const $pageButtons = $pagination.find('.pagination__page');
      const totalPages = $pagination.data('total-pages');
      const padding = $pagination.data('padding') || 2;
      const isInFirstBlock = page - padding <= 0;
      const isInLastBlock = page + padding >= totalPages;
      const visiblePageCount = 2 * padding + 1;
      let lowerBound = 1;
      let upperBound = visiblePageCount;

      if (!isInFirstBlock) {
        if (!isInLastBlock) {
          lowerBound = page - padding;
          upperBound = page + padding;
        } else {
          lowerBound = totalPages - visiblePageCount + 1;
          upperBound = totalPages;
        }
      }

      $pagination.data('current-page', page);

      $pageButtons
        .removeClass('pagination__page--is-active')
        .removeClass('disabled')
        .filter(`[data-page="${page}"]`)
        .addClass('pagination__page--is-active')
        .addClass('disabled');

      $pageButtons.each((i, pageButton) => {
        const $pageButton = $(pageButton);
        const thePage = $pageButton.data('page');
        const hideButton = totalPages > visiblePageCount
          && (thePage < lowerBound || thePage > upperBound);

        if (hideButton) {
          $pageButton
            .addClass('hide-i')
            .parent()
            .attr('aria-hidden', true);
        } else {
          $pageButton
            .removeClass('hide-i')
            .parent()
            .attr('aria-hidden', false);
        }
      });
    });
  };

  const attachHandlers = ($pagination) => {
    const totalPages = $pagination.data('total-pages');

    $pagination.find('.arrow-button--prev, .arrow-button--next').on('click', ({ currentTarget }) => {
      const currentPage = $pagination.data('current-page');
      const page = currentPage + ($(currentTarget).hasClass('arrow-button--prev') ? -1 : 1);

      if (page === 0 || page > totalPages) {
        return;
      }

      updatePagination(page);
      loadPage(page);
    });

    $pagination.find('.pagination__page').on('click', ({ currentTarget }) => {
      const $target = $(currentTarget);
      const page = $target.data('page');

      if ($target.hasClass('pagination__page--is-active')) {
        return;
      }

      updatePagination(page);
      loadPage(page);
    });
  };

  $paginations.each((index, pagination) => attachHandlers($(pagination)));

  return {
    loadPage,
    update: updatePagination,
  };
};
