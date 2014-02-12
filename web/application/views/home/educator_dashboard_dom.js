/*
 * Dom-manipulation functions for the educator dashboard.
 *
 * DEPENDENCY: YUI lib (at least YAHOO.util and its dependencies)
 * DEPENDENCY: DHTMLX Scheduler
 * DEPENDENCY: scripts/ilios_ui.js
 * DEPENDENCY: scripts/ilios_utilities.js
 */
ilios.namespace('home');

ilios.home.currentlySelectingCalendarDates = false;

ilios.home.constructedCSSRules = [];

ilios.home.resetUserReminderDialog = function (dialog) {
    var element = document.getElementById('ur_textarea');

    element.value = ilios.home.reminder.inEditReminderModel.getNote();

    element = document.getElementById('ur_due_date');
    if (ilios.home.reminder.inEditReminderModel.getDueDate() != null) {
        element.innerHTML = ilios.home.reminder.inEditReminderModel.getDueDate().format("m/d/yyyy");
    } else {
        element.innerHTML = '';
    }

    element = document.getElementById('ur_complete_checkbox');
    element.selected = false;

    if (dialog.modifying) {
        element = document.getElementById('ur_creation_date');
        element.innerHTML = ilios.home.reminder.inEditReminderModel.getCreationDate().format("m/d/yyyy h:MM TT");
    }
};

ilios.home.populateArchivingPermissionsDialog = function () {
    var Element = YAHOO.util.Element;
    var preferencesModel;
    var deselectIdString = null;
    var selectIdStringBase = null;
    var element = null;

    if (! ilios.global.preferencesModel) {
        ilios.global.installPreferencesModel();
    }
    preferencesModel = ilios.global.preferencesModel;

    if (preferencesModel.getProgramYearArchiving()) {
        deselectIdString = "ap_py_radio_inactive_label";
        selectIdStringBase = "ap_py_radio_active";
    } else {
        deselectIdString = "ap_py_radio_active_label";
        selectIdStringBase = "ap_py_radio_inactive";
    }
    element = new Element(document.getElementById(deselectIdString));
    element.setStyle('font-weight', 'normal');

    element = new Element(document.getElementById(selectIdStringBase + '_label'));
    element.setStyle('font-weight', 'bold');

    element = document.getElementById(selectIdStringBase);
    element.setAttribute('checked', 'true');


    if (preferencesModel.getCourseArchiving()) {
        deselectIdString = "ap_course_radio_inactive_label";
        selectIdStringBase = "ap_course_radio_active";
    } else {
        deselectIdString = "ap_course_radio_active_label";
        selectIdStringBase = "ap_course_radio_inactive";
    }
    element = new Element(document.getElementById(deselectIdString));
    element.setStyle('font-weight', 'normal');

    element = new Element(document.getElementById(selectIdStringBase + '_label'));
    element.setStyle('font-weight', 'bold');

    element = document.getElementById(selectIdStringBase);
    element.setAttribute('checked', 'true');
};

ilios.home.populateRolloverPermissionsDialog = function () {
    var Element = YAHOO.util.Element;
    var preferencesModel;
    var deselectIdString = null;
    var selectIdStringBase = null;
    var element = null;

    if (! ilios.global.preferencesModel) {
        ilios.global.installPreferencesModel();
    }
    preferencesModel = ilios.global.preferencesModel;

    if (preferencesModel.getCourseRollover()) {
        deselectIdString = "rp_radio_inactive_label";
        selectIdStringBase = "rp_radio_active";
    } else {
        deselectIdString = "rp_radio_active_label";
        selectIdStringBase = "rp_radio_inactive";
    }
    element = new Element(document.getElementById(deselectIdString));
    element.setStyle('font-weight', 'normal');

    element = new Element(document.getElementById(selectIdStringBase + '_label'));
    element.setStyle('font-weight', 'bold');

    element = document.getElementById(selectIdStringBase);
    element.setAttribute('checked', 'true');
};

ilios.home.registerReminderUIListeners = function () {
    var Event = YAHOO.util.Event;
    var element = document.getElementById('ur_textarea');

    Event.addListener(element, 'keyup', function () {
        var newNote = document.getElementById('ur_textarea').value;
        ilios.home.reminder.inEditReminderModel.setNote(newNote);
    });

    element = document.getElementById('due_date_calendar_button');
    Event.addListener(element, 'click', function () {
        var dateObject,
            xyCoordinates,
            yElement;

        // position the calendar picker in the center of the alert reminder dialog
        xyCoordinates = YAHOO.util.Dom.getXY(this);
        yElement = new YAHOO.util.Element(document.getElementById('date_picking_calendar_container'));
        yElement.setStyle('left', (xyCoordinates[0] + 'px'));
        yElement.setStyle('top', (xyCoordinates[1] + 'px'));

        if (ilios.home.reminder.inEditReminderModel.getDueDate() == null) {
            dateObject = new Date();
        } else {
            dateObject = ilios.home.reminder.inEditReminderModel.getDueDate();
        }

        ilios.home.reminder.yuiCalendarInstance.setMonth(dateObject.getMonth());
        ilios.home.reminder.yuiCalendarInstance.setYear(dateObject.getFullYear());

        ilios.home.currentlySelectingCalendarDates = true;
        ilios.home.reminder.yuiCalendarInstance.select(dateObject);
        ilios.home.currentlySelectingCalendarDates = false;

        ilios.home.reminder.yuiCalendarInstance.render();

        ilios.home.reminder.yuiCalendarInstance.show();
    });

    element = document.getElementById('ur_complete_checkbox');
    Event.addListener(element, 'click', function () {
        ilios.home.reminder.inEditReminderModel.toggleCloseState();
    });
};

/*
 * Should be considered @protected
 */
ilios.home.calendarSelectionHandler = function (type, args, obj) {
    // MAY RETURN THIS BLOCK
    if (ilios.home.currentlySelectingCalendarDates) {
        return;
    }

    if (ilios.home.reminder.inEditReminderModel != null) {
        var selectedDates = ilios.home.reminder.yuiCalendarInstance.getSelectedDates();
        var element = document.getElementById('ur_due_date');

        ilios.home.reminder.inEditReminderModel.setDueDate(selectedDates[0]);

        element.innerHTML = ilios.home.reminder.inEditReminderModel.getDueDate().format("m/d/yyyy");
    }

    ilios.home.reminder.yuiCalendarInstance.hide();
};
