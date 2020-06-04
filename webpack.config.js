const Encore = require('@symfony/webpack-encore');

if (!Encore.isRuntimeEnvironmentConfigured()) {
  Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
  .setOutputPath('public/build/')
  .setPublicPath('/build')
  .addEntry('app', './assets/ts/main.ts')
  .addEntry('create-project', './assets/ts/pages/create-project.ts')
  .addEntry('create-project-group', './assets/ts/pages/create-project-group.ts')
  .addEntry('link-form', './assets/ts/pages/link-form.ts')
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
  .autoProvidejQuery()
  .enableSassLoader()
  .enableTypeScriptLoader()
  .enableReactPreset()
  .enablePostCssLoader()
  .enableEslintLoader(options => {
    options.configFile = './.eslintrc.js';
    options.parser = '@typescript-eslint/parser';
  })
  .configureLoaderRule('eslint', loader => {
    loader.test = /\.(js|ts)$/;
  })
  .copyFiles([
    { from: './assets/images' }
  ])
;

module.exports = Encore.getWebpackConfig();
