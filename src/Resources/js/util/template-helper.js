import $ from 'jquery';

export default (items, $wrapper, $item) => {
  items.forEach((item) => {
    const $template = $($item.html());

    Object.entries(item).forEach(([key, value]) => {
      const isAttribute = key.indexOf('__') >= 0;
      let reference = key;

      if (isAttribute) {
        [reference] = key.split('__');
      }

      const $reference = $template.find(`[data-reference="${reference}"]`);
      const condition = $reference.data('condition');

      if (condition && !item[condition]) {
        $reference.remove();

        return;
      }

      if (isAttribute) {
        $reference.attr(key.replace(`${reference}__`, ''), value);
      } else if ($reference.data('is-html')) {
        $reference.html(value);
      } else {
        $reference.text(value);
      }

      $reference.removeAttr('data-condition');
    });

    const $conditionals = $template.find('[data-condition]');

    $conditionals.each((index, conditional) => {
      const $conditional = $(conditional);

      if (!item[$conditional.data('condition')]) {
        $conditional.remove();
      } else {
        $($conditional.html()).insertBefore($conditional);
        $conditional.remove();
      }
    });

    $template.find('[data-reference]').removeAttr('data-reference');
    $template.find('[data-is-html]').removeAttr('data-is-html');
    $wrapper.append($template);
  });
};
