/**
 * Course Developer Dashboard dialog infrastructure.
 * @see views/home/course_developer_dashboard_view.php
 */

ilios.namespace("home.preferences");

/**
 * ============================================================================
 * "archiving permissions" dialog support
 * ============================================================================
 */

ilios.home.preferences.apDialog = null;

/**
 * Event handler function.
 * @method assembleArchivingPermissionsDialog
 * @param {String} type
 * @param {Array} args
 * @param {Object} me
 *     submit_override:
 *         if this is non-null the function is subscribed to
 *         the manual submit event of the dialog and the
 *         dialog is configured to fire that event when the
 *         user clicks the 'Save' button on the dialog.
 *     display_handler:
 *         a function which is called just prior to the dialog
 *         being displayed
 */
ilios.home.preferences.assembleArchivingPermissionsDialog = function (type, args, me) {
    var handleSave = function () {
        ilios.home.transaction.saveArchivingPreferences();
    };

    var handleCancel = function () {
        this.cancel();
    };

    var cancelStr = ilios_i18nVendor.getI18NString('general.terms.cancel');
    var saveStr = ilios_i18nVendor.getI18NString('general.terms.save');
    var buttonArray = [
        {text: saveStr, handler: handleSave, isDefault: true},
        {text: cancelStr, handler: handleCancel}
    ];
    var panelWidth = "580px";
    var displayOnTriggerHandler = null;
    var dialog = new YAHOO.widget.Dialog('archiving_permissions_dialog', {
        width: panelWidth,
        modal: true,
        visible: false,
        constraintoviewport: false,
        buttons: buttonArray
    });

    dialog.showDialogPane = function () {
        var displayHandler = me['display_handler'];

        if (displayHandler != null) {
            displayHandler(dialog);
        }

        dialog.center();
        dialog.show();
    };

    dialog.setHeader(ilios_i18nVendor.getI18NString("preferences.archiving.title"));
    // Render the Dialog
    dialog.render();

    if (me['submit_override'] != null) {
        dialog.cfg.setProperty('postmethod', 'manual');
        dialog.manualSubmitEvent.subscribe(me['submit_override'], null);
    }

    displayOnTriggerHandler = function (type, handlerArgs) {
        if (handlerArgs[0].action == 'ap_dialog_open') {
            dialog.showDialogPane();
        }
    };

    IEvent.subscribe(displayOnTriggerHandler);

    ilios.home.preferences.apDialog = dialog;
}

/**
 * ============================================================================
 * "rollover permissions" dialog support
 * ============================================================================
 */

ilios.home.preferences.rpDialog = null;

/**
 * Event handler function.
 * @method assembleRolloverPermissionsDialog
 * @param {String} type
 * @param {Array} args
 * @param {Object} me
 *     submit_override:
 *         if this is non-null the function is subscribed to
 *         the manual submit event of the dialog and the
 *         dialog is configured to fire that event when the
 *         user clicks the 'Save' button on the dialog.
 *     display_handler:
 *         a function which is called just prior to the dialog
 *         being displayed
 */
ilios.home.preferences.assembleRolloverPermissionsDialog = function (type, args, me) {
    var handleSave = function () {
        ilios.home.transaction.saveRolloverPreference();
    };

    var handleCancel = function () {
        this.cancel();
    };

    var cancelStr = ilios_i18nVendor.getI18NString('general.terms.cancel');
    var saveStr = ilios_i18nVendor.getI18NString('general.terms.save');
    var buttonArray = [
        {text: saveStr, handler: handleSave, isDefault: true},
        {text: cancelStr, handler: handleCancel}
    ];
    var panelWidth = "580px";
    var displayOnTriggerHandler = null;
    var dialog = new YAHOO.widget.Dialog('rollover_permissions_dialog', {
        width: panelWidth,
        modal: true,
        visible: false,
        constraintoviewport: false,
        buttons: buttonArray
    });

    dialog.showDialogPane = function () {
        var displayHandler = me['display_handler'];

        if (displayHandler != null) {
            displayHandler(dialog);
        }

        dialog.center();
        dialog.show();
    };

    dialog.setHeader(ilios_i18nVendor.getI18NString('preferences.rollover.title'));
    // Render the Dialog
    dialog.render();

    if (me['submit_override'] != null) {
        dialog.cfg.setProperty('postmethod', 'manual');
        dialog.manualSubmitEvent.subscribe(me['submit_override'], null);
    }

    displayOnTriggerHandler = function (type, handlerArgs) {
        if (handlerArgs[0].action == 'rp_dialog_open') {
            dialog.showDialogPane();
        }
    };

    IEvent.subscribe(displayOnTriggerHandler);

    ilios.home.preferences.rpDialog = dialog;
}

/**
 * ============================================================================
 * "user reminder" dialog support
 * ============================================================================
 */
ilios.namespace('home.reminder');

ilios.home.reminder.userReminderDialog = null;
ilios.home.reminder.inEditReminderModel = null;
ilios.home.reminder.yuiCalendarInstance = null;

ilios.home.reminder.finishUserReminderDialogCancel = function (clickButton) {
    this.cancel();
    ilios.home.reminder.userReminderDialog.cancel();
};

/**
 * Event handler function.
 * @method assembleUserReminderDialog
 * @param {String} type
 * @param {Array} args
 * @param {Object} me
 *     submit_override:
 *         if this is non-null the function is subscribed to
 *         the manual submit event of the dialog and the
 *         dialog is configured to fire that event when the
 *         user clicks the 'Save' button on the dialog.
 *     display_handler:
 *         a function which is called just prior to the dialog
 *         being displayed
 */
 ilios.home.reminder.assembleUserReminderDialog = function (type, args, me) {
    var handleSave = function () {
        if (ilios.home.reminder.inEditReminderModel.getNote() == '') {
            var msg = ilios_i18nVendor.getI18NString('dashboard.warning.empty_reminder_note');
             ilios.alert.alert(msg);
        } else if (ilios.home.reminder.inEditReminderModel.getDueDate() == null) {
            var msg = ilios_i18nVendor.getI18NString('dashboard.warning.empty_reminder_due_date');
            ilios.alert.alert(msg);
        } else {
            ilios.home.transaction.saveReminder(ilios.home.reminder.inEditReminderModel);
            ilios.home.reminder.userReminderDialog.cancel();
        }
    };

    var handleCancel = function () {
        if (ilios.home.reminder.inEditReminderModel.compareTo(ilios.home.reminder.userReminderDialog.reminderModel) != 0) {
            var dirtyStr = ilios_i18nVendor.getI18NString('dashboard.warning.dirty_reminder');
            var continueStr = ilios_i18nVendor.getI18NString('general.phrases.want_to_continue');
            var yesStr = ilios_i18nVendor.getI18NString('general.terms.yes');
            ilios.alert.inform(('<p style="margin-bottom:9px; text-align:justify;">' + dirtyStr
                + '</p><center><b>' + continueStr + '</b></center>'),
                yesStr, ilios.home.reminder.finishUserReminderDialogCancel);
        } else {
            this.cancel();
        }
    };

    var cancelStr = ilios_i18nVendor.getI18NString("general.terms.cancel");
    var saveStr = ilios_i18nVendor.getI18NString("general.terms.done");
    var buttonArray = [
        {text: saveStr, handler: handleSave, isDefault: true},
        {text: cancelStr, handler: handleCancel}
    ];
    var panelWidth = "440px";
    var dialog = null;
    var displayOnTriggerHandler = null;
    var yElement = null;

    dialog = new YAHOO.widget.Dialog('user_reminder_dialog', {
        width: panelWidth,
        modal: true,
        visible: false,
        constraintoviewport: false,
        buttons: buttonArray
    });

    dialog.showDialogPane = function () {
        var displayHandler = me['display_handler'];

        if (displayHandler != null) {
            displayHandler(dialog);
        }
        dialog.center();
        dialog.show();
    };

    // Render the Dialog
    dialog.render();

    if (me['submit_override'] != null) {
        dialog.cfg.setProperty('postmethod', 'manual');
        dialog.manualSubmitEvent.subscribe(me['submit_override'], null);
    }

    // register a listener on some page element to trigger the display of this popup dialog
    displayOnTriggerHandler = function (type, handlerArgs) {
        var Element = YAHOO.util.Element;
        if (handlerArgs[0].action == 'ur_dialog_open') {
            var titleElement = document.getElementById('user_reminder_dialog_title');
            if (handlerArgs[0].reminder_model != null) {
                // modify existing case
                dialog.reminderModel = handlerArgs[0].reminder_model;
                ilios.home.reminder.inEditReminderModel = handlerArgs[0].reminder_model.clone();
                dialog.modifying = true;

                titleElement.innerHTML = ilios_i18nVendor.getI18NString('dashboard.reminder.edit_title');

                yElement = new Element(document.getElementById('ur_creation_div'));
                yElement.setStyle('display', 'block');

                yElement = new Element(document.getElementById('ur_complete_div'));
                yElement.setStyle('display', 'block');

                yElement = document.getElementById('ur_complete_checkbox');
                yElement.checked = false;
            } else {
                // Add New case
                dialog.reminderModel = new ReminderModel();
                ilios.home.reminder.inEditReminderModel = new ReminderModel();
                dialog.modifying = false;

                titleElement.innerHTML = ilios_i18nVendor.getI18NString('dashboard.reminder.add_title');

                yElement = new Element(document.getElementById('ur_creation_div'));
                yElement.setStyle('display', 'none');

                yElement = new Element(document.getElementById('ur_complete_div'));
                yElement.setStyle('display', 'none');
            }
            dialog.showDialogPane();
        }
    };
    IEvent.subscribe(displayOnTriggerHandler);

    ilios.home.reminder.userReminderDialog = dialog;

    ilios.home.reminder.yuiCalendarInstance = new YAHOO.widget.Calendar("dueDateYUICal",
        "date_picking_calendar_container", {title: '', close: true});
    ilios.home.reminder.yuiCalendarInstance.render();
    ilios.home.reminder.yuiCalendarInstance.hide();
    ilios.home.reminder.yuiCalendarInstance.selectEvent.subscribe(ilios.home.calendarSelectionHandler,
        ilios.home.reminder.yuiCalendarInstance, true);
}

/**
 * ============================================================================
 * "calendar event details" dialog support
 * ============================================================================
 */

/**
 * Event handler function.
 * @method assembleCalendarEventDetailsDialog
 * @param {String} type
 * @param {Array} args
 * @param {Object} me
 * @todo improve code docs
 */
ilios.home.calendar.assembleCalendarEventDetailsDialog = function (type, args, me) {
    var handleCancel = function () {
        this.cancel();
    };
    var doneStr = ilios_i18nVendor.getI18NString("general.terms.done");
    var buttonArray = [
        {text: doneStr, handler: handleCancel, isDefault: true}
    ];
    var panelWidth = "538px";
    var displayOnTriggerHandler = null;
    var dialog = new YAHOO.widget.Dialog('calendar_event_details_dialog', {
        width: panelWidth,
        modal: true,
        visible: false,
        constraintoviewport: false,
        buttons: buttonArray
    });

    dialog.showDialogPane = function () {
        dialog.center();
        dialog.show();
    };

    dialog.setHeader(ilios_i18nVendor.getI18NString("dashboard.event_details"));
    // Render the Dialog
    dialog.render();

    // register a listener on some page element to trigger the display of this popup dialog
    displayOnTriggerHandler = function (type, handlerArgs) {
        if (handlerArgs[0].action == 'lv_dialog_open') {
            var model = handlerArgs[0].model;
            var container = document.getElementById('learner_view_content_div');

            ilios.ui.renderIndeterminateInView(container);

            dialog.showDialogPane();

            if (model.isIndependentLearningEvent()) {
                ilios.learner_view.loadSILMDetailsIntoDivContainer(model.sessionId, container, false);
            } else {
                ilios.learner_view.loadOfferingDetailsIntoDivContainer(model.offeringId, container, false);
            }
        }
    };

    IEvent.subscribe(displayOnTriggerHandler);

    ilios.learner_view.learnerViewDialog = dialog; // overwrite!
};
