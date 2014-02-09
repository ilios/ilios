describe("PreferencesModel", function () {
  describe("constructor", function () {
    it("should default all properties to false", function () {
      var prefs = new PreferencesModel();
      expect(prefs.showCourseArchiving()).toBe(false);
      expect(prefs.showProgramYearArchiving()).toBe(false);
      expect(prefs.showCourseRollover()).toBe(false);
    });
  });
});
