window.Ilios = Ember.Application.create();

//Get the URL for the api from symonfy and then drop the leading slash
var apiurl = Routing.generate(
    'ilios_core_apiinfo'
).substr(1);
Ilios.ApplicationAdapter = DS.RESTAdapter.extend({
  namespace: apiurl
});

Ilios.Objective = DS.Model.extend({
  title: DS.attr('string')
});

Ilios.Router = Ember.Router.extend({
    location: 'hash'
});

Ilios.Router.map(function() {
    this.route("index", {path: "/"});
    this.resource('objectives', function() {
        this.resource('objective', { path: ':objective_id' });
    });
});
