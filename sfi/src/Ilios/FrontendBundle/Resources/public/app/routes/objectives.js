Ilios.ObjectivesRoute = Ember.Route.extend({
    model: function(params) {
      return this.store.find('objective');
  }
});

Ilios.ObjectivesController = Ember.ArrayController.extend({
  actions: {
    createObjective: function() {
      // Get the objective title set by the "New Objective" text field
      var title = this.get('newTitle');
      if (!title) { return false; }
      if (!title.trim()) { return; }

      // Create the new Todo model
      var objective = this.store.createRecord('objective', {
        title: title
      });
      this.set('newTitle', '');
      objective.save();
    }
  }
});
