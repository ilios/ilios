App.Session = DS.Model.extend({
    title: DS.attr('string'),
    course: DS.belongsTo('course', {async: true}),
    offerings: DS.hasMany('offering', {async: true})
});

App.Session.FIXTURES = [
  {
    id: 0,
    title: 'First Test Session',
    course: 1,
    offerings: [0,1,2,3,4]
  },
];

    // private $sessionId;
    // private $title;
    // private $supplemental;
    // private $deleted;
    // private $publishedAsTbd;
    // private $lastUpdatedOn;
    // private $sessionType;
    // private $course;
    // private $ilmSessionFacet;
    // private $disciplines;
    // private $objectives;
    // private $meshDescriptors;
    // private $publishEvent;
