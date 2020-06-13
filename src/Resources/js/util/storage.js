import $ from 'jquery';

export const storageNames = {
  consent: 'DAVEGAME_CONSENT',
  levels: 'DAVEGAME_LEVELS',
  progression: 'DAVEGAME_PROGRESSION',
  scores: 'DAVEGAME_SCORES',
};

export const initStorageConsent = () => {
  if (localStorage.getItem(storageNames.consent)) {
    return;
  }

  $('body').append(templates.storageConsent);

  const $storageConsent = $('.storage-consent');

  $storageConsent.find('.accept').on('click', () => {
    localStorage.setItem(storageNames.consent, true);
    $storageConsent.hide();
  });

  $storageConsent.find('.decline').on('click', () => {
    $storageConsent.hide();
  });
};
