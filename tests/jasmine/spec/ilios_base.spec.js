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
    });
  });
});
