App.DashboardRoute = Ember.Route.extend({
	model: function() {
		var promises = {
			user: this.get('currentUser'),
		    events: this.get('currentUser.events')
		};
		return new Ember.RSVP.hash(promises);
	},
	setupController: function(controller, resolvedPromises){
		controller.set('model', resolvedPromises.user);
		controller.set('events', resolvedPromises.events);
	}
});

App.DashboardIndexRoute = App.DashboardRoute.extend({
	//@todo user should be able to save their prefered dashboard
	afterModel: function(currentUser, transition) {
		this.transitionTo('dashboard.week');
	}
});
