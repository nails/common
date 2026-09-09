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
                            /**
                             * Sass prepends a charset declaration to any partial containing
                             * non-ASCII characters, which becomes a byte order mark once the
                             * output is concatenated. Landing mid-file, it invalidates the
                             * selector that follows it — which silently dropped the :root
                             * block holding the spacing and typography tokens.
                             */
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
