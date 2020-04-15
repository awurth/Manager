const Encore = require('@symfony/webpack-encore');

if (!Encore.isRuntimeEnvironmentConfigured()) {
  Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
  .setOutputPath('public/build/')
  .setPublicPath('/build')
  .addEntry('app', './assets/ts/main.ts')
  .splitEntryChunks()
  .enableSingleRuntimeChunk()
  .cleanupOutputBeforeBuild()
  .enableBuildNotifications()
  .enableSourceMaps(!Encore.isProduction())
  .enableVersioning(Encore.isProduction())
  .configureBabelPresetEnv(config => {
    config.useBuiltIns = 'usage';
    config.corejs = 3;
  })
  .enableSassLoader()
  .enableTypeScriptLoader()
  .enablePostCssLoader()
  .copyFiles([
    { from: './assets/images' }
  ])
;

module.exports = Encore.getWebpackConfig();
