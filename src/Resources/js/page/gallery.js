import $ from 'jquery';

import initModal from '../module/modal';

$(() => {
  const modal = initModal($('.upload-images-modal'));

  $('.upload-images').on('click', () => modal.open());
});
