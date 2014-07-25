window.App = Ember.Application.create();

//Get the URL for the api from symonfy and then drop the leading slash
var apiurl = Routing.generate(
    'ilios_core_apiinfo'
).substr(1);
App.ApplicationAdapter = DS.RESTAdapter.extend({
  namespace: apiurl
});

App.Router = Ember.Router.extend({
    location: 'hash'
});

App.Router.map(function() {
    this.route("dashboard", {path: "/"});
});

/**
 * Add an afterREnderEvent hook to all views
 */
Ember.View.reopen({
  didInsertElement : function(){
    this._super();
    Ember.run.scheduleOnce('afterRender', this, this.afterRenderEvent);
  },
  afterRenderEvent : function(){
    // implement this hook in your own subclasses and run your jQuery logic there
  }
});