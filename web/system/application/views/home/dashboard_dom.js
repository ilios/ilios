/*
 * Dom-manipulation functions for the user dashboard.
 *
 * DEPENDENCY: YUI lib (at least YAHOO.util and its dependencies)
 * DEPENDENCY: DHTMLX Scheduler
 * DEPENDENCY: scripts/ilios_ui.js
 * DEPENDENCY: scripts/ilios_utilities.js
 */

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
    var preferencesModel = ilios.global.preferencesModel;
    var deselectIdString = null;
    var selectIdStringBase = null;
    var element = null;

    if (preferencesModel.showProgramYearArchiving()) {
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


    if (preferencesModel.showCourseArchiving()) {
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
    var preferencesModel = ilios.global.preferencesModel;
    var deselectIdString = null;
    var selectIdStringBase = null;
    var element = null;

    if (preferencesModel.showCourseRollover()) {
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

// @private
ilios.home.moveCalendarToDOMElement = function (element) {
    var xyCoordinates = YAHOO.util.Dom.getXY(element);
    var yElement = new YAHOO.util.Element(document.getElementById('date_picking_calendar_container'));

    yElement.setStyle('left', (xyCoordinates[0] + 'px'));
    yElement.setStyle('top', (xyCoordinates[1] + 'px'));
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
        var dateObject = null;

        ilios.home.moveCalendarToDOMElement(this);

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


/**
 * Custom DataTable formatter for the "Event" column in the offerings table.
 *
 * @method home.offeringTableTitleFormatter
 * @param el {HTMLElement} The element to format with markup.
 * @param oRecord {YAHOO.widget.Record} Record instance.
 * @param oColumn {YAHOO.widget.Column} Column instance.
 * @param oData {String} (Optional) Data value for the cell. Values are
 * HTML-escaped.
 * @param oDataTable {YAHOO.widget.DataTable} DataTable instance.
 * @static
 */
ilios.home.offeringTableTitleFormatter = function (el, oRecord, oColumn, oData, oDataTable) {
    if (oRecord._oData.recentlyUpdated) {
        // add CSS class selector for "recently updated" offerings/silms
        // to indicate that fact
        YAHOO.util.Dom.addClass(el, 'recently_updated_calendar_event');
    }
    YAHOO.widget.DataTable.formatText(el, oRecord, oColumn, oData, oDataTable);
};


/**
 * Custom DataTable formatter for the "Date" column in the offerings table.
 *
 * Relies on date_formatter.js in the scripts/third_party directory.
 *
 * @method home.offeringTableDateFormatter
 * @param el {HTMLElement} The element to format with markup.
 * @param oRecord {YAHOO.widget.Record} Record instance.
 * @param oColumn {YAHOO.widget.Column} Column instance.
 * @param oData {String} (Optional) Data value for the cell. Values are
 * HTML-escaped.
 * @param oDataTable {YAHOO.widget.DataTable} DataTable instance.
 * @static
 */
ilios.home.offeringTableDateFormatter = function (el, oRecord, oColumn, oData, oDataTable) {
    if ((oData == null) || (! (oData instanceof Date))) {
        oData = oRecord.getData('date');
    }

    if (oData != null) {
        var id = oRecord.getData('id');
        var html = '<a href="" onclick="ilios.home.calendar.focusCalendarOnStartDateOfOfferingWithId(';
        html += id + '); return false;">' + oData.format('ddd mmm d yyyy') + '</a>';
        el.innerHTML = html;
    }
};
