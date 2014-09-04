App.Course = DS.Model.extend({
    title: DS.attr('string'),
    starDate: DS.attr('date'),
    endDate: DS.attr('date'),
    sessions: DS.hasMany('session', {async: true})
});

App.Course.FIXTURES = [
  {
    id: 0,
    title: 'First Test Course',
    startDate: moment().subtract(1, 'week'),
    endDate: moment(),
  },
];

// private $courseId;
// private $title;
// private $courseLevel;
// private $year;
// private $startDate;
// private $endDate;
// private $deleted;
// private $externalId;
// private $locked;
// private $archived;
// private $publishedAsTbd;
// private $clerkshipType;
// private $owningSchool;
// private $directors;
// private $cohorts;
// private $disciplines;
// private $objectives;
// private $meshDescriptors;
// private $publishEvent;
