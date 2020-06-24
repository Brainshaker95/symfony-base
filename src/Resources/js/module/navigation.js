import $ from 'jquery';

import keycode from '../util/keycode';
import scssVars from '../util/scss-vars';

const isMobile = () => window.innerWidth < scssVars.mainNavigationBreakpoint;

export default () => {
  const $mainNavigation = $('.navigation--main');
  const $mainNavigationList = $mainNavigation.find('.navigation__list');
  const $subNavigationLinks = $('.navigation__link--has-subpages');
  const $hamburger = $('.hamburger');
  const $body = $('body');

  const toggleMainNavigation = () => {
    $body.toggleClass('no-scroll');
    $hamburger.toggleClass('hamburger--is-expanded');
    $mainNavigation.toggleClass('navigation--is-expanded');
    $mainNavigationList.stop().slideToggle();
  };

  const closeMainNavigation = () => {
    $body.removeClass('no-scroll');
    $hamburger.removeClass('hamburger--is-expanded');
    $mainNavigation.removeClass('navigation--is-expanded');
    $mainNavigationList.stop().slideUp();
  };

  $('.navigation__link.button').on('click', ({ currentTarget }) => {
    if (!isMobile()) {
      return;
    }

    const $target = $(currentTarget);

    $target.toggleClass('navigation__link--is-expanded');

    $target
      .next('.subnavigation')
      .stop()
      .slideToggle('fast');
  });

  $hamburger
    .on('click', toggleMainNavigation)
    .on('keydown', (event) => {
      const { which } = event;

      if (which === keycode.escape) {
        closeMainNavigation();
      } else if (which === keycode.arrowDown) {
        event.preventDefault();

        $mainNavigation
          .find('.navigation__link')
          .first()
          .focus();
      }
    });

  $mainNavigationList
    .add($('.navigation--secondary .navigation__list'))
    .on('keydown', (event) => {
      const { which } = event;
      const $focusedLink = $(':focus');
      const $focusedItem = $focusedLink.closest('.navigation__item');
      const isLink = $focusedLink.hasClass('navigation__link');
      const hasSubNavigation = $focusedItem.find('.subnavigation').length;

      const focusPrevItem = () => {
        $focusedItem.prev().find('.navigation__link').focus();
      };

      const focusNextItem = () => {
        $focusedItem.next().find('.navigation__link').focus();
      };

      if (which === keycode.escape) {
        if ($(event.currentTarget).closest('.navigation--main').length && isMobile()) {
          closeMainNavigation();
        }
      } else if (which === keycode.arrowUp) {
        event.preventDefault();

        if (!hasSubNavigation) {
          focusPrevItem();

          return;
        }

        const $prevLink = $focusedLink.parent().prev().find('.subnavigation__link');

        if (isLink && !isMobile()) {
          $focusedItem.prev().find('.navigation__link').focus();
        } else if ($prevLink.length) {
          $prevLink.focus();
        } else if ($focusedLink[0] === $focusedItem.find('.subnavigation__link').first()[0]) {
          $focusedItem.find('.navigation__link').focus();
        } else {
          focusPrevItem();
        }
      } else if (which === keycode.arrowDown) {
        event.preventDefault();

        if (!hasSubNavigation) {
          focusNextItem();

          return;
        }

        const $nextLink = $focusedLink.parent().next().find('.subnavigation__link');

        if (isLink && !isMobile()) {
          $focusedItem.find('.subnavigation .subnavigation__link').first().focus();
        } else if ($nextLink.length) {
          $nextLink.focus();
        } else {
          focusNextItem();
        }
      }
    })
    .on('focusout', (event) => {
      if (!$(event.currentTarget).closest('.navigation--main').length) {
        return;
      }

      setTimeout(() => {
        if (!$mainNavigationList.find($(':focus')).length && isMobile()) {
          closeMainNavigation();
        }
      }, 0);
    });

  $('.navigation__toggle-submenu').on('click', ({ currentTarget }) => {
    const $target = $(currentTarget);

    $target
      .toggleClass('navigation__toggle-submenu--is-expanded')
      .closest('.navigation__item')
      .find('.subnavigation')
      .stop()
      .slideToggle('fast');
  });

  $subNavigationLinks
    .on('focus', ({ currentTarget }) => {
      if (isMobile()) {
        return;
      }

      const $target = $(currentTarget);

      $target.addClass('navigation__link--is-expanded');

      $target
        .closest('.navigation__item')
        .find('.subnavigation')
        .stop()
        .slideDown('fast');
    });

  $subNavigationLinks.parent()
    .on('mouseover', ({ currentTarget }) => {
      if (isMobile()) {
        return;
      }

      const $target = $(currentTarget);

      $target
        .find('.navigation__link')
        .addClass('navigation__link--is-expanded');

      $target
        .find('.subnavigation')
        .stop()
        .slideDown('fast');
    })
    .on('mouseleave focusout', (event) => {
      const $target = $(event.currentTarget);

      setTimeout(() => {
        if (event.type === 'focusout' && $target.find($(':focus')).length) {
          return;
        }

        $target
          .find('.navigation__link')
          .removeClass('navigation__link--is-expanded');

        $target
          .find('.subnavigation')
          .stop()
          .slideUp('fast');
      }, 0);
    });
};
