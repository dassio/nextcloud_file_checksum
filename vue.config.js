const MiniCssExtractPlugin = require('mini-css-extract-plugin');

module.exports = {
    configureWebpack: {
        output: {
            filename: 'main.js',
            chunkFilename: '[id].js'
        },
        plugins: [
            new MiniCssExtractPlugin({
                chunkFilename: '../css/[id].css',
                filename: "../css/[name].css"
            }),
        ],
    },
    outputDir: "./js",
    // disable hashes in filenames
    filenameHashing: false,
    // delete HTML related webpack plugins
    chainWebpack: config => {
        config.plugins.delete('html')
        config.plugins.delete('preload')
        config.plugins.delete('prefetch')
    }

}