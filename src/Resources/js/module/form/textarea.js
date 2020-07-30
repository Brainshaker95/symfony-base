import $ from 'jquery';

const borderWidth = 1;

const setHeight = ($input) => {
  $input.css('height', 0);
  $input.css('height', `${$input[0].scrollHeight + (borderWidth * 2)}px`);
};

export default () => {
  $('.textarea__input').each((index, input) => {
    const $input = $(input);
    const maxLength = $input.attr('maxlength');
    const $counter = $input
      .closest('.textarea')
      .find('.textarea__counter');

    setHeight($input);

    $input.on('input', () => {
      if (maxLength) {
        const currentLength = $input.val().length;

        if (currentLength > maxLength) {
          return;
        }

        $counter.text(`${currentLength} / ${maxLength}`);
      }

      setHeight($input);
    });
  });
};
