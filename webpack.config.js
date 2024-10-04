const Encore = require("@symfony/webpack-encore");

if (! Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || "dev");
}

Encore
    .setOutputPath("public/dist/")
    .setPublicPath("/dist")
    .setManifestKeyPrefix('dist/')

    .addEntry("app","./resources/js/app.js")
    .addEntry("auth", "./resources/js/login.js")
    .addEntry("board", "./resources/js/board.js")


    .splitEntryChunks()
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(! Encore.isProduction())

    .enableVersioning()

    .configureBabel((config) => {
        config.plugins.push("@babel/plugin-proposal-class-properties")
    })

    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = "usage"
        config.corejs      = 3
    })

    .copyFiles({
        from: "./resources/images",
        to: "images/[path][name].[hash:8].[ext]",
        pattern: /\.(png|jpg|jpeg|gif|mp4|svg|ico)$/
    })

    .enableSassLoader()

let config = Encore.getWebpackConfig();

module.exports = config;