import $ from 'jquery';

import scrollTo from '../util/scroll-to';
import isInView from '../util/is-in-view';

const firstFocusable = ($target) => $target
  .find('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])')
  .first();

export default () => {
  $('a[href*=\\#]').on('click', (event) => {
    const { currentTarget } = event;
    const $anchor = $(currentTarget);
    const $target = $(currentTarget.hash);
    const isSkipToContent = $anchor.hasClass('skip-to-page-content');

    event.preventDefault();
    window.location.hash = $anchor.attr('href');

    if (!isSkipToContent) {
      scrollTo($target);

      return;
    }

    const $firstFocusable = firstFocusable($target);

    $anchor.hide();

    scrollTo($target, 0, () => {
      $anchor.show();

      if (isInView($firstFocusable)) {
        $firstFocusable.trigger('focus');
      }
    });
  });

  if (window.location.hash) {
    scrollTo($(`${window.location.hash}`));
  }
};
