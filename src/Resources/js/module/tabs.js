import $ from 'jquery';

const initTabs = ($tabs) => {
  const $tabButtons = $tabs.find('.tabs__button');
  const $tabContents = $tabs.find('.tabs__content');

  $tabButtons.on('click', ({ currentTarget }) => {
    const $target = $(currentTarget);

    $tabContents.hide();
    $tabContents.filter(`[data-id="${$target.data('id')}"]`).show();
    $tabButtons.removeClass('tabs__button--is-active');
    $target.addClass('tabs__button--is-active');
  });
};

export default () => {
  $('.tabs').each((index, tabs) => initTabs($(tabs)));
};
