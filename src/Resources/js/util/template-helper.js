import $ from 'jquery';

export default (items, $wrapper, $item) => {
  items.forEach((item) => {
    const $template = $($item.html());

    Object.entries(item).forEach(([key, value]) => {
      const isImage = key.indexOf('image__') === 0;
      const isLink = key.indexOf('link__') === 0;
      let reference = key;

      if (isImage) {
        reference = 'image';
      } else if (isLink) {
        reference = 'link';
      }

      const $reference = $template.find(`[data-reference="${reference}"]`);
      const condition = $reference.data('condition');

      if (condition && !item[condition]) {
        $reference.remove();

        return;
      }

      if (isImage) {
        $reference.attr(key.replace('image__', ''), value);
      } else if (isLink) {
        $reference.attr(key.replace('link__', ''), value);
      } else if ($reference.data('is-html')) {
        $reference.html(value);
      } else {
        $reference.text(value);
      }
    });

    $template.find('[data-reference]').removeAttr('data-reference');
    $template.find('[data-is-html]').removeAttr('data-is-html');
    $template.find('[data-condition]').removeAttr('data-condition');
    $wrapper.append($template);
  });
};
