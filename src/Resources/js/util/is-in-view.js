export default ($element) => {
  const boundingBox = $element[0].getBoundingClientRect();

  return (
    boundingBox.top >= 0
      && boundingBox.left >= 0
      && boundingBox.bottom <= (window.innerHeight || document.documentElement.clientHeight)
      && boundingBox.right <= (window.innerWidth || document.documentElement.clientWidth)
  );
};
