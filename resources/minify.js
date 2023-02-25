import minify from '@node-minify/core';
import uglifyjs from '@node-minify/uglify-js';
import csso from '@node-minify/csso';

minify({
    compressor: uglifyjs,
    input: [
        './js/prism.js',
        './js/line-number.js',
        './js/line-highlight.js',
        './js/pretty.js',
    ],
    output: './js/script.min.js',
    callback: function (err, min) {}
});

minify({
    compressor: csso,
    input: [
        './css/prism.css',
        './css/line-number.css',
        './css/line-highlight.css',
        './css/pretty.css',
    ],
    output: './css/style.min.css',
    callback: function(err, min) {}
});