describe("PreferencesModel", function () {
  describe("constructor", function () {
    it("should default all properties to false", function () {
      var prefs = new PreferencesModel();
      expect(prefs.getCourseArchiving()).toBe(false);
      expect(prefs.getProgramYearArchiving()).toBe(false);
      expect(prefs.getCourseRollover()).toBe(false);
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
      expect(prefs.getCourseArchiving()).toBe(true);
      expect(prefs.getProgramYearArchiving()).toBe(true);
      expect(prefs.getCourseRollover()).toBe(true);
    });
  });
});
