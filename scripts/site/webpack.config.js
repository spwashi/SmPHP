const path       = require('path');
const HtmlPlugin = require('html-webpack-plugin');
const URL        = 'http://localhost/wanghorn/';
const app_name   = "example-app";
module.exports   = {
    // Tell webpack to start bundling our app at app/index.js
    entry: './app/resources/js/app',
    
    // Output our app to the dist/ directory
    output: {
        filename:   `${app_name}.js`,
        publicPath: `${URL}public/js/`,
        path:       `${__dirname}/public/js`
    },
    
    // Emit source maps so we can debug our code in the browser
    devtool: 'source-map',
    
    // Tell webpack to run our source code through Babel
    module: {
        loaders: [{
            test:    /\.js$/,
            exclude: /node_modules/,
            loader:  'babel-loader'
        }],
    },
    
    // Since Webpack only understands JavaScript, we need to
    // add a plugin to tell it how to handle html files.
    plugins: [
        // Configure HtmlPlugin to use our own index.html file
        // as a template.
        // Check out https://github.com/jantimon/html-webpack-plugin
        // for the full list of options.
        new HtmlPlugin({template: 'app/view/html/react.html'})
    ]
};