import $ from 'jquery';

import ajax from '../../util/ajax';
import { validateForm } from './validate';

export default (opts = {}) => {
  const options = {
    $form: $('.form').eq(0),
    ...opts,
  };

  const { $form } = options;

  $form.data('ajax', true);

  $form.on('submit', (event) => {
    event.preventDefault();

    if (!validateForm($form)) {
      return;
    }

    ajax({
      method: $form.attr('method'),
      url: $form.attr('action'),
      data: $form.serialize(),
      $button: $form.find('[type="submit"]'),
      ...options,
    });
  });
};
