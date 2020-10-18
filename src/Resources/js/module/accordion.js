import $ from 'jquery';

import keycode from '../util/keycode';

const toggleAccordion = ($target) => {
  const $activeTitle = $target
    .closest('.accordion')
    .find('.accordion__title--is-expanded');

  const $content = $target
    .closest('.accordion__item')
    .find('.accordion__content');

  $activeTitle
    .closest('.accordion__item')
    .find('.accordion__content')
    .stop()
    .slideUp();

  $content.stop().slideToggle();

  if ($activeTitle[0] !== $target[0]) {
    $activeTitle.removeClass('accordion__title--is-expanded');
  }

  $target.toggleClass('accordion__title--is-expanded');
};

const initAccordion = ($accordion) => {
  const $accordionItem = $accordion.find('.accordion__item');

  $accordionItem.attr('tabindex', 0);

  $accordion
    .find('.accordion__title')
    .on('click', ({ currentTarget }) => toggleAccordion($(currentTarget)));

  $accordionItem.on('keydown', (event) => {
    if (event.type === 'keydown' && event.key !== keycode.enter) {
      return;
    }

    toggleAccordion(
      $(event.currentTarget).find('.accordion__title'),
    );
  });
};

export default () => {
  $('.accordion').each((index, accordion) => initAccordion($(accordion)));
};
