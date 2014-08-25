emq.globalize();

App.set('rootElement', '#ember-testing');
App.set('LOG_ACTIVE_GENERATION', false);
App.set('LOG_VIEW_LOOKUPS', false);

App.setupForTesting();
App.injectTestHelpers();
App.ApplicationAdapter = DS.FixtureAdapter;

setResolver(Ember.DefaultResolver.create({ namespace: App }));
