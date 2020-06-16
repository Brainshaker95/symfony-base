const Encore = require('@symfony/webpack-encore');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const StyleLintPlugin = require('stylelint-webpack-plugin');

const sourceMap = Encore.isProduction();

Encore
  .setOutputPath('public/build/')
  .setPublicPath('/build')
  .addEntry('app', './src/Resources/js/app.js')
  .addEntry('gallery', './src/Resources/js/page/gallery.js')
  .addEntry('users', './src/Resources/js/page/users.js')
  .addStyleEntry('main', './src/Resources/scss/main.scss')
  .addStyleEntry('page_users', './src/Resources/scss/page/users.scss')
  .enableSourceMaps(!sourceMap)
  .enablePostCssLoader()
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
  }))
  .addPlugin(new MiniCssExtractPlugin({
    filename: '[name].[chunkhash].css',
    chunkFilename: '[name].[chunkhash].css',
  }))
  .addLoader({
    test: /\.s[ac]ss$/,
    use: [
      MiniCssExtractPlugin.loader,
      {
        loader: 'css-loader',
        options: {
          sourceMap,
          importLoaders: 1,
        },
      },
      {
        loader: 'postcss-loader',
        options: { sourceMap },
      },
      {
        loader: 'resolve-url-loader',
        options: { sourceMap },
      },
      {
        loader: 'sass-loader',
        options: { sourceMap },
      },
    ],
  });

module.exports = Encore.getWebpackConfig();
