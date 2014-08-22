/**
 * Inject the current user into all controllers and routes
 * Based on http://ember.zone/ember-application-initializers/
 */
Ember.Application.initializer({
  name: "currentUserLoader",

  initialize: function(container, application) {
      // Wait until all of the following promises are resolved
      application.deferReadiness();
      var url = Routing.generate(
          'ilios_core_apiinfo'
      ) + '/currentsession';

      Ember.$.getJSON(url).then(function(data) {
          container.lookup('store:main').find('user', data.currentsession.userId).then( function(user) {
            // Register the `user:current` namespace
            container.register('user:current', user, {instantiate: false, singleton: true});

            // Inject the namespace into controllers and routes
            container.injection('route', 'currentUser', 'user:current');
            container.injection('controller', 'currentUser', 'user:current');

            // Continue the Application boot process, allowing other Initializers to run
            application.advanceReadiness();
          })
      });
   }
});
