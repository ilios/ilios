/*
 * Base script of the Ilios JavaScript library.
 *
 * Contains the foundation of the library, but also a variety of seemingly arbitrary (yet commonly used) functionality.
 *
 * Defines the following namespaces:
 *
 * ilios.alert
 * ilios.global
 * ilios.lang
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
 * Extensions to core JavaScript functionality, such as additional text processing methods.
 * @class ilios.lang
 * @static
 */
ilios.namespace("lang");

/**
 * Truncates a given text string after a given number of characters
 * and appends an ellipsis (...).
 * If the given text's length is less then the given number of characters
 * then no truncation is performed and no ellipsis is appended.
 * @method ellipsisedOfLength
 * @param {String} str text
 * @param {Number} length number of characters
 * @return {String} the ellipsified string
 */
ilios.lang.ellipsisedOfLength = function (str, length) {
    var rhett = str;
    if (str.length > length) {
        rhett = str.substring(0, length) + "...";
    }
    return rhett;
};

/**
 * Provides overrides for the window object's various dialog-functions.
 * @class ilios.alert
 * @static
 */
ilios.namespace('alert');

/**
 * Queues to-be-displayed network-activity messages.
 * @property {Array} networkActivityI18NStrings
 */
ilios.alert.networkActivityI18NStrings = [];

/**
 * Displays the first message in the <code>networkActivityI18NStrings</code> queue, if applicable.
 * @method updateServerInteractionProgress
 */
ilios.alert.updateServerInteractionProgress = function () {
    var Element = YAHOO.util.Element;
    var element;
    var i18nStr;

    if (0 < ilios.alert.networkActivityI18NStrings.length) { // message queued for display, show the first
        i18nStr = ilios_i18nVendor.getI18NString(ilios.alert.networkActivityI18NStrings[0]);

        element = document.getElementById('save_in_progress_text');
        if (element) {
            element.appendChild(document.createTextNode(i18nStr));
            element = document.getElementById('save_in_progress_div');
            (new Element(element)).setStyle('display', 'block');
        }
    } else { // no messages, hide the display container
        element = document.getElementById('save_in_progress_div');
        if (element) {
            (new Element(element)).setStyle('display', 'none');
        }
    }
};

/**
 * Default button-click event handler for the "inform" and "alert" dialogs.
 * Closes the dialog that is has been invoked on.
 * @method simpleHidingHandler
 */
ilios.alert.simpleHidingHandler = function () {
    this.hide();
};

/**
 * Override-function for the window object's alert function.
 * @method alert
 * @param {String} str the dialog's message
 * @param {String} acceptBtnText defaults to general.terms.ok in the i18n file if null
 * @param {Function} acceptHandler defaults to the ilios.alert.simpleHidingHandler function which just closes the dialog
 * @param {Object} acceptHandlerArgs an optional argument to pass along to the accept handler; this will
 *     be ignored if the acceptHandler param is null.
 */
ilios.alert.alert = function (str, acceptBtnText, acceptHandler, acceptHandlerArgs) {
    var lang = YAHOO.lang;
    var btnConfig = [];

    acceptHandler = lang.isFunction(acceptHandler) ? acceptHandler : ilios.alert.simpleHidingHandler;
    acceptBtnText = acceptBtnText || ilios_i18nVendor.getI18NString('general.terms.ok');
    acceptHandlerArgs = lang.isObject(acceptHandlerArgs) ? acceptHandlerArgs : {};
    btnConfig.push({
        text: acceptBtnText,
        handler: {
            fn: acceptHandler,
            obj: acceptHandlerArgs,
            scope: ilios.alert.confirmDialog
        },
        isDefault: true
    });

    if (! ilios.alert.confirmDialog) { // lazy load
        ilios.alert.confirmDialog = ilios.alert.createConfirmDialog();
        ilios.alert.confirmDialog.render(document.body);
    }
    // configure and display
    ilios.alert.confirmDialog.configButtons(null, [btnConfig], null);
    ilios.alert.confirmDialog.setBody(str);
    ilios.alert.confirmDialog.cfg.setProperty('icon', YAHOO.widget.SimpleDialog.ICON_WARN);
    ilios.alert.confirmDialog.cfg.queueProperty('zIndex', 9999);
    ilios.alert.confirmDialog.bringToTop();
    ilios.alert.confirmDialog.show();
};

/**
 * Override-function for the window object's inform() function.
 * @method inform
 * @param {String} str the dialog's message
 * @param {String} acceptBtnText defaults to general.terms.yes if null
 * @param {Function} acceptHandler defaults to the ilios.alert.simpleHidingHandler function which just closes the dialog
 * @param {Object} acceptHandlerArgs an optional argument to pass along to the accept handler; this will
 *     be ignored if the acceptHandler param is null.
 * @param {String} declineBtnText defaults to general.terms.no if null
 * @param {Function} declineHandler defaults to the ilios.alert.simpleHidingHandler function which just closes the dialog
 * @param {Object} declineHandlerArgs an optional argument to pass along to the decline handler; this
 *     will be ignored if the declineHandler param is null.
 */
ilios.alert.inform = function (str, acceptBtnText, acceptHandler, acceptHandlerArgs, declineBtnText, declineHandler, declineHandlerArgs) {
    var lang = YAHOO.lang;
    var btnConfig = [];

    acceptHandler = lang.isFunction(acceptHandler) ? acceptHandler : ilios.alert.simpleHidingHandler;
    declineHandler = lang.isFunction(declineHandler) ? declineHandler : ilios.alert.simpleHidingHandler;
    acceptBtnText = acceptBtnText || ilios_i18nVendor.getI18NString('general.terms.yes');
    declineBtnText = declineBtnText || ilios_i18nVendor.getI18NString('general.terms.no');
    acceptHandlerArgs = lang.isObject(acceptHandlerArgs) ? acceptHandlerArgs : {};
    declineHandlerArgs = lang.isObject(declineHandlerArgs) ? declineHandlerArgs : {};

    btnConfig.push({
        text: acceptBtnText,
        handler: {
            fn: acceptHandler,
            obj: acceptHandlerArgs,
            scope: ilios.alert.informDialog
        }
    });
    btnConfig.push({
        text: declineBtnText,
        handler: {
            fn: declineHandler,
            obj: declineHandlerArgs,
            scope: ilios.alert.informDialog
        },
        isDefault: true
    });

    if (! ilios.alert.informDialog) { // lazy load
        ilios.alert.informDialog = ilios.alert.createInformDialog();
        ilios.alert.informDialog.render(document.body);
    }
    ilios.alert.informDialog.configButtons(null, [btnConfig], null);
    ilios.alert.informDialog.setBody(str);
    ilios.alert.informDialog.cfg.setProperty('icon', YAHOO.widget.SimpleDialog.ICON_WARN);
    ilios.alert.informDialog.cfg.queueProperty('zIndex', 9999);
    ilios.alert.informDialog.bringToTop();
    ilios.alert.informDialog.show();
};


/**
 * Creates and returns a simple "confirmation" dialog.
 * Does NOT render the dialog.
 * @return {YAHOO.widget.SimpleDialog}
 */
ilios.alert.createConfirmDialog = function () {
    var confirmDialog = new YAHOO.widget.SimpleDialog('ilios_alert_panel', {
        visible:false,
        width: '350px',
        zIndex: 9999,
        close: false,
        fixedcenter: 'contained',
        modal: true,
        draggable: true,
        constraintoviewport: true,
        icon: YAHOO.widget.SimpleDialog.ICON_WARN,
        buttons: [{
            text: ilios_i18nVendor.getI18NString('general.terms.ok'),
            handler: ilios.alert.simpleHidingHandler,
            isDefault: true
        }]
    });
    confirmDialog.setHeader(ilios_i18nVendor.getI18NString('general.terms.alert') + "!");
    confirmDialog.setBody('');
    return confirmDialog;
};

/**
 * Creates and returns a simple "info" ("Alert!") dialog.
 * Does NOT render the dialog.
 * @return {YAHOO.widget.SimpleDialog}
 */
ilios.alert.createInformDialog = function () {
    var informDialog = new YAHOO.widget.SimpleDialog('ilios_inform_panel', {
        visible:false,
        width: '350px',
        zIndex: 9999,
        close: false,
        fixedcenter: 'contained',
        modal: true,
        draggable: true,
        constraintoviewport: true,
        icon: YAHOO.widget.SimpleDialog.ICON_WARN,
        buttons: [{
            text: ilios_i18nVendor.getI18NString('general.terms.ok'),
            handler: ilios.alert.simpleHidingHandler,
            isDefault: true
        }]
    });
    informDialog.setHeader(ilios_i18nVendor.getI18NString('general.notification.dialog_title'));
    informDialog.setBody('');
    return informDialog;
};

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
