App.Offering = DS.Model.extend({
    start: DS.attr('date'),
    end: DS.attr('date'),
    session: DS.belongsTo('session', {async: true}),
    users: DS.hasMany('user', {async: true}),
    title: function(){
        return this.get('session.title');
    }.property('session.title')
});

App.Offering.FIXTURES = [
    {
        id: 0,
        start: moment().subtract(1, 'hours').toDate(),
        end: moment().toDate(),
        session: 0,
        users: [0]
    },
    {
        id: 1,
        start: moment().subtract(1, 'day').subtract(1, 'hours').toDate(),
        end: moment().subtract(1, 'day').toDate(),
        session: 0,
        users: [0]
    },
    {
        id: 2,
        start: moment().subtract(1, 'week').subtract(1, 'hours').toDate(),
        end: moment().subtract(1, 'week').toDate(),
        session: 0,
        users: [0]
    },
    {
        id: 3,
        start: moment().subtract(1, 'year').subtract(1, 'hours').toDate(),
        end: moment().subtract(1, 'year').toDate(),
        session: 0,
        users: [0]
    },
    {
        id: 4,
        start: moment().subtract(2, 'year').subtract(1, 'hours').toDate(),
        end: moment().subtract(2, 'year').toDate(),
        session: 0,
        users: [0]
    }
];
