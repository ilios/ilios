App.ApplicationController = Ember.Controller.extend({
	logoPath: function(){
		return App.get('config').get('assetsBaseDir') + '/images/ilios-logo.png';
	}.property()
});
