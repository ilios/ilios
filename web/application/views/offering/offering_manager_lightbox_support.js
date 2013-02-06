/*
 * "Edit offering" dialog.
 *
 * DEPENDENCY: YUI lib (at least YAHOO.util and its dependencies)
 * DEPENDENCY: DHTMLX Scheduler
 * DEPENDENCY: scripts/ilios_ui.js
 * DEPENDENCY: scripts/ilios_utilities.js
 */
ilios.namespace('om.lightbox');


ilios.om.lightbox.inEditOfferingModel = null;

ilios.om.lightbox.calendarLightbox = null;

ilios.om.lightbox.yuiStartCalendar = null;
ilios.om.lightbox.yuiEndCalendar = null;
ilios.om.lightbox.yuiRecurringCalendar = null;

ilios.om.lightbox.instructorGroupAutoCompleter = null;

/**
 * This method is messaged by the DHTMLX scheduler (via configuration in our
 *  ilios.om.calendar.initCalendar) when the user performs an action requesting the display of the
 *  lightbox. The lightbox DOM itself is actually largely generated via HTML specified in the
 *  calendar_lightbox_include.php file.
 */
ilios.om.lightbox.displayCalendarLightbox = function (id) {
    var calendarEvent = scheduler.getEvent(id);

    if ((! calendarEvent.iliosModel.isReadOnly())
                                        && (! calendarEvent.iliosModel.isAggregatePlaceholder)) {
        var element = document.getElementById('instructors_lightbox_textfield');
        var model = calendarEvent.iliosModel;
        var tmpArray = null;
        var tmpModel = null;
        var displayString = null;

        element.innerHTML = model.getInstructorsAsFormattedText();

        element = document.getElementById('calendar_instructor_selected');
        ilios.utilities.removeAllChildren(element);
        tmpArray = model.getInstructors();
        for (var key in tmpArray) {
            tmpModel = tmpArray[key];

            if (tmpModel instanceof UserModel) {
                displayString = tmpModel.getFormattedName(ilios.utilities.USER_NAME_FORMAT_LAST_FIRST);
            }
            else {
                displayString = tmpModel.title;
            }

            ilios.dom.addNewLIElementWithIliosModel(element, tmpModel, displayString);
        }

        element = document.getElementById('location_lightbox_textfield');
        element.value = model.getRoom();

        element = document.getElementById('lightbox_start_time_select');
        ilios.om.lightbox.setCalendarToDate(ilios.om.lightbox.yuiStartCalendar,model.getStartDate());
        ilios.om.lightbox.setSelectorWidgetToTime(element, model.getStartDate());

        element = document.getElementById('lightbox_end_time_select');
        ilios.om.lightbox.setCalendarToDate(ilios.om.lightbox.yuiEndCalendar, model.getEndDate());
        ilios.om.lightbox.setSelectorWidgetToTime(element, model.getEndDate());

        ilios.om.lightbox.inEditOfferingModel = model.clone();

        ilios.lg.picker.constructOrResetLearnerTreeViews(model.getStudentGroups());
        ilios.lg.picker.updateLearnerTextField();

        ilios.om.lightbox.calendarLightbox.workingCalendarEvent = calendarEvent;

        ilios.om.lightbox.updateLightboxTitle();

        ilios.om.lightbox.setRecurringBlock(ilios.om.lightbox.inEditOfferingModel.isRecurring(), false);

        ilios.om.lightbox.populateRecurringWeekdayUIComponent();

        ilios.om.lightbox.calendarLightbox.showDialogPane();
    }
    else if (calendarEvent.iliosModel.isAggregatePlaceholder) {
        scheduler.setCurrentView(calendarEvent.iliosModel.getStartDate(), 'day');
    }
};

/*
 * This method is responsible for appropriately filling in the UI with data from the existing
 *  recurring event (or emptying filled in UI should the current offering have no recurring event).
 *
 * Should be considered @private
 */
ilios.om.lightbox.populateRecurringWeekdayUIComponent = function () {
    var Element = YAHOO.util.Element;
    var recurringEventModel = ilios.om.lightbox.inEditOfferingModel.getRecurringEvent();
    var dayArray = [];
    var element = null;

    if (recurringEventModel == null) {
        var startDate = ilios.om.lightbox.inEditOfferingModel.getStartDate();

        dayArray.push(startDate.getDay());
    }
    else {
        dayArray = recurringEventModel.eventDaysAsArray();
    }

    for (var i = 0; i < 7; i++) {
        element = new Element(document.getElementById('repeat_week_' + i));
        element.removeClass('selected_day');
    }

    for (var day in dayArray) {
        element = new Element(document.getElementById('repeat_week_' + dayArray[day]));
        element.addClass('selected_day');
    }
};

/*
 * This method is invoked onDomReady; what DOM isn't built by the HTML in
 *  calendar_lightbox_include.php is crafted here. This includes YUI related components like
 *  calendars.
 *
 * Should be considered @protected
 */
ilios.om.lightbox.buildLightboxDOM = function () {
    var element = null;

    var handleSave = function () {
        ilios.om.lightbox.userAcceptedLightboxChanges(this);
        this.hide();
    };

    var handleCancel = function () {
        this.cancel();
    };

    var cancelStr = ilios_i18nVendor.getI18NString('general.terms.cancel');
    var saveStr = ilios_i18nVendor.getI18NString('general.terms.save');
    var buttonArray = [{text: saveStr, handler: handleSave, isDefault: true},
                       {text: cancelStr, handler: handleCancel}];

    var panelWidth = "530px";
    var dialog = new YAHOO.widget.Dialog('ilios_calendar_lightbox',
                                         {width: panelWidth, modal: true, visible: false,
                                          constraintoviewport: false, buttons: buttonArray});
    var titleStr = ilios_i18nVendor.getI18NString('general.phrases.choose_date') + ':';

    dialog.showDialogPane = function () {
        dialog.center();
        dialog.show();
    };

    dialog.showEvent.subscribe(function() {
        // check if the instructor picker container is expanded
        // if and ONLY so, refresh the autocomplete list
        if ('none' !== YAHOO.util.Dom.getStyle('ilios_calendar_instructors_selector_div', 'display')) {
            document.getElementById('calendar_instructor_ac_input').value = '';
            ilios.om.lightbox.instructorGroupAutoCompleter.sendQuery('');
        }
    });

    // Render the Dialog
    dialog.render();

    ilios.om.lightbox.calendarLightbox = dialog;

    element = new YAHOO.util.Element(document.getElementById('ilios_calendar_lightbox'));
    element.setStyle('display', 'block');

    ilios.om.lightbox.yuiStartCalendar = new YAHOO.widget.Calendar("startYUICal",
                                                                   "lightbox_start_time_calendar",
                                                                   { close: false } );
    ilios.om.lightbox.yuiStartCalendar.render();
    ilios.om.lightbox.yuiStartCalendar.selectEvent.subscribe(ilios.om.lightbox.calendarSelectionHandler,
                                                             ilios.om.lightbox.yuiStartCalendar,
                                                             true);

    ilios.om.lightbox.yuiEndCalendar = new YAHOO.widget.Calendar("endYUICal",
                                                                 "lightbox_end_time_calendar",
                                                                 { close: false } );
    ilios.om.lightbox.yuiEndCalendar.render();
    ilios.om.lightbox.yuiEndCalendar.selectEvent.subscribe(ilios.om.lightbox.calendarSelectionHandler,
                                                           ilios.om.lightbox.yuiEndCalendar, true);


    ilios.om.lightbox.yuiRecurringCalendar
                = new YAHOO.widget.Calendar("recurYUICal", "date_picking_calendar_container",
                                            { title: titleStr, close: true } );
    ilios.om.lightbox.yuiRecurringCalendar.render();
    ilios.om.lightbox.yuiRecurringCalendar.hide();

    ilios.om.lightbox.yuiRecurringCalendar.selectEvent.subscribe(ilios.om.lightbox.recurringCalendarSelectionHandler,
                                                                 ilios.om.lightbox.yuiRecurringCalendar,
                                                                 true);
};

/*
 * This method is invoked onDomReady; it is responsible for setting up the wiring such that UI
 *  events on input widgets of interest affect the model state appropriately.
 *
 * Should be considered @protected
 */
ilios.om.lightbox.registerLightboxUIListeners = function () {
    var Event = YAHOO.util.Event;
    var element = document.getElementById('calendar_instructor_collapse_selector');
    var itemSelectHandler = null;

    Event.addListener(element, 'click', function () {
        ilios.om.lightbox.showInstructors(false);
    });

    element = document.getElementById('location_lightbox_textfield');
    Event.addListener(element, 'keyup', function (event) {
        var room = document.getElementById('location_lightbox_textfield').value;
        ilios.om.lightbox.inEditOfferingModel.setRoom(room);
    });

    element = document.getElementById('calendar_student_group_collapse_selector');
    Event.addListener(element, 'click', function () {
        ilios.om.lightbox.showLearners(false);
    });

    element = document.getElementById('calendar_instructor_selected');
    Event.addListener(element, 'click', function (event) {
        ilios.om.lightbox.handleInstructorGroupDeselection(event);
    });

    element = document.getElementById('lightbox_start_time_select');
    Event.addListener(element, 'change', function () {
        ilios.om.lightbox.setTimeValue(document.getElementById('lightbox_start_time_select'), true);
    });

    element = document.getElementById('lightbox_end_time_select');
    Event.addListener(element, 'change', function () {
        ilios.om.lightbox.setTimeValue(document.getElementById('lightbox_end_time_select'), false);
    });

    element = document.getElementById('repeat_end_date_calendar_button');
    Event.addListener(element, 'click', function () {
        ilios.om.lightbox.setRecurringCalendarToDate(document.getElementById('repeat_ends_on_date').innerHTML);
        ilios.om.lightbox.moveRecurringCalendarToDOMElement(this);
        ilios.om.lightbox.yuiRecurringCalendar.show();
    });

    element = document.getElementById('lightbox_repeat_count_select');
    Event.addListener(element, 'change', function () {
        ilios.om.lightbox.userSetRecurringCount();
    });


    ilios.om.lightbox.instructorGroupAutoCompleter
                    = new YAHOO.widget.AutoComplete('calendar_instructor_ac_input',
                                                    'calendar_instructor_autolist',
                                                    ilios.om.lightbox.instructorGroupDataSource);

    ilios.om.lightbox.instructorGroupAutoCompleter.alwaysShowContainer = true;
    ilios.om.lightbox.instructorGroupAutoCompleter.allowBrowserAutocomplete = false;
    ilios.om.lightbox.instructorGroupAutoCompleter.minQueryLength = 0;
    ilios.om.lightbox.instructorGroupAutoCompleter.maxResultsDisplayed = 2500;

    ilios.om.lightbox.instructorGroupAutoCompleter.autoHighlight = false;

    ilios.om.lightbox.instructorGroupAutoCompleter.applyLocalFilter = true;
    ilios.om.lightbox.instructorGroupAutoCompleter.filterResults = ilios.om.lightbox.filterInstructors;

    ilios.om.lightbox.instructorGroupAutoCompleter.resultTypeList = false;

    ilios.om.lightbox.instructorGroupAutoCompleter.formatResult = function (resultDataObject, queryString,
                                                                            resultMatch) {
        if (resultDataObject instanceof UserModel) {
            var rhett = '<span title="' + resultDataObject.getEmailAddress() + '">';

            rhett += resultDataObject.getFormattedName(ilios.utilities.USER_NAME_FORMAT_LAST_FIRST);
            rhett += '</span>';

            return rhett;
        }

        return resultDataObject.title;
    };

    ilios.om.lightbox.instructorGroupAutoCompleter.dataReturnEvent.subscribe(function (sType, aArgs) {
        YAHOO.util.Dom.setStyle('calendar_instructor_ac_progress', 'visibility', 'hidden');
    });

    ilios.om.lightbox.instructorGroupAutoCompleter.dataRequestEvent.subscribe(function (sType, aArgs) {
        YAHOO.util.Dom.setStyle('calendar_instructor_ac_progress', 'visibility', 'visible');
        var myAC = aArgs[0];
        myAC.clearList();
    });

    itemSelectHandler = function (selectionType, selectionArgs) {
        ilios.om.lightbox.handleInstructorGroupSelection(selectionArgs[2]);

        selectionArgs[1].parentNode.removeChild(selectionArgs[1]);

        document.getElementById('calendar_instructor_ac_input').value = '';
    };
    ilios.om.lightbox.instructorGroupAutoCompleter.itemSelectEvent.subscribe(itemSelectHandler);
};

/*
 * Sets the YUI calendar state to reflect whatever the end date of the current recurring event is.
 *
 * Should be considered @private
 */
ilios.om.lightbox.setRecurringCalendarToDate = function () {
    var recurrentEventModel = ilios.om.lightbox.getInEditRecurringEventModel();

    if (recurrentEventModel.getEndDate() != null) {
        // todo if we were being super-optimized, we'd check to see if the state was actually
        //          changing prior to setting and re-rendering
        ilios.om.lightbox.yuiRecurringCalendar.setYear(recurrentEventModel.getEndDate().getUTCFullYear());
        ilios.om.lightbox.yuiRecurringCalendar.setMonth(recurrentEventModel.getEndDate().getUTCMonth());

        ilios.om.calendar.currentlySelectingCalendarDates = true;
        ilios.om.lightbox.yuiRecurringCalendar.select(recurrentEventModel.getEndDate());
        ilios.om.calendar.currentlySelectingCalendarDates = false;

        ilios.om.lightbox.yuiRecurringCalendar.render();
    }
};

/*
 * Responsible for attempting to make the recurring event calendar appear pretty near the calendar
 *  button's click event space while still being in the lightbox's general bounds.
 *
 * Should be considered @private
 */
ilios.om.lightbox.moveRecurringCalendarToDOMElement = function (element) {
    var xyCoordinates = YAHOO.util.Dom.getXY(element);
    var yElement = new YAHOO.util.Element(document.getElementById('date_picking_calendar_container'));

    yElement.setStyle('left', ((xyCoordinates[0] - 160) + 'px'));
    yElement.setStyle('top', ((xyCoordinates[1] - 220) + 'px'));
};

/*
 * This is messaged from the YUI calendars in the lightbox which represent start and end dates,
 *
 * Should be considered @protected
 */
ilios.om.lightbox.calendarSelectionHandler = function (type, args, obj) {
    var selected = null,
        selectedDate = null,
        scratchDate = null,
        previouslySetDate = null,
        isForEndDate = (this == ilios.om.lightbox.yuiEndCalendar),  // 'this' is the calendar
        msg = null,
        model = ilios.om.lightbox.inEditOfferingModel;

    // MAY RETURN THIS BLOCK
    if (ilios.om.calendar.currentlySelectingCalendarDates) {
        return;
    }

    selected = args[0];
    selectedDate = this.toDate(selected[0]);
    previouslySetDate = isForEndDate ? model.getEndDate() : model.getStartDate();
    scratchDate = new Date(selectedDate.getUTCFullYear(), selectedDate.getUTCMonth(),
               selectedDate.getUTCDate(), previouslySetDate.getHours(),
               previouslySetDate.getMinutes(), previouslySetDate.getSeconds(),
               previouslySetDate.getMilliseconds());

    if (isForEndDate) {
        // MAY RETURN THIS BLOCK
        if (scratchDate.getTime()
                                <= model.getStartDate().getTime()) {
            msg = ilios_i18nVendor.getI18NString('general.phrases.end_start_violation');

            ilios.om.lightbox.setCalendarToDate(ilios.om.lightbox.yuiEndCalendar,
                                                previouslySetDate);

            ilios.alert.alert(msg);

            return;
        }
    }
    else {
        previouslySetDate = model.getStartDate();
    }


    if (isForEndDate) {
        model.setEndDate(scratchDate);
    }
    else {
        var newEndDate = new Date(model.getEndDate().getTime());

        newEndDate.setFullYear(selectedDate.getFullYear());
        newEndDate.setMonth(selectedDate.getMonth());
        newEndDate.setDate(selectedDate.getDate());

        // MAY RETURN THIS BLOCK
        if (newEndDate.getTime() <= scratchDate.getTime()) {
            msg = ilios_i18nVendor.getI18NString('general.phrases.end_start_violation');

            ilios.om.lightbox.setCalendarToDate(ilios.om.lightbox.yuiStartCalendar,
                                                model.getStartDate());

            ilios.alert.alert(msg);

            return;
        }

        model.setStartDate(scratchDate);
        model.setEndDate(newEndDate);
        ilios.om.lightbox.setCalendarToDate(ilios.om.lightbox.yuiEndCalendar, newEndDate);

        ilios.om.lightbox.populateRecurringWeekdayUIComponent();
    }
};

/*
 * This method is messaged when the user changes the value of the time via the pulldown boxes
 *  in the lightbox (either for start or for end time).
 *
 * Should be considered @protected
 */
ilios.om.lightbox.setTimeValue = function (selector, isStartTime) {
    var selectedValue = selector.options[selector.selectedIndex].value;
    var timeChunks = selectedValue.split(':');
    var hours = ilios.utilities.parseIntIgnoringLeadingZeros(timeChunks[0]);
    var minutes = ilios.utilities.parseIntIgnoringLeadingZeros(timeChunks[1]);
    var startDate = new Date(ilios.om.lightbox.inEditOfferingModel.getStartDate().getTime());
    var endDate = new Date(ilios.om.lightbox.inEditOfferingModel.getEndDate().getTime());
    var changedEndDateToo = false;

    if (isStartTime) {
        startDate.setHours(hours, minutes, 0, 0);

        if (endDate.getTime() <= startDate.getTime()) {
            var deltaHours = startDate.getHours()
                                - ilios.om.lightbox.inEditOfferingModel.getStartDate().getHours();
            var deltaMinutes = startDate.getMinutes()
                                - ilios.om.lightbox.inEditOfferingModel.getStartDate().getMinutes();
            var newEndHours = endDate.getHours() + deltaHours;
            var newEndMinutes = endDate.getMinutes() + deltaMinutes;

            endDate.setHours(newEndHours, newEndMinutes, 0, 0);
            changedEndDateToo = true;

            ilios.om.lightbox.setSelectorWidgetToTime(document.getElementById('lightbox_end_time_select'),
                                                      endDate);

            ilios.om.lightbox.setCalendarToDate(ilios.om.lightbox.yuiEndCalendar, endDate);
        }
    }
    else {
        endDate.setHours(hours, minutes, 0, 0);
    }

    // MAY RETURN THIS BLOCK
    if (endDate.getTime() <= startDate.getTime()) {
        var msg = ilios_i18nVendor.getI18NString('general.phrases.end_start_violation');

        ilios.om.lightbox.setSelectorWidgetToTime(selector,
                                                  isStartTime ? ilios.om.lightbox.inEditOfferingModel.getStartDate()
                                                              : ilios.om.lightbox.inEditOfferingModel.getEndDate());

        ilios.alert.alert(msg);

        return;
    }

    if (isStartTime) {
        ilios.om.lightbox.inEditOfferingModel.setStartDate(startDate);

        if (changedEndDateToo) {
            ilios.om.lightbox.inEditOfferingModel.setEndDate(endDate);
        }
    }
    else {
        ilios.om.lightbox.inEditOfferingModel.setEndDate(endDate);
    }

    ilios.om.lightbox.updateLightboxTitle();
};

/*
 * Assures that the calendar to display is set with the appropriate year and month.
 *
 * @param dateObj a javascript date object
 */
ilios.om.lightbox.setCalendarToDate = function (calendar, dateObj) {
    // TODO if we were being optimized, we'd check to see if the state was actually
    //          changing prior to setting and re-rendering
    calendar.setYear(dateObj.getUTCFullYear());
    calendar.setMonth(dateObj.getUTCMonth());

    ilios.om.calendar.currentlySelectingCalendarDates = true;
    calendar.select(dateObj);
    ilios.om.calendar.currentlySelectingCalendarDates = false;

    calendar.render();
};

/*
 * This method is used to reflect the dateObj value in the passed select element (for start
 *  or end time).
 *
 * Should be considered @private
 */
ilios.om.lightbox.setSelectorWidgetToTime = function (selector, dateObj) {
    var timeString = ilios.utilities.paddedTimeValue(dateObj.getHours()) + ':'
                                    + ilios.utilities.paddedTimeValue(dateObj.getMinutes());

    ilios.utilities.selectOptionWithValue(selector, timeString);
};

/*
 * This method either displays or hides the DIV in the lightbox which allows selection of
 *  instructors / instructor groups.
 *
 * Should be considered @protected
 */
ilios.om.lightbox.showInstructors = function (showSelectorDiv) {
    var Element = YAHOO.util.Element;
    var element = document.getElementById('ilios_calendar_instructors_selector');

    ilios.utilities.toggleShowMoreOrLess('ilios_calendar_instructors_selector_div', null);

    if (showSelectorDiv) {
        (new Element(element)).setStyle('display', 'none');
        document.getElementById('calendar_instructor_ac_input').value = '';
        ilios.om.lightbox.instructorGroupAutoCompleter.sendQuery('');
    }
    else {
        (new Element(element)).setStyle('display', 'inline');
    }
};

ilios.om.lightbox.instructorGroupDataSource = new YAHOO.util.XHRDataSource(controllerURL + 'searchInstructors');
ilios.om.lightbox.instructorGroupDataSource.responseType = YAHOO.util.XHRDataSource.TYPE_JSON;
ilios.om.lightbox.instructorGroupDataSource.responseSchema = {
    resultsList: "results",
    fields: [
        {key: "user_id"},
        {key: "last_name"},
        {key: "first_name"},
        {key: "middle_name"},
        {key: "phone"},
        {key: "email"},
        {key: "uc_uid"},
        {key: "other_id"},
        {key: "instructor_group_id"},
        {key: "title"},
        {key: "school_id"}
    ]
};

/**
 * Parses the JSON data in a given response into a response object.
 * Overrides the YAHOO.util.DataSourceBase.parseJSONData().
 * See http://developer.yahoo.com/yui/docs/YAHOO.util.DataSourceBase.html#method_parseJSONData
 * @method parseJSONData
 * @param {Object} oRequest
 * @param {Object} oFullResponse
 * @return {Object}
 */
ilios.om.lightbox.instructorGroupDataSource.parseJSONData = function (oRequest , oFullResponse) {
    var rhett = {results: [], error: false};
    var i, n, o, userModel, groupObj;
    if (oFullResponse.results && oFullResponse.results.length) {
        for (i = 0, n = oFullResponse.results.length; i < n; i++) {
            o = oFullResponse.results[i];
            if (o.instructor_group_id) { // instructor group
                groupObj = {};
                groupObj.isGroup = true;
                groupObj.dbId = o.instructor_group_id;
                groupObj.title = o.title;
                rhett.results.push(groupObj);
            } else { // instructor
                userModel = new UserModel(o);
                userModel.isGroup = false;
                rhett.results.push(userModel);
            }
        }
    } else if (oFullResponse.error) { // error handling
        rhett.error = true;
    }
    return rhett;
};


/*
 * This method either displays or hides the DIV in the lightbox which allows selection of
 *  student groups.
 *
 * Should be considered @protected
 */
ilios.om.lightbox.showLearners = function (showSelectorDiv) {
    var Element = YAHOO.util.Element;
    var element = document.getElementById('ilios_calendar_student_groups_selector');

    if (showSelectorDiv) {
        (new Element(element)).setStyle('display', 'none');
    } else {
        (new Element(element)).setStyle('display', 'inline');
    }

    ilios.utilities.toggleShowMoreOrLess('ilios_calendar_student_groups_selector_div', null);
};

/*
 * This is messaged when the user selects an instructor / instructor group from the list of them
 *  in the lightbox.
 *
 * Should be considered @private
 */
ilios.om.lightbox.handleInstructorGroupSelection = function (selectedModel) {
    var displayString = null;

    if (selectedModel instanceof UserModel) {
        displayString = selectedModel.getFormattedName(ilios.utilities.USER_NAME_FORMAT_LAST_FIRST);
    } else {
        displayString = selectedModel.title;
    }

    ilios.dom.addNewLIElementWithIliosModel(document.getElementById('calendar_instructor_selected'),
        selectedModel, displayString);
    ilios.om.lightbox.inEditOfferingModel.addInstructor(selectedModel);
    ilios.om.lightbox.updateInstructorGroupTextField();
};

/*
 * This is messaged when the user clicks on an instructor / instructor group from the left pane of
 *  previously selected items (thereby semantically deselecting the item).
 *
 * Should be considered @private
 */
ilios.om.lightbox.handleInstructorGroupDeselection = function (event) {
    var target = ilios.utilities.getEventTarget(event);

    if (target.tagName.toLowerCase() === 'li') {
        var model = target.iliosModel;
        target.parentNode.removeChild(target);
        ilios.om.lightbox.inEditOfferingModel.removeInstructor(model);
        ilios.om.lightbox.updateInstructorGroupTextField();
        document.getElementById('calendar_instructor_ac_input').value = '';
        ilios.om.lightbox.instructorGroupAutoCompleter.sendQuery('');
        return false;
    }
    return true;
};

/*
 * This method populates the read-only UI text field representing the instructors list with the
 *  contents of the current in-edit offering model.
 *
 * Should be considered @private
 */
ilios.om.lightbox.updateInstructorGroupTextField = function () {
    var element = document.getElementById('instructors_lightbox_textfield');
    element.innerHTML = ilios.om.lightbox.inEditOfferingModel.getInstructorsAsFormattedText();
};

/*
 * This method populates the title bar of the YUI modal panel with the appropriate start-end time
 *  range text.
 *
 * Should be considered @private
 */
ilios.om.lightbox.updateLightboxTitle = function () {
    var element = document.getElementById('ilios_lightbox_title');
    var start = ilios.om.lightbox.inEditOfferingModel.getStartDate();
    var end = ilios.om.lightbox.inEditOfferingModel.getEndDate();
    var title = '' + ilios.utilities.paddedTimeValue(start.getHours()) + ':'
                    + ilios.utilities.paddedTimeValue(start.getMinutes())
                    + ' - ' + ilios.utilities.paddedTimeValue(end.getHours())
                    + ':' + ilios.utilities.paddedTimeValue(end.getMinutes());

    element.innerHTML = title;
};

/*
 * This is messaged when the user dismisses the light box with a click on the 'Save' button. If
 *  the in-edit OfferingModel has changed during the lifecycle of the lightbox, then a save
 *  transaction is initiated.
 *
 * Should be considered @private
 */
ilios.om.lightbox.userAcceptedLightboxChanges = function (dialog) {
    var event = ilios.om.lightbox.calendarLightbox.workingCalendarEvent;
    var originalModel = event.iliosModel;
    var selectedLearnerGroups = ilios.lg.picker.getSelectedLearnerModels();

    ilios.om.lightbox.inEditOfferingModel.removeAllStudentGroups();
    for (var key in selectedLearnerGroups) {
        ilios.om.lightbox.inEditOfferingModel.addStudentGroup(selectedLearnerGroups[key]);
    }

    if (originalModel.compareTo(ilios.om.lightbox.inEditOfferingModel) != 0) {
        originalModel.replaceContentWithModel(ilios.om.lightbox.inEditOfferingModel, true);

        event.text = ilios.om.calendar.generateEventObjectDisplayTextForModel(originalModel);

        event.start_date = originalModel.getStartDate();
        event.end_date = originalModel.getEndDate();

        scheduler.updateEvent(event.id);

        ilios.om.transaction.saveCalendarEventToServer(event);
    }
};

/*
 * This method filters the instructors for the autocompleter
 * This filtering is done pre-markup of the list and,
 * here, takes out elements in the full list which have already been selected.
 *
 * TODO identical to ilios.cm.session.ilm.filterInstructors
 *
 * Should be considered @protected
 */
ilios.om.lightbox.filterInstructors = function (queryString, fullResponse, parsedResponse,
                                                callback) {
    var len = parsedResponse.results.length;
    var selectedList = document.getElementById('calendar_instructor_selected');
    var filteredResults = [];
    var i = 0;
    for (; i < len; i++) {
        if (! ilios.dom.iliosModeledLIElementsContainMatchingModel(selectedList, parsedResponse.results[i])) {
            filteredResults.push(parsedResponse.results[i]);
        }
    }
    parsedResponse.results = filteredResults;
    return parsedResponse;
};

/*
 * For offerings which either have no recurring event, or their recurring event is not immutable
 *  (because it hasn't yet been saved back to the server), this toggles the display of the recurring
 *  events DIV.
 *
 * Should be considered @protected.
 */
ilios.om.lightbox.toggleRecurring = function () {
    var divElement = null;

    if (ilios.om.lightbox.inEditOfferingModel.recurringEventIsImmutable()) {
        return;
    }

    divElement = new YAHOO.util.Element(document.getElementById('lightbox_repeat_div'));

    ilios.om.lightbox.setRecurringBlock((divElement.getStyle('display') == 'none'), true);
};

/*
 * @param display whether or not the div should be displayed
 * @param setModelOrLoadFromModel if true, setRecurring() will be call appropriately, if false
 *                      the selector and end date will be set from the in edit RE model.
 *                      this does NOT call ilios.om.lightbox.populateRecurringWeekdayUIComponent
 *
 * Should be considered @private
 */
ilios.om.lightbox.setRecurringBlock = function (display, setModelOrLoadFromModel) {
    var element = document.getElementById('repeat_ends_on_date');
    var linkElement = document.getElementById('lightbox_recurring_link');

    element.innerHTML = '';

    element = document.getElementById('lightbox_repeat_count_select');
    ilios.utilities.selectOptionWithValue(element, 1);

    if (display && (! setModelOrLoadFromModel)) {
        var model = ilios.om.lightbox.getInEditRecurringEventModel();

        ilios.utilities.selectOptionWithValue(element, model.getRepetitionCount());

        if (model.endDateWasExplicitlySet()) {
            element = document.getElementById('repeat_ends_on_date');
            element.innerHTML = ilios.utilities.dateObjectToMySQLFriendly(model.getEndDate(),
                                                                          false);

            element = document.getElementById('repeat_ends_on_date_radio');
        }
        else {
            element = document.getElementById('repeat_ends_on_count_radio');
        }
        element.checked = true;
    }

    element = new YAHOO.util.Element(document.getElementById('lightbox_repeat_div'));

    if (display) {
        var obscurer = document.getElementById('lightbox_recurring_obscuring_div');

        element.setStyle('display', 'block');

        linkElement.innerHTML = ilios_i18nVendor.getI18NString('general.phrases.is_recurring');

        if (ilios.om.lightbox.inEditOfferingModel.recurringEventIsImmutable()) {
            if (obscurer == null) {
                obscurer = document.createElement('div');
                obscurer.setAttribute('id', 'lightbox_recurring_obscuring_div');
                obscurer.setAttribute('class', 'obscuring_shade');

                element.get('element').appendChild(obscurer);
            }
        }
        else {
            if (obscurer != null) {
                obscurer.parentNode.removeChild(obscurer);
            }
        }

        if (setModelOrLoadFromModel) {
            ilios.om.lightbox.inEditOfferingModel.setRecurring(true);
        }
    }
    else {
        element.setStyle('display', 'none');

        linkElement.innerHTML = ilios_i18nVendor.getI18NString('general.phrases.not_recurring');

        if (setModelOrLoadFromModel) {
            ilios.om.lightbox.inEditOfferingModel.setRecurring(false);
        }
    }
};

/*
 * This is a convenience method to get the recurring event associated to the in-edit offering model
 *  which constructs and associates a recurring event should one not already exist.
 *
 * Should be considered @private.
 */
ilios.om.lightbox.getInEditRecurringEventModel = function () {
    var recurringEventModel = ilios.om.lightbox.inEditOfferingModel.getRecurringEvent();

    if (recurringEventModel == null) {
        recurringEventModel = new RecurringEventModel();

        recurringEventModel.setStartDate(ilios.om.lightbox.inEditOfferingModel.getStartDate());

        ilios.om.lightbox.inEditOfferingModel.setRecurringEvent(recurringEventModel);
    }

    return recurringEventModel;
};

/*
 * This method is messaged via a click event coming from the LI element in the UI which
 *  represents one of the days of the week listed in the recurring events UI.
 *
 * Should be considered @protected
 */
ilios.om.lightbox.repeatDayClicked = function (dayOfWeekInt, liElement) {
    var yElement = new YAHOO.util.Element(liElement);
    var actionSelects = (! yElement.hasClass('selected_day'));
    var recurringEventModel = ilios.om.lightbox.getInEditRecurringEventModel();

    if (actionSelects) {
        yElement.addClass('selected_day');

        recurringEventModel.setDayToState(dayOfWeekInt, 1);
    }
    else {
        // check to make sure the start day isn't this day trying to be deselected
        if (ilios.om.lightbox.inEditOfferingModel.getStartDate().getUTCDay() != dayOfWeekInt) {
            yElement.removeClass('selected_day');

            recurringEventModel.setDayToState(dayOfWeekInt, 0);
        }
    }
};

/*
 * This method is messaged from the YUI calendar being used for the end date of recurring events.
 *
 * Should be considered @protected
 */
ilios.om.lightbox.recurringCalendarSelectionHandler = function (type, args, obj) {
    if (! ilios.om.calendar.currentlySelectingCalendarDates) {
        var selected = args[0];
        var selectedDate = this.toDate(selected[0]);    // 'this' is the calendar
        var formattedDate = ilios.utilities.dateObjectToMySQLFriendly(selectedDate, false);
        var element = document.getElementById('repeat_ends_on_date');
        var recurringEventModel = ilios.om.lightbox.getInEditRecurringEventModel();

// todo alert and disallow if this date is past course end date

        recurringEventModel.setEndDate(selectedDate);

        element.innerHTML = formattedDate;

        element = document.getElementById('repeat_ends_on_date_radio');
        element.checked = true;

        this.hide();
    }
};

/*
 * This method reflects the repetition count of the current recurring event model in the pulldown
 *  UI in the recurring events DIV.
 *
 * Should be considered @protected
 */
ilios.om.lightbox.userSetRecurringCount = function () {
    var element = document.getElementById('lightbox_repeat_count_select');
    var selectedValue = element.options[element.selectedIndex].value;
    var recurringEventModel = ilios.om.lightbox.getInEditRecurringEventModel();

// todo alert and disallow if this would put the end date past course end date

    recurringEventModel.setRepetitionCount(selectedValue);

    element = document.getElementById('repeat_ends_on_count_radio');
    element.checked = true;
};
