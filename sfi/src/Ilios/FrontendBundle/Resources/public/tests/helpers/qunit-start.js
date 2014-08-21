emq.globalize();
window.assetsBaseDir = "";

var attributes = {
  rootElement: '#ember-testing',
  LOG_ACTIVE_GENERATION:false,
  LOG_VIEW_LOOKUPS: false
}

window.App = Ember.Application.create(attributes);

App.setupForTesting();
App.injectTestHelpers();
setResolver(Ember.DefaultResolver.create({ namespace: App }));
