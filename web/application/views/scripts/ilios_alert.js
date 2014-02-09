
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
    var element;
    var i18nStr;

    if (ilios.alert.networkActivityI18NStrings.length) { // message queued for display, show the first
        i18nStr = ilios_i18nVendor.getI18NString(ilios.alert.networkActivityI18NStrings[0]);

        element = document.getElementById('save_in_progress_text');
        if (element) {
            element.textContent = i18nStr;
            element = document.getElementById('save_in_progress_div');
            element.style.display = 'block';
        }
    } else { // no messages, hide the display container
        element = document.getElementById('save_in_progress_div');
        if (element) {
            element.style.display = 'none';
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