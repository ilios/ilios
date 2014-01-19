/**
 * DEPENDENCY: YUI lib (at least YAHOO.util and its dependencies)
 * DEPENDENCY: scripts/ilios_ui.js
 * DEPENDENCY: scripts/ilios_utilities.js
 */

ilios.namespace('cm.session.mo');

ilios.cm.session.mo.inEditRecurringEventModel = null;

ilios.cm.session.mo.currentlySelectingCalendarDates = false;

ilios.cm.session.mo.yuiStartCalendar = null;
ilios.cm.session.mo.yuiEndCalendar = null;
ilios.cm.session.mo.yuiRecurringCalendar = null;

ilios.cm.session.mo.lastAcceptableStartDate = null;
ilios.cm.session.mo.lastAcceptableEndDate = null;

ilios.cm.session.mo.showMultiOfferingLightbox = function (containerNumber) {
    var Element = YAHOO.util.Element;
    var element = null;
    var sessionModel = ilios.cm.currentCourseModel.getSessionForContainer(containerNumber);
    var i18nStr = null;
    var startDate = ilios.cm.currentCourseModel.getStartDateObjectForNewSession();

    // MAY RETURN THIS BLOCK
    if (ilios.cm.currentCourseModel.isModelDirty() || (sessionModel.getDBId() == -1)) {
        ilios.alert.alert(ilios_i18nVendor.getI18NString('course_management.error.multi_offering_launch'));

        return;
    } else if (ilios.utilities.arraySize(ilios.cm.currentCourseModel.getCohorts()) == 0) {
        ilios.alert.alert(ilios_i18nVendor.getI18NString('course_management.warning.multi_offering.no_cohorts'));

        return;
    } else if (ilios.utilities.arraySize(ilios.lg.picker.learnerTreeModel) == 0) {
        ilios.alert.alert(ilios_i18nVendor.getI18NString('course_management.warning.multi_offering.no_learners'));

        return;
    }

    element = document.getElementById('multipurpose_lightbox_title');
    element.innerHTML = ilios_i18nVendor.getI18NString('course_management.multiple_offerings');

    i18nStr = ilios_i18nVendor.getI18NString('general.terms.done');
    ilios.ui.hackilySetButtonTitleOnYUIDialog(i18nStr, ilios.cm.session.multipurposeSessionLightbox, 0);

    element = document.getElementById('learner_list_lightbox_textfield');
    element.innerHTML = '';

    element = document.getElementById('lightbox_start_time_select');
    ilios.utilities.selectOptionWithValue(element, '08:00');

    element = document.getElementById('lightbox_end_time_select');
    ilios.utilities.selectOptionWithValue(element, '09:00');


    element = document.getElementById('lightbox_repeat_count_select');
    ilios.utilities.selectOptionWithValue(element, 1);
    element = document.getElementById('repeat_ends_on_count_radio');
    element.checked = true;


    element = document.getElementById('repeat_ends_on_date');
    element.innerHTML = '';


    element = new Element(document.getElementById('multipurpose_instructors_div'));
    element.setStyle('display', 'none');

    element = new Element(document.getElementById('ilm_calendar_div'));
    element.setStyle('display', 'none');

    element = new Element(document.getElementById('multi_offering_calendar_div'));
    element.setStyle('display', 'block');

    element = new Element(document.getElementById("learner_assignment_strategy_div"));
    element.setStyle("display", "block");

    ilios.cm.session.mo.resetLearnerGroupAssignmentStrategySelection();

    ilios.cm.session.mo.showRecurringBlock(false);

    ilios.lg.picker.constructOrResetLearnerTreeViews();

    ilios.cm.session.mo.setLightboxCalendarToDate(ilios.cm.session.mo.yuiStartCalendar, startDate);
    ilios.cm.session.mo.setLightboxCalendarToDate(ilios.cm.session.mo.yuiEndCalendar, startDate);

    ilios.cm.session.mo.populateRecurringWeekdayUIComponent();

    ilios.cm.session.mo.lastAcceptableStartDate = ilios.cm.session.mo.getTimeMarker(true);
    ilios.cm.session.mo.lastAcceptableEndDate = ilios.cm.session.mo.getTimeMarker(false);

    ilios.cm.session.multipurposeSessionLightbox.cnumber = containerNumber;
    ilios.cm.session.multipurposeSessionLightbox.showingForMultiOffering = true;
    ilios.cm.session.multipurposeSessionLightbox.showDialogPane();
};

ilios.cm.session.mo.recursivelyBuildSimplifiedGroupModel = function (parsedGroupModel) {
    var pgid = (parsedGroupModel.parent_group_id != null) ? parsedGroupModel.parent_group_id : -1;
    var rhett = new SimplifiedGroupModel(parsedGroupModel.title, pgid, parsedGroupModel.group_id);
    var subgroupModel = null;

    for (var key in parsedGroupModel.subgroups) {
        subgroupModel = parsedGroupModel.subgroups[key];

        rhett.addSubgroup(ilios.cm.session.mo.recursivelyBuildSimplifiedGroupModel(subgroupModel));
    }

    return rhett;
};

/*
 * This method is invoked onDomReady; it is responsible for setting up the wiring such that UI
 *  events on input widgets of interest affect the model state appropriately.
 *
 * Should be considered @protected
 */
ilios.cm.session.mo.registerMultiOfferingLightboxUIListeners = function () {
    var Event = YAHOO.util.Event;
    var element = document.getElementById('lightbox_start_time_select');

    Event.addListener(element, 'change', function () {
        ilios.cm.session.mo.setTimeValue(document.getElementById('lightbox_start_time_select'), true);
    });

    element = document.getElementById('lightbox_end_time_select');
    Event.addListener(element, 'change', function () {
        ilios.cm.session.mo.setTimeValue(document.getElementById('lightbox_end_time_select'), false);
    });

    element = document.getElementById('repeat_end_date_calendar_button');
    Event.addListener(element, 'click', function () {
        ilios.cm.session.mo.setRecurringCalendarToDate(document.getElementById('repeat_ends_on_date').innerHTML);
        ilios.cm.session.mo.yuiRecurringCalendar.show();
        ilios.cm.session.mo.moveRecurringCalendarToDOMElement(this);
    });

    element = document.getElementById('lightbox_repeat_count_select');
    Event.addListener(element, 'change', function () {
        ilios.cm.session.mo.userSetRecurringCount();
    });
};

/*
 * This method is invoked onDomReady; most of the lightbox is built during
 *                              ilios.cm.session.buildILMLightboxDOM()
 *
 * Should be considered @protected
 */
ilios.cm.session.mo.buildMultiOfferingLightboxDOMComponents = function () {
    var titleStr = ilios_i18nVendor.getI18NString('general.phrases.choose_date') + ':';

    ilios.cm.session.mo.yuiStartCalendar = new YAHOO.widget.Calendar("startYUICal", "lightbox_start_time_calendar", {
        close: false
    });
    ilios.cm.session.mo.yuiStartCalendar.render();
    ilios.cm.session.mo.yuiStartCalendar.selectEvent.subscribe(
        ilios.cm.session.mo.lightboxCalendarSelectionHandler,
        ilios.cm.session.mo.yuiStartCalendar,
        true);

    ilios.cm.session.mo.yuiEndCalendar = new YAHOO.widget.Calendar("endYUICal", "lightbox_end_time_calendar", {
        close: false
    });
    ilios.cm.session.mo.yuiEndCalendar.render();
    ilios.cm.session.mo.yuiEndCalendar.selectEvent.subscribe(
        ilios.cm.session.mo.lightboxCalendarSelectionHandler,
        ilios.cm.session.mo.yuiEndCalendar,
        true);

    ilios.cm.session.mo.yuiRecurringCalendar = new YAHOO.widget.Calendar("recurYUICal",
        "multiple_offerings_recurring_date_picking_calendar_container", {
            title: titleStr,
            close: true
        });

    ilios.cm.session.mo.yuiRecurringCalendar.render();
    ilios.cm.session.mo.yuiRecurringCalendar.hide();

    ilios.cm.session.mo.yuiRecurringCalendar.selectEvent.subscribe(
        ilios.cm.session.mo.recurringCalendarSelectionHandler,
        ilios.cm.session.mo.yuiRecurringCalendar,
        true);
};

/*
 * This is messaged when the user dismisses the light box with a click on the 'Save' button.
 *
 * Should be considered @protected
 */
ilios.cm.session.mo.validateMultiOfferingLightboxSave = function (dialog) {
    var errorMessage = null;
    var selectedModels = ilios.lg.picker.getSelectedLearnerModels();

    // 1 or more groups selected?
    if (selectedModels.length == 0) {
        errorMessage = ilios_i18nVendor.getI18NString(
                                        'course_management.warning.multi_offering.student_groups');
    }

    // if recurring, day array has at least 1 day?
    if (errorMessage == null) {
        if (ilios.cm.session.mo.lightboxStateIsRecurring()) {
            var error = false;

            if (ilios.cm.session.mo.inEditRecurringEventModel == null) {
                error = true;
            }
            else {
                var dayArray = ilios.cm.session.mo.inEditRecurringEventModel.eventDaysAsArray();

                error = (dayArray.length == 0);
            }

            if (error) {
                errorMessage = ilios_i18nVendor.getI18NString(
                                        'course_management.warning.multi_offering.recurring_days');
            }
        }
    }

    // end time is after start time?
    if (errorMessage == null) {
        var startDate = ilios.cm.session.mo.getTimeMarker(true);
        var endDate = ilios.cm.session.mo.getTimeMarker(false);

        if (startDate.getTime() >= endDate.getTime()) {
            errorMessage = ilios_i18nVendor.getI18NString(
                                    'course_management.warning.multi_offering.invalid_date_range');
        }
    }

    if (errorMessage != null) {
        ilios.alert.alert(errorMessage);
    }
    else {
        ilios.cm.session.mo.userAcceptedMultiOfferingLightboxChanges(dialog);

        dialog.hide();
    }
};

/*
 * This method is messaged when the user changes the value of the end time via the pulldown boxes
 *  in the lightbox.
 *
 * Should be considered @private
 */
ilios.cm.session.mo.setTimeValue = function (selector, isStartTime) {
    var startDate = ilios.cm.session.mo.getTimeMarker(true);
    var endDate = ilios.cm.session.mo.getTimeMarker(false);
    var changedEndDateToo = false;

    if (isStartTime && (endDate.getTime() <= startDate.getTime())) {
        var deltaHours = startDate.getHours()
                                - ilios.cm.session.mo.lastAcceptableStartDate.getHours();
        var deltaMinutes = startDate.getMinutes()
                                - ilios.cm.session.mo.lastAcceptableStartDate.getMinutes();
        var newEndHours = endDate.getHours() + deltaHours;
        var newEndMinutes = endDate.getMinutes() + deltaMinutes;

        endDate.setHours(newEndHours, newEndMinutes, 0, 0);
        changedEndDateToo = true;

        ilios.cm.session.mo.setSelectorWidgetToTime(document.getElementById('lightbox_end_time_select'),
                                                    endDate);

        ilios.cm.session.mo.setLightboxCalendarToDate(ilios.cm.session.mo.yuiEndCalendar,
                                                      endDate);
    }

    // MAY RETURN THIS BLOCK
    if (endDate.getTime() <= startDate.getTime()) {
        var msg = ilios_i18nVendor.getI18NString('general.phrases.end_start_violation');

        ilios.cm.session.mo.setSelectorWidgetToTime(selector,
                                                    isStartTime
                                                        ? ilios.cm.session.mo.lastAcceptableStartDate
                                                        : ilios.cm.session.mo.lastAcceptableEndDate);

        ilios.alert.alert(msg);

        return;
    }

    if (isStartTime) {
        ilios.cm.session.mo.lastAcceptableStartDate = new Date(startDate.getTime());

        if (changedEndDateToo) {
            ilios.cm.session.mo.lastAcceptableEndDate = new Date(endDate.getTime());
        }
    }
    else {
        ilios.cm.session.mo.lastAcceptableEndDate = new Date(endDate.getTime());
    }
};

/*
 * This method is used to reflect the dateObj value in the passed select element (for start
 *  or end time).
 *
 * Should be considered @private
 */
ilios.cm.session.mo.setSelectorWidgetToTime = function (selector, dateObj) {
    ilios.utilities.selectOptionWithValue(selector, dateObj.format('HH:MM'));
};

ilios.cm.session.mo.lightboxStateIsRecurring = function () {
    var element = new YAHOO.util.Element(document.getElementById('multi_offering_recurring_div'));

    return (element.getStyle('display') == 'block');
};

/**
 * Returns the selected learner-group assigment strategy from the "learner_assignment_strategy_select"
 * dropdown box.
 * @method getSelectedLearnerGroupAssignmentStrategy
 * @return {String} the selected strategy (either "roots" or "leaves")
 */
ilios.cm.session.mo.getSelectedLearnerGroupAssignmentStrategy = function () {
    var element = document.getElementById("learner_assignment_strategy_select");
    var selectedIndex = element.selectedIndex;
    if (-1 === selectedIndex) { // default to first option if none is selected
        selectedIndex = 0;
    }
    return element.options[selectedIndex].value;
}

/**
 * Resets the selected learner-group assigment strategy in the "learner_assignment_strategy_select"
 * dropdown box to the first option.
 * @method resetLearnerGroupAssignmentStrategySelection
 */
ilios.cm.session.mo.resetLearnerGroupAssignmentStrategySelection = function () {
    document.getElementById("learner_assignment_strategy_select").selectedIndex = 0;
};


ilios.cm.session.mo.getTimeMarker = function (startTime) {
    var calendar = startTime ? ilios.cm.session.mo.yuiStartCalendar
                             : ilios.cm.session.mo.yuiEndCalendar;
    var selectedDates = calendar.getSelectedDates();
    var rhett = new Date(selectedDates[0].getTime());
    var selectElementId = startTime ? 'lightbox_start_time_select' : 'lightbox_end_time_select';
    var selectElement = document.getElementById(selectElementId);
    var selectedTimeValue = selectElement.options[selectElement.selectedIndex].value;
    var timeChunks = selectedTimeValue.split(':');
    var hours = parseInt(timeChunks[0], 10);
    var minutes = parseInt(timeChunks[1], 10);

    rhett.setHours(hours, minutes, 0, 0);

    return rhett;
};

/*
 * Assures that the calendar to display is set with the appropriate year and month.
 *
 * @param dateObj a javascript date object
 * @private
 */
ilios.cm.session.mo.setLightboxCalendarToDate = function (calendar, dateObj) {
    // todo if we were being super-optimized, we'd check to see if the state was actually
    //          changing prior to setting and re-rendering
    calendar.setYear(dateObj.getUTCFullYear());
    calendar.setMonth(dateObj.getUTCMonth());

    ilios.cm.session.mo.currentlySelectingCalendarDates = true;
    calendar.select(dateObj);
    ilios.cm.session.mo.currentlySelectingCalendarDates = false;

    calendar.render();
};

// @private
ilios.cm.session.mo.initRecurringCalendarEventInEditIfNeeded = function () {
    if (ilios.cm.session.mo.inEditRecurringEventModel == null) {
        var selectedDates = ilios.cm.session.mo.yuiStartCalendar.getSelectedDates();

        ilios.cm.session.mo.inEditRecurringEventModel = new RecurringEventModel();

        ilios.cm.session.mo.inEditRecurringEventModel.setStartDate(selectedDates[0]);

        // this will incur a double population the first time its called with a presently null REM
        ilios.cm.session.mo.populateRecurringWeekdayUIComponent();
    }
};

/*
 * Sets the YUI calendar state to reflect whatever the end date of the current recurring event is.
 *
 * Should be considered @private
 */
ilios.cm.session.mo.setRecurringCalendarToDate = function () {
    ilios.cm.session.mo.initRecurringCalendarEventInEditIfNeeded();

    if (ilios.cm.session.mo.inEditRecurringEventModel.getEndDate() != null) {
        // todo if we were being super-optimized, we'd check to see if the state was actually
        //          changing prior to setting and re-rendering
        ilios.cm.session.mo.yuiRecurringCalendar.setYear(ilios.cm.session.mo.inEditRecurringEventModel.getEndDate().getUTCFullYear());
        ilios.cm.session.mo.yuiRecurringCalendar.setMonth(ilios.cm.session.mo.inEditRecurringEventModel.getEndDate().getUTCMonth());

        ilios.cm.session.mo.currentlySelectingCalendarDates = true;
        ilios.cm.session.mo.yuiRecurringCalendar.select(ilios.cm.session.mo.inEditRecurringEventModel.getEndDate());
        ilios.cm.session.mo.currentlySelectingCalendarDates = false;

        ilios.cm.session.mo.yuiRecurringCalendar.render();
    }
};

/*
 * Responsible for attempting to make the recurring event calendar appear pretty near the calendar
 *  button's click event space while still being in the lightbox's general bounds.
 *
 * Should be considered @private
 */
ilios.cm.session.mo.moveRecurringCalendarToDOMElement = function (element) {
    var xyCoordinates = YAHOO.util.Dom.getXY(element);
    var yElement
        = new YAHOO.util.Element(document.getElementById('multiple_offerings_recurring_date_picking_calendar_container'));

    yElement.setStyle('left', ((xyCoordinates[0] - 160) + 'px'));
    yElement.setStyle('top', ((xyCoordinates[1] - 220) + 'px'));
};

/*
 * This method is messaged from the YUI calendar being used for the end date of recurring events.
 *
 * Should be considered @private
 */
ilios.cm.session.mo.recurringCalendarSelectionHandler = function (type, args, obj) {
    if (! ilios.cm.session.mo.currentlySelectingCalendarDates) {
        var selected = args[0];
        var selectedDate = this.toDate(selected[0]);    // 'this' is the calendar
        var formattedDate = ilios.utilities.dateObjectToMySQLFriendly(selectedDate, false);
        var element = document.getElementById('repeat_ends_on_date');

        // TODO alert and disallow if this date is past course end date

        ilios.cm.session.mo.initRecurringCalendarEventInEditIfNeeded();

        ilios.cm.session.mo.inEditRecurringEventModel.setEndDate(selectedDate);

        element.innerHTML = formattedDate;

        element = document.getElementById('repeat_ends_on_date_radio');
        element.checked = true;

        this.hide();
    }
};

/*
 * This is messaged from the YUI calendars in the lightbox which represent start and end dates,
 *
 * Should be considered @private
 */
ilios.cm.session.mo.lightboxCalendarSelectionHandler = function (type, args, obj) {
    var isForStartDate = (this != ilios.cm.session.mo.yuiEndCalendar);
    var startDate = null;
    var msg = null;
    var selectedDates = null;

    // MAY RETURN THIS BLOCK
    if (ilios.cm.session.mo.currentlySelectingCalendarDates) {
        return;
    }

    startDate = ilios.cm.session.mo.getTimeMarker(true);

    // MAY RETURN THIS BLOCK
    if (! isForStartDate) {
        var proposedEndDate = null;

        selectedDates = ilios.cm.session.mo.yuiEndCalendar.getSelectedDates();
        proposedEndDate = new Date(selectedDates[0].getTime());

        proposedEndDate.setHours(ilios.cm.session.mo.lastAcceptableEndDate.getHours(),
                                 ilios.cm.session.mo.lastAcceptableEndDate.getMinutes(), 0, 0);

        if (proposedEndDate.getTime() <= startDate.getTime()) {
            msg = ilios_i18nVendor.getI18NString('general.phrases.end_start_violation');

            ilios.cm.session.mo.setLightboxCalendarToDate(ilios.cm.session.mo.yuiEndCalendar,
                                                          ilios.cm.session.mo.lastAcceptableEndDate);

            ilios.alert.alert(msg);

            return;
        }

        ilios.cm.session.mo.lastAcceptableEndDate = new Date(proposedEndDate.getTime());
    }
    else {
        var newEndDate = new Date(ilios.cm.session.mo.lastAcceptableEndDate.getTime());

        selectedDates = ilios.cm.session.mo.yuiStartCalendar.getSelectedDates();

        newEndDate.setFullYear(selectedDates[0].getFullYear());
        newEndDate.setMonth(selectedDates[0].getMonth());
        newEndDate.setDate(selectedDates[0].getDate());

        startDate.setFullYear(selectedDates[0].getFullYear());
        startDate.setMonth(selectedDates[0].getMonth());
        startDate.setDate(selectedDates[0].getDate());

        // MAY RETURN THIS BLOCK
        if (newEndDate.getTime() <= startDate.getTime()) {
            msg = ilios_i18nVendor.getI18NString('general.phrases.end_start_violation');

            ilios.cm.session.mo.setLightboxCalendarToDate(ilios.cm.session.mo.yuiStartCalendar,
                                                          ilios.cm.session.mo.lastAcceptableStartDate);

            ilios.alert.alert(msg);

            return;
        }

        ilios.cm.session.mo.setLightboxCalendarToDate(ilios.cm.session.mo.yuiEndCalendar,
                                                      newEndDate);

        ilios.cm.session.mo.lastAcceptableStartDate = new Date(startDate.getTime());
        ilios.cm.session.mo.lastAcceptableEndDate = new Date(newEndDate.getTime());

        if (ilios.cm.session.mo.inEditRecurringEventModel != null) {
            ilios.cm.session.mo.inEditRecurringEventModel.setStartDate(selectedDates[0]);
        }

        ilios.cm.session.mo.populateRecurringWeekdayUIComponent();
    }
};

/*
 * This method is messaged via a click event coming from the LI element in the UI which
 *  represents one of the days of the week listed in the recurring events UI.
 *
 * TODO nearly identical to ilios.om.lightbox.repeatDayClicked
 *
 * Should be considered @protected
 */
ilios.cm.session.mo.repeatDayClicked = function (dayOfWeekInt, liElement) {
    var yElement = new YAHOO.util.Element(liElement);
    var actionSelects = (! yElement.hasClass('selected_day'));

    ilios.cm.session.mo.initRecurringCalendarEventInEditIfNeeded();

    if (actionSelects) {
        yElement.addClass('selected_day');

        ilios.cm.session.mo.inEditRecurringEventModel.setDayToState(dayOfWeekInt, 1);
    }
    else {
        // check to make sure the start day isn't this day trying to be deselected
        if (ilios.cm.session.mo.inEditRecurringEventModel.getStartDate().getUTCDay() != dayOfWeekInt) {
            yElement.removeClass('selected_day');

            ilios.cm.session.mo.inEditRecurringEventModel.setDayToState(dayOfWeekInt, 0);
        }
    }
};

/*
 * This method reflects the repetition count of the current recurring event model in the pulldown
 *  UI in the recurring events DIV.
 *
 * Should be considered @private
 */
ilios.cm.session.mo.userSetRecurringCount = function () {
    var element = document.getElementById('lightbox_repeat_count_select');
    var selectedValue = element.options[element.selectedIndex].value;

    ilios.cm.session.mo.initRecurringCalendarEventInEditIfNeeded();

    // TODO alert and disallow if this would put the end date past course end date

    ilios.cm.session.mo.inEditRecurringEventModel.setRepetitionCount(selectedValue);

    element = document.getElementById('repeat_ends_on_count_radio');
    element.checked = true;
};

/*
 * This is messaged when the user dismisses the light box with a click on the 'Save' button and
 *  the data validation check succeeds.
 *
 * Should be considered @private
 */
ilios.cm.session.mo.userAcceptedMultiOfferingLightboxChanges = function (dialog) {
    ilios.cm.transaction.performMultiOfferingSave(dialog);

    if (! ilios.cm.session.mo.lightboxStateIsRecurring()) {
        ilios.cm.session.mo.inEditRecurringEventModel = null;
    }
};

ilios.cm.session.mo.userCanceledMultiOfferingLightboxChanges = function (dialog) {
    ilios.cm.session.mo.inEditRecurringEventModel = null;
};

/*
 * This method is responsible for appropriately filling in the UI with data from the existing
 *  recurring event (or emptying filled in UI should the current offering have no recurring event).
 *
 * Should be considered @private
 */
ilios.cm.session.mo.populateRecurringWeekdayUIComponent = function () {
    var Element = YAHOO.util.Element;
    var dayArray = [];
    var element = null;

    ilios.cm.session.mo.initRecurringCalendarEventInEditIfNeeded();

    dayArray = ilios.cm.session.mo.inEditRecurringEventModel.eventDaysAsArray();

    ilios.cm.session.mo.clearRecurringWeekdayUIComponent();

    for (var day in dayArray) {
        element = new Element(document.getElementById('repeat_week_' + dayArray[day]));

        element.addClass('selected_day');
    }
};

// @private
ilios.cm.session.mo.clearRecurringWeekdayUIComponent = function () {
    var Element = YAHOO.util.Element;
    var element = null;

    for (var i = 0; i < 7; i++) {
        element = new Element(document.getElementById('repeat_week_' + i));
        element.removeClass('selected_day');
    }
};

/*
 * For offerings which either have no recurring event, or their recurring event is not immutable
 *  (because it hasn't yet been saved back to the server), this toggles the display of the recurring
 *  events DIV.
 *
 * Should be considered @protected.
 */
ilios.cm.session.mo.toggleRecurringDisplay = function () {
    var divElement = null;

    divElement = new YAHOO.util.Element(document.getElementById('multi_offering_recurring_div'));

    ilios.cm.session.mo.showRecurringBlock((divElement.getStyle('display') == 'none'));
};

/*
 * @param display whether or not the div should be displayed
 *
 * Should be considered @private
 */
ilios.cm.session.mo.showRecurringBlock = function (shouldDisplay) {
    var element = document.getElementById('repeat_ends_on_date');
    var linkElement = document.getElementById('lightbox_recurring_link');

    element.innerHTML = '';

    element = new YAHOO.util.Element(document.getElementById('multi_offering_recurring_div'));

    if (shouldDisplay) {
        element.setStyle('display', 'block');

        linkElement.innerHTML = ilios_i18nVendor.getI18NString('general.phrases.is_recurring');
    } else {
        element.setStyle('display', 'none');

        linkElement.innerHTML = ilios_i18nVendor.getI18NString('general.phrases.not_recurring');
    }
};
