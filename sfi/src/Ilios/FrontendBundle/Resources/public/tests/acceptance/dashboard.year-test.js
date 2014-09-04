module('Acceptances - Dashboard Year', {
    setup: function() {
        Ember.run(function() {
            App.reset();
        });
    }
});

test('dashboard year renders', function(){
    expect(3);
    visit('/dashboard/year');

    andThen(function(){
        var welcome = find('p:first');
        var events = find('div.container ol li');
        equal(welcome.text().trim(), 'Hello Test User,');
        // equal(events.length, 3);
    });

    visit('/dashboard/year?year=2013');

    andThen(function(){
        var welcome = find('p:first');
        var events = find('div.container ol li');
        equal(welcome.text().trim(), 'Hello Test User,');
        // equal(events.length, 1);
    });

    visit('/dashboard/year?year=2014');

    andThen(function(){
        var welcome = find('p:first');
        var events = find('div.container ol li');
        equal(welcome.text().trim(), 'Hello Test User,');
        // equal(events.length, 3);
    });
});
