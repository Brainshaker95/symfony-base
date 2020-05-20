import $ from 'jquery';

export default ($modal, opts = {}) => {
  const options = {
    onOpen: () => {},
    onClose: () => {},
    onConfirm: (close) => close(),
    onDecline: (close) => close(),
    ...opts,
  };

  const $body = $('body');
  const $overlay = $modal.next('.modal__overlay');

  const open = () => {
    $modal.addClass('modal--is-visible');
    $overlay.addClass('modal__overlay--is-visible');
    $body.addClass('no-scroll');
    options.onOpen();
  };

  const close = () => {
    $modal.removeClass('modal--is-visible');
    $overlay.removeClass('modal__overlay--is-visible');
    $body.removeClass('no-scroll');
    options.onClose();
  };

  $overlay.on('click', close);
  $modal.find('.modal__close').on('click', close);
  $modal.find('.modal__confirm').on('click', () => options.onConfirm(close));
  $modal.find('.modal__decline').on('click', () => options.onDecline(close));

  return {
    open,
    close,
  };
};
