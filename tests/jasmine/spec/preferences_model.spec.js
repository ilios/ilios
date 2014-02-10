describe("PreferencesModel", function () {
  describe("constructor", function () {
    var container;

    it("should default all properties to false", function () {
      var prefs = new PreferencesModel();
      expect(prefs.showCourseArchiving()).toBe(false);
      expect(prefs.showProgramYearArchiving()).toBe(false);
      expect(prefs.showCourseRollover()).toBe(false);
    });
  });

  describe("updateWithServerDispatchedObject()", function () {
    it("should update from provided object", function () {
      var prefs = new PreferencesModel();
      var updateObject = {
        py_archiving: true,
        course_archiving: true,
        course_rollover: true
      };
      prefs.updateWithServerDispatchedObject(updateObject);
      expect(prefs.showCourseArchiving()).toBe(true);
      expect(prefs.showProgramYearArchiving()).toBe(true);
      expect(prefs.showCourseRollover()).toBe(true);
    });
  });
});
