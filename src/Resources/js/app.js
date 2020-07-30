import $ from 'jquery';
import 'babel-polyfill';
import 'lazysizes';

import accordion from './module/accordion';
import anchor from './module/anchor';
import initAjaxForm from './module/form/ajax';
import flashMessages from './module/flash-messages';
import form from './module/form';
import navigation from './module/navigation';
import translate from './util/translate';

$(() => {
  // TODO: Remove this button
  $('.toggle-theme').on('click', () => {
    $('body').toggleClass('theme--dark');
  });

  accordion();
  anchor();
  flashMessages();
  form();
  navigation();

  // Form example usage

  // initAjaxForm({
  //   $form: $('.form'),
  //   done: (response) => {
  //     console.log(response);
  //   },
  //   fail: (response) => {
  //     console.log(response);
  //   },
  //   errorMessage: translate('error.general'),
  //   errorType: 'error',
  //   errorTime: 3000,
  // });

  // Modal example usage

  // const modal = initModal($('.modal'), {
  //   onClose: () => console.log('closed'),
  //   onOpen: () => console.log('opened'),
  //   onConfirm: (close) => {
  //     console.log('confirmed');

  //     setTimeout(() => {
  //       close();
  //     }, 1000);
  //   },
  //   onDecline: (close) => {
  //     console.log('declined');

  //     setTimeout(() => {
  //       close();
  //     }, 1000);
  //   },
  // });

  // modal.open();
});
