describe("ilios_base", function() {

  it("should create a global ilios object", function() {
    expect(ilios).toBeDefined();
  });

  describe("ilios", function () {

    it("should have a namespace method", function () {
      expect(typeof ilios.namespace).toBe("function");
    });

    it("should have a global namespace", function () {
      expect(typeof ilios.global).toBe("object");
    });

    describe("namespace()", function () {
      afterEach(function () {
        delete ilios.foo;
      });

      it("should create the supplied namespace", function () {
        expect(typeof ilios.foo).toBe("undefined");
        ilios.namespace('foo');
        expect(typeof ilios.foo).toBe("object");
      });

      it("should create namespaces with depth > 1", function () {
        expect(typeof ilios.foo).toBe("undefined");
        ilios.namespace('foo.bar');
        expect(typeof ilios.foo).toBe("object");
        expect(typeof ilios.foo.bar).toBe("object");
      });

      it("should allow you to create a space twice", function () {
        expect(typeof ilios.foo).toBe("undefined");
        ilios.namespace('foo.bar');
        ilios.namespace('foo.baz');
        expect(typeof ilios.foo.bar).toBe("object");
        expect(typeof ilios.foo.baz).toBe("object");
      });
    });

    describe("global", function () {

      describe("startIdleTimer()", function () {
        var container;

        beforeEach(function () {
          // test double
          YAHOO.util.IdleTimer = {subscribe: function () {}, start: function () {}};
          spyOn(YAHOO.util.IdleTimer, "subscribe");
          spyOn(YAHOO.util.IdleTimer, "start");

          container = document.createElement('script');
          container.setAttribute("id", "iliosIdleTimer");
          container.setAttribute("type", "application/json");
        });

        afterEach(function () {
          // clean up test double
          delete YAHOO.util.IdleTimer;

          if (container && container.parentNode) {
            container.parentNode.removeChild(container);
          }
        });

        it("should use the timeout specified in the idle timer data in the DOM", function () {
          container.innerHTML = "{\"timeout\":9999999}";
          document.body.appendChild(container);
          ilios.global.startIdleTimer();
          expect(YAHOO.util.IdleTimer.start).toHaveBeenCalledWith(9999999, document);
        });

        it("should use default timeout if supplied timeout is not a number", function () {
          container.innerHTML = "{\"timeout\":\"totally not a number\"}";
          document.body.appendChild(container);
          ilios.global.startIdleTimer();
          expect(YAHOO.util.IdleTimer.start).not.toHaveBeenCalledWith("totally not a number", document);
          expect(YAHOO.util.IdleTimer.start).toHaveBeenCalledWith(2700000, document);
        });

        it("should call subscribe with the hardcoded callback", function () {
          container.innerHTML = "{}";
          document.body.appendChild(container);
          ilios.global.startIdleTimer();
          expect(YAHOO.util.IdleTimer.subscribe).toHaveBeenCalledWith("idle", jasmine.any(Function));
        });

        it("should be called through YAHOO.util.Event.onDOMReady()", function () {
          spyOn(YAHOO.util.Event, "onDOMReady");
          ilios.global.startIdleTimer();
          expect(YAHOO.util.Event.onDOMReady).toHaveBeenCalled();
        });

        it("should not set an IdleTimer if no DOM data object is present", function () {
          ilios.global.startIdleTimer();
          expect(YAHOO.util.IdleTimer.subscribe).not.toHaveBeenCalled();
        });
      });

      describe("defaultAJAXFailureHandler()", function () {
        beforeEach(function () {
          spyOn(ilios.alert, "alert");
                // test double
                window.ilios_i18nVendor = {getI18NString: function (string) { return string; }};
              });

        afterEach(function () {
                // clean up test double
                delete window.ilios_i18nVendor;
              });

        it("should call ilios.alert.alert()", function () {
          ilios.global.defaultAJAXFailureHandler({responseText: "foo"}, {description: "bar"});
          expect(ilios.alert.alert).toHaveBeenCalled();
        });

        it("should use the rootException.description if root expection is defined", function () {
          ilios.global.defaultAJAXFailureHandler({responseText: "foo"}, {description: "bar"});
          expect(ilios.alert.alert).toHaveBeenCalledWith("general.error.fatal (bar)");
        });

        it("should use the resultObject.responseText if rootException is undefined", function () {
          ilios.global.defaultAJAXFailureHandler({responseText: "foo"});
          expect(ilios.alert.alert).toHaveBeenCalledWith("general.error.fatal (foo)");
        });
      });

      describe("longDayOfWeekI18NStrings", function () {
        it("should be initialized to null", function () {
          expect(ilios.global.longDayOfWeekI18NStrings).toBe(null);
        });
      });

      describe("shortDayOfWeekI18NStrings", function () {
        it("should be initialized to null", function () {
          expect(ilios.global.shortDayOfWeekI18NStrings).toBe(null);
        });
      });

      describe("getI18NStringForDayOfWeek()", function () {
        beforeEach(function () {
                // test double
                window.ilios_i18nVendor = {getI18NString: function (string) { return string; }};
              });

        afterEach(function () {
                // clean up test double
                delete window.ilios_i18nVendor;

                // reset property side effects
                ilios.global.longDayOfWeekI18NStrings = null;
                ilios.global.shortDayOfWeekI18NStrings = null;
              });

        it("should return empty string if day is less than 0", function () {
          expect(ilios.global.getI18NStringForDayOfWeek(-1)).toBe("");
        });

        it("should return empty string if day is greatern than 6", function () {
          expect(ilios.global.getI18NStringForDayOfWeek(7)).toBe("");
        });

        it("should load shortDayOfWeekI18NStrings and not longDayOfWeekI18NStrings if shortString is true", function () {
          expect(ilios.global.shortDayOfWeekI18NStrings).toBe(null);
          expect(ilios.global.longDayOfWeekI18NStrings).toBe(null);
          ilios.global.getI18NStringForDayOfWeek(0, true);
          expect(ilios.global.shortDayOfWeekI18NStrings).not.toBe(null);
          expect(ilios.global.longDayOfWeekI18NStrings).toBe(null);
        });

        it("should not load shortDayOfWeekI18NStrings and load longDayOfWeekI18NStrings if shortString is false", function () {
          expect(ilios.global.shortDayOfWeekI18NStrings).toBe(null);
          expect(ilios.global.longDayOfWeekI18NStrings).toBe(null);
          ilios.global.getI18NStringForDayOfWeek(0, false);
          expect(ilios.global.shortDayOfWeekI18NStrings).toBe(null);
          expect(ilios.global.longDayOfWeekI18NStrings).not.toBe(null);
        });

        it("should return short string if requested", function () {
          expect(ilios.global.getI18NStringForDayOfWeek(0, true)).toBe("general.calendar.sunday_short");
        });

        it("should return long string if requested", function () {
          expect(ilios.global.getI18NStringForDayOfWeek(0, false)).toBe("general.calendar.sunday_long");
        });
      });
    });
  });
});
