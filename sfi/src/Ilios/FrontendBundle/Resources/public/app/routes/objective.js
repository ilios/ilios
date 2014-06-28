Ilios.ObjectiveRoute = Ember.Route.extend({
    model: function(params) {
      return this.store.find('objective', params.objective_id);
  }
});

Ilios.ObjectiveController = Ember.ObjectController.extend({
  isEditing: false,
  actions: {
      edit: function() {
        this.set('isEditing', true);
      },
      doneEditing: function() {
        this.set('isEditing', false);
        this.get('model').save();
      }
  }
});
