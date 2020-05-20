import $ from 'jquery';
import 'babel-polyfill';

import initForm from './module/form';
import { initInputs } from './module/input';
import initModal from './module/modal';

$(() => {
  initInputs();

  // Form example usage

  // initForm({
  //   $form: $('.form'),
  //   done: (response) => {
  //     // console.log(response);
  //   },
  //   fail: (response) => {
  //     // console.log(response);
  //   },
  //   errorMessage: translate('error.general'),
  //   errorType: 'danger',
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
