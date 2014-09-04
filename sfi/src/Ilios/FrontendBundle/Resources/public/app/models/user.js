App.User = DS.Model.extend({
    firstName: DS.attr('string'),
    lastName: DS.attr('string'),
    middleName: DS.attr('string'),
    phone: DS.attr('string'),
    email:  DS.attr('string'),
    enabled:  DS.attr('boolean'),
    ucUid:  DS.attr('string'),
    otherId:  DS.attr('string'),
    offerings: DS.hasMany('offering', {async: true}),
    fullName: function() {
        return this.get('firstName') + ' ' + this.get('lastName');
    }.property('firstName', 'lastName'),
    events: function(){
        var promises = {
          offerings: this.get('offerings')
        };
        return new Ember.RSVP.hash(promises).then(function(results) {
          var events = [];
          results.offerings.forEach(function(offering){
              events.push(offering);
          });

          var eventPromises = [];
          events.forEach(function(event){
             eventPromises.push(event.get('start'));
             eventPromises.push(event.get('end'));
             eventPromises.push(event.get('title'));
          });
          return new Ember.RSVP.all(eventPromises).then(function(){
              return events;
          });
        });
    }.property('offerings.@each', 'offerings.@each.session')
});

App.User.FIXTURES = [
  {
    id: 0,
    firstName: 'Test',
    lastName: 'User',
    middleName: 'First',
    email: 'test.user@example.com',
    enabled: true,
    ucUid: '123456789',
    offerings: [0,1,2,3,4]
  },
  {
    id: 1,
    firstName: 'Test',
    lastName: 'Person',
    middleName: 'Second',
    email: 'test.person@example.com',
    enabled: true,
    ucUid: '123456798'
  },
];
