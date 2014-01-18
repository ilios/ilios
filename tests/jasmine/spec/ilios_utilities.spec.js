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
      // Don't use real events. Let those get found in integration tests.
      // Just use a simple object for testing instead. We're just testing
      // the logic in the method, not browser quirks, etc.
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

  describe("getParsedResponseObjectFromFormUploadResponseText()", function () {
    it("should strip off leading and trailing <pre> tags", function () {
      var responseText = "<pre>{\"foo\": \"bar\"}</pre>";
      var parsed = ilios.utilities.getParsedResponseObjectFromFormUploadResponseText(responseText);
      expect(parsed).toEqual({foo: "bar"});
    });

    it("should strip off leading and trailing <PRE> tags", function () {
      var responseText = "<PRE>{\"foo\": \"bar\"}</PRE>";
      var parsed = ilios.utilities.getParsedResponseObjectFromFormUploadResponseText(responseText);
      expect(parsed).toEqual({foo: "bar"});
    });

    it("should handle the string if there are no pre elements", function () {
      var responseText = "{\"foo\": \"bar\"}";
      var parsed = ilios.utilities.getParsedResponseObjectFromFormUploadResponseText(responseText);
      expect(parsed).toEqual({foo: "bar"});
    });

    it("should call ilios.global.defaultAJAXFailureHandler and return null if JSON is invalid", function () {
      var responseText = "invalid JSON!";
      spyOn(ilios.global, "defaultAJAXFailureHandler");
      var parsed = ilios.utilities.getParsedResponseObjectFromFormUploadResponseText(responseText);
      expect(ilios.global.defaultAJAXFailureHandler).toHaveBeenCalled();
      expect(parsed).toBe(null);
    });

    it("should not call ilios.global.defaultAJAXFailureHandler if JSON is valid", function () {
      var responseText = "{\"foo\": \"bar\"}";
      spyOn(ilios.global, "defaultAJAXFailureHandler");
      ilios.utilities.getParsedResponseObjectFromFormUploadResponseText(responseText);
      expect(ilios.global.defaultAJAXFailureHandler).not.toHaveBeenCalled();
    });
  });
});
