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

  describe("dateObjectToMySQLFriendly()", function () {
    var utcDate = new Date("2010-06-28 15:26:02 UTC");
    var pdtDate = new Date("2010-06-28 15:26:02 PDT");

    it("should return MySQL-formatted date string for date object", function () {
      var mysqlFriendly = ilios.utilities.dateObjectToMySQLFriendly(utcDate);
      expect(mysqlFriendly).toBe("2010-06-28");
    });

    it("should return date but not time if includeTime is false", function () {
      var mysqlFriendly = ilios.utilities.dateObjectToMySQLFriendly(utcDate, false);
      expect(mysqlFriendly).toBe("2010-06-28");
    });

    it("should return the date and time if includeTime is true", function () {
      var mysqlFriendly = ilios.utilities.dateObjectToMySQLFriendly(utcDate, true);
      expect(mysqlFriendly).toBe("2010-06-28 15:26:02");
    });

    it("should convert time to UTC", function () {
      var mysqlFriendly = ilios.utilities.dateObjectToMySQLFriendly(pdtDate, true);
      expect(mysqlFriendly).toBe("2010-06-28 22:26:02");
    });
  });

  describe("mySQLTimelessDateToDateObject()", function () {
    it("should return a Date object for midnight local time of the yyyyMMDDStr", function () {
      var mySqlTime = ilios.utilities.mySQLTimelessDateToDateObject("2010-06-28");
      expect(mySqlTime).toEqual(new Date("2010-06-28 00:00:00"));
    });
  });

  describe("mySQLDateToDateObject()", function () {
    it("should work with a two-digit year", function () {
      // Current implementation requires a trailing space. Bug?
      // Current implementation accepts a two-digit year, but treats it like a two-digit year. 10 = 10 AD. Bug?
      // I'm guessing two-digit years and date-only (no time) just simply really aren't supported.
      var mySQLDateString = "10-06-28 ";
      var DateObject = ilios.utilities.mySQLDateToDateObject(mySQLDateString);
      var expected = new Date("June 28");
      expected.setFullYear(10);
      expect(DateObject).toEqual(expected);
    });

    it("should work without a timestamp", function () {
      // Current implementation requires a trailing space. Bug?
      var mySQLDateString = "2010-06-28 ";
      var DateObject = ilios.utilities.mySQLDateToDateObject(mySQLDateString);
      var expected = new Date("June 28, 2010");
      expect(DateObject).toEqual(expected);
    });

    it("should work with a timestamp", function () {
      var mySQLDateString = "2010-06-28 15:26:02";
      var DateObject = ilios.utilities.mySQLDateToDateObject(mySQLDateString);
      var expected = new Date("2010-06-28 15:26:02");
      expect(DateObject).toEqual(expected);
    });

    it("should use UTC time if valueIsUTC is true", function () {
      var mySQLDateString = "2010-06-28 15:26:02";
      var DateObject = ilios.utilities.mySQLDateToDateObject(mySQLDateString, true);
      var expected = new Date("2010-06-28 15:26:02 UTC");
      expect(DateObject).toEqual(expected);
    });
  });

  describe("convertMimeTypeToCSSClassName()", function () {
    it("should return an empty string if mimeType is not a string", function () {
      var mimeType = 5;
      expect(ilios.utilities.convertMimeTypeToCSSClassName(mimeType)).toBe('');
    });

    it("should replace / with --", function () {
      var mimeType = "text/plain";
      expect(ilios.utilities.convertMimeTypeToCSSClassName(mimeType)).toBe('text--plain');
    });

    it("should replace . with __", function () {
      var mimeType = "image/vnd.adobe.photoshop";
      expect(ilios.utilities.convertMimeTypeToCSSClassName(mimeType)).toBe('image--vnd__adobe__photoshop');
    });
  });

  describe("removeAllChildren()", function () {
    it("should return a childless element if passed a childless element", function () {
      var container = document.createElement('div');
      expect(container.hasChildNodes()).toBe(false);
      ilios.utilities.removeAllChildren(container);
      expect(container.hasChildNodes()).toBe(false);
    });

    it("should remove children from an element with children", function () {
      var container = document.createElement('div');
      container.appendChild(document.createElement('span'));
      expect(container.hasChildNodes()).toBe(true);
      ilios.utilities.removeAllChildren(container);
      expect(container.hasChildNodes()).toBe(false);
    });
  });
});
