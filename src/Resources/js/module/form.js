import $ from 'jquery';

import ajax from '../util/ajax';
import scrollTo from '../util/scroll-to';
import { validate } from './input';

const validateForm = ($form) => {
  let errorCount = 0;

  $form.find('[required]').each((index, input) => {
    errorCount += validate($(input)) ? 0 : 1;
  });

  return errorCount === 0;
};

export default (opts = {}) => {
  const $firstForm = $('.form').eq(0);
  const options = {
    $form: $firstForm,
    ...opts,
  };

  const { $form } = options;

  $form.on('submit', (event) => {
    event.preventDefault();

    if (!validateForm($form)) {
      const $firstError = $form.find('.form__error').first();

      scrollTo(
        $firstError,
        $firstError.height() + $firstError.prev('input').height(),
      );

      return;
    }

    ajax({
      method: $form.attr('method'),
      url: $form.attr('action'),
      data: $form.serialize(),
      $button: $firstForm.find('[type="submit"]'),
      ...options,
    });
  });
};
