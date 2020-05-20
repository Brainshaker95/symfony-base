import $ from 'jquery';

export default ($target, offset = 0) => {
  $('html, body').animate({
    scrollTop: $target.offset().top - $('.header').height() - offset,
  }, 300);
};
