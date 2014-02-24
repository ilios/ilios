/*
 * Base script of the Ilios JavaScript library.
 *
 * Contains the foundation of the library, but also a variety of seemingly arbitrary (yet commonly used) functionality.
 *
 * Defines the following namespaces:
 *
 * ilios.global
 *
 * Dependencies:
 *
 * ilios.alert
 * Ilios i18n utility
 * YUI utilities
 * YUI containers
 * YUI IdleTimer
 *
 * @todo move YUI custom configuration into separate file
 * @todo modularize related code
 */

/**
 * The ilios global namespace object.
 * @class ilios
 * @static
 */
var ilios = {};

/**
 * Creates and returns the specified namespace within the ilios namespace.
 * Example:
 * <pre>
 * ilios.namespace("ui.widget");
 * </pre>
 * This will create the "ilios.ui" namespace object, and then create and return
 * the "ilios.ui.widget" namespace object
 * @method namespace
 * @static
 * @param {String} namespace the namespaces to create, delimited by "."
 * @return {Object} a reference to the last namespace object created
 */
ilios.namespace = function (namespace) {
    var token = namespace.split(".");
    var o = ilios;
    var i, n;

    for (i = 0, n = token.length; i < n; i++) {
        if (0 === i && token[0] === "ilios") {
            // "ilios" namespace is implied as base
        } else {
            o[token[i]] = o[token[i]] || {};
            o = o[token[i]];
        }
    }
    return o;
};

// use JavaScript JSON parse/stringify implementation
// since the browser-native implementations choke on our server-side generated JSON
// in some cases.
// @see Redmine issue #869
// @link http://developer.yahoo.com/yui/json/#native
if (YAHOO.lang.JSON) {
    YAHOO.lang.JSON.useNativeStringify = false;
    YAHOO.lang.JSON.useNativeParse = false;
}

/**
 * All kinds of crazy went into this object.
 * @class ilios.global
 * @todo clean up this mess.
 */
ilios.namespace('global');

/**
 * Reads JSON data in element with id and returns an object
 * @method readJsonFromDom
 * @param {String} id
 * @return {Object}
 */
ilios.global.readJsonFromDom = function (id) {
    var el = document.getElementById(id);
    var data = null;

    if (el) {
        try {
            data = JSON.parse(el.innerHTML);
        } catch (e) {
            ilios.global.defaultAJAXFailureHandler(null, e);
        }
    }
    return data;
};

/**
 * Default handler function for failed XHR calls.
 * Pops up an dialog displaying the given error message and exception.
 * @method defaultAJAXFailureHandler
 * @param {Object} resultObject the response object
 * @param {Exception} rootException a thrown exception
 */
ilios.global.defaultAJAXFailureHandler = function (resultObject, rootException) {
    var cause = (typeof rootException == 'undefined') ? resultObject.responseText : rootException.description;
    ilios.alert.alert(ilios_i18nVendor.getI18NString('general.error.fatal')  + " (" + cause + ")");
};

/**
 * Returns the I18Ned name of a given week day.
 * @method getI18NStringForDayOfWeek
 * @param {Number} day of the week index. Sunday = 0, ... , Saturday = 6
 * @param {Boolean} shortString TRUE to return an abbreviated name, FALSE for full name
 * @return {String} the name of the week day.
 */
ilios.global.getI18NStringForDayOfWeek = function (dayOfWeek) {

    var stringIdentifiers = [
        'general.calendar.sunday_long',
        'general.calendar.monday_long',
        'general.calendar.tuesday_long',
        'general.calendar.wednesday_long',
        'general.calendar.thursday_long',
        'general.calendar.friday_long',
        'general.calendar.saturday_long'
    ];

    return ilios_i18nVendor.getI18NString(stringIdentifiers[dayOfWeek]) || '';
};
