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

      if (isImage) {
        $reference.attr(key.replace('image__', ''), value);
      } else if (isLink) {
        $reference.attr(key.replace('link__', ''), value);
      } else {
        $reference.text(value);
      }
    });

    $template.find('[data-reference]').removeAttr('data-reference');
    $wrapper.append($template);
  });
};
