App.IndexRoute = Ember.Route.extend({
  beforeModel: function() {
    this.transitionTo('dashboard');
  }
});
