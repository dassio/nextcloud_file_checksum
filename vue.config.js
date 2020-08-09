const MiniCssExtractPlugin = require('mini-css-extract-plugin');

module.exports = {
    configureWebpack: {
        output: {
            filename: 'main.js',
            chunkFilename: '[id].js'
        },
        plugins: [
            new MiniCssExtractPlugin({
                filename: "../css/style.css",
                chunkFilename: "../css/[name].css"
            }),
        ],
        module: {
            rules: [{
                test: /\.(png|jpe?g|gif)$/i,
                loader: 'file-loader',
                options: {
                    outputPath: '../img',
                },
            }, ]
        },
        devtool: 'source-map'
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