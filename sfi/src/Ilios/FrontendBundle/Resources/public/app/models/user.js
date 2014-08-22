App.User = DS.Model.extend({
  firstName: DS.attr('string'),
  lastName: DS.attr('string'),
  middleName: DS.attr('string'),
  phone: DS.attr('string'),
  email:  DS.attr('string'),
  enabled:  DS.attr('boolean'),
  ucUid:  DS.attr('string'),
  otherId:  DS.attr('string'),
  fullName: function() {
    return this.get('firstName') + ' ' + this.get('lastName');
  }.property('firstName', 'lastName')
});

App.User.FIXTURES = [
  {
    id: 1,
    firstName: 'Test',
    lastName: 'User',
    middleName: 'First',
    email: 'test.user@example.com',
    enabled: true,
    ucUid: '123456789'
  },
  {
    id: 2,
    firstName: 'Test',
    lastName: 'Person',
    middleName: 'Second',
    email: 'test.person@example.com',
    enabled: true,
    ucUid: '123456798'
  },
];