import $ from 'jquery';
import '@fancyapps/fancybox';

export default () => {
  $.fancybox.defaults = {
    ...$.fancybox.defaults,
    lang: $('html').attr('lang'),
    gutter: 50,
    buttons: [
      'slideShow',
      'fullScreen',
      'thumbs',
      'close',
    ],
    transitionEffect: 'tube',
    spinnerTpl: '<div class="loading"></div>',
    btnTpl: {
      ...$.fancybox.defaults.btnTpl,
      close: '<button data-fancybox-close class="button button--close fancybox-button" title="{{CLOSE}}" aria-label="{{CLOSE}}"></button>',
      arrowLeft: '<button data-fancybox-prev class="arrow-button arrow-button--prev fancybox-button" title="{{PREV}}" aria-label="{{PREV}}"></button>',
      arrowRight: '<button data-fancybox-next class="arrow-button arrow-button--next fancybox-button" title="{{NEXT}}" aria-label="{{NEXT}}"></button>',
      slideShow: '<button data-fancybox-play class="button fancybox-button" title="{{PLAY_START}}">'
          + '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M6.5 5.4v13.2l11-6.6z"></path></svg><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M8.33 5.75h2.2v12.5h-2.2V5.75zm5.15 0h2.2v12.5h-2.2V5.75z"></path></svg>'
        + '</button>',
      thumbs: '<button data-fancybox-thumbs class="button fancybox-button" title="{{THUMBS}}" aria-label="{{THUMBS}}">'
          + '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M14.59 14.59h3.76v3.76h-3.76v-3.76zm-4.47 0h3.76v3.76h-3.76v-3.76zm-4.47 0h3.76v3.76H5.65v-3.76zm8.94-4.47h3.76v3.76h-3.76v-3.76zm-4.47 0h3.76v3.76h-3.76v-3.76zm-4.47 0h3.76v3.76H5.65v-3.76zm8.94-4.47h3.76v3.76h-3.76V5.65zm-4.47 0h3.76v3.76h-3.76V5.65zm-4.47 0h3.76v3.76H5.65V5.65z"></path></svg>'
        + '</button>',
      fullScreen: '<button data-fancybox-fullscreen class="button fancybox-button" title="{{FULL_SCREEN}}" aria-label="{{FULL_SCREEN}}">'
          + '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M7 14H5v5h5v-2H7v-3zm-2-4h2V7h3V5H5v5zm12 7h-3v2h5v-5h-2v3zM14 5v2h3v3h2V5h-5z"></path></svg><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M5 16h3v3h2v-5H5zm3-8H5v2h5V5H8zm6 11h2v-3h3v-2h-5zm2-11V5h-2v5h5V8z"></path></svg>'
        + '</button>',
    },
    thumbs: {
      ...$.fancybox.defaults.thumbs,
      autoStart: true,
      axis: 'x',
    },
  };
};
