describe("course_manager_rollover", function () {
  it("should define a cm.rollover namespace", function () {
    expect(ilios.cm.rollover).toBeDefined();
  });

  describe("ilios.cm.rollover", function () {
    it("should define setRolloverStartDate()", function () {
      expect(ilios.cm.rollover.setRolloverStartDate).toEqual(jasmine.any(Function));
    });

    describe("ilios.cm.rollover.setRolloverStartDate", function () {
      var cleanupCm = false;
      var cleanupi18n = false;

      beforeEach(function () {
        //conditional test doubles
        // rm after course_manager_dom.js and ilios_i18nVendor are being tested
        //  and therefore loaded by spec runner
        if (! ilios.cm.currentCourseModel) {
          ilios.cm.currentCourseModel = {
            getStartDateAsDateObject: function () { return new Date(2014,1,4); }
          };
          cleanupCm = true;
        }

        if (! window.ilios_i18nVendor) {
          window.ilios_i18nVendor = {
            getI18NString: function (arg) { return arg; }
          };
          cleanupi18n = true;
        }
      });

      afterEach(function () {
        //clean up test doubles
        if (cleanupCm) {
          delete ilios.cm.currentCourseModel;
        }
        if (cleanupi18n) {
          delete window.ilios_i18nVendor;
        }
      });

      it("should call alert if old and new start dates are not the same day of the week", function () {
        var bigGanglyTestString = 'course_management.rollover.warning.start_date_dow_1 general.calendar.monday_long course_management.rollover.warning.start_date_dow_2, general.calendar.tuesday_long, course_management.rollover.warning.start_date_dow_3';
        spyOn(ilios.alert, "alert");
        ilios.cm.rollover.setRolloverStartDate(new Date(2014,1,3));
        expect(ilios.alert.alert).toHaveBeenCalledWith('course_management.rollover.warning.start_date_dow_1 general.calendar.monday_long course_management.rollover.warning.start_date_dow_2, general.calendar.tuesday_long, course_management.rollover.warning.start_date_dow_3');
      });
    });
  });
});