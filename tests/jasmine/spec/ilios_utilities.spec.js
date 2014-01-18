describe("ilios_utilities", function() {
  it("should create a utilities namespace on the ilios global object", function () {
    expect(typeof ilios.utilities).toBe("object");
  });

  it("should define 3 different user name formats", function () {
    var formats = [
      ilios.utilities.USER_NAME_FORMAT_LAST_FIRST,
      ilios.utilities.USER_NAME_FORMAT_FIRST_FIRST,
      ilios.utilities.USER_NAME_FORMAT_FIRST_INITIAL_FIRST
    ];
    expect(formats[0]).toBeDefined();
    expect(formats[1]).toBeDefined();
    expect(formats[2]).toBeDefined();
    expect(formats[0]).not.toEqual(formats[1]);
    expect(formats[0]).not.toEqual(formats[2]);
    expect(formats[1]).not.toEqual(formats[2]);
  });

  describe("setToolTipForElement()", function () {
    it("should set the title attribute of the element", function () {
      var element = document.createElement('div');
      var tooltip = "Should tooltip text really go in the title attribute?";
      ilios.utilities.setToolTipForElement(element, tooltip);
      expect(element.getAttribute('title')).toBe(tooltip);
    });
  });

  describe("getEventTarget()", function () {
    var event;
    var div;

    beforeEach(function () {
      event = {};
      div = document.createElement('div');
    });

    it("should return target attribute if it exists", function () {
      event.target = div;
      expect(ilios.utilities.getEventTarget(event)).toEqual(div);
    });

    it("should return srcElement attribute if target attribute does not exist", function () {
      event.srcElement = div;
      expect(ilios.utilities.getEventTarget(event)).toEqual(div);
    });
  });
});
