document.write('<div id="ember-testing-container"><div id="ember-testing"></div></div>');
window.assetsBaseDir = "";
emq.globalize();
__karma__.loaded = function() {};

var attributes = {
  rootElement: '#ember-testing',
  LOG_ACTIVE_GENERATION:false,
  LOG_VIEW_LOOKUPS: false
};

window.App = Ember.Application.create(attributes);
App.setupForTesting();
App.injectTestHelpers();
App.ApplicationAdapter = DS.FixtureAdapter;

//this gate/check is required given that standard practice in Ember tests to is to call
//Ember.reset() in the afterEach/tearDown for each test.  Doing so, causes the application
//to 're-initialize', resulting in repeated calls to the initialize function below
var karma_started = false;
App.initializer({
   name: "run tests",
   initialize: function(container, application) {
       if (!karma_started) {
           karma_started = true;
           __karma__.start();
       }
   }
});

setResolver(Ember.DefaultResolver.create({ namespace: App }));
