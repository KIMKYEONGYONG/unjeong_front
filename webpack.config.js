const Encore = require("@symfony/webpack-encore");

if (! Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || "dev");
}

Encore
    .setOutputPath("public/dist/")
    .setPublicPath("/dist")
    .setManifestKeyPrefix('dist/')

    .addEntry("app","./resources/js/app.js")
    .addEntry("fullpage","./resources/js/jquery.fullpage.js")
    .addEntry("common","./resources/js/common.js")
    .addEntry("signup","./resources/js/menu8/signup.js")
    .addEntry("signup_terms","./resources/js/menu8/signup_terms.js")
    .addEntry("login","./resources/js/menu7/login.js")
    .addEntry("findId","./resources/js/menu7/findId.js")
    .addEntry("findPass","./resources/js/menu7/findPass.js")
    .addEntry("findPass2","./resources/js/menu7/findPass2.js")


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