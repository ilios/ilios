describe("CourseModel", function () {
  var cm;

  beforeEach(function () {
    cm = new CourseModel({external_id: 1, unique_id: 2, start_date: '2010-06-28', end_date: '2010-06-29'});
  });

  it("should be initialized with no objectives", function () {
    expect(cm.objectives).toEqual([]);
    expect(cm.objectivesCount).toBeUndefined();
  });

  describe("clone()", function () {
    var deleteUniques = function (course) {
      delete course.externalId;
      delete course.uniqueId;
      // Doesn't clone the dirtyStateListener, not sure if it should
      delete course.dirtyStateListener;
    };

    it("should clone the object except the externalId and uniqueId", function () {
      var clone = cm.clone();
      expect(clone.externalId).not.toEqual(cm.externalId);
      expect(clone.uniqueId).not.toEqual(cm.uniqueId);

      // Doesn't clone the dirtyStateListener, not sure if it should, just check that it's a function
      expect(typeof cm.dirtyStateListener.modelChanged).toEqual("function");
      expect(typeof clone.dirtyStateListener.modelChanged).toEqual("function");

      deleteUniques(cm);
      deleteUniques(clone);

      expect(clone).toEqual(cm);
      expect(clone).not.toBe(cm);
    });

    it("should not alter objectives property during cloning (see GitHub issue #545)", function () {
      var objective1 = new ObjectiveModel();
      var objective2 = new ObjectiveModel();
      cm.addObjectiveForContainer(objective1, 0);
      cm.addObjectiveForContainer(objective2, 1);
      var clone = cm.clone();
      expect(clone.objectives).toEqual(cm.objectives);
      expect(clone.objectives).not.toBe(cm.objectives);
    });
  });
});

