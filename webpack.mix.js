let mix = require('laravel-mix')
let path = require('path')
let NovaExtension = require('laravel-nova-devtool')

mix.extend('nova', new NovaExtension())

// Production builds emit minified `*.min.*` files: the bundle shipped to
// consumers and tracked in git. Dev/watch builds emit unminified `*.js`/`*.css`
// that are gitignored, so iterating locally never dirties the shipped bundle.
// See docs/adr/0005-dev-asset-builds-isolated-by-filename.md.
let suffix = mix.inProduction() ? '.min' : ''

mix
  .setPublicPath('dist')
  .js('resources/js/asset.js', `js/asset${suffix}.js`)
  .postCss('resources/css/asset.css', `css/asset${suffix}.css`, [
      require('postcss-partial-import')
  ])
  .vue({ version: 3 })
  .alias({
    '@': path.join(__dirname, 'resources/js/'),
  })
  .nova('markwalet/nova-modal-response')
