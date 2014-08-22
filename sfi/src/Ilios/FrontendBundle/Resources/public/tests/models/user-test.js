moduleForModel('User', 'Unit - User Model');

test("it exists", function(){
  expect(1);
  ok(this.subject() instanceof window.App.User);
});

test('#properties', function() {
  expect(7);

  var user = this.subject(App.User.FIXTURES[0]);

  equal(user.get('firstName'), 'Test');
  equal(user.get('lastName'), 'User');
  equal(user.get('middleName'), 'First');
  equal(user.get('email'), 'test.user@example.com');
  equal(user.get('enabled'), true);
  equal(user.get('ucUid'), '123456789');
  equal(user.get('fullName'), 'Test User');

});

test('#properties2', function() {
  expect(7);

  var user = this.subject(App.User.FIXTURES[1]);

  equal(user.get('firstName'), 'Test');
  equal(user.get('lastName'), 'Person');
  equal(user.get('middleName'), 'Second');
  equal(user.get('email'), 'test.person@example.com');
  equal(user.get('enabled'), true);
  equal(user.get('ucUid'), '123456798');
  equal(user.get('fullName'), 'Test Person');

});