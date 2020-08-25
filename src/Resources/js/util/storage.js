// TODO: This file is just for demo purposes, remove if not needed

import $ from 'jquery';

export const storageNames = {
  consent: 'SYMFONY_BASE_CONSENT',
};

export const initStorageConsent = () => {
  if (localStorage.getItem(storageNames.consent)) {
    return;
  }

  $('body').append(`<div class="storage-consent">
    <button type="button" class="accept">accept</button>
    <button type="button" class="decline">decline</button>
  </div>`);

  const $storageConsent = $('.storage-consent');

  $storageConsent.css({
    background: 'royalblue',
    'z-index': 1,
    position: 'absolute',
  });

  $storageConsent.find('.accept').on('click', () => {
    localStorage.setItem(storageNames.consent, true);
    $storageConsent.hide();
  });

  $storageConsent.find('.decline').on('click', () => {
    $storageConsent.hide();
  });
};
