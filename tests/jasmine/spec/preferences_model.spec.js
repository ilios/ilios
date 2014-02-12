describe("PreferencesModel", function () {
  describe("constructor", function () {
    it("should default all properties to false", function () {
      var prefs = new PreferencesModel();
      expect(prefs.courseArchiving).toBe(false);
      expect(prefs.programYearArchiving).toBe(false);
      expect(prefs.courseRollover).toBe(false);
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
      expect(prefs.courseArchiving).toBe(true);
      expect(prefs.programYearArchiving).toBe(true);
      expect(prefs.courseRollover).toBe(true);
    });
  });
});
