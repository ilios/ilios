describe("ilios_base", function() {

  it("should create a global ilios object", function() {
    expect(ilios).toBeDefined();
  });

  describe("ilios", function () {

    it("should have a namespace method", function () {
      expect(typeof ilios.namespace).toBe("function");
    });

    it("should have a lang namespace", function () {
      expect(typeof ilios.lang).toBe("object");
    });

    it("should have an alert namespace", function () {
      expect(typeof ilios.alert).toBe("object");
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

    describe("lang", function () {
      describe("trim()", function () {
        it("should remove leading white space", function () {
          expect(ilios.lang.trim(" foo")).toBe("foo");
        });

        it("should remove trailing white space", function () {
          expect(ilios.lang.trim("foo  ")).toBe("foo");
        });

        it("should remove leading and trailing white space", function () {
          expect(ilios.lang.trim("  foo   ")).toBe("foo");
        });

        it("should not affect strings without leading or trailing spaces", function () {
          expect(ilios.lang.trim("foo")).toBe("foo");
        });

        it("should not affect spaces that are not leading or trailing", function () {
          expect(ilios.lang.trim("foo bar")).toBe("foo bar");
        });
      });

      describe("ellipsisedOfLength()", function () {
        it("should truncate str to specified number of characters and add an ellipsis", function () {
          expect(ilios.lang.ellipsisedOfLength("abcdefghijklmnopqrstuvwxyz", 10)).toBe("abcdefghij...");
        });

        it("should return str if it is shorter than length", function () {
          expect(ilios.lang.ellipsisedOfLength("abc", 10)).toBe("abc");
        });
      });
    });

    describe("alert", function () {
      describe("networkActivityI18NStrings", function () {
        it("should initialize networkActivityI18NStrings to an empty array", function () {
          expect(ilios.alert.networkActivityI18NStrings).toEqual([]);
        });
      });

      describe("updateServerInteractionProgress()", function () {
        var div;
        var text;

        beforeEach(function () {
          div = document.createElement("div");
          div.setAttribute("id", "save_in_progress_div");

          text = document.createElement("span");
          text.setAttribute("id", "save_in_progress_text");

          div.appendChild(text);

          document.body.appendChild(div);

          // test double
          window.ilios_i18nVendor = {getI18NString: function (string) { return string; }};
        });

        afterEach(function () {
          div.parentNode.removeChild(div);
          // reset alert queue to empty
          ilios.alert.networkActivityI18NStrings = [];

          // delete test double
          delete window.ilios_i18nVendor;
        });

        it("should hide save_in_progress_div if no messages in queue", function () {
          div.setAttribute("style", "display:block");
          ilios.alert.updateServerInteractionProgress();
          expect(div.getAttribute("style")).toMatch(/display:\s*none;?/);
        });

        it("should show save_in_progress_div a message is in the queue", function () {
          div.setAttribute("style", "display:none");
          ilios.alert.networkActivityI18NStrings = ["Whoa! Something's happening!"];
          ilios.alert.updateServerInteractionProgress();
          expect(div.getAttribute("style")).toMatch(/display:\s*block;?/);
        });

        it("should update text with first message in queue", function () {
          ilios.alert.networkActivityI18NStrings = ["I am Spartacus!", "No, I am Spartacus!"];
          ilios.alert.updateServerInteractionProgress();
          expect(text.innerHTML).toBe("I am Spartacus!");
        });
      });

      describe("simpleHidingHandler()", function () {
        it("should call hide() on the element", function () {
          var testDouble = {hide: function () {}};
          spyOn(testDouble, "hide");
          ilios.alert.simpleHidingHandler.call(testDouble);
          expect(testDouble.hide).toHaveBeenCalled();
        });
      });

      describe("alert()", function () {
        var testDouble;
        beforeEach(function () {
          window.ilios_i18nVendor = {getI18NString: function (string) { return string; }};
          testDouble = {
            render: jasmine.createSpy(),
            configButtons: jasmine.createSpy(),
            setBody: jasmine.createSpy(),
            cfg: {
              setProperty: jasmine.createSpy(),
              queueProperty: jasmine.createSpy()
            },
            bringToTop: jasmine.createSpy(),
            show: jasmine.createSpy()
          };
          spyOn(ilios.alert, "createConfirmDialog").and.returnValue(testDouble);
          delete ilios.alert.confirmDialog;
        });

        afterEach(function () {
          delete ilios.alert.confirmDialog;
          delete window.ilios_i18nVendor;
        });

        it("should call configButtons()", function () {
          ilios.alert.alert();
          expect(testDouble.configButtons).toHaveBeenCalled();
        });

        it("should call configButtons() using simpleHidingHandler() if we do not send an acceptHandler", function () {
          ilios.alert.alert("foo", "bar");
          var expected = [[{
            text: "bar",
            handler: jasmine.objectContaining({fn: ilios.alert.simpleHidingHandler}),
            isDefault: true
          }]];
          expect(testDouble.configButtons).toHaveBeenCalledWith(null, expected, null);
        });

        it("should call configButtons() using acceptHandler if specified", function () {
          var acceptHandler = function () { return "I am the handler!";};
          ilios.alert.alert("foo", "bar", acceptHandler);
          var expected = [[{
            text: "bar",
            handler: jasmine.objectContaining({fn: acceptHandler}),
            isDefault: true
          }]];
          expect(testDouble.configButtons).toHaveBeenCalledWith(null, expected, null);
        });

        it("should call ConfigButtons() using simpleHidingHandler() if acceptHandler specified but it is not a function", function () {
          var acceptHandler = "Not a function";
          ilios.alert.alert("foo", "bar", acceptHandler);
          var expected = [[{
            text: "bar",
            handler: jasmine.objectContaining({fn: ilios.alert.simpleHidingHandler}),
            isDefault: true
          }]];
          expect(testDouble.configButtons).toHaveBeenCalledWith(null, expected, null);
        });

        it("should call configButtons() using general.terms.ok if no button text provided", function () {
          var expected = [[jasmine.objectContaining({text: "general.terms.ok"})]];
          ilios.alert.alert("foo");
          expect(testDouble.configButtons).toHaveBeenCalledWith(null, expected, null);
        });

        it("should call configButtons() using button text if provided", function () {
          var expected = [[jasmine.objectContaining({text: "button text"})]];
          ilios.alert.alert("foo", "button text");
          expect(testDouble.configButtons).toHaveBeenCalledWith(null, expected, null);
        });

        it("should call configButtons() using an empty object if no handler args provided", function () {
          var expected = [[{
            text: "bar",
            handler: jasmine.objectContaining({obj: {}}),
            isDefault: true
          }]];
          ilios.alert.alert("foo", "bar");
          expect(testDouble.configButtons).toHaveBeenCalledWith(null, expected, null);
        });

        it("should call configButtons() using provided object if handler args object provided", function () {
          var expected = [[{
            text: "bar",
            handler: jasmine.objectContaining({obj: {arg1: "arg1"}}),
            isDefault: true
          }]];
          ilios.alert.alert("foo", "bar", null, {arg1: "arg1"});
          expect(testDouble.configButtons).toHaveBeenCalledWith(null, expected, null);
        });

        it("should call configButtons() using provided value if handler args provided as an array", function () {
          var expected = [[{
            text: "bar",
            handler: jasmine.objectContaining({obj: ["array"]}),
            isDefault: true
          }]];
          ilios.alert.alert("foo", "bar", null, ["array"]);
          expect(testDouble.configButtons).toHaveBeenCalledWith(null, expected, null);
        });

        it("should call configButtons() using empty object if non-object provided for handler args", function () {
          var expected = [[{
            text: "bar",
            handler: jasmine.objectContaining({obj: {}}),
            isDefault: true
          }]];
          ilios.alert.alert("foo", "bar", null, "not an object");
          expect(testDouble.configButtons).toHaveBeenCalledWith(null, expected, null);
        });

        it("should call configButtons() with a scope with value undefined if confirmDialog does not already exist (bug?)", function () {
          var expected = [[{
            text: "bar",
            handler: jasmine.objectContaining({scope: undefined}),
            isDefault: true
          }]];
          ilios.alert.alert("foo", "bar");
          expect(testDouble.configButtons).toHaveBeenCalledWith(null, expected, null);
        });

        it("should call configButtons() with a scope of the confirmDialog if it exists", function () {
          ilios.alert.confirmDialog = testDouble;
          var scope = testDouble;
          var expected = [[{
            text: "bar",
            handler: jasmine.objectContaining({scope: scope}),
            isDefault: true
          }]];
          ilios.alert.alert("foo", "bar");
          expect(testDouble.configButtons).toHaveBeenCalledWith(null, expected, null);
        });

        it("should call configButtons with isDefault set to true", function () {
          var expected = [[jasmine.objectContaining({isDefault: true})]];
          ilios.alert.alert("foo", "bar");
          expect(testDouble.configButtons).toHaveBeenCalledWith(null, expected, null);
        });

        it("should call createConfirmDialog() if confirmDialog does not exist", function () {
          ilios.alert.alert("foo", "bar");
          expect(ilios.alert.createConfirmDialog).toHaveBeenCalled();
        });

        it("should not call createConfirmDialog() if confirmDialog already exists", function () {
          ilios.alert.confirmDialog = testDouble;
          ilios.alert.alert("foo", "bar");
          expect(ilios.alert.createConfirmDialog).not.toHaveBeenCalled();
        });

        it("should create confirmDialog if it does not exist", function () {
          ilios.alert.alert("foo", "bar");
          expect(ilios.alert.confirmDialog).toEqual(jasmine.any(Object));
        });

        it("should call render(document.body) on confirmDialog when it creates confirmDialog", function () {
          ilios.alert.alert("foo", "bar");
          expect(ilios.alert.confirmDialog.render).toHaveBeenCalledWith(document.body);
        });

        it("should not call render() if confirmDialog already exists", function () {
          ilios.alert.confirmDialog = testDouble;
          ilios.alert.alert("foo", "bar");
          expect(ilios.alert.confirmDialog.render).not.toHaveBeenCalled();
        });

        it("should call setBody() with provided str", function () {
          ilios.alert.alert("foo");
          expect(testDouble.setBody).toHaveBeenCalledWith("foo");
        });

        it("should call cfg.setProperty('icon', YAHOO.widget.SimpleDialog.ICON_WARN)", function () {
          ilios.alert.alert();
          expect(testDouble.cfg.setProperty).toHaveBeenCalledWith("icon", YAHOO.widget.SimpleDialog.ICON_WARN);
        });

        it("should call cfg.queueProperty('zIndex', 9999)", function () {
          ilios.alert.alert();
          expect(testDouble.cfg.queueProperty).toHaveBeenCalledWith("zIndex", 9999);
        });

        it("should call bringToTop()", function () {
          ilios.alert.alert();
          expect(testDouble.bringToTop).toHaveBeenCalled();
        });

        it("should call show()", function () {
          ilios.alert.alert();
          expect(testDouble.show).toHaveBeenCalled();
        });
      });



      describe("inform()", function () {
        var testDouble;
        beforeEach(function () {
          window.ilios_i18nVendor = {getI18NString: function (string) { return string; }};
          testDouble = {
            render: jasmine.createSpy(),
            configButtons: jasmine.createSpy(),
            setBody: jasmine.createSpy(),
            cfg: {
              setProperty: jasmine.createSpy(),
              queueProperty: jasmine.createSpy()
            },
            bringToTop: jasmine.createSpy(),
            show: jasmine.createSpy()
          };
          spyOn(ilios.alert, "createInformDialog").and.returnValue(testDouble);
          delete ilios.alert.informDialog;
        });

        afterEach(function () {
          delete ilios.alert.informDialog;
          delete window.ilios_i18nVendor;
        });

        it("should call configButtons()", function () {
          ilios.alert.inform();
          expect(testDouble.configButtons).toHaveBeenCalled();
        });

        it("should call configButtons() using simpleHidingHandler() if we do not send an acceptHandler", function () {
          ilios.alert.inform("foo", "bar");
          var expected = [[{
            text: "bar",
            handler: jasmine.objectContaining({fn: ilios.alert.simpleHidingHandler})
          }, jasmine.any(Object)]];
          expect(testDouble.configButtons).toHaveBeenCalledWith(null, expected, null);
        });

        it("should call configButtons() using simpleHidingHandler() if we do not send an declineHandler", function () {
          ilios.alert.inform("foo", "bar", null, null, "baz");
          var expected = [[jasmine.any(Object), {
            text: "baz",
            handler: jasmine.objectContaining({fn: ilios.alert.simpleHidingHandler}),
            isDefault: true
          }]];
          expect(testDouble.configButtons).toHaveBeenCalledWith(null, expected, null);
        });

        it("should call configButtons() using acceptHandler if specified", function () {
          var acceptHandler = function () { return "I am the handler!";};
          ilios.alert.inform("foo", "bar", acceptHandler);
          var expected = [[{
            text: "bar",
            handler: jasmine.objectContaining({fn: acceptHandler})
          }, jasmine.any(Object)]];
          expect(testDouble.configButtons).toHaveBeenCalledWith(null, expected, null);
        });

        it("should call configButtons() using declineHandler if specified", function () {
          var declineHandler = function () { return "I am the handler!";};
          ilios.alert.inform("foo", "bar", null, null, "baz", declineHandler, null);
          var expected = [[jasmine.any(Object), {
            text: "baz",
            handler: jasmine.objectContaining({fn: declineHandler}),
            isDefault: true
          }]];
          expect(testDouble.configButtons).toHaveBeenCalledWith(null, expected, null);
        });

        it("should call ConfigButtons() using simpleHidingHandler() if acceptHandler specified but it is not a function", function () {
          var acceptHandler = "Not a function";
          ilios.alert.inform("foo", "bar", acceptHandler);
          var expected = [[{
            text: "bar",
            handler: jasmine.objectContaining({fn: ilios.alert.simpleHidingHandler}),
          }, jasmine.any(Object)]];
          expect(testDouble.configButtons).toHaveBeenCalledWith(null, expected, null);
        });

        it("should call ConfigButtons() using simpleHidingHandler() if declineHandler specified but it is not a function", function () {
          var declineHandler = "Not a function";
          ilios.alert.inform("foo", "bar", null, null, "baz", declineHandler);
          var expected = [[jasmine.any(Object), {
            text: "baz",
            handler: jasmine.objectContaining({fn: ilios.alert.simpleHidingHandler}),
            isDefault: true
          }]];
          expect(testDouble.configButtons).toHaveBeenCalledWith(null, expected, null);
        });

        it("should call configButtons() using general.terms.yes if no accept button text provided", function () {
          var expected = [[jasmine.objectContaining({text: "general.terms.yes"}), jasmine.any(Object)]];
          ilios.alert.inform("foo");
          expect(testDouble.configButtons).toHaveBeenCalledWith(null, expected, null);
        });

        it("should call configButtons() using general.terms.no if no decline button text provided", function () {
          var expected = [[jasmine.any(Object), jasmine.objectContaining({text: "general.terms.no"})]];
          ilios.alert.inform("foo");
          expect(testDouble.configButtons).toHaveBeenCalledWith(null, expected, null);
        });

        it("should call configButtons() using accept button text if provided", function () {
          var expected = [[jasmine.objectContaining({text: "accept button text"}), jasmine.any(Object)]];
          ilios.alert.inform("foo", "accept button text");
          expect(testDouble.configButtons).toHaveBeenCalledWith(null, expected, null);
        });

        it("should call configButtons() using decline button text if provided", function () {
          var expected = [[jasmine.any(Object), jasmine.objectContaining({text: "decline button text"})]];
          ilios.alert.inform("foo", null, null, null, "decline button text");
          expect(testDouble.configButtons).toHaveBeenCalledWith(null, expected, null);
        });

        it("should call configButtons() using an empty object if no accept handler args provided", function () {
          var expected = [[{
            text: "bar",
            handler: jasmine.objectContaining({obj: {}})
          }, jasmine.any(Object)]];
          ilios.alert.inform("foo", "bar");
          expect(testDouble.configButtons).toHaveBeenCalledWith(null, expected, null);
        });

        it("should call configButtons() using an empty object if no decline handler args provided", function () {
          var expected = [[jasmine.any(Object), {
            text: "baz",
            handler: jasmine.objectContaining({obj: {}}),
            isDefault: true
          }]];
          ilios.alert.inform("foo", "bar", null, null, "baz");
          expect(testDouble.configButtons).toHaveBeenCalledWith(null, expected, null);
        });

        it("should call configButtons() using provided object if accept handler args object provided", function () {
          var expected = [[{
            text: "bar",
            handler: jasmine.objectContaining({obj: {arg1: "arg1"}})
          }, jasmine.any(Object)]];
          ilios.alert.inform("foo", "bar", null, {arg1: "arg1"});
          expect(testDouble.configButtons).toHaveBeenCalledWith(null, expected, null);
        });

        it("should call configButtons() using provided object if decline handler args object provided", function () {
          var expected = [[jasmine.any(Object), {
            text: "baz",
            handler: jasmine.objectContaining({obj: {arg1: "arg1"}}),
            isDefault: true
          }]];
          ilios.alert.inform("foo", "bar", null, null, "baz", null, {arg1: "arg1"});
          expect(testDouble.configButtons).toHaveBeenCalledWith(null, expected, null);
        });

        it("should call configButtons() using provided value if accept handler args provided as an array", function () {
          var expected = [[{
            text: "bar",
            handler: jasmine.objectContaining({obj: ["array"]})
          }, jasmine.any(Object)]];
          ilios.alert.inform("foo", "bar", null, ["array"]);
          expect(testDouble.configButtons).toHaveBeenCalledWith(null, expected, null);
        });

        it("should call configButtons() using provided value if decline handler args provided as an array", function () {
          var expected = [[jasmine.any(Object), {
            text: "baz",
            handler: jasmine.objectContaining({obj: ["array"]}),
            isDefault: true
          }]];
          ilios.alert.inform("foo", "bar", null, null, "baz", null, ["array"]);
          expect(testDouble.configButtons).toHaveBeenCalledWith(null, expected, null);
        });

        it("should call configButtons() using empty object if non-object provided for accept handler args", function () {
          var expected = [[{
            text: "bar",
            handler: jasmine.objectContaining({obj: {}})
          }, jasmine.any(Object)]];
          ilios.alert.inform("foo", "bar", null, "not an object");
          expect(testDouble.configButtons).toHaveBeenCalledWith(null, expected, null);
        });

        it("should call configButtons() using empty object if non-object provided for accept handler args", function () {
          var expected = [[jasmine.any(Object), {
            text: "baz",
            handler: jasmine.objectContaining({obj: {}}),
            isDefault: true
          }]];
          ilios.alert.inform("foo", "bar", null, null, "baz", null, "not an object");
          expect(testDouble.configButtons).toHaveBeenCalledWith(null, expected, null);
        });

        it("should call configButtons() with a scope with value undefined if informDialog does not already exist (bug?)", function () {
          var expected = [[
            {
              text: "bar",
              handler: jasmine.objectContaining({scope: undefined})
            },
            {
              text: "baz",
              handler: jasmine.objectContaining({scope: undefined}),
              isDefault: true
            }
          ]];
          ilios.alert.inform("foo", "bar", null, null, "baz");
          expect(testDouble.configButtons).toHaveBeenCalledWith(null, expected, null);
        });

        it("should call configButtons() with a scope of the informDialog if it exists", function () {
          ilios.alert.informDialog = testDouble;
          var scope = testDouble;
          var expected = [[
            {
              text: "bar",
              handler: jasmine.objectContaining({scope: scope})
            },
            {
              text: "baz",
              handler: jasmine.objectContaining({scope: scope}),
              isDefault: true
            }
          ]];
          ilios.alert.inform("foo", "bar", null, null, "baz");
          expect(testDouble.configButtons).toHaveBeenCalledWith(null, expected, null);
        });

        it("should call configButtons with isDefault set to true for decline only", function () {
          var expected = [[jasmine.any(Object), jasmine.objectContaining({isDefault: true})]];
          var notExpected = [[jasmine.objectContaining({isDefault: true}, jasmine.any(Object))]];
          ilios.alert.inform("foo", "bar", null, null, "baz");
          expect(testDouble.configButtons).toHaveBeenCalledWith(null, expected, null);
          expect(testDouble.configButtons).not.toHaveBeenCalledWith(null, notExpected, null);
        });

        it("should call createInformDialog() if informDialog does not exist", function () {
          ilios.alert.inform("foo", "bar");
          expect(ilios.alert.createInformDialog).toHaveBeenCalled();
        });

        it("should not call createInformDialog() if informDialog already exists", function () {
          ilios.alert.informDialog = testDouble;
          ilios.alert.inform("foo", "bar");
          expect(ilios.alert.createInformDialog).not.toHaveBeenCalled();
        });

        it("should create informDialog if it does not exist", function () {
          ilios.alert.inform("foo", "bar");
          expect(ilios.alert.informDialog).toEqual(jasmine.any(Object));
        });

        it("should call render(document.body) on informDialog when it creates informDialog", function () {
          ilios.alert.inform("foo", "bar");
          expect(ilios.alert.informDialog.render).toHaveBeenCalledWith(document.body);
        });

        it("should not call render() if informDialog already exists", function () {
          ilios.alert.informDialog = testDouble;
          ilios.alert.inform("foo", "bar");
          expect(ilios.alert.informDialog.render).not.toHaveBeenCalled();
        });

        it("should call setBody() with provided str", function () {
          ilios.alert.inform("foo");
          expect(testDouble.setBody).toHaveBeenCalledWith("foo");
        });

        it("should call cfg.setProperty('icon', YAHOO.widget.SimpleDialog.ICON_WARN)", function () {
          ilios.alert.inform();
          expect(testDouble.cfg.setProperty).toHaveBeenCalledWith("icon", YAHOO.widget.SimpleDialog.ICON_WARN);
        });

        it("should call cfg.queueProperty('zIndex', 9999)", function () {
          ilios.alert.inform();
          expect(testDouble.cfg.queueProperty).toHaveBeenCalledWith("zIndex", 9999);
        });

        it("should call bringToTop()", function () {
          ilios.alert.inform();
          expect(testDouble.bringToTop).toHaveBeenCalled();
        });

        it("should call show()", function () {
          ilios.alert.inform();
          expect(testDouble.show).toHaveBeenCalled();
        });
      });

      describe("createConfirmDialog()", function () {
        beforeEach(function () {
          // test double of ilios_i18nVendor global
          window.ilios_i18nVendor = {getI18NString: function (str) { return str; }};
        });

        afterEach(function () {
          // clean up test double
          delete window.ilios_i18nVendor;
        });

        it("should return an instance of YAHOO.widget.SimpleDialog", function () {
          var dialog = ilios.alert.createConfirmDialog();
          expect(dialog instanceof YAHOO.widget.SimpleDialog).toBe(true);
        });

        it("should set the id to 'ilios_alert_panel'", function () {
          var dialog = ilios.alert.createConfirmDialog();
          expect(dialog.id).toBe("ilios_alert_panel");
        });

        it("should set visible to false", function () {
          var dialog = ilios.alert.createConfirmDialog();
          expect(dialog.cfg.config.visible.value).toBe(false);
        });

        it("should be 350px wide", function () {
          var dialog = ilios.alert.createConfirmDialog();
          expect(dialog.cfg.config.width.value).toBe("350px");
        });

        it("should have zIndex of 9999", function () {
          var dialog = ilios.alert.createConfirmDialog();
          expect(dialog.cfg.config.zindex.value).toBe(9999);
        });

        it("should set close to false", function () {
          var dialog = ilios.alert.createConfirmDialog();
          expect(dialog.cfg.config.close.value).toBe(false);
        });

        it("should set fixedcenter to 'contained'", function () {
          var dialog = ilios.alert.createConfirmDialog();
          expect(dialog.cfg.config.fixedcenter.value).toBe("contained");
        });

        it("should set modal to true", function () {
          var dialog = ilios.alert.createConfirmDialog();
          expect(dialog.cfg.config.modal.value).toBe(true);
        });

        it("should set draggable to true", function () {
          var dialog = ilios.alert.createConfirmDialog();
          expect(dialog.cfg.config.draggable.value).toBe(true);
        });

        it("should set constraintoviewport to true", function () {
          var dialog = ilios.alert.createConfirmDialog();
          expect(dialog.cfg.config.constraintoviewport.value).toBe(true);
        });

        it("should set icon to YAHOO.widget.SimpleDialog.ICON_WARN", function () {
          var dialog = ilios.alert.createConfirmDialog();
          expect(dialog.cfg.config.icon.value).toBe(YAHOO.widget.SimpleDialog.ICON_WARN);
        });
      });
    });
  });
});
