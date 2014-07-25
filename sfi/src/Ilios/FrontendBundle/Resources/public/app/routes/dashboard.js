App.DashboardRoute = Ember.Route.extend({

});

App.DashboardController = Ember.ObjectController.extend({
});

App.DashboardView = Ember.View.extend({
  afterRenderEvent : function(){
    this._super();
    var schedulerDiv = $('#scheduler_here');
    schedulerDiv.css('height', '600px');
    schedulerDiv.css('width', '100%');
    scheduler.config.container_autoresize = true;
    scheduler.init('scheduler_here', new Date(),"month");
    scheduler.setCurrentView(new Date(), 'week');
    
  }
});
