const ie = (userAgent) => userAgent.indexOf('MSIE ') > 0
  || !!userAgent.match(/Trident.*rv:11\./);

export default () => {
  const { userAgent } = window.navigator;

  return {
    ie: () => ie(userAgent),
  };
};
