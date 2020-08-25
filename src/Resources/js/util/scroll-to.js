import $ from 'jquery';

export default ($theTarget, offset = 0, callback = () => {}) => {
  let $target = $theTarget;

  if (!$target || !$target.length) {
    $target = $('main');
  }

  $('html, body').animate({
    scrollTop: $target.offset().top - $('.header').height() - offset,
  }, 300, 'swing', callback);
};
