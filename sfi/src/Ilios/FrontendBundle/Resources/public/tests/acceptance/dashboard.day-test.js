module('Acceptances - Dashboard Day', {
    setup: function() {
        Ember.run(function() {
            App.reset();
        });
    }
});

test('dashboard day renders', function(){
    expect(1);
    visit('/dashboard/day');

    andThen(function(){
        var welcome = find('p:first');
        var events = find('div.container ol li');
        equal(welcome.text().trim(), 'Hello Test User,');
        // equal(events.length, 1);
    });
});
