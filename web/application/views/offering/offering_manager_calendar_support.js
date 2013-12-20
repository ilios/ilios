/**
 * DEPENDENCY: YUI lib (at least YAHOO.util and its dependencies)
 * DEPENDENCY: DHTMLX Scheduler
 * DEPENDENCY: scripts/ilios_ui.js
 * DEPENDENCY: scripts/ilios_utilities.js
 */

ilios.namespace('om.calendar');


ilios.om.calendar.currentlyAddingEvents = false;
ilios.om.calendar.currentlySelectingCalendarDates = false;

ilios.om.calendar.currentlySelectedIliosModel = null;

ilios.om.calendar.lastStartDateUsedInAddingEvents = null;
ilios.om.calendar.lastEndDateUsedInAddingEvents = null;
ilios.om.calendar.lastModeUsedInAddingEvents = null;

/**
 * This is called onDOMReady; added to that event queue in offering_manager.php. This sets
 *     up the configuration of the DHTMLX scheduler canvas including registering for event
 *     notifications from that canvas.
 */
ilios.om.calendar.initCalendar = function () {
    var weekHeaderFormat = scheduler.date.date_to_str("%M %j, %Y");
    var weekColumnFormat = scheduler.date.date_to_str("%M %j, %D");

    scheduler.config.first_hour = 0;
    scheduler.config.last_hour = 24;
    scheduler.config.scroll_hour = 8;

    scheduler.config.start_on_monday = false;
    scheduler.config.time_step = 15;

    scheduler.config.edit_on_create = false;
    scheduler.config.details_on_create = true;
    scheduler.config.details_on_dblclick = true;

    scheduler.config.icons_select = ['icon_details','icon_delete'];

    scheduler.config.xml_date = "%Y-%m-%d %H:%i";

    scheduler.config.multi_day = true;

    // if null, no delete confirmation message is showed when the user clicks for delete
    scheduler.locale.labels.confirm_deleting = null;

    scheduler.init('dhtmlx_scheduler_container', new Date(), "week");

    scheduler.templates.event_class = function (start, end, event) {
        var rhett = '';
        var sessionTypeModel;

        // shouldn't ever be non-null but DHMTLX surprises
        if (event.iliosModel != null) {

            sessionTypeModel = ilios.om.loadedSessionTypes[event.iliosModel.sessionTypeId];
            if (sessionTypeModel) {
                rhett += sessionTypeModel.sessionTypeCssClass;
            }

            rhett += event.iliosModel.isReadOnly() ? ' read-only-event' : '';

            if (event.iliosModel.isSelected) {
                rhett += " selected_calendar_event";
            }
        }

        return rhett;
    };

    scheduler.templates.week_scale_date = function (date) { return weekColumnFormat(date); };
    scheduler.templates.week_date
        = function (date1, date2) {
            return weekHeaderFormat(date1) + " - " + weekHeaderFormat(date2);
        };

    scheduler.attachEvent("onClick", ilios.om.calendar.offeringSelected);
    scheduler.attachEvent("onEventAdded", ilios.om.calendar.offeringAddedViaCalendar);
    scheduler.attachEvent("onEventChanged", ilios.om.calendar.offeringEditedViaCalendar);
    scheduler.attachEvent("onBeforeEventDelete", ilios.om.calendar.offeringWillDeleteViaCalendar);
    scheduler.attachEvent("onBeforeDrag", ilios.om.calendar.calendarDragWillStart);
    scheduler.attachEvent("onViewChange", ilios.om.calendar.calendarViewChanged);

    scheduler.showLightbox = ilios.om.lightbox.displayCalendarLightbox;
};

/**
 * This method sets the displayed calendar date to either the first day of the course, or the
 *  first day of all offerings for this session (if this session has offerings); the view mode
 *  is set as 'week'.
 */
ilios.om.calendar.resetCurrentCalendarViewToStart = function () {
    var dateBegin = ilios.om.calendarStartDate;  // set in offering_manager.php during page load
    var dateEnd = ilios.om.calendarStartDate;
    var offeringModels = sessionModel.getOfferings();

    if (ilios.utilities.arraySize(offeringModels) > 0) {
    var model = null;

    dateBegin = new Date(offeringModels[0].getStartDate().getTime());
    dateEnd = new Date(offeringModels[0].getEndDate().getTime());

        for (var key in offeringModels) {
            model = offeringModels[key];

            if (model.getStartDate() < dateBegin) {
                dateBegin = new Date(model.getStartDate().getTime());
            }
        if (model.getEndDate() > dateEnd) {
            dateEnd = new Date(model.getEndDate().getTime());
        }
        }
    }

    scheduler.setCurrentView(dateBegin, 'week');
};

/**
 * Changes the calendar view to a specified date preserving the present view mode.
 *
 * Should be considered @protected
 */
ilios.om.calendar.focusCalendarOnStartDate = function (startDate) {
    var viewMode = ((ilios.om.calendar.lastModeUsedInAddingEvents != null)
                            ? ilios.om.calendar.lastModeUsedInAddingEvents
                            : 'week');

    scheduler.setCurrentView(startDate, viewMode);
};

/**
 * Given an offering id for a model in the offering table data structure, change the calendar view
 *  to focus on that offering's start date.
 */
ilios.om.calendar.focusCalendarOnStartDateOfOfferingWithId = function (offeringId) {
    var newDate = new Date();
    var model = null;

    // TODO optimize datastructure -- consider storing associated to id
    for (var key in ilios.om.offeringTableDataStructure) {
        model = ilios.om.offeringTableDataStructure[key];

        if (model.id == offeringId) {
            newDate = new Date(model.date.getTime());

            break;
        }
    }

    ilios.om.calendar.focusCalendarOnStartDate(newDate);
};

/**
 * This is messaged via the canvas' onClick which allows us a chance to populate the inspector pane.
 *
 * http://docs.dhtmlx.com/doku.php?id=dhtmlxscheduler:event_onclick
 */
ilios.om.calendar.offeringSelected = function (eventId, domEventElement) {
    var iliosModel = scheduler.getEvent(eventId).iliosModel;

    if (ilios.om.calendar.currentlySelectedIliosModel != null) {
        if (ilios.om.calendar.currentlySelectedIliosModel == iliosModel) {
            return true;
        }

        ilios.om.calendar.currentlySelectedIliosModel.isSelected = false;
        scheduler.updateEvent(ilios.om.calendar.currentlySelectedIliosModel.calendarEventId);
    }

    ilios.om.calendar.currentlySelectedIliosModel = iliosModel;
    ilios.om.calendar.currentlySelectedIliosModel.isSelected = true;
    ilios.om.calendar.currentlySelectedIliosModel.calendarEventId = eventId;

    ilios.om.inspector.populateInspectorForModel(iliosModel);

    if (ilios.om.calendar.lastModeUsedInAddingEvents != 'month') {
        scheduler.updateEvent(eventId);
    } else {
        // known DHTMLX scheduler bug when laying out custom events in month view; was supposed to
        //      be fixed for 2.1, though we're using 2.2 and it's still gefickt
        //  http://forum.dhtmlx.com/viewtopic.php?f=6&t=3435&p=10223&hilit=updateEvent#p10223
        scheduler.render_view_data();
    }

    return true;
};

/**
 * This is messaged via the canvas' onEventAdded which allows us a chance to instantiate
 *  OfferingModel and attach it to the new calendar event.
 *
 * http://docs.dhtmlx.com/doku.php?id=dhtmlxscheduler:event_oneventadded
 */
ilios.om.calendar.offeringAddedViaCalendar = function (eventId, eventObject) {
    if (! ilios.om.calendar.currentlyAddingEvents) {
        var i18nStr = ilios_i18nVendor.getI18NString('general.acronyms.to_be_decided');
        var model = new OfferingModel();

        model.setStartDate(eventObject.start_date);
        model.setEndDate(eventObject.end_date);
        model.setRoom(i18nStr);
        model.setSessionTypeId(sessionModel.getSessionTypeId());
        model.setPublishEventId(sessionModel.getPublishEventId());

        eventObject.iliosType = ilios.om.calendar.generateEventTypeForModel(sessionModel);
        eventObject.iliosModel = model;

        eventObject.text = ilios.om.calendar.generateEventObjectDisplayTextForModel(model);

        ilios.om.transaction.saveCalendarEventToServer(eventObject);
    }
};

/**
 * This is messaged via the canvas' onEventChanged which allows us a chance to pick up date/time
 *  changes of an event due to user drag and modify our OfferingModel accordingly.
 *
 * http://docs.dhtmlx.com/doku.php?id=dhtmlxscheduler:event_oneventchanged
 */
ilios.om.calendar.offeringEditedViaCalendar = function (eventId, eventObject) {
    var model = eventObject.iliosModel;

    model.setStartDate(eventObject.start_date);
    model.setEndDate(eventObject.end_date);

    ilios.om.transaction.saveCalendarEventToServer(eventObject);
};

/**
 * This is messaged via the canvas' onBeforeEventDelete which allows us a chance veto the deletion
 *  attempt of a calendar event (for instance if the event represents a read-only offering) or
 *  otherwise accept the user's action and accordingly delete the event on the server.
 *
 * http://docs.dhtmlx.com/doku.php?id=dhtmlxscheduler:event_onbeforeeventdelete
 */
ilios.om.calendar.offeringWillDeleteViaCalendar = function (eventId, eventObject) {
    if (ilios.om.transaction.calendarEventInDeletionCycle(eventObject)) {
        return true;
    }

    // disallow deletion if the model is read only or represents a number of offerings or a SILM
    if (ilios.om.calendar.modelCannotBeAltered(scheduler.getEvent(eventId).iliosModel)) {
        return false;
    }

    ilios.om.transaction.deleteCalendarEventOnServer(eventObject);

    // returning false vetoes this action (we'll handle the delete from the scheduler on a success
    //                                          from the server transaction)
    return false;
};

/**
 * This is messaged via the canvas' onBeforeDrag which gives the opportunity to veto drag events.
 *  We want to veto events which attempt to drag read-only events and do such here.
 *
 * http://docs.dhtmlx.com/doku.php?id=dhtmlxscheduler:event_onbeforedrag
 */
ilios.om.calendar.calendarDragWillStart = function (eventId, mode, domEventElement) {
    if (mode != 'create') {
        var calendarEvent = scheduler.getEvent(eventId);

        if (calendarEvent != null) {
            var model = calendarEvent.iliosModel;

            if (model != null) {
                return (! ilios.om.calendar.modelCannotBeAltered(model));
            }
        }
    }

    return true;
};

/**
 * This is messaged via the canvas' onViewChange which notifies us that the user's view of the
 *  calendar has changed; we take this opportunity to populate the calendar view with just the
 *  events which are visible in the given time window display (in order to keep the scheduler's
 *  state store as light as possible)
 *
 * http://docs.dhtmlx.com/doku.php?id=dhtmlxscheduler:event_onviewchange
 */
ilios.om.calendar.calendarViewChanged = function (mode, date) {
    var element = document.getElementById('show_sessions_radio');
    var showSessionsOnly = element.checked;
    var startDate = date;

    if (mode == 'week' || mode == 'week_agenda') {
        startDate = ilios.om.calendar.getLastSundayForDate(date);
    } else if (mode == 'month') {
        startDate = ilios.om.calendar.getFirstOfMonthForDate(date);
    }

    ilios.om.calendar.addEventsFromModelToScheduler(startDate, mode, showSessionsOnly);

    element = document.getElementById('show_busy_instructors_checkbox');
    if (element.checked) {
        ilios.om.calendar.deleteAllBusyEventDivsWithUniqueClassName(ilios.om.instructorGroupsUniqueClassName);

        ilios.om.calendar.renderBusyEvents(ilios.om.associatedInstructorGroups,
                                           ilios.om.instructorGroupsUniqueClassName);
    }

    element = document.getElementById('show_busy_students_checkbox');
    if (element.checked) {
        ilios.om.calendar.deleteAllBusyEventDivsWithUniqueClassName(ilios.om.learnersUniqueClassName);

        ilios.om.calendar.renderBusyEvents(ilios.om.associatedLearners,
                                           ilios.om.learnersUniqueClassName);
    }

};

/**
 * This method generates the text which is rendered in the visible calendar event.
 *
 * Should be considered @protected
 */
ilios.om.calendar.generateEventObjectDisplayTextForModel = function (model) {
    var i18nStr = ilios_i18nVendor.getI18NString('general.acronyms.to_be_decided');
    var multipleI18NStr = ilios_i18nVendor.getI18NString('general.terms.multiple');
    var groups = (model.isAggregatePlaceholder ? '' : model.getStudentGroupsAsFormattedText());
    var eventText = ((model.getOwningSessionTitle() == null)
                            ? sessionModel.getTitle()
                            : model.getOwningSessionTitle())
                        + ', '
                        + (model.isAggregatePlaceholder ? multipleI18NStr : model.getRoom()) + ', '
                        + (model.isAggregatePlaceholder ? multipleI18NStr
                                                        : ((groups == '') ? i18nStr : groups));

    return eventText;
};

/**
 * @private
 */
ilios.om.calendar.generateEventTypeForModel = function (model) {
    return ((model.isIndependentLearningEvent && model.isIndependentLearningEvent()) ? 'ilm'
                                                                                     : model.getSessionTypeId());
};

ilios.om.calendar.modelCannotBeAltered = function (model) {
    return (model.isReadOnly() || model.isAggregatePlaceholder || model.isIndependentLearningEvent());
};

/**
 * Given a start date, view mode, and sessions v. all course offerings, add the appropriate
 *      events to the scheduler [in order to keep the internal state of the scheduler at any
 *      given moment as light as possible while still rendering the view correctly].
 *
 * @param viewStartDate will be set to have a UTC time of 00:00:00; if null, then the value used
 *                              when this method was last used will be reused (as will the
 *                              viewMode); since we call a fully populated version of this method
 *                              on DOM ready, the state will never be unprepared.
 * @param viewMode one of the three dhtmlx scheduler mode strings: 'day', 'week', 'month'
 * @param onlySessionEvents if true, only events from the sessionModel will be added, else all
 *                              events from the ilios.om.transaction.loadedOfferingModels will be
 *                              added. (... will be added if they sit within the date range defined
 *                              by viewStartDate and viewMode).
 */
ilios.om.calendar.addEventsFromModelToScheduler = function (viewStartDate, viewMode,
                                                            onlySessionEvents) {
    var modelsToAdd = new Array();
    var modelArray = (onlySessionEvents ? sessionModel.getOfferings()
                                        : ilios.om.transaction.loadedOfferingModels);
    var model = null;
    var nextModel = null;
    var viewEndDate = null;
    var i = 0;
    var k = 0;
    var len = 0;
    var startDateToUse = null;

    if (viewStartDate == null) {
        startDateToUse = new Date(ilios.om.calendar.lastStartDateUsedInAddingEvents.getTime());
        viewMode = ilios.om.calendar.lastModeUsedInAddingEvents;
    }
    else {
        startDateToUse = new Date(viewStartDate.getTime());
    }

    viewEndDate = ilios.om.calendar.getCurrentViewEndDate(startDateToUse, viewMode);

    startDateToUse.setHours(0, 0, 0, 0);

    for (var key in modelArray) {
        model = modelArray[key];

        if ((model.getStartDate() > startDateToUse) && (model.getStartDate() < viewEndDate)) {
            modelsToAdd.push(model);
        }
    }

    modelsToAdd.sort(ilios.om.calendar.offeringModelComparator);

    scheduler.clearAll();

    len = modelsToAdd.length;
    ilios.om.calendar.currentlyAddingEvents = true;

    // condense multiple same-time-same-day-same-session events into single ones in
    //      non-day view
    for (i = 0; i < len; i++) {
        model = modelsToAdd[i];

        if (viewMode != 'day') {
            for (k = (i + 1); k < len; k++) {
                nextModel = modelsToAdd[k];

                if (! nextModel.isSchedulerEqual(model)) {
                    break;
                }
            }

            if (i != (k - 1)) {
                nextModel = new OfferingModel();
                nextModel.readOnly = model.isReadOnly();
                nextModel.ilmEvent = model.isIndependentLearningEvent();
                nextModel.owningSessionTitle = (model.getOwningSessionTitle() == null)
                                                    ? sessionModel.getTitle()
                                                    : model.getOwningSessionTitle();
                nextModel.setStartDate(model.getStartDate());
                nextModel.setEndDate(model.getEndDate());
                nextModel.setSessionId(model.getSessionId());
                nextModel.setSessionTypeId(model.getSessionTypeId());
                nextModel.setOwningSessionTitle(model.getOwningSessionTitle());

                nextModel.isAggregatePlaceholder = true;

                i = (k - 1);
            }
            else {
                nextModel = model;
            }
        }
        else {
            nextModel = model;
        }

        scheduler.addEvent(model.getStartDate(), model.getEndDate(),
                           ilios.om.calendar.generateEventObjectDisplayTextForModel(nextModel),
                           model.getDBId(),
                           { iliosModel: nextModel,
                             iliosType: ilios.om.calendar.generateEventTypeForModel(model) });
    }
    ilios.om.calendar.currentlyAddingEvents = false;

    ilios.om.calendar.lastStartDateUsedInAddingEvents = startDateToUse;
    ilios.om.calendar.lastEndDateUsedInAddingEvents = viewEndDate;
    ilios.om.calendar.lastModeUsedInAddingEvents = viewMode;
};

/**
 * This is called on a successful save of an offering where its recurrence has been saved for the
 *  first time.
 *
 * Should be considered @protected
 */
ilios.om.calendar.createEventsResultingFromRecurrence = function (offeringModel) {
    var recurringEventModel = offeringModel.getRecurringEvent();
    var currentDate = new Date(offeringModel.getStartDate().getTime());
    var currentDay = currentDate.getDay();
    var currentEndDate = new Date(offeringModel.getEndDate().getTime());
    var patternEndDate = new Date(recurringEventModel.getEndDate().getTime());
    var dayPattern = recurringEventModel.getRawEventDays();
    var startEndDayDelta =
            Math.floor((currentEndDate.getTime() - currentDate.getTime()) / (1000 * 60 * 60 * 24));
    var newOfferingModel = null;
    var newCalendarEvent = null;
    var model = null;
    var newEventId = null;

    patternEndDate.setHours(offeringModel.getEndDate().getHours(),
                            offeringModel.getEndDate().getMinutes(),
                            offeringModel.getEndDate().getSeconds(),
                            offeringModel.getEndDate().getMilliseconds());
    currentDate.setDate(currentDate.getDate()
                            + ilios.om.calendar.getDayDeltaToNextRecurrence(currentDay,
                                                                            dayPattern));

    while (currentDate <= patternEndDate) {
        currentEndDate.setFullYear(currentDate.getFullYear(), currentDate.getMonth(),
                                   currentDate.getDate());
        currentEndDate.setDate(currentEndDate.getDate() + startEndDayDelta);

        newOfferingModel = offeringModel.clone();
        newOfferingModel.setDBId(-1);
        newOfferingModel.setStartDate(currentDate);
        newOfferingModel.setEndDate(currentEndDate);

        sessionModel.addOffering(newOfferingModel);

        newEventId = scheduler.uid();
        ilios.om.calendar.currentlyAddingEvents = true;
        scheduler.addEvent(newOfferingModel.getStartDate(), newOfferingModel.getEndDate(),
                           ilios.om.calendar.generateEventObjectDisplayTextForModel(newOfferingModel),
                           newEventId,
                           { iliosModel: newOfferingModel,
                             iliosType: ilios.om.calendar.generateEventTypeForModel(newOfferingModel) });
        ilios.om.calendar.currentlyAddingEvents = false;

        newCalendarEvent = scheduler.getEvent(newEventId);

        ilios.om.transaction.saveCalendarEventToServer(newCalendarEvent);

        currentDay = currentDate.getDay();
        currentDate.setDate(currentDate.getDate()
                                + ilios.om.calendar.getDayDeltaToNextRecurrence(currentDay,
                                                                                dayPattern));
    }
};

/**
 * This is a helper method to calculate how many days are between the current day (day, not date)
 *  and the next day (day, not date) of an offering as defined by the pattern.
 *
 * Should be considered @private
 */
ilios.om.calendar.getDayDeltaToNextRecurrence = function (currentDay, dayPattern) {
    var rhett = (currentDay + 1);

    while (dayPattern[(rhett % 7)] == 0) {
        rhett++;
    }

    return (rhett - currentDay);
};

/**
 * @return a javascript Date instance for 00:00:00 (local TZ) last Sunday (or the param, if it's Sunday)
 *
 * Should be considered @private
 */
ilios.om.calendar.getLastSundayForDate = function (originalDate) {
    var rhett = new Date();

    rhett.setFullYear(originalDate.getFullYear(), originalDate.getMonth(),
                         originalDate.getDate());

    if (rhett.getDay() != 0) {
        rhett.setDate(rhett.getDate() - rhett.getDay());
    }

    rhett.setHours(0, 0, 0, 0);

    return rhett;
};

/**
 * @return a javascript Date instance for 00:00:00 (local TZ) on the first day of the month of the param
 *
 * Should be considered @private
 */
ilios.om.calendar.getFirstOfMonthForDate = function (originalDate) {
    var rhett = new Date();

    rhett.setFullYear(originalDate.getFullYear(), originalDate.getMonth(),
                         originalDate.getDate());
    rhett.setDate(1);
    rhett.setHours(0, 0, 0, 0);

    return rhett;
};

/**
 * This is a helper method used to calculate where, in terms of the spatial coordinates of the
 *  parentContainer laid out in the browser page UI, a given date range should sit.
 *
 * @return an array with keys 'x' 'y' 'w' 'h', or null if the dates would be non-visible
 *
 * Should be considered @private
 */
ilios.om.calendar.getSchedulerSpatialLocationForDateRange = function (parentContainer, startDate,
                                                                      endDate) {
    var rhett = new Array();
    var startMinutes = (startDate.getHours() * 60) + startDate.getMinutes();
    var endMinutes = (endDate.getHours() * 60) + endDate.getMinutes();
    var top
        = Math.round(((startMinutes * 60 * 1000) - (scheduler.config.first_hour * 60 * 60 * 1000))
                            * scheduler.config.hour_size_px / (60 * 60 * 1000))
            % (scheduler.config.hour_size_px * 24) + 1; //42px/hour
    var height = Math.max(scheduler.xy.min_event_height,
                          ((endMinutes - startMinutes) * (scheduler.config.hour_size_px / 60)))
                    + 1; //42px/hour

    rhett['x'] = 1;
    rhett['y'] = top;
    rhett['w'] = parentContainer.clientWidth - 8;   // 8 assumes a uniform padding of 3px
    rhett['h'] = height;

    return rhett;
};

/**
 * This method clears and then inserts appropriate DIVs into the scheduler canvas in order to
 *  render busy times in the calendar display.
 *
 * @param busyArray a non-associative array of Object instances; each instance will have the
 *                      following properties:
 *      .displayName
 *      .startDate
 *      .endDate
 *      .sessionTypeId
 * @param uniqueClassFamilyName typically one of the constants defined in the offering_manager_dom
 *                                  javascript file (ilios.om.instructorGroupsUniqueClassName or
 *                                  ilios.om.learnersUniqueClassName).
 *
 * Should be considered @protected
 */
ilios.om.calendar.renderBusyEvents = function (busyArray, uniqueClassFamilyName) {
    var divElement = null;
    var textNode = null;
    var busyPeriod = null;
    var parent = null;
    var renderLocation = null;

    ilios.om.calendar.deleteAllBusyEventDivsWithUniqueClassName(uniqueClassFamilyName);

    for (var key in busyArray) {
        busyPeriod = busyArray[key];

        // todo multi-day support - including checking for current view as follows
        if (! ilios.om.calendar.isDateInCurrentView(busyPeriod.startDate)) {
            continue;
        }

        parent = scheduler.locate_holder(busyPeriod.startDate.getDay());

        if (parent != null) {   // null => attempt to render non-visible event
            renderLocation
                = ilios.om.calendar.getSchedulerSpatialLocationForDateRange(parent,
                                                                            busyPeriod.startDate,
                                                                            busyPeriod.endDate);

            divElement = document.createElement('div');
            divElement.setAttribute('style',
                                    'top: ' + renderLocation['y'] + 'px; '
                                        + 'left: ' + renderLocation['x'] + 'px; '
                                        + 'width: ' + renderLocation['w'] + 'px; '
                                        + 'height: ' + renderLocation['h'] + 'px;');
            divElement.setAttribute('class',
                                    'busy_time_calendar_div ' + uniqueClassFamilyName);

            textNode = document.createTextNode(busyPeriod.displayName);
            divElement.appendChild(textNode);

            parent.appendChild(divElement);
        }
    }
};

/**
 * @param date a javascript date object
 *
 * Should be considered @private
 */
ilios.om.calendar.isDateInCurrentView = function (date) {
    return ((date > ilios.om.calendar.lastStartDateUsedInAddingEvents)
                                    && (date < ilios.om.calendar.lastEndDateUsedInAddingEvents));
};

/**
 * @param viewStartDate if null, ilios.om.calendar.lastStartDateUsedInAddingEvents
 *                                  & ilios.om.calendar.lastModeUsedInAddingEvents
 *                          will be used
 *
 * Should be considered @private
 */
ilios.om.calendar.getCurrentViewEndDate = function (viewStartDate, viewMode) {
    var rhett = null;

    if (viewStartDate == null) {
        viewStartDate = ilios.om.calendar.lastStartDateUsedInAddingEvents;
        viewMode = ilios.om.calendar.lastModeUsedInAddingEvents;
    }

    rhett = new Date(viewStartDate.getTime());
    rhett.setHours(0, 0, 0, 0);

    if (viewMode == 'week' || viewMode == 'week_agenda') {
        rhett.setDate(viewStartDate.getDate() + 7);
    } else if (viewMode == 'day') {
        rhett.setDate(viewStartDate.getDate() + 1);
    } else {
        rhett.setMonth(viewStartDate.getMonth() + 1);
    }

    return rhett;
};

/**
 * Removes all busy event divs from the DOM.
 *
 * @param uniqueClassFamilyName typically one of the constants defined in the offering_manager_dom
 *                                  javascript file (ilios.om.instructorGroupsUniqueClassName or
 *                                  ilios.om.learnersUniqueClassName).
 *
 * Should be considered @protected
 */
ilios.om.calendar.deleteAllBusyEventDivsWithUniqueClassName = function (uniqueClassFamilyName) {
    var divSet = YAHOO.util.Dom.getElementsByClassName(uniqueClassFamilyName, '', 'dhx_cal_data');
    var div = null;

    for (var key in divSet) {
        div = divSet[key];

        div.parentNode.removeChild(div);
    }
};

/**
 * This function will be passed to Array.sort() when an array instance is homogenous in
 *  OfferingModel and we want that array sorted for earliest start -> latest start; in the case
 *  of two OM with the same start, and a secondary comparison is performed to sort the one with the
 *  earlier end-date as the 'before' element.
 *
 * @private
 */
ilios.om.calendar.offeringModelComparator = function (om1, om2) {
    var t1 = om1.getStartDate().getTime();
    var t2 = om2.getStartDate().getTime();

    if (t1 != t2) {
        return t1 - t2;
    }

    t1 = om1.getEndDate().getTime();
    t2 = om2.getEndDate().getTime();

    if (t1 != t2) {
        return t1 - t2;
    }

    return parseInt(om1.getSessionId()) - parseInt(om2.getSessionId());
};
