module('Acceptances - Dashboard Year', {
  teardown: function() {
    Ember.run(function() {
        App.reset();
    });
  }
});

test('index renders', function(){
  expect(3);

  visit('/').then(function(){
    var title = find('h1');
    var list = find('header div.nav li a');

    equal(title.text(), 'Dashboard');

    equal(list.length, 5);
    equal(list.text(), 'HomeProgramsInstructorsLearner GroupsCourses and Sessions');


  });
});
