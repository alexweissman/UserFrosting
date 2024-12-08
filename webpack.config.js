import Encore from '@symfony/webpack-encore'
import ForkTsCheckerWebpackPlugin from 'fork-ts-checker-webpack-plugin'
import webpack from 'webpack'
import App from './webpack.entries.js'

// List dependent sprinkles and local entries files
const sprinkles = {
    // AdminLTE: require('@userfrosting/theme-adminlte/webpack.entries'),
    // Admin: require('@userfrosting/sprinkle-admin/webpack.entries'),
    App
}

// Merge dependent Sprinkles entries with local entries
let entries = {}
Object.values(sprinkles).forEach(sprinkle => {
    entries = Object.assign(entries, sprinkle);
});

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.UF_MODE || 'dev');
}

Encore
    // directory where compiled assets will be stored
    .setOutputPath('public/assets')
    
    // public path used by the web server to access the output path
    .setPublicPath('/assets/')
    
    // only needed for CDN's or sub-directory deploy
    //.setManifestKeyPrefix('build/')

    // Include all entries
    .addEntries(entries)

    // Copy public assets
    .copyFiles({ from: './app/assets/public', to: '[path][name].[ext]' })

    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()
    // .disableSingleRuntimeChunk()
    
    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableTypeScriptLoader()

    // Allow typescript to parse vue components imported from source
    // eg.: @userfrosting/theme-pink-cupcake-example/src/views/DashboardAlerts.vue
    .configureLoaderRule('typescript', loaderRule => {
        loaderRule.exclude = undefined
    })
    .enableSourceMaps(!Encore.isProduction())

    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())
    .enableVueLoader(() => {}, { 
        runtimeCompilerBuild: false
    })
    .enableLessLoader()
    .addPlugin(new webpack.DefinePlugin({
        __VUE_OPTIONS_API__: true,
        __VUE_PROD_DEVTOOLS__: false,
        __VUE_PROD_HYDRATION_MISMATCH_DETAILS__: false
    }))
    .addPlugin(new ForkTsCheckerWebpackPlugin())

    // Disable client overlay
    // @see https://github.com/vuejs/vue-cli/issues/7431#issuecomment-1804682832
    .configureDevServerOptions(options => {
        options.client = {
            overlay: false
        }
    })
;

export default Encore.getWebpackConfig();