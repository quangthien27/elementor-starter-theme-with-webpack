const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const UglifyJsPlugin = require('uglifyjs-webpack-plugin');
const FixStyleOnlyEntriesPlugin = require('webpack-fix-style-only-entries');
const glob = require('glob');

module.exports = {
  entry: Object.assign({
    'main': './app/assets/js/main.js',
  }, (() => {
    const files = {};
    const foundFiles = Array.from(glob.sync('./app/modules/elementor/widgets/**/*.scss'));

    foundFiles.forEach((filePath) => {
      const filename = filePath.split(/(\\|\/)/g).pop();
      files[filename.replace(/\.scss/g, '')] = filePath;
    });

    return files;
  })()),

  output: {
    path: path.resolve(__dirname, 'dist'),
    filename: 'js/[name].min.js',
  },

  devtool: (process.env.NODE_ENV === 'production' ? 'cheap-source-map' : 'source-map'),

  module: {
    rules: [
      {
        test: /\.js$/,
        exclude: /(node_modules)/,
        use: {
          loader: 'babel-loader',
          options: {
            presets: ['@babel/preset-env']
          }
        }
      },
      {
        test: /\.(sa|sc|c)ss$/,
        use: [
          {
            loader: MiniCssExtractPlugin.loader,
            options: {
              publicPath: '../'
            }
          },
          {
            loader: 'css-loader',
          },
          {
            loader: 'postcss-loader'
          },
          {
            loader: 'sass-loader',
            options: {
              implementation: require('sass')
            }
          }
        ]
      },
      {
        test: /\.(png|jpe?g|gif|svg)$/,
        use: [
          {
            loader: 'file-loader',
            options: {
              outputPath: 'images'
            }
          }
        ]
      },
      {
        test: /\.(woff|woff2|ttf|otf|eot)$/,
        use: [
          {
            loader: 'file-loader',
            options: {
              outputPath: 'fonts'
            }
          }
        ]
      }
    ]
  },

  optimization: {
    minimizer: [
      new UglifyJsPlugin({
        uglifyOptions: {
          output: {
            comments: false
          }
        }
      })
    ]
  },

  plugins: [
    new FixStyleOnlyEntriesPlugin(),
    new MiniCssExtractPlugin({
      filename: 'css/[name].min.css'
    })
  ],

  resolve: {
    alias: {
      'app': path.resolve(__dirname, 'app'),
    },
  },
};
