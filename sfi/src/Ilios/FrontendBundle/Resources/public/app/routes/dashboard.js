App.DashboardRoute = Ember.Route.extend({
	model: function() {
		return this.currentUser;
	}
});
