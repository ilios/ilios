/*
 * Base script of the Ilios JavaScript library.
 *
 * Contains the foundation of the library, but also a variety of seemingly arbitrary (yet commonly used) functionality.
 *
 * Defines the following namespaces:
 *
 * ilios.alert
 * ilios.global
 *
 * Dependencies:
 *
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
 * The page's preferences model.
 * Should be instantiated by calling <code>ilios.global.installPreferencesModel()</code>.
 * @property {PreferencesModel} preferencesModel
 */
ilios.global.preferencesModel = null;

/**
 * Instantiates <code>ilios.global.preferencesModel</code>.
 * @method installPreferencesModel
 */
ilios.global.installPreferencesModel = function () {
    ilios.global.preferencesModel = new PreferencesModel();

    var domData = document.getElementById("iliosPreferencesModel");
    if (domData) {
        try {
            ilios.global.preferencesModel.updateWithServerDispatchedObject(JSON.parse(domData.innerHTML));
        }  catch (e) {
            // SOL
            ilios.global.defaultAJAXFailureHandler(null, e);
        }
    }
};

/**
 * Instantiates and starts the idle timer, subscribes a timeout-handler function to it.
 * @method startIdleTimer
 * @param {Number} Timeout period in milliseconds
 * @param {String} logoutUrl logout action URL to redirect to on timeout
 */
ilios.global.startIdleTimer = function (timeout, logoutUrl) {
    var idleTimer = YAHOO.util.IdleTimer;
    timeout = YAHOO.lang.isNumber(timeout) ? timeout : 2700000; // default to 45 mins
    idleTimer.subscribe("idle", function () {
        if (! YAHOO.util.IdleTimer.isIdle()) {
            return;
        }
        ilios.alert.alert(
            ilios_i18nVendor.getI18NString('general.notification.idle_timeout_message'),
            ilios_i18nVendor.getI18NString('general.terms.ok'),
            function () { window.location.href = logoutUrl; }
        );
    });
    idleTimer.start(timeout, document);
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
 * Full names of week days (e.g. "Sunday", ..., "Saturday")
 * @property {Array} longDayOfWeekI18NStrings
 */
ilios.global.longDayOfWeekI18NStrings = null;

/**
 * Abbreviated names of week days (e.g. "Sun", ..., "Sat")
 * @property {Array} shortDayOfWeekI18NStrings
 */
ilios.global.shortDayOfWeekI18NStrings = null;

/**
 * Returns the I18Ned name of a given week day.
 * @method getI18NStringForDayOfWeek
 * @param {Number} day of the week index. Sunday = 0, ... , Saturday = 6
 * @param {Boolean} shortString TRUE to return an abbreviated name, FALSE for full name
 * @return {String} the name of the week day.
 */
ilios.global.getI18NStringForDayOfWeek = function (dayOfWeek, shortString) {
    var dayArray = null;

    if ((dayOfWeek < 0) || (dayOfWeek > 6)) { // boundary check
        return '';
    }

    if (shortString) { // full
        if (ilios.global.shortDayOfWeekI18NStrings == null) { // lazy load
            ilios.global.shortDayOfWeekI18NStrings = [
                ilios_i18nVendor.getI18NString('general.calendar.sunday_short'),
                ilios_i18nVendor.getI18NString('general.calendar.monday_short'),
                ilios_i18nVendor.getI18NString('general.calendar.tuesday_short'),
                ilios_i18nVendor.getI18NString('general.calendar.wednesday_short'),
                ilios_i18nVendor.getI18NString('general.calendar.thursday_short'),
                ilios_i18nVendor.getI18NString('general.calendar.friday_short'),
                ilios_i18nVendor.getI18NString('general.calendar.saturday_short')
            ];
        }
        dayArray = ilios.global.shortDayOfWeekI18NStrings;
    } else { // abbrev
        if (ilios.global.longDayOfWeekI18NStrings == null) { // lazy load
            ilios.global.longDayOfWeekI18NStrings = [
                ilios_i18nVendor.getI18NString('general.calendar.sunday_long'),
                ilios_i18nVendor.getI18NString('general.calendar.monday_long'),
                ilios_i18nVendor.getI18NString('general.calendar.tuesday_long'),
                ilios_i18nVendor.getI18NString('general.calendar.wednesday_long'),
                ilios_i18nVendor.getI18NString('general.calendar.thursday_long'),
                ilios_i18nVendor.getI18NString('general.calendar.friday_long'),
                ilios_i18nVendor.getI18NString('general.calendar.saturday_long')
            ];
        }
        dayArray = ilios.global.longDayOfWeekI18NStrings;
    }
    return dayArray[dayOfWeek]; // return the weekday
};
