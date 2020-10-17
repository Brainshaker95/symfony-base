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
    <button type="button" class="button button--success storage-consent__accept">Accept</button>
    <button type="button" class="button button--error storage-consent__decline">Decline</button>
  </div>`);

  const $storageConsent = $('.storage-consent');

  $storageConsent.css({
    background: 'royalblue',
    'z-index': 1,
    position: 'fixed',
    width: '100%',
    height: '100px',
    bottom: 0,
  });

  $storageConsent.find('.storage-consent__accept').on('click', () => {
    localStorage.setItem(storageNames.consent, true);
    $storageConsent.hide();
  });

  $storageConsent.find('.storage-consent__decline').on('click', () => {
    $storageConsent.hide();
  });
};
