const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const path = require('path');


module.exports = {
    entry: './assets/js/nails.js',
    output: {
        filename: 'nails.min.js',
        path: path.resolve(__dirname, 'assets/js/')
    },
    module: {
        rules: [
            {
                test: /\.(css|scss|sass)$/,
                use: [
                    MiniCssExtractPlugin.loader,
                    {
                        loader: 'css-loader',
                        options: {
                            url: false
                        }
                    },
                    'postcss-loader',
                    {
                        loader: 'sass-loader',
                        options: {
                            //  Prevents Dart Sass emitting a U+FEFF BOM when the output contains
                            //  non-ASCII characters; the BOM is not hoisted with the @import
                            //  statements and ends up mid-file, invalidating the following rule
                            sassOptions: {
                                charset: false
                            }
                        }
                    }
                ]
            },
        ]
    },
    plugins: [
        new MiniCssExtractPlugin({
            filename: '../css/nails.min.css'
        })
    ],
    mode: 'production'
};
