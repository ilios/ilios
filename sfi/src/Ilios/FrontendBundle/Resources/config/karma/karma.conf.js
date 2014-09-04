// Karma configuration
// Generated on Tue Aug 19 2014 13:19:55 GMT-0700 (PDT)

module.exports = function(config) {
  config.set({

    // base path that will be used to resolve all patterns (eg. files, exclude)
    basePath: '../../public',


    // frameworks to use
    // available frameworks: https://npmjs.org/browse/keyword/karma-adapter
    frameworks: ['qunit'],


    // list of files / patterns to load in the browser
    files: [
      'components/jquery/dist/jquery.js',
      'components/handlebars/handlebars.js',
      'components/ember/ember.js',
      'components/moment/moment.js',
      'components/ember-data/ember-data.js',
      'components/ember-qunit/dist/globals/main.js',
      'app/ilios.js',
      'app/router.js',
      'tests/helpers/karma-start.js',
      'app/routes/*.js',
      'app/models/*.js',
      'app/controllers/*.js',
      'app/templates/*.hbs',
      'tests/controllers/*-test.js',
      'tests/models/*-test.js',
      'tests/routes/*-test.js',
      'tests/acceptance/*-test.js',
    ],

    plugins: [
     'karma-qunit',
     'karma-ember-preprocessor',
     'karma-phantomjs-launcher'
   ],

   preprocessors: {
     '**/*.hbs': 'ember'
   },

    // start these browsers
    // available browser launchers: https://npmjs.org/browse/keyword/karma-launcher
    browsers: ['PhantomJS'],

    // enable / disable watching file and executing tests whenever any file changes
    autoWatch: true,

    // Continuous Integration mode
    // if true, Karma captures browsers, runs the tests and exits
    singleRun: false
  });
};
