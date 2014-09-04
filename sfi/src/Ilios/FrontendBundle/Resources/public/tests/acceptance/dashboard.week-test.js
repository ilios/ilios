module('Acceptances - Dashboard Week', {
    setup: function() {
        Ember.run(function() {
            App.reset();
        });
    }
});

test('dashboard week renders', function(){
    expect(1);
    visit('/dashboard/week');

    andThen(function(){
        var welcome = find('p:first');
        var events = find('div.container ol li');
        equal(welcome.text().trim(), 'Hello Test User,');
        // equal(events.length, 1);
    });
});
