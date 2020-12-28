const Encore = require('@symfony/webpack-encore');
const StyleLintPlugin = require('stylelint-webpack-plugin');

Encore
  .setOutputPath('public/build/')
  .setPublicPath('/build')
  .addEntry('app', './src/Resources/js/app.js')
  .addEntry('admin_news', './src/Resources/js/page/admin-news.js')
  .addEntry('gallery', './src/Resources/js/page/gallery.js')
  .addEntry('news', './src/Resources/js/page/news.js')
  .addEntry('users', './src/Resources/js/page/users.js')
  .addStyleEntry('main', './src/Resources/scss/main.scss')
  .addStyleEntry('page_admin_news', './src/Resources/scss/page/admin-news.scss')
  .addStyleEntry('page_news', './src/Resources/scss/page/news.scss')
  .addStyleEntry('page_users', './src/Resources/scss/page/users.scss')
  .addStyleEntry('user_styles', './src/Resources/scss/theme/user-styles.scss')
  .splitEntryChunks()
  .enableSassLoader()
  .enablePostCssLoader()
  .enableSourceMaps(!Encore.isProduction())
  .cleanupOutputBeforeBuild()
  .enableVersioning()
  .enableSingleRuntimeChunk()
  .autoProvidejQuery()
  .copyFiles({
    from: './src/Resources/img',
    to: 'images/[path][name].[ext]',
  })
  .configureBabel((babelConfig) => {
    babelConfig.plugins.push('@babel/plugin-proposal-object-rest-spread');
  })
  .addPlugin(new StyleLintPlugin({
    context: 'src',
    emitErrors: false,
  }));

module.exports = Encore.getWebpackConfig();
