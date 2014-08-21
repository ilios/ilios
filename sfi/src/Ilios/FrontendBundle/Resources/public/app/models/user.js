App.User = DS.Model.extend({
  firstName: DS.attr('string'),
  lastName: DS.attr('string'),
  middleName: DS.attr('string'),
  phone: DS.attr('string'),
  email:  DS.attr('string'),
  enabled:  DS.attr('boolean'),
  ucUid:  DS.attr('string'),
  otherId:  DS.attr('string')
});