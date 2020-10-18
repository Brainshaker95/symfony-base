window.translations = window.translations || {};

export default (key, placeholders) => {
  let translation = window.translations[key] || key;

  if (placeholders) {
    Object.entries(placeholders).forEach(([placeholder, value]) => {
      const replaceRegEx = new RegExp(`{{ ${placeholder} }}`, 'g');

      translation = translation.replace(replaceRegEx, value);
    });
  }

  return translation;
};
