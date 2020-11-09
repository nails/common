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
                    'sass-loader'
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
