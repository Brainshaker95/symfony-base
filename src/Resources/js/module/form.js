import $ from 'jquery';

import initCheckboxes from './form/checkbox';
import initDateInputs from './form/date';
import initFileInputs from './form/file';
import initInputs from './form/input';
import initRadioButtons from './form/radio';
import initSelects from './form/select';
import initTextareas from './form/textarea';
import { validateForm } from './form/validate';

export default () => {
  initCheckboxes();
  initDateInputs();
  initFileInputs();
  initInputs();
  initRadioButtons();
  initSelects();
  initTextareas();

  $('.form').on('submit', (event) => {
    const $form = $(event.currentTarget);

    if ($form.data('ajax')) {
      return true;
    }

    return validateForm($form);
  });
};
