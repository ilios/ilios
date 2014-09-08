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
          case 'testing':
          case 'dev':
              Ember.Logger.debug('Environment: ' + environment + '.  Using FixtureAdapter');
              application.ApplicationAdapter = DS.FixtureAdapter.extend({});

              break;
          default:
              throw new Ember.Error(environment + ' is not a valid environment');
      }
   }
});
