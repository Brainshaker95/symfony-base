import $ from 'jquery';
import 'babel-polyfill';

import anchor from './module/anchor';
import flashMessages from './module/flash-messages';
import form from './module/form';
import translate from './util/translate';

$(() => {
  anchor();
  flashMessages();
  form();

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
