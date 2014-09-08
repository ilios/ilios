App.DashboardDayController = Ember.ObjectController.extend({
    queryParams: ['year','day'],
    year: moment().format('YYYY'),
    day: moment().format('DDD'),
    calendarEvents: function(){
        var self = this;
        return this.get('events').filter(function(event){
            return event.get('start') != undefined &&
                moment(event.get('start')).format('YYYY') == self.get('year') &&
                moment(event.get('start')).format('DDD') == self.get('day');
        });
    }.property('events.@each', 'year')
});

App.DashboardWeekController = Ember.ObjectController.extend({
    queryParams: ['year','week'],
    year: moment().format('YYYY'),
    week: moment().format('W'),
    calendarEvents: function(){
        var self = this;
        return this.get('events').filter(function(event){
            return event.get('start') != undefined &&
                moment(event.get('start')).format('YYYY') == self.get('year') &&
                moment(event.get('start')).format('W') == self.get('week');
        });
    }.property('events.@each', 'year')
});

App.DashboardYearController = Ember.ObjectController.extend({
    queryParams: ['year'],
    year: moment().format('YYYY'),
    calendarEvents: function(){
        var self = this;
        return this.get('events').filter(function(event){
            return event.get('start') != undefined &&
                moment(event.get('start')).format('YYYY') == self.get('year');
        });
    }.property('events.@each', 'year')
});
