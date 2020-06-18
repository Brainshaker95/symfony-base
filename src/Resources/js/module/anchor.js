import $ from 'jquery';

import scrollTo from '../util/scroll-to';

const focusFirstFocusable = (hash) => {
  $(`${hash}`)
    .find('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])')
    .first()
    .focus();
};

export default () => {
  $('a[href*=\\#]').on('click', (event) => {
    const { currentTarget } = event;
    const { hash } = currentTarget;
    const $target = $(currentTarget);
    const isSkipToContent = $target.hasClass('skip-to-page-content')

    event.preventDefault();
    window.location.hash = $target.attr('href');

    if (isSkipToContent) {
      $target.hide();
      focusFirstFocusable(hash);
    }

    scrollTo($(`${hash}`), 0, () => {
      if (isSkipToContent) {
        $target.show();
      }
    });
  });

  if (window.location.hash) {
    scrollTo($(`${window.location.hash}`));
  }
};
