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

  describe("modelItemNeedsPublishing()", function () {
    var makeModelTestDouble = function (publishEventId, modelDirty) {
      return {
        getPublishEventId: function () { return publishEventId; },
        isModelDirty: function () { return modelDirty; }
      };
    };

    it("should return true if model.getPublishEventId() returns null", function () {
      var model = makeModelTestDouble(null, false);
      expect(ilios.utilities.modelItemNeedsPublishing(model)).toBe(true);
    });

    it("should return true if model.getPublishEventId() returns less than 1", function () {
      var model = makeModelTestDouble(0, true);
      expect(ilios.utilities.modelItemNeedsPublishing(model)).toBe(true);
    });

    it("should return false if model.getPublishEventId() is 1 or greater and isModelDirty() returns false", function () {
      var model = makeModelTestDouble(1, false);
      expect(ilios.utilities.modelItemNeedsPublishing(model)).toBe(false);
    });

    it("should return true if model.getPublishEventId() is 1 or greater and isModelDirty() returns true", function () {
      var model = makeModelTestDouble(1, true);
      expect(ilios.utilities.modelItemNeedsPublishing(model)).toBe(true);
    });
  });

  describe("canPublishModelItem()", function () {
    var makeModelTestDouble = function (modelItemNeedsPublishing, getPublishability, CANNOT_BE_PUBLISHED) {
      var rv = {};
      if (modelItemNeedsPublishing === true) {
        rv.getPublishEventId = function () { return null; };
        rv.isModelDirty = function () { return true; };
      } else {
        rv.getPublishEventId = function () { return 1; };
        rv.isModelDirty = function () { return false; };
      }
      rv.getPublishability = function () { return getPublishability; };
      rv.CANNOT_BE_PUBLISHED = CANNOT_BE_PUBLISHED;
      return rv;
    };

    it("should be false if modelItemNeedsPublishing() is false", function () {
      var model = makeModelTestDouble(false, 1, 0);
      expect(ilios.utilities.canPublishModelItem(model)).toBe(false);
    });

    it("should be false if mobileItemNeedsPublishing() is true but getPublishability() is CANNOT_BE_PUBLISHED", function () {
      var model = makeModelTestDouble(true, 0, 0);
      expect(ilios.utilities.canPublishModelItem(model)).toBe(false);
    });

    it("should be true if mobileItemNeedsPublishing() is true and getPublishability() is not CANNOT_BE_PUBLISHED", function () {
      var model = makeModelTestDouble(true, 1, 0);
      expect(ilios.utilities.canPublishModelItem(model)).toBe(true);
    });
  });

  describe("appendClearingDivToContainer()", function () {
    it("should add a div with class clear as a child to HTMLElement container", function () {
      var container = document.createElement("div");
      ilios.utilities.appendClearingDivToContainer(container);
      expect(container.innerHTML).toBe("<div class=\"clear\"></div>");
    });

    it("should add a div with class clear as a child to YAHOO.util.Element container", function () {
      var container = new YAHOO.util.Element(document.createElement("div"), {});
      ilios.utilities.appendClearingDivToContainer(container);
      expect(container.get("element").innerHTML).toBe("<div class=\"clear\"></div>");
    });
  });

  describe("toggleShowMoreOrLess()", function () {
    var container;
    var getI18NStringTestDouble = function (phrase) {
      return phrase === "general.phrases.show_less" ? "Show Less" : "Show More";
    };

    beforeEach(function () {
      container = document.createElement('div');
      container.setAttribute("id", "foo");
      document.body.appendChild(container);
      window.ilios_i18nVendor = {getI18NString: getI18NStringTestDouble};
    });

    afterEach(function () {
      container.parentNode.removeChild(container);
      delete window.ilios_i18nVendor;
    });

    it("should toggle display:none to display:block", function () {
      container.setAttribute("style", "display: none;");
      ilios.utilities.toggleShowMoreOrLess("foo", null);
      expect(container.getAttribute("style")).toEqual("display: block;");
    });

    it("should toggle display:block to display:none", function () {
      container.setAttribute("style", "display: block;");
      ilios.utilities.toggleShowMoreOrLess("foo", null);
      expect(container.getAttribute("style")).toEqual("display: none;");
    });

    it("should change text to lowercased show_less when toggling from display:none", function () {
      container.setAttribute("style", "display: none;");
      var linkElement = document.createElement('a');
      expect(linkElement.innerHTML).toBe("");
      ilios.utilities.toggleShowMoreOrLess("foo", linkElement);
      expect(linkElement.innerHTML).toBe("show less");
    });

    it("should change text to lowercased show_more when toggling from display:block", function () {
      container.setAttribute("style", "display: block;");
      var linkElement = document.createElement('a');
      expect(linkElement.innerHTML).toBe("");
      ilios.utilities.toggleShowMoreOrLess("foo", linkElement);
      expect(linkElement.innerHTML).toBe("show more");
    });
  });

  describe("toggle()", function () {
    var container;

    beforeEach(function () {
      container = document.createElement('div');
      container.setAttribute("id", "foo");
      document.body.appendChild(container);
    });

    afterEach(function () {
      container.parentNode.removeChild(container);
    });

    it("should toggle display:none to display:block", function () {
      container.setAttribute("style", "display: none;");
      ilios.utilities.toggle("foo", null);
      expect(container.getAttribute("style")).toEqual("display: block;");
    });

    it("should toggle display:block to display:none", function () {
      container.setAttribute("style", "display: block;");
      ilios.utilities.toggle("foo", null);
      expect(container.getAttribute("style")).toEqual("display: none;");
    });

    it("should set class to icon-minus when toggling from display:none", function () {
      var linkElement = document.createElement("div");
      var linkChild = document.createElement("span");
      linkElement.appendChild(linkChild);
      container.setAttribute("style", "display: none;");
      ilios.utilities.toggle("foo", linkElement);
      expect(linkChild.getAttribute("class")).toBe("icon-minus");
    });

    it("should set class to icon-plus when toggling from display:block", function () {
      var linkElement = document.createElement("div");
      var linkChild = document.createElement("span");
      linkElement.appendChild(linkChild);
      container.setAttribute("style", "display: block;");
      ilios.utilities.toggle("foo", linkElement);
      expect(linkChild.getAttribute("class")).toBe("icon-plus");
    });

    it("should remove icon-plus when toggling from display:none", function () {
      var linkElement = document.createElement("div");
      var linkChild = document.createElement("span");
      linkChild.setAttribute("class", "icon-plus");
      linkElement.appendChild(linkChild);
      container.setAttribute("style", "display: none;");
      ilios.utilities.toggle("foo", linkElement);
      expect(linkChild.getAttribute("class")).toBe("icon-minus");
    });

    it("should remove icon-minus when toggling from display:block", function () {
      var linkElement = document.createElement("div");
      var linkChild = document.createElement("span");
      linkChild.setAttribute("class", "icon-minus");
      linkElement.appendChild(linkChild);
      container.setAttribute("style", "display: block;");
      ilios.utilities.toggle("foo", linkElement);
      expect(linkChild.getAttribute("class")).toBe("icon-plus");
    });

    it("should leave existing non-icon-plus/minus classes alone", function () {
      var linkElement = document.createElement("div");
      var linkChild = document.createElement("span");
      linkChild.setAttribute("class", "classy");
      linkElement.appendChild(linkChild);
      container.setAttribute("style", "display: block;");
      ilios.utilities.toggle("foo", linkElement);
      expect(linkChild.getAttribute("class")).toBe("classy icon-plus");
    });
  });

  describe("selectOptionWithValue()", function () {
    var selectElement;

    beforeEach(function () {
      selectElement = document.createElement('select');
      var options = [];
      // Generate option0, option1, and option2.
      for (var i=0; i<3; i++) {
        options[i] = document.createElement('option');
        options[i].value = "option" + (i);
        options[i].text = "Option #" + (i);
        selectElement.appendChild(options[i]);
      }
    });

    it("should select the option with the requested value", function () {
      ilios.utilities.selectOptionWithValue(selectElement, "option1");
      expect(selectElement.selectedIndex).toBe(1);
    });

    it("should return true if requested value exists", function () {
      expect(ilios.utilities.selectOptionWithValue(selectElement, "option1")).toBe(true);
    });

    it("should select the first option if null is sent for the requested value", function () {
      ilios.utilities.selectOptionWithValue(selectElement, null);
      expect(selectElement.selectedIndex).toBe(0);
    });

    it("should return true if null is requested value", function () {
      expect(ilios.utilities.selectOptionWithValue(selectElement, null)).toBe(true);
    });

    it("should select the first option if requested value is not found", function () {
      ilios.utilities.selectOptionWithValue(selectElement, "option10");
      expect(selectElement.selectedIndex).toBe(0);
    });

    it("should return false if requested value is not found", function () {
      expect(ilios.utilities.selectOptionWithValue(selectElement, "option10")).toBe(false);
    });
  });

  describe("getXMLHttpRequest()", function () {
    /* TODO: Use dependency injection or a wrapper to make getXMLHttpRequest() more testable.
             Better yet, get rid of it entirely and use a library/framework abstraction like YUI or jQuery.
    */
    it("should return an XMLHTTPRequest object in a supported browser", function () {
      expect(ilios.utilities.getXMLHttpRequest() instanceof XMLHttpRequest).toBe(true);
    });
  });

  describe("arrayContains()", function () {
    it("should return true if value is in array", function () {
      expect(ilios.utilities.arrayContains(["a","b","c"], "b")).toBe(true);
    });

    it("should return false if value is not in array", function () {
      expect(ilios.utilities.arrayContains(["a","b","c"], "d")).toBe(false);
    });
  });

  describe("makeUniqueArray()", function () {
    it("should return an array with duplicate values removed", function () {
      expect(ilios.utilities.makeUniqueArray(["a","b","b","c","c","d"])).toEqual(["a","b","c","d"]);
    });

    it("should not alter an array that does not have duplicate values", function () {
      expect(ilios.utilities.makeUniqueArray(["a","b"])).toEqual(["a","b"]);
    });
  });

  describe("simplyArrayEquality()", function () {
    it("should return false if arrays are of different sizes", function () {
      expect(ilios.utilities.simplyArrayEquality(["a"], ["a","a"])).toBe(false);
    });

    it("should return true if arrays are the same", function () {
      expect(ilios.utilities.simplyArrayEquality(["a","b","c"], ["a","b","c"])).toBe(true);
    });

    it("should return true if arrays have the same contents in a different order", function () {
      expect(ilios.utilities.simplyArrayEquality(["a","b"], ["b","a"])).toBe(true);
    });

    it("should return false if arrays are the same length but have the different contents", function () {
      expect(ilios.utilities.simplyArrayEquality(["a","b"], ["b","b"])).toBe(false);
    });

    it("should return false if arr1 and arr2 are the same length but arr1 doesn't contain everything in arr2", function () {
      expect(ilios.utilities.simplyArrayEquality(["a","a"], ["a","b"])).toBe(false);
    });
  });

  describe("objectEquality()", function () {
    it("should return false if objects have a different number of keys", function () {
      expect(ilios.utilities.objectEquality({a:1}, {a:1,b:2})).toBe(false);
    });

    it("should return false if objects have same number of keys but the keys are different", function () {
      expect(ilios.utilities.objectEquality({a:1}, {b:1})).toBe(false);
    });

    it("should return false if objects have the same keys but different values", function () {
      expect(ilios.utilities.objectEquality({a:1}, {a:2})).toBe(false);
    });

    it("should return true if objects have the same key/value pairs, even in different 'order'", function () {
      expect(ilios.utilities.objectEquality({a:1,b:2}, {b:2,a:1})).toBe(true);
    });
  });

  describe("objectPropertyCount()", function () {
    it("should return the length of an array", function () {
      expect(ilios.utilities.objectPropertyCount([1,2,3])).toBe(3);
    });

    it("should return the number of property/value pairs in an object", function () {
      expect(ilios.utilities.objectPropertyCount({a:1,b:2,c:3})).toBe(3);
    });
  });

  describe("removeElementWithValue()", function () {
    it("should remove the first element with the value", function () {
      var anArray = ["a","b","c","a","c","a","b"];
      ilios.utilities.removeElementWithValue(anArray, "c");
      expect(anArray).toEqual(["a","b","a","c","a","b"]);
    });

    it("should not modify an array that does not have the value", function () {
      var anArray = ["a","b","a","c","a","b"];
      ilios.utilities.removeElementWithValue(anArray, "d");
      expect(anArray).toEqual(["a","b","a","c","a","b"]);
    });
  });

  // Wow. Just wow. Total facepalm wat going on with this and deepcloneAssociativeArray().
  describe("cloneAssociativeArray()", function () {
    // This one spec is all I can really muster for this horrible method.
    it("should return different instance of array with same instances of values", function () {
      var myThing = [];
      myThing.foo = [];
      myThing.foo.bar = "baz";
      var newThing = ilios.utilities.cloneAssociativeArray(myThing);
      expect(newThing.foo).toBe(myThing.foo);
      expect(newThing).not.toBe(myThing);
      expect(newThing.length).toBe(1);
    });
  });

  describe("deepCloneAssociativeArray()", function () {
    // And this one inadequate spec is all I can muster for this horrible method.
    it("should return different instance of array with different instances of values", function () {
      var myThing = [];
      myThing.foo = [];
      myThing.foo.bar = "baz";
      // It's dependent on a clone method being predefined. It just keeps getting worse.
      myThing.foo.clone = function () {return;};
      spyOn(myThing.foo, "clone");
      var newThing = ilios.utilities.deepCloneAssociativeArray(myThing);
      expect(newThing.foo).not.toBe(myThing.foo);
      expect(newThing).not.toBe(myThing);
      expect(newThing.length).toBe(1);
      expect(myThing.foo.clone).toHaveBeenCalled();
    });
  });

  describe("arrayHasElementsMissingInArray()", function () {
    it("should return false if arrays have all the same key/value pairs", function () {
      expect(ilios.utilities.arrayHasElementsMissingInArray([1,2,3], [1,2,3])).toBe(false);
    });

    it("should return false if arrays have all the same values but in different orders", function () {
      expect(ilios.utilities.arrayHasElementsMissingInArray([1,2,3], [3,2,1])).toBe(false);
    });

    it("should return false if reference array has a subset of the values in other array", function () {
      expect(ilios.utilities.arrayHasElementsMissingInArray([1,2,], [3,1,2])).toBe(false);
    });

    it("should return true if a value in reference array is missing in other array", function () {
      expect(ilios.utilities.arrayHasElementsMissingInArray([1,2,3], [1,2])).toBe(true);
    });
  });

  describe("titledNaturalComparator()", function () {
    var a = {getTitle: function () { return "a";}};
    var b = {getTitle: function () { return "b";}};
    var b2 = {getTitle: function () { return "b";}};

    it("should return negative if first arg sorts before second arg", function () {
      expect(ilios.utilities.titledNaturalComparator(a,b)).toBeLessThan(0);
    });

    it("should return positive if first arg sorts after second arg", function () {
      expect(ilios.utilities.titledNaturalComparator(b,a)).toBeGreaterThan(0);
    });

    it("should return 0 if first arg sorts neither before nor after second arg", function () {
      expect(ilios.utilities.titledNaturalComparator(b,b2)).toBe(0);
    });
  });

  describe("stringifyObjectAsArray()", function () {
    it("should return an object as a JSON array because...I don't know...what is this nonsense?", function() {
      var myObj = {rock: "lobster", love: "shack"};
      expect(ilios.utilities.stringifyObjectAsArray(myObj)).toBe("[\"lobster\",\"shack\"]");
    });

    it("should wrap YUI stringify, passing whitelist and spaces", function () {
      spyOn(YAHOO.lang.JSON, "stringify");
      ilios.utilities.stringifyObjectAsArray({}, ["a","b","c"], 5);
      expect(YAHOO.lang.JSON.stringify).toHaveBeenCalledWith([], ["a","b","c"], 5);
    });
  });

  describe("delimitedStringOfTitledObjects()", function () {
    it("should return an empty string if given an empty array", function () {
      expect(ilios.utilities.delimitedStringOfTitledObjects([], ",")).toBe("");
    });

    it("should return an empty string if given an empty object", function () {
      expect(ilios.utilities.delimitedStringOfTitledObjects({}, ",")).toBe("");
    });

    it("should return just the one title and no delimiter if given an array of length 1", function () {
      expect(ilios.utilities.delimitedStringOfTitledObjects([{title: "Abbey Road"}], ",")).toBe("Abbey Road");
    });

    it("should return just the one title and no delimiter if given an object of length 1", function () {
      expect(ilios.utilities.delimitedStringOfTitledObjects({a: {title: "Rubber Soul"}}, ",")).toBe("Rubber Soul");
    });

    it("should return titles separated by delimiter+space if given an array", function () {
      var myArr = [{title: "The Trees"}, {title: "Tom Sawyer"}];
      expect(ilios.utilities.delimitedStringOfTitledObjects(myArr, ",")).toBe("The Trees, Tom Sawyer");
    });

    it("should return title separated by delimiter+space if given an object", function () {
      var myObj = {a: {title: "Xanadu"}, b: {title: "2112"}};
      expect(ilios.utilities.delimitedStringOfTitledObjects(myObj, ",")).toBe("Xanadu, 2112");
    });

    it("should handle a title property", function () {
      var myArr = [{title: "A Farewell To Kings"}];
      expect(ilios.utilities.delimitedStringOfTitledObjects(myArr, ",")).toBe("A Farewell To Kings");
    });

    it("should handle a getTitle() function", function () {
      var myArr = [{getTitle: function () { return "By-Tor and the Snow Dog"; }}];
      expect(ilios.utilities.delimitedStringOfTitledObjects(myArr, ",")).toBe("By-Tor and the Snow Dog");
    });

    it("should favor getTitle() over title if both exist", function () {
      var myArr = [{title: "A Passage To Bangkok", getTitle: function () { return "The Spirit of Radio"; }}];
      expect(ilios.utilities.delimitedStringOfTitledObjects(myArr, ",")).toBe("The Spirit of Radio");
    });
  });

  describe("searchListElementForModel()", function () {
    var listElement;
    var listItem;
    var compareToTestDoubleMatch = function () { return 0; };
    var compareToTestDoubleNoMatch = function () { return 1; };

    beforeEach(function () {
      listElement = document.createElement("ul");
      listItem = document.createElement("li");
      listItem.innerHTML = "foo";
    });

    it("should return false if listElement has no list items", function () {
      expect(ilios.utilities.searchListElementForModel(listElement, {})).toBe(false);
    });

    it("should return false if list item has no iliosModel attribute", function () {
      listElement.appendChild(listItem);
      expect(ilios.utilities.searchListElementForModel(listElement, {})).toBe(false);
    });

    it("should return false if list item has iliosModel.compareTo() that returns non-zero;", function () {
      listItem.iliosModel = {compareTo: compareToTestDoubleNoMatch};
      listElement.appendChild(listItem);
      expect(ilios.utilities.searchListElementForModel(listElement, {})).toBe(false);
    });

    it("should return true if list item has iliosModel.compareTo() that returns zero", function () {
      listItem.iliosModel = {compareTo: compareToTestDoubleMatch};
      listElement.appendChild(listItem);
      expect(ilios.utilities.searchListElementForModel(listElement, {})).toBe(true);
    });

    it("should return true if at least one (but not all) list items has iliosModel.compareTo() that returns zero", function () {
      listItem.iliosModel = {compareTo: compareToTestDoubleNoMatch};
      listElement.appendChild(listItem);
      var secondItem = document.createElement("li");
      secondItem.innerHTML = "bar";
      secondItem.iliosModel = {compareTo: compareToTestDoubleMatch};
      listElement.appendChild(secondItem);
      expect(ilios.utilities.searchListElementForModel(listElement, {})).toBe(true);
    });

    it("should pass candidateModel to compareTo()", function () {
      var candidateModel = {hello: "I am a fake model object"};
      listItem.iliosModel = {compareTo: function () {}};
      spyOn(listItem.iliosModel, "compareTo");
      listElement.appendChild(listItem);
      ilios.utilities.searchListElementForModel(listElement, candidateModel);
      expect(listItem.iliosModel.compareTo).toHaveBeenCalledWith(candidateModel);
    });
  });

  describe("percentUnicodeToHTML()", function () {
    it("should leave text without %u unchanged", function () {
      expect(ilios.utilities.percentUnicodeToHTML("I am a string.")).toBe("I am a string.");
    });

    it("should convert %u to &#x", function () {
      expect(ilios.utilities.percentUnicodeToHTML("Change this, please: %u.")).toBe("Change this, please: &#x.");
    });

    it("should replace multiple instances of %u", function () {
      expect(ilios.utilities.percentUnicodeToHTML("Change all of these, please: %u! %u! %u!")).toBe("Change all of these, please: &#x! &#x! &#x!");
    });
  });

  describe("htmlEntitiesDecode()", function () {
    it("should decode &lt; as <", function () {
      expect(ilios.utilities.htmlEntitiesDecode("&lt;")).toBe("<");
    });

    it("should decode &gt; as >", function () {
      expect(ilios.utilities.htmlEntitiesDecode("&gt;")).toBe(">");
    });

    it("should not alter a string with no entities", function () {
      expect(ilios.utilities.htmlEntitiesDecode("Entity-free!")).toBe("Entity-free!");
    });

    it("should decode &amp; as &", function () {
      expect(ilios.utilities.htmlEntitiesDecode("&amp;")).toBe("&");
    });
  });

  describe("htmlEntities()", function () {
    it("should encode & as &amp;", function () {
      expect(ilios.utilities.htmlEntities("&")).toBe("&amp;");
    });

    it("should encode \"", function () {
      expect(ilios.utilities.htmlEntities("\"")).toBe("&quot;");
    });

    it("should encode apostrophe", function () {
      expect(ilios.utilities.htmlEntities("'")).toBe("&#039;");
    });

    it("should encode HTML characters", function () {
      expect(ilios.utilities.htmlEntities("<p>Encode me!</p>")).toBe("&lt;p&gt;Encode me!&lt;/p&gt;");
    });

    it("should encode ©", function () {
      expect(ilios.utilities.htmlEntities("©")).toBe(("&copy;"));
    });

    it("should convert non-strings to strings", function () {
      expect(ilios.utilities.htmlEntities(5)).toBe("5");
    });
  });

  describe("createFormattedUserName()", function () {
    var firstName = "Jane";
    var middleName = "Quinn";
    var lastName = "Public";

    it("should return last name first if requested", function () {
      var formattedName = ilios.utilities.createFormattedUserName(firstName, middleName, lastName, ilios.utilities.USER_NAME_FORMAT_LAST_FIRST);
      expect(formattedName).toBe("Public, Jane Quinn");
    });

    it("should have correct spacing if middle name is empty string for last name first format", function () {
      var formattedName = ilios.utilities.createFormattedUserName(firstName, "", lastName, ilios.utilities.USER_NAME_FORMAT_LAST_FIRST);
      expect(formattedName).toBe("Public, Jane");
    });

    it("should have correct spacing if middle name is null for last name first format", function () {
      var formattedName = ilios.utilities.createFormattedUserName(firstName, null, lastName, ilios.utilities.USER_NAME_FORMAT_LAST_FIRST);
      expect(formattedName).toBe("Public, Jane");
    });

    it("should return first name first if requested", function () {
      var formattedName = ilios.utilities.createFormattedUserName(firstName, middleName, lastName, ilios.utilities.USER_NAME_FORMAT_FIRST_FIRST);
      expect(formattedName).toBe("Jane Quinn Public");
    });

    it("should have correct spacing if middle name is empty string for first name first format", function () {
      var formattedName = ilios.utilities.createFormattedUserName(firstName, "", lastName, ilios.utilities.USER_NAME_FORMAT_FIRST_FIRST);
      expect(formattedName).toBe("Jane Public");
    });

    it("should have correct spacing if middle name is null for first name first format", function () {
      var formattedName = ilios.utilities.createFormattedUserName(firstName, null, lastName, ilios.utilities.USER_NAME_FORMAT_FIRST_FIRST);
      expect(formattedName).toBe("Jane Public");
    });

    it("should return first initial first (omitting middle name) if requested", function () {
      var formattedName = ilios.utilities.createFormattedUserName(firstName, middleName, lastName, ilios.utilities.USER_NAME_FORMAT_FIRST_INITIAL_FIRST);
      expect(formattedName).toBe("J. Public");
    });
  });

  describe("getDomainFromURL()", function () {
    it("should return the url in full if it does not contain //", function () {
      expect(ilios.utilities.getDomainFromURL("abcdefg")).toBe("abcdefg");
    });

    it("should return everything after the // if there is no / after that", function () {
      expect(ilios.utilities.getDomainFromURL("abcd//efg")).toBe("efg");
    });

    it("should return everything after the // and before the next /", function () {
      expect(ilios.utilities.getDomainFromURL("abcd//efgh/ijkl")).toBe("efgh");
    });
  });

  describe("getTimeRangeString()", function () {
    it("should return the times as HH:MM separated by \" - \"", function () {
      var beginning = new Date("May 1 9:05");
      var end = new Date("May 1 10:10");
      expect(ilios.utilities.getTimeRangeString(beginning, end)).toBe("09:05 - 10:10");
    });
  });

  describe("paddedTimeValue()", function () {
    it("should prepend any value less than 10 with a 0", function () {
      expect(ilios.utilities.paddedTimeValue(0)).toBe("00");
    });

    it("should return any value of 10 or greater as is", function () {
      expect(ilios.utilities.paddedTimeValue(59)).toBe("59");
    });

    it("should work for string arguments", function () {
      expect(ilios.utilities.paddedTimeValue("10")).toBe("10");
    });
  });
});
