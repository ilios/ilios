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

      describe("startsWith()", function () {
        it("should return true if str starts with prefix", function () {
          expect(ilios.lang.startsWith("food", "foo")).toBe(true);
        });

        it("should return false if str does not start with prefix", function () {
          expect(ilios.lang.startsWith("salad", "bar")).toBe(false);
        });
      });

    });
  });

});
