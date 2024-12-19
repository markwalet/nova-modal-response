let mix = require('laravel-mix')
let path = require('path')
let NovaExtension = require('laravel-nova-devtool')

mix.extend('nova', new NovaExtension())

mix
  .setPublicPath('dist')
  .js('resources/js/asset.js', 'js')
  .postCss('resources/css/asset.css', 'css', [
      require('postcss-partial-import')
  ])
  .vue({ version: 3 })
  .alias({
    '@': path.join(__dirname, 'resources/js/'),
  })
  .nova('markwalet/nova-modal-response')
