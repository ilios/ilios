describe("SessionModel", function () {
  var sm;

  beforeEach(function () {
    sm = new SessionModel();
  });

  it("should be initialized with no objectives", function () {
    expect(sm.objectives).toEqual([]);
    expect(sm.objectivesCount).toBeUndefined();
  });

  describe("clone()", function () {

    var deleteUniques = function (session) {
      // Doesn't clone the dirtyStateListener, not sure if it should
      delete session.dirtyStateListener;
    };

    it("should clone the object", function () {
      var clone = sm.clone();

      // Doesn't clone the dirtyStateListener, not sure if it should, just check that it's a function
      expect(typeof sm.dirtyStateListener.modelChanged).toEqual("function");
      expect(typeof clone.dirtyStateListener.modelChanged).toEqual("function");

      deleteUniques(sm);
      deleteUniques(clone);

      expect(clone).toEqual(sm);
      expect(clone).not.toBe(sm);
    });

    it("should not alter objectives property during cloning (see GitHub issue #545)", function () {
      var objective1 = new ObjectiveModel();
      var objective2 = new ObjectiveModel();
      sm.addObjectiveForContainer(objective1, 0);
      sm.addObjectiveForContainer(objective2, 1);
      var clone = sm.clone();
      expect(clone.objectives).toEqual(sm.objectives);
      expect(clone.objectives).not.toBe(sm.objectives);
    });
  });
});
