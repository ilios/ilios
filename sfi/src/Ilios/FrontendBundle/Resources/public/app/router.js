App.Router = Ember.Router.extend({
    location: 'hash'
});
App.Router.map(function() {
    this.route("dashboard", {path: "/"});
});