/**
 * This script defines a plethora of utility functions.
 * Defines the following namespaces
 *
 * ilios.utilities
 *
 * Dependencies:
 *
 * YUI utilities
 * scripts/ilios_base.js
 * scripts/ilios_dom.js
 */

/**
 * Namespace object to hang utility functions onto.
 * @class utilities
 * @static
 */
ilios.namespace('utilities');


ilios.utilities.USER_NAME_FORMAT_LAST_FIRST = 0;
ilios.utilities.USER_NAME_FORMAT_FIRST_FIRST = 1;
ilios.utilities.USER_NAME_FORMAT_FIRST_INITIAL_FIRST = 2;

ilios.utilities.setToolTipForElement = function (element, toolTipText) {
    element.setAttribute('title', toolTipText);
};

ilios.utilities.getEventTarget = function (e) {
    var event = (e || window.event); // window.event is for IE8 support

    return (event.target || event.srcElement);
};

/*
 * The response from a file upload is "<pre>useful information we want</pre>" -- this
 *  method digs out the good stuff, de-json's it, and hands the object back.
 */
ilios.utilities.getParsedResponseObjectFromFormUploadResponseText = function (responseText) {
    var whatWeWant = responseText;
    var rhett = null;

    if (ilios.lang.startsWith(whatWeWant, '<pre>') || ilios.lang.startsWith(whatWeWant, '<PRE>')) {
        whatWeWant = whatWeWant.substring(5);

        if (ilios.lang.endsWith(whatWeWant, '</pre>') || ilios.lang.endsWith(whatWeWant, '</PRE>')) {
            whatWeWant = whatWeWant.substring(0, (whatWeWant.length - 6));
        }
    }

    try {
        rhett = YAHOO.lang.JSON.parse(whatWeWant);
    } catch (e) {
        ilios.global.defaultAJAXFailureHandler(null, e);
        return null;
    }

    return rhett;
};

ilios.utilities.dateObjectToMySQLFriendly = function (dateObject, includeTime) {
    return dateObject.getUTCFullYear()
                + '-' + ilios.utilities.paddedTimeValue(dateObject.getUTCMonth() + 1)
                + '-' + ilios.utilities.paddedTimeValue(dateObject.getUTCDate())
                + (includeTime
                        ? (' ' + ilios.utilities.paddedTimeValue(dateObject.getUTCHours())
                                + ':' + ilios.utilities.paddedTimeValue(dateObject.getUTCMinutes())
                                + ':' + ilios.utilities.paddedTimeValue(dateObject.getUTCSeconds()))
                        : '');
};

ilios.utilities.mySQLTimelessDateToDateObject = function (yyyyMMDDStr) {
    var dateStr = yyyyMMDDStr + " 00:00:00";

    return ilios.utilities.mySQLDateToDateObject(dateStr, false);
};

/**
 * @param mySQLDateString must be in the usual format of 2010-06-28 15:26:02
 * @param valueIsUTC if true, the hours / minutes / seconds represent UTC values
 */
ilios.utilities.mySQLDateToDateObject = function (mySQLDateString, valueIsUTC) {
    // regex demands a space after the date string even if the optional time string is not there. Bug?
    // regex permits two-digit date but then sets it to the literal date, so 10 => 10 A.D., not 2010. Bug?
    var regex
           = /^([0-9]{2,4})-([0-1][0-9])-([0-3][0-9]) (?:([0-2][0-9]):([0-5][0-9]):([0-5][0-9]))?$/;
    var parts = mySQLDateString.replace(regex, "$1 $2 $3 $4 $5 $6").split(' ');
    var rhett = new Date();

    rhett.setFullYear(parts[0], (parts[1] - 1), parts[2]);

    if (valueIsUTC) {
        var utcHoursOffset = Math.floor(rhett.getTimezoneOffset() / 60);
        var utcMinutesOffset = rhett.getTimezoneOffset() - (utcHoursOffset * 60);

        rhett.setHours((parts[3] - utcHoursOffset), (parts[4] - utcMinutesOffset), parts[5], 0);
    }
    else {
        rhett.setHours(parts[3], parts[4], parts[5], 0);
    }

    return rhett;
};

/**
 * Generates a CSS class name for a given mime-type.
 * @method ilios.utilities.convertMimeTypeToCSSClassName
 * @param {String} mimeType
 * @return {String} the CSS class name
 */
ilios.utilities.convertMimeTypeToCSSClassName = function (mimeType) {
    var rhett;
    mimeType = YAHOO.lang.isString(mimeType) ? mimeType : '';
    rhett = mimeType.replace(new RegExp('/', 'g'), '--');
    return rhett.replace(new RegExp('\\.', 'g'), "__");
};

ilios.utilities.removeAllChildren = function (container) {
    if (container.hasChildNodes()) {
        while (container.childNodes.length >= 1) {
            container.removeChild(container.firstChild);
        }
    }
};

ilios.utilities.modelItemNeedsPublishing = function (model) {
    return ((model.getPublishEventId() == null)
                    || (model.getPublishEventId() < 1)
                    || model.isModelDirty());
};

ilios.utilities.canPublishModelItem = function (model) {
    if (ilios.utilities.modelItemNeedsPublishing(model)) {
        return (model.getPublishability()
                                    != model.CANNOT_BE_PUBLISHED);
    }

    return false;
};

/**
 * Appends a clearing <div> to a given container element.
 * @method appendClearingDivToContainer
 * @param {YAHOO.util.Element || HTMLElement} container the container-element
 * @static
 */
ilios.utilities.appendClearingDivToContainer = function (container) {
    var Dom = YAHOO.util.Dom;
    var el = document.createElement('div');
    Dom.addClass(el, 'clear');
    container.appendChild(el);
};

ilios.utilities.toggleShowMoreOrLess = function (containerIdToToggle, toggleLinkElement) {
    var container = new YAHOO.util.Element(document.getElementById(containerIdToToggle));
    var str;

    if (container.getStyle('display') == 'none') {
        str = ilios_i18nVendor.getI18NString('general.phrases.show_less').toLowerCase();

        container.setStyle('display', 'block');
    }
    else {
        str = ilios_i18nVendor.getI18NString('general.phrases.show_more').toLowerCase();

        container.setStyle('display', 'none');
    }

    if (toggleLinkElement) {
        toggleLinkElement.innerHTML = str;
    }
};

/**
 * Container-support function. Toggles the visibility of a given element and a given toggle-button.
 *
 * @method toggle
 * @param {String} containerIdToToggle The ID of the element that is has its display toggled.
 * @param {String|HTMLElement} toggleLinkElement The toggle button (or the id of that element).
 */
ilios.utilities.toggle = function (containerIdToToggle, toggleLinkElement) {
    var container = new YAHOO.util.Element(document.getElementById(containerIdToToggle));
    var icon = YAHOO.util.Dom.getFirstChild(toggleLinkElement);
    var newClass;

    if (container.getStyle('display') == 'none') {
        newClass = 'icon-minus';
        container.setStyle('display', 'block');
    }
    else {
        newClass = 'icon-plus';
        container.setStyle('display', 'none');
    }

    if (toggleLinkElement) {
        YAHOO.util.Dom.removeClass(icon, 'icon-minus');
        YAHOO.util.Dom.removeClass(icon, 'icon-plus');
        YAHOO.util.Dom.addClass(icon, newClass);
    }
};


/**
 * Sets the option element in a given SELECT input field as "selected" that matches a given value.
 * If the value is NULL, or value is not in the select options array, the first option is selected.
 * @method selectOptionWithValue
 * @param HTMLSelectElement selectElement The SELECT input element.
 * @param mixed optionValue The value to match.
 * @return boolean TRUE if a match was found, otherwise FALSE.
 */
ilios.utilities.selectOptionWithValue = function (selectElement, optionValue) {
    var i, n;

    for (i = 0, n = selectElement.options.length; i < n; i++) {
        if ((selectElement.options[i].value == optionValue) || (optionValue == null)) {
            selectElement.selectedIndex = i;
            return true;
        }
    }
    if (n) {
        selectElement.selectedIndex = 0;
    }
    return false;
};

/**
 * Creates and returns a XHR object.
 * @returns {XMLHttpRequest | null}
 */
ilios.utilities.getXMLHttpRequest = function () {
    var xmlHTTPRequest = null;

    if (window.XMLHttpRequest) {
        xmlHTTPRequest = new XMLHttpRequest();

        if (xmlHTTPRequest.overrideMimeType) {
            xmlHTTPRequest.overrideMimeType('text/xml');
        }
    } else {
        ilios.alert.alert('It appears that your browser does not support XMLHTTPRequests. ...?');
    }
    return xmlHTTPRequest;
};

// move to indexOf usage -- TODO (use YAHOO.Array.indexOf for IE8 support, otherwise use native Array.indexOf)
ilios.utilities.arrayContains = function (arr, value) {
    var size = ilios.utilities.objectPropertyCount(arr);

    for (var i = 0; i < size; i++) {
        if (value == arr[i]) {
            return true;
        }
    }

    return false;
};

/**
 * Removes duplicates from a given array of strings or numbers.
 * ACHTUNG! this will not work for arrays containing non-spatial elements, such as objects.
 * @method makeUniqueArray
 * @param {Array} a an array of strings or numbers.
 * @return {Array} the de-duped array.
 * @static
 * @todo Replace with YAHOO.Array.unique() or YAHOO.Array.dedupe()
 */
ilios.utilities.makeUniqueArray = function (a) {
    var map = {};
    var rhett = [];
    var i, n;

    for (i = 0, n = a.length; i < n; i++) {
        if (map[a[i]]) {
            continue;
        }
        map[a[i]] = true;
        rhett.push(a[i]);
    }
    return rhett;
};

ilios.utilities.simplyArrayEquality = function (arr1, arr2) {
    var size = ilios.utilities.objectPropertyCount(arr1);

    if (size != ilios.utilities.objectPropertyCount(arr2)) {
        return false;
    }

    // Copy arrays so we don't mutate the originals
    var a = arr1.concat();
    var b = arr2.concat();

    // Sort the arrays because we want [1,2,3] to be treated as equal to [3,2,1]
    // Or at least that's what I got from the original algorithm.
    a.sort();
    b.sort();

    for (var i = 0; i < size; i++) {
        // Using not-equal (!=) instead of strict not-equal (!==) because original algorithm used that.
        // Existing code might depend on that. I haven't looked.
        if (a[i] != b[i]) {
            return false;
        }
    }

    return true;
};

ilios.utilities.objectEquality = function (obj1, obj2) {
    var element = null;

    if (ilios.utilities.objectPropertyCount(obj1) != ilios.utilities.objectPropertyCount(obj2)) {
        return false;
    }

    for (var key in obj1) {
        element = obj1[key];

        if ((obj2[key] == null) || (obj2[key] != element)) {
            return false;
        }
    }

    return true;
};

ilios.utilities.objectPropertyCount = function (arr) {
    var rhett = 0;

    for (var key in arr) {
        rhett++;
    }

    return rhett;
};

ilios.utilities.removeElementWithValue = function (anArray, value) {
    var index = -1;
    var len = anArray.length;

    for (var i = 0; i < len; i++) {
        if (anArray[i] == value) {
            index = i;

            break;
        }
    }

    if (index != -1) {
        anArray.splice(index, 1);
    }
};

/**
 * Written because slice() is dysfunctional with associative arrays.
 *
 * @return a shallow copy of the associative array (the returned array is its own unique
 *              Array instance, but the objects it contains are the same instances as in
 *              the original array). If originalArray is null, null is returned.
 * ACHTUNG:
 *   going forward, DO NOT USE THIS FUNCTION!
 *   There are no associative arrays in JavaScript, use objects instead.
 *   [ST 2012/07/14]
 * @todo find and replace all function calls to this nonsense
 */
ilios.utilities.cloneAssociativeArray = function (originalArray) {
    var rhett = null;

    if (originalArray != null) {
        rhett = [];

        for (var key in originalArray) {
            rhett[key] = originalArray[key];

            rhett.length++;
        }
    }

    return rhett;
};

/**
 * Written because slice() is dysfunctional with associative arrays.
 *
 * @return a deep copy of the associative array (the returned array is its own unique
 *              Array instance, and the objects in the array have clone() called on them -
 *              so they must implement that). If originalArray is null, null is returned.
 * ACHTUNG:
 *   going forward, DO NOT USE THIS FUNCTION!
 *   There are no associative arrays in JavaScript, use objects instead.
 *   [ST 2012/07/14]
 * @todo find and replace all function calls to this nonsense
 */
ilios.utilities.deepCloneAssociativeArray = function (originalArray) {
    var rhett = null;

    if (originalArray != null) {
        rhett = [];

        for (var key in originalArray) {
            rhett[key] = originalArray[key].clone();

            rhett.length++;
        }
    }

    return rhett;
};

ilios.utilities.arrayHasElementsMissingInArray = function (referenceArray, possiblyAlteredArray) {
    var object = null;
    var found = false;

    for (var key in referenceArray) {
        object = referenceArray[key];

        found = false;
        for (var key2 in possiblyAlteredArray) {
            if (object == possiblyAlteredArray[key2]) {
                found = true;

                break;
            }
        }

        if (! found) {
            return true;
        }
    }

    return false;
};

ilios.utilities.titledNaturalComparator = function (a, b) {
    return a.getTitle().localeCompare(b.getTitle());
};

/**
 * Wrapper around <code>YAHOO.lang.JSON.stringify()</code>.
 * Converts a given object by assigning object properties as array elements,
 * before stringifying that array.
 *
 * @param {Object} o any arbitrary object to convert to JSON string
 * @param  {Array|Function} w whitelist of acceptable object keys
 *     to include OR a function(value,key) to alter values
 *     before serialization (optional)
 * @param {Number|String} s indentation character(s) or
 *     depthy of spaces to format the output (optional)
 * @return {String} JSON string representation of the input
 * @static
 */
ilios.utilities.stringifyObjectAsArray = function (o, w, s) {
    var a = [],
        stringify = YAHOO.lang.JSON.stringify,
        rhett;
    for (var key in o) {
        a.push(o[key]);
    }
    try {
        rhett = stringify(a, w, s);
    } catch (e) {
        console.error(e);
        throw e;
    }
    return rhett;
};

ilios.utilities.isNumber = function (str) {
    return ((! isNaN(parseFloat(str))) && isFinite(str));
};

/**
 * Given an array (arr) of objects which each have the method 'getTitle()' or attribute 'title',
 *  generate a delimited (plus a space after the delimiter) string of all of those titles
 *  elements' titles.
 */
ilios.utilities.delimitedStringOfTitledObjects = function (arr, delimiter) {
    var rhett = '';

    for (var key in arr) {
        if (rhett.length > 0) {
            rhett += delimiter + ' ';
        }

        if (! arr[key].getTitle) {
            rhett += arr[key].title;
        }
        else {
            rhett += arr[key].getTitle();
        }
    }

    return rhett;
};

/*
 * Assuming that the LI elements contained within the listElement obey the Ilios-standard of
 *  having an attribute 'iliosModel' being some sort of the Ilios javascript model class that
 *  implements compareTo (and will perform it on the candidateModel without throwing an error).
 */
ilios.utilities.searchListElementForModel = function (listElement, candidateModel) {
    var liElements = listElement.childNodes;
    var len = (liElements != null) ? liElements.length : -1;
    var i = 0;
    var liModel = null;

    for (; i < len; i++) {
        liModel = liElements[i].iliosModel;

        if (liModel != null) {
            if (liModel.compareTo(candidateModel) == 0) {
                return true;
            }
        }
    }

    return false;
};

ilios.utilities.percentUnicodeToHTML = function (str) {
    return str.replace(/\%u/g, "&#x");
};

ilios.utilities.htmlEntitiesDecode = function (str) {
    var scratchTextArea = document.createElement("textArea");
    scratchTextArea.innerHTML = str.replace(/</g,"&lt;").replace(/>/g,"&gt;");

    return scratchTextArea.value;
};

/**
 * @todo get rid of this.
 */
ilios.utilities.htmlEntities = function (string) {
    var entities = [
        {decimal: 38, entity: '&amp;'},
        {decimal: 160, entity: '&nbsp;'},
        {decimal: 161, entity: '&iexcl;'},
        {decimal: 162, entity: '&cent;'},
        {decimal: 163, entity: '&pound;'},
        {decimal: 164, entity: '&curren;'},
        {decimal: 165, entity: '&yen;'},
        {decimal: 166, entity: '&brvbar;'},
        {decimal: 167, entity: '&sect;'},
        {decimal: 168, entity: '&uml;'},
        {decimal: 169, entity: '&copy;'},
        {decimal: 170, entity: '&ordf;'},
        {decimal: 171, entity: '&laquo;'},
        {decimal: 172, entity: '&not;'},
        {decimal: 173, entity: '&shy;'},
        {decimal: 174, entity: '&reg;'},
        {decimal: 175, entity: '&macr;'},
        {decimal: 176, entity: '&deg;'},
        {decimal: 177, entity: '&plusmn;'},
        {decimal: 178, entity: '&sup2;'},
        {decimal: 179, entity: '&sup3;'},
        {decimal: 180, entity: '&acute;'},
        {decimal: 181, entity: '&micro;'},
        {decimal: 182, entity: '&para;'},
        {decimal: 183, entity: '&middot;'},
        {decimal: 184, entity: '&cedil;'},
        {decimal: 185, entity: '&sup1;'},
        {decimal: 186, entity: '&ordm;'},
        {decimal: 187, entity: '&raquo;'},
        {decimal: 188, entity: '&frac14;'},
        {decimal: 189, entity: '&frac12;'},
        {decimal: 190, entity: '&frac34;'},
        {decimal: 191, entity: '&iquest;'},
        {decimal: 192, entity: '&Agrave;'},
        {decimal: 193, entity: '&Aacute;'},
        {decimal: 194, entity: '&Acirc;'},
        {decimal: 195, entity: '&Atilde;'},
        {decimal: 196, entity: '&Auml;'},
        {decimal: 197, entity: '&Aring;'},
        {decimal: 198, entity: '&AElig;'},
        {decimal: 199, entity: '&Ccedil;'},
        {decimal: 200, entity: '&Egrave;'},
        {decimal: 201, entity: '&Eacute;'},
        {decimal: 202, entity: '&Ecirc;'},
        {decimal: 203, entity: '&Euml;'},
        {decimal: 204, entity: '&Igrave;'},
        {decimal: 205, entity: '&Iacute;'},
        {decimal: 206, entity: '&Icirc;'},
        {decimal: 207, entity: '&Iuml;'},
        {decimal: 208, entity: '&ETH;'},
        {decimal: 209, entity: '&Ntilde;'},
        {decimal: 210, entity: '&Ograve;'},
        {decimal: 211, entity: '&Oacute;'},
        {decimal: 212, entity: '&Ocirc;'},
        {decimal: 213, entity: '&Otilde;'},
        {decimal: 214, entity: '&Ouml;'},
        {decimal: 215, entity: '&times;'},
        {decimal: 216, entity: '&Oslash;'},
        {decimal: 217, entity: '&Ugrave;'},
        {decimal: 218, entity: '&Uacute;'},
        {decimal: 219, entity: '&Ucirc;'},
        {decimal: 220, entity: '&Uuml;'},
        {decimal: 221, entity: '&Yacute;'},
        {decimal: 222, entity: '&THORN;'},
        {decimal: 223, entity: '&szlig;'},
        {decimal: 224, entity: '&agrave;'},
        {decimal: 225, entity: '&aacute;'},
        {decimal: 226, entity: '&acirc;'},
        {decimal: 227, entity: '&atilde;'},
        {decimal: 228, entity: '&auml;'},
        {decimal: 229, entity: '&aring;'},
        {decimal: 230, entity: '&aelig;'},
        {decimal: 231, entity: '&ccedil;'},
        {decimal: 232, entity: '&egrave;'},
        {decimal: 233, entity: '&eacute;'},
        {decimal: 234, entity: '&ecirc;'},
        {decimal: 235, entity: '&euml;'},
        {decimal: 236, entity: '&igrave;'},
        {decimal: 237, entity: '&iacute;'},
        {decimal: 238, entity: '&icirc;'},
        {decimal: 239, entity: '&iuml;'},
        {decimal: 240, entity: '&eth;'},
        {decimal: 241, entity: '&ntilde;'},
        {decimal: 242, entity: '&ograve;'},
        {decimal: 243, entity: '&oacute;'},
        {decimal: 244, entity: '&ocirc;'},
        {decimal: 245, entity: '&otilde;'},
        {decimal: 246, entity: '&ouml;'},
        {decimal: 247, entity: '&divide;'},
        {decimal: 248, entity: '&oslash;'},
        {decimal: 249, entity: '&ugrave;'},
        {decimal: 250, entity: '&uacute;'},
        {decimal: 251, entity: '&ucirc;'},
        {decimal: 252, entity: '&uuml;'},
        {decimal: 253, entity: '&yacute;'},
        {decimal: 254, entity: '&thorn;'},
        {decimal: 255, entity: '&yuml;'},
        {decimal: 34, entity:'&quot;'},
        {decimal: 60, entity:'&lt;'},
        {decimal: 62, entity:'&gt;'},
        {decimal: 39, entity:'&#039;'}
    ];
    var symbol;
    var entity;
    var tmp_str = string.toString();
    var decimal;

    for (var i=0, l=entities.length; i<l; i++) {
        entity = entities[i].entity;
        symbol = String.fromCharCode(entities[i].decimal);
        tmp_str = tmp_str.split(symbol).join(entity);
    }

    return tmp_str;
};

/**
 * Formats a given first, middle and last name according to a given format.
 * @method {createFormattedUserName}
 * @static
 * @param {String} firstName the first name
 * @param {String} middleName the middle name
 * @param {String} lastName the last name
 * @param {Number} userFormatType the format id, either one of
 *     ilios.utilities.USER_NAME_FORMAT_LAST_FIRST (Format: "<last name>, <first name> <middle name>")
 *     ilios.utilities.USER_NAME_FORMAT_FIRST_FIRST (Format: "<first name> <middle name> <last name>"")
 *     ilios.utilities.USER_NAME_FORMAT_FIRST_INITIAL_FIRST (Format: "<inital of first name>. <last name>")
 * @return {String} the formatted name, or an empty string if no/invalid format was given
 * @todo provide a default format
 */
ilios.utilities.createFormattedUserName = function (firstName, middleName, lastName, userNameFormatType) {
    var rhett = '';
    if (ilios.utilities.USER_NAME_FORMAT_LAST_FIRST == userNameFormatType) {
        rhett = lastName + ', ' + firstName;
        if (middleName) {
            rhett += ' ' + middleName;
        }
    } else if (ilios.utilities.USER_NAME_FORMAT_FIRST_FIRST == userNameFormatType) {
        rhett = firstName;
        if (middleName) {
            rhett += ' ' + middleName;
        }
        rhett += ' ' + lastName;
    } else if (ilios.utilities.USER_NAME_FORMAT_FIRST_INITIAL_FIRST == userNameFormatType) {
        rhett = firstName.substr(0, 1) + '. ' + lastName;
    }
    return rhett;
};

ilios.utilities.getDomainFromURL = function (url) {
    var index = url.indexOf('//');

    if (index != -1) {
        var substring = url.substring((index + 2));

        index = substring.indexOf('/');

        if (index != -1) {
            return substring.substring(0, index);
        }

        return substring;
    }

    return url;
};

ilios.utilities.getTimeRangeString = function (beginning, end) {
    return ilios.utilities.paddedTimeValue(beginning.getHours())
                + ':' + ilios.utilities.paddedTimeValue(beginning.getMinutes())
                + ' - ' + ilios.utilities.paddedTimeValue(end.getHours())
                + ':' + ilios.utilities.paddedTimeValue(end.getMinutes());
};

// Prepends a 0 if the value is a single digit
ilios.utilities.paddedTimeValue = function (value) {
    var intVal = parseInt(value, 10);

    if (intVal < 10) {
        return '0' + intVal;
    }

    return value.toString();
};

/**
 * Replacer-function that can be passed to <code>YUI.lang.JSON.stringify()</code>
 *
 * It replaces any given value with NULL when the given key is "stateChangeListenerArguments"
 * during stringification.
 *
 * The idea is to prevent exceptions from being raised due to circular references
 * when stringifying Ilios model objects by omitting state change listener arguments.
 *
 * See Redmine Ticket #926
 *
 * @method yahooJSONStringifyStateChangeListenerArgumentsReplacer
 * @param k {String} the property key
 * @param v {MIXED} the property value
 * @return {MIXED|null} the given value, or NULL on key match.
 * @link http://developer.yahoo.com/yui/json/
 * @static
 * @public
 */
ilios.utilities.yahooJSONStringifyStateChangeListenerArgumentsReplacer = function (k, v) {
    if ('stateChangeListenerArguments' == k) {
        return null;
    }
    return v;
};

/**
* simple utility to scroll the window to a specific element - not smooth, but functional
*/
ilios.utilities.scrollElementIntoView = function(element){
    if (element && typeof element.scrollIntoView === "function") {
        element.scrollIntoView();
    }
};

ilios.utilities.hide = function(elem) {
    YAHOO.util.Dom.setStyle(elem, "display", "none");
};

ilios.utilities.show = function(elem) {
    YAHOO.util.Dom.setStyle(elem, "display", "");
};

//
// password checker
//

// "constants"
ilios.utilities.MIN_PASSWORD_LENGTH = 8;
ilios.utilities.MAX_PASSWORD_LENGTH = 12;

// password-strength-check bitmask values
ilios.utilities.PASSWORD_STRENGTH_CHECK_OK = 0;
ilios.utilities.PASSWORD_STRENGTH_CHECK_TOO_SHORT = 1;
ilios.utilities.PASSWORD_STRENGTH_CHECK_TOO_LONG = 2;
ilios.utilities.PASSWORD_STRENGTH_CHECK_INVALID_CHARS = 4;
ilios.utilities.PASSWORD_STRENGTH_CHECK_DIGIT_MISSING = 8;
ilios.utilities.PASSWORD_STRENGTH_CHECK_LOWERCASE_CHAR_MISSING = 16;
ilios.utilities.PASSWORD_STRENGTH_CHECK_UPPERCASE_CHAR_MISSING = 32;
ilios.utilities.PASSWORD_STRENGTH_CHECK_SPECIAL_CHAR_MISSING = 64;

/**
 * Checks the strength of a given password.
 * @method checkPasswordStrength
 * @param {String} password
 * @return {Number} 0 on success, or a bitmap composed of the various ilios.utilities.PASSWORD_STRENGTH_CHECK_* values on failure
 */
ilios.utilities.checkPasswordStrength = function (pwd) {
    var rhett = ilios.utilities.PASSWORD_STRENGTH_CHECK_OK;
    var len = pwd.length;

    if (ilios.utilities.MIN_PASSWORD_LENGTH > len) {
        rhett = rhett ^ ilios.utilities.PASSWORD_STRENGTH_CHECK_TOO_SHORT;
    } else if (ilios.utilities.MAX_PASSWORD_LENGTH < len) {
        rhett = rhett ^ ilios.utilities.PASSWORD_STRENGTH_CHECK_TOO_LONG;
    }

    if (! pwd.match(/^[0-9a-zA-Z$*_-]+$/)) {
        rhett = rhett ^ ilios.utilities.PASSWORD_STRENGTH_CHECK_INVALID_CHARS;
    }

    if (! pwd.match(/[0-9]/)) {
        rhett = rhett ^ ilios.utilities.PASSWORD_STRENGTH_CHECK_DIGIT_MISSING;
    }

    if (! pwd.match(/[a-z]/)) {
        rhett = rhett ^ ilios.utilities.PASSWORD_STRENGTH_CHECK_LOWERCASE_CHAR_MISSING;
    }
    if (! pwd.match(/[A-Z]/)) {
        rhett = rhett ^ ilios.utilities.PASSWORD_STRENGTH_CHECK_UPPERCASE_CHAR_MISSING;
    }
    if (! pwd.match(/[$*_-]/)) {
        rhett = rhett ^ ilios.utilities.PASSWORD_STRENGTH_CHECK_SPECIAL_CHAR_MISSING;
    }
    return rhett;
};

/**
 * Returns a list of warning messages for a given password strength checker return value.
 * @param {Number} passwordStrength the bitmask value
 * @return {Array} an array with warning messages.
 */
ilios.utilities.getPasswordStrengthCheckWarnings = function (passwordStrength) {
    var msg = [];
    if (ilios.utilities.PASSWORD_STRENGTH_CHECK_TOO_SHORT & passwordStrength) {
        msg.push('The given password is too short.');
    }
    if (ilios.utilities.PASSWORD_STRENGTH_CHECK_TOO_LONG & passwordStrength) {
        msg.push('The given password is too long.');
    }
    if (ilios.utilities.PASSWORD_STRENGTH_CHECK_INVALID_CHARS & passwordStrength) {
        msg.push('The given password contains invalid characters.');
    }
    if (ilios.utilities.PASSWORD_STRENGTH_CHECK_DIGIT_MISSING & passwordStrength) {
        msg.push('The given password does not contain any digits.');
    }
    if (ilios.utilities.PASSWORD_STRENGTH_CHECK_LOWERCASE_CHAR_MISSING & passwordStrength) {
        msg.push('The given password does not contain any lower-case characters.');
    }
    if (ilios.utilities.PASSWORD_STRENGTH_CHECK_UPPERCASE_CHAR_MISSING & passwordStrength) {
        msg.push('The given password does not contain any upper-case characters.');
    }
    if (ilios.utilities.PASSWORD_STRENGTH_CHECK_SPECIAL_CHAR_MISSING & passwordStrength) {
        msg.push('The given password does not contain any special characters.');
    }
    return msg;
};
