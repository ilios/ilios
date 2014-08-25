/**
 * Setup Emberdata dependign on the environment
 */
Ember.Application.initializer({
  name: "setupEmberData",
  after: "loadConfig",
  initialize: function(container, application) {
      var environment = application.get('config').get('environment');
      switch(environment){
          case 'production':
              var apiurl = Routing.generate(
                  'ilios_core_apiinfo'
              ).substr(1);
              application.ApplicationAdapter = DS.RESTAdapter.extend({
                namespace: apiurl
              });
              break;
          case 'dev':
          case 'testing':
              application.ApplicationAdapter = DS.FixtureAdapter;
              break;
          default:
              throw new Ember.Error(environment + ' is not a valid environment');
      }
   }
});
