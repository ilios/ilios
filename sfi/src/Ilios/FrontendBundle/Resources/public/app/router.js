App.Router = Ember.Router.extend({
    location: 'hash'
});
App.Router.map(function() {
    this.resource('dashboard', function() {
        this.route('day');
        this.route('week');
        this.route('year');
    });
});
