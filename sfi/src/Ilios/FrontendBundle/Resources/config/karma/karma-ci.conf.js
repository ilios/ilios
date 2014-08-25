module.exports = function(config) {

  if (!process.env.SAUCE_USERNAME) {
    console.log('You have to run EXPORT SAUCE_USERNAME=foo before running these tests');
    process.exit(1);
  }
  if (!process.env.SAUCE_ACCESS_KEY) {
    console.log('You have to run EXPORT SAUCE_ACCESS_KEY=foo before running these tests');
    process.exit(1);
  }

  // Browsers to run on Sauce Labs
  var customLaunchers = {
    'SL_Chrome': {
      base: 'SauceLabs',
      browserName: 'chrome'
    },
    'SL_Firefox': {
      base: 'SauceLabs',
      browserName: 'firefox',
      version: '26'
    },
    'SL_Safari': {
      base: 'SauceLabs',
      browserName: 'safari',
      platform: 'OS X 10.9',
      version: '7'
    },
    'SL_IE_9': {
      base: 'SauceLabs',
      browserName: 'internet explorer',
      platform: 'Windows 2008',
      version: '9'
    }
  };

  config.set({
    basePath: '../../public',
    frameworks: ['qunit'],
    files: [
      'components/jquery/dist/jquery.js',
      'components/handlebars/handlebars.js',
      'components/ember/ember.js',
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
     'karma-sauce-launcher'
    ],

    preprocessors: {
      '**/*.hbs': 'ember'
    },

    reporters: ['dots', 'saucelabs'],

    sauceLabs: {
      testName: 'Symfony EmberJS Edition Tests'
    },
    captureTimeout: 1200000,
    customLaunchers: customLaunchers,

    browsers: Object.keys(customLaunchers),
    singleRun: true
  });
};
