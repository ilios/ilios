describe("PreferencesModel", function () {
  describe("constructor", function () {
    it("should default all properties to false", function () {
      var prefs = new PreferencesModel();
      expect(prefs.canArchiveProgramYears).toBe(false);
      expect(prefs.canArchiveCourses).toBe(false);
      expect(prefs.canRolloverCourses).toBe(false);
    });
  });
});
