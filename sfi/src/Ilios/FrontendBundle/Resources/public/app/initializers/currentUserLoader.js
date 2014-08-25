/**
 * Inject the current user into all controllers and routes
 * Based on http://ember.zone/ember-application-initializers/
 */
Ember.Application.initializer({
  name: "currentUserLoader",
  after: ['setupEmberData', 'loadConfig'],
  initialize: function(container, application) {
      var self = this;
      var environment = application.get('config').get('environment');
      application.deferReadiness();
      switch(environment){
          case 'production':
              var url = Routing.generate(
                  'ilios_core_apiinfo'
              ) + '/currentsession';

              Ember.$.getJSON(url).then(function(data) {
                  container.lookup('store:main').find('user', data.currentsession.userId)
                  .then( function(user) {
                      // Register the `user:current` namespace
                      container.register('user:current', user, {instantiate: false, singleton: true});

                      // Inject the namespace into controllers and routes
                      container.injection('route', 'currentUser', 'user:current');
                      container.injection('controller', 'currentUser', 'user:current');
                      application.advanceReadiness();
                  })
              });
              break;
          case 'dev':
          case 'testing':
              container.lookup('store:main').find('user', 1)
              .then( function(user) {
                  // Register the `user:current` namespace
                  container.register('user:current', user, {instantiate: false, singleton: true});

                  // Inject the namespace into controllers and routes
                  container.injection('route', 'currentUser', 'user:current');
                  container.injection('controller', 'currentUser', 'user:current');
                  application.advanceReadiness();
              })
              break;
          default:
              throw new Ember.Error(environment + ' is not a valid environment');
      }
  }
});
