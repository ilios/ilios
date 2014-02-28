/**
 * This file contains all of the server transactions related to the offering management page; all
 * transactions reflect that they are midst action via the display of an indeterminate progress
 * indicator on the page.
 */

/*
 * DEPENDENCY: scripts/ilios_ui.js
 * DEPENDENCY: scripts/ilios_utilities.js
 */

ilios.namespace('om.transaction');

ilios.om.transaction.loadedOfferingModels = [];

// This array holds models for the short lifespan of an ajax return notification of successful
// delete and when the associated calendar event is removed from the DHTMLX model/canvas
ilios.om.transaction.successfulDeletions = [];

/*
 * We use this as an internal notification scheme to denote that we have a received a successful
 * response from the server concerning an offering delete and so that it's ok to now remove
 * that event from the calendar.
 *
 * Should be considered @private
 *
 * @see ilios.om.calendar.offeringWillDeleteViaCalendar
 * @see ilios.om.transaction.deleteCalendarEventOnServer
 */
ilios.om.transaction.successfulDeletionReceived = function (calendarEvent) {
    ilios.om.transaction.successfulDeletions[calendarEvent.id] = calendarEvent;
};

/*
 * We use this as an internal notification scheme to denote that we have a received a successful
 * response from the server concerning an offering delete and so that it's ok to now remove
 * that event from the calendar.
 *
 * Should be considered @private
 *
 * @see ilios.om.calendar.offeringWillDeleteViaCalendar
 * @see ilios.om.transaction.deleteCalendarEventOnServer
 */
ilios.om.transaction.doneWithDeletionCycle = function (calendarEvent) {
    delete ilios.om.transaction.successfulDeletions[calendarEvent.id];
};

/*
 * We use this as an internal notification scheme to denote that we have a received a successful
 * response from the server concerning an offering delete and so that it's ok to now remove
 * that event from the calendar.
 *
 * Should be considered @private
 *
 * @see ilios.om.calendar.offeringWillDeleteViaCalendar
 * @see ilios.om.transaction.deleteCalendarEventOnServer
 */
ilios.om.transaction.calendarEventInDeletionCycle = function (calendarEvent) {
    for (var key in ilios.om.transaction.successfulDeletions) {
        if (ilios.om.transaction.successfulDeletions[key] == calendarEvent) {
            return true;
        }
    }

    return false;
};

/**
 * This starts the AJAX-ian server transaction to delete the offering associated to the param'd
 * calendar event. A successful response from the transaction will remove the offering model
 * from current state structures and remove the associated calendar event from the DHTMLX
 * scheduler canvas.
 */
ilios.om.transaction.deleteCalendarEventOnServer = function (calendarEvent) {
    var url = controllerURL + 'deleteOffering';
    var method = "POST";
    var offeringModel = calendarEvent.iliosModel;
    var paramString = "oid=" + offeringModel.getDBId() + "&calendar_id=" + calendarEvent.id;
    var ajaxCallback = {
        success: function (resultObject) {
            var parsedObject = null;
            var calendarEvent = null;
            var newOfferingTableData = [];
            var refreshDataTable = false;

            ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                'offering_management.saving_offering');
            ilios.alert.updateServerInteractionProgress();

            try {
                parsedObject = YAHOO.lang.JSON.parse(resultObject.responseText);
            }
            catch (e) {
                ilios.global.defaultAJAXFailureHandler(null, e);

                return;
            }

            calendarEvent = scheduler.getEvent(parsedObject.calendar_id);

            // MAY RETURN THIS BLOCK
            if (parsedObject.error != null) {
                var msg
                    = ilios_i18nVendor.getI18NString('offering_management.error.failed_delete');

                ilios.alert.alert(msg + ": " + parsedObject.error);

                return;
            }

            ilios.om.transaction.successfulDeletionReceived(calendarEvent);

            sessionModel.removeOffering(calendarEvent.iliosModel);

            for (var key in ilios.om.offeringTableDataStructure) {
                if (ilios.om.offeringTableDataStructure[key].id
                    != calendarEvent.iliosModel.getDBId()) {
                    newOfferingTableData.push(ilios.om.offeringTableDataStructure[key]);
                }
            }

            if (newOfferingTableData.length != ilios.om.offeringTableDataStructure) {
                ilios.om.offeringTableDataStructure = newOfferingTableData;

                refreshDataTable = true;
            }

            newOfferingTableData = [];
            for (var key in ilios.om.sessionOnlyOfferingTableDataStructure) {
                if (ilios.om.sessionOnlyOfferingTableDataStructure[key].id
                    != calendarEvent.iliosModel.getDBId()) {
                    newOfferingTableData.push(ilios.om.sessionOnlyOfferingTableDataStructure[key]);
                }
            }

            if (newOfferingTableData.length != ilios.om.sessionOnlyOfferingTableDataStructure) {
                ilios.om.sessionOnlyOfferingTableDataStructure = newOfferingTableData;

                refreshDataTable = true;
            }

            if (refreshDataTable) {
                ilios.om.forceDataTableRefresh();
            }

            for (var key in ilios.om.transaction.loadedOfferingModels) {
                if (ilios.om.transaction.loadedOfferingModels[key].getDBId()
                    == calendarEvent.iliosModel.getDBId()) {
                    delete ilios.om.transaction.loadedOfferingModels[key];

                    break;
                }
            }

            scheduler.deleteEvent(parsedObject.calendar_id);

            ilios.om.transaction.doneWithDeletionCycle(calendarEvent);
        },

        failure: function (resultObject) {
            ilios.global.defaultAJAXFailureHandler(resultObject);
        }};

    ilios.alert.networkActivityI18NStrings.push('offering_management.saving_offering');
    ilios.alert.updateServerInteractionProgress();

    YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);
};

/**
 * This starts the AJAX-ian save transaction with the server to store the offering associated to
 * the calendar event.
 */
ilios.om.transaction.saveCalendarEventToServer = function (calendarEvent) {
    var url = controllerURL + 'saveOffering',
        method = "POST",
        offeringModel = calendarEvent.iliosModel,
        startDate = ilios.utilities.dateObjectToMySQLFriendly(offeringModel.getStartDate(), true),
        endDate = ilios.utilities.dateObjectToMySQLFriendly(offeringModel.getEndDate(), true),
        paramString = "sid=" + sessionModel.getDBId() + "&start_date=" + encodeURIComponent(startDate)
            + "&end_date=" + encodeURIComponent(endDate)
            + "&location=" + encodeURIComponent(offeringModel.getRoom())
            + "&offering_id=" + offeringModel.getDBId()
            + "&calendar_id=" + calendarEvent.id
            + "&parent_publish_event_id=" + offeringModel.getPublishEventId()
            + "&is_recurring=" + (offeringModel.isRecurring() ? 'true' : 'false'),
        replacer = ilios.utilities.yahooJSONStringifyStateChangeListenerArgumentsReplacer,
        stringify = ilios.utilities.stringifyObjectAsArray;

    var ajaxCallback = {
        success: function (resultObject) {
            var parsedObject = null;
            var calendarEvent = null;
            var modelIsAlreadyInDataTable = false;
            var iliosModel = null;

            ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                'offering_management.saving_offering');
            ilios.alert.updateServerInteractionProgress();

            try {
                parsedObject = YAHOO.lang.JSON.parse(resultObject.responseText);
            }
            catch (e) {
                ilios.global.defaultAJAXFailureHandler(null, e);

                return;
            }

            calendarEvent = scheduler.getEvent(parsedObject.calendar_id);
            iliosModel = calendarEvent.iliosModel;

            // MAY RETURN THIS BLOCK
            if (parsedObject.error != null) {
                var msg
                    = ilios_i18nVendor.getI18NString('offering_management.error.failed_save');

                ilios.alert.alert(msg + ": " + parsedObject.error);

                // TODO what do we do with the calendar event? remove it from the scheduler?
                //                mark it with an exclamation mark? ... ?

                return;
            }

            iliosModel.setDBId(parsedObject.offering_id);
            iliosModel.setRoom(parsedObject.location);

            sessionModel.addOffering(iliosModel);

            for (var key in ilios.om.offeringTableDataStructure) {
                if (ilios.om.offeringTableDataStructure[key].id == parsedObject.offering_id) {
                    var model = ilios.om.offeringTableDataStructure[key];

                    modelIsAlreadyInDataTable = true;

                    model.date = iliosModel.getStartDate();
                    model.group = iliosModel.getStudentGroupsAsFormattedText();
                    model.instructor = iliosModel.getInstructorsAsFormattedText();
                    model.time = ilios.utilities.getTimeRangeString(iliosModel.getStartDate(),
                        iliosModel.getEndDate());
                    model.location = iliosModel.getRoom();
                    model.status = (iliosModel.getPublishEventId() == -1)
                        ? ilios_i18nVendor.getI18NString('general.terms.draft')
                        : ilios_i18nVendor.getI18NString('general.terms.published');

                    break;
                }
            }

            if (!modelIsAlreadyInDataTable) {
                var model = {};

                model.id = parsedObject.offering_id;
                model.date = iliosModel.getStartDate();
                model.group = iliosModel.getStudentGroupsAsFormattedText();
                model.instructor = iliosModel.getInstructorsAsFormattedText();
                model.time = ilios.utilities.getTimeRangeString(iliosModel.getStartDate(),
                    iliosModel.getEndDate());
                model.location = iliosModel.getRoom();
                model.status = (iliosModel.getPublishEventId() == -1)
                    ? ilios_i18nVendor.getI18NString('general.terms.draft')
                    : ilios_i18nVendor.getI18NString('general.terms.published');

                ilios.om.offeringTableDataStructure.push(model);
                ilios.om.sessionOnlyOfferingTableDataStructure.push(model);
            }

            modelIsAlreadyInDataTable = false;
            for (var key in ilios.om.transaction.loadedOfferingModels) {
                if (ilios.om.transaction.loadedOfferingModels[key].getDBId()
                    == iliosModel.getDBId()) {
                    modelIsAlreadyInDataTable = true;

                    break;
                }
            }

            if (!modelIsAlreadyInDataTable) {
                ilios.om.transaction.loadedOfferingModels.push(iliosModel);
            }

            if (ilios.om.inspector.presentlyInspectedOfferingModel == iliosModel) {
                ilios.om.inspector.populateInspectorForModel(iliosModel);
            }

            if (iliosModel.isRecurring()) {
                iliosModel.getRecurringEvent().setDBId(parsedObject.recurring_event_id);

                if (parsedObject.recurring_event_was_added == 'true') {
                    ilios.om.calendar.createEventsResultingFromRecurrence(iliosModel);
                }
            }

            ilios.om.forceDataTableRefresh();
        },

        failure: function (resultObject) {
            ilios.global.defaultAJAXFailureHandler(resultObject);
        }};
    var tmpArray = offeringModel.getInstructors();
    var gids = '';
    var groupModel = null;

    paramString += "&instructors="
        + encodeURIComponent(stringify(tmpArray, replacer));

    tmpArray = offeringModel.getStudentGroups();
    for (var key in tmpArray) {
        if (gids.length > 0) {
            gids += ',';
        }

        groupModel = tmpArray[key];
        gids += groupModel.getDBId();
    }
    paramString += "&student_group_ids=" + gids;

    if (offeringModel.isRecurring()) {
        // force calculation of end date prior to json-ing
        offeringModel.getRecurringEvent().generateMySQLEndDate();

        paramString += "&recurring_event="
            + encodeURIComponent(YAHOO.lang.JSON.stringify(offeringModel.getRecurringEvent()));
    }

    ilios.alert.networkActivityI18NStrings.push('offering_management.saving_offering');
    ilios.alert.updateServerInteractionProgress();

    YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);
};

/**
 * This method is called via onDomReady and loads all offerings associated to the course which
 * is the parent of the session-in-edit. In handling a successful transaction from the server,
 * the code instantiates the offering models as appropriate, adding them to the session model
 * as appropriate. It finished the handling by making sure that the both the calendar view
 * and the data table view are appropriately updated.
 */
ilios.om.transaction.loadAllOfferings = function () {
    var url = controllerURL + 'loadOfferingsForCourse';
    var method = "POST";
    var paramString = "course_id=" + sessionModel.getCourseId();
    var ajaxCallback = {
        success: function (resultObject) {
            var parsedObject = null;
            var offerings = null;
            var instructors = null;
            var learnerGroups = null;
            var offering = null;
            var instructor = null;
            var learnerGroup = null;
            var offeringModel = null;
            var i = 0;
            var j = 0;
            var len = 0;
            var len2 = 0;
            var model = null;
            var draftStr = ilios_i18nVendor.getI18NString('general.terms.draft');
            var publishStr = ilios_i18nVendor.getI18NString('general.terms.published');

            try {
                parsedObject = YAHOO.lang.JSON.parse(resultObject.responseText);
            }
            catch (e) {
                ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                    'general.phrases.loading_offerings');
                ilios.alert.updateServerInteractionProgress();

                ilios.global.defaultAJAXFailureHandler(null, e);

                return;
            }

            // MAY RETURN THIS BLOCK
            if (parsedObject.error != null) {
                var msg
                    = ilios_i18nVendor.getI18NString('offering_management.error.failed_load');

                ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                    'general.phrases.loading_offerings');
                ilios.alert.updateServerInteractionProgress();

                ilios.alert.alert(msg + ": " + parsedObject.error);

                return;
            }

            ilios.om.offeringTableDataStructure = [];

            offerings = parsedObject.offerings;
            len = offerings.length;
            for (; i < len; i++) {
                offering = offerings[i];

                offeringModel = new OfferingModel(offering);

                if (offering.recurring_event != null) {
                    offeringModel.setRecurring(true);

                    model = new RecurringEventModel(offering.recurring_event);
                    model.setStartDate(offeringModel.getStartDate());

                    offeringModel.setRecurringEvent(model);
                }

                instructors = offering.instructors;
                j = 0;
                len2 = instructors.length;
                for (; j < len2; j++) {
                    instructor = instructors[j];

                    if (instructor.instructor_group_id != null) {
                        model = {};
                        model.isGroup = true;
                        model.dbId = instructor.instructor_group_id;
                        model.title = instructor.title;
                    }
                    else {
                        model = new UserModel(instructor);
                        model.isGroup = false;
                    }

                    offeringModel.addInstructor(model);
                }

                learnerGroups = offering.learner_groups;
                j = 0;
                len2 = learnerGroups.length;
                for (; j < len2; j++) {
                    learnerGroup = learnerGroups[j];

                    model = new SimplifiedGroupModel(learnerGroup.title,
                        ((learnerGroup.parent_group_id == null)
                            ? -1
                            : learnerGroup.parent_group_id),
                        learnerGroup.group_id);

                    offeringModel.addStudentGroup(model);
                }

                if (offering.session_id == sessionModel.getDBId()) {
                    sessionModel.addOffering(offeringModel);
                }
                else {
                    offeringModel.setReadOnly(true);
                    offeringModel.setOwningSessionTitle(offering.session_title);
                }

                ilios.om.transaction.loadedOfferingModels.push(offeringModel);

                model = {};
                model.id = offeringModel.getDBId();
                model.date = offeringModel.getStartDate();
                model.group = offeringModel.getStudentGroupsAsFormattedText();
                model.instructor = offeringModel.getInstructorsAsFormattedText();
                model.time = ilios.utilities.getTimeRangeString(offeringModel.getStartDate(),
                    offeringModel.getEndDate());
                model.location = offeringModel.getRoom();
                model.status = (offeringModel.getPublishEventId() == -1) ? draftStr
                    : publishStr;

                ilios.om.offeringTableDataStructure.push(model);

                if (offeringModel.getSessionId() == sessionModel.getDBId()) {
                    ilios.om.sessionOnlyOfferingTableDataStructure.push(model);
                }
            }

            ilios.om.forceDataTableRefresh();

            ilios.om.calendar.resetCurrentCalendarViewToStart();

            ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                'general.phrases.loading_offerings');
            ilios.alert.updateServerInteractionProgress();
        },

        failure: function (resultObject) {
            ilios.global.defaultAJAXFailureHandler(resultObject);
        }};

    ilios.alert.networkActivityI18NStrings.push('general.phrases.loading_offerings');
    ilios.alert.updateServerInteractionProgress();

    YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);
}

/**
 * This is called in response to a user action ticking the checkbox asking to display the busy
 * time for the associated instructors. Since the results of this request can change given
 * modifications the user is doing in this page, and/or due to concurrent modification by
 * another user, this [AJAX-ian] transaction is done each time the user clicks the checkbox.
 */
ilios.om.transaction.fetchAssociatedInstructorsBusyTime = function () {
    var url = controllerURL + 'getOfferingsForAllInstructorsInSession';
    var method = "POST";
    var paramString = "session_id=" + sessionModel.getDBId();
    var ajaxCallback = {
        success: function (resultObject) {
            var parsedObject = null;
            var instructors = null;
            var offerings = null;
            var instructor = null;
            var offering = null;
            var displayName = null;
            var model = null;
            var recurringEventModel = null;

            try {
                parsedObject = YAHOO.lang.JSON.parse(resultObject.responseText);
            }
            catch (e) {
                ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                    'offering_management.busy_instructor_fetch');
                ilios.alert.updateServerInteractionProgress();

                ilios.global.defaultAJAXFailureHandler(null, e);

                return;
            }

            // MAY RETURN THIS BLOCK
            if (parsedObject.error != null) {
                var msg
                    = ilios_i18nVendor
                    .getI18NString('offering_management.error.failed_busy_instructor');

                ilios.alert.alert(msg + ": " + parsedObject.error);

                ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                    'offering_management.busy_instructor_fetch');
                ilios.alert.updateServerInteractionProgress();

                return;
            }

            ilios.om.associatedInstructorGroups = [];
            instructors = parsedObject.instructors;
            for (var key in instructors) {
                instructor = instructors[key];

                if (instructor.user_id != null) {
                    model = new UserModel(instructor);
                    displayName = model.getFormattedName(ilios.utilities.UserNameFormatEnum.LAST_FIRST);
                }
                else {
                    displayName = instructor.title;
                }

                offerings = instructor.offerings;
                for (var key2 in offerings) {
                    offering = offerings[key2];

                    model = {};
                    model.displayName = displayName;
                    model.sessionTypeId = offering.session_type_id;
                    model.startDate = ilios.utilities.mySQLDateToDateObject(offering.start_date,
                        true);
                    model.endDate = ilios.utilities.mySQLDateToDateObject(offering.end_date,
                        true);

                    if (offering.recurring_event != null) {
                        recurringEventModel = new RecurringEventModel(offering.recurring_event);
                        recurringEventModel.setStartDate(model.startDate);

                        model.recurringEvent = recurringEventModel;
                    }

                    ilios.om.associatedInstructorGroups.push(model);
                }
            }

            ilios.om.calendar.renderBusyEvents(ilios.om.associatedInstructorGroups,
                ilios.om.instructorGroupsUniqueClassName);

            ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                'offering_management.busy_instructor_fetch');
            ilios.alert.updateServerInteractionProgress();
        },

        failure: function (resultObject) {
            ilios.global.defaultAJAXFailureHandler(resultObject);
        }};

    ilios.alert.networkActivityI18NStrings.push('offering_management.busy_instructor_fetch');
    ilios.alert.updateServerInteractionProgress();

    YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);
};

/**
 * This is called in response to a user action ticking the checkbox asking to display the busy
 * time for the associated student groups. Since the results of this request can change given
 * modifications the user is doing in this page, and/or due to concurrent modification by
 * another user, this [AJAX-ian] transaction is done each time the user clicks the checkbox.
 */
ilios.om.transaction.fetchAssociatedLearnerBusyTime = function () {
    var url = controllerURL + 'getOfferingsForAllLearnersInSession';
    var method = "POST";
    var paramString = "session_id=" + sessionModel.getDBId();
    var ajaxCallback = {
        success: function (resultObject) {
            var parsedObject = null;
            var learners = null;
            var offerings = null;
            var learner = null;
            var offering = null;
            var displayName = null;
            var model = null;
            var recurringEventModel = null;

            try {
                parsedObject = YAHOO.lang.JSON.parse(resultObject.responseText);
            }
            catch (e) {
                ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                    'offering_management.busy_learner_fetch');
                ilios.alert.updateServerInteractionProgress();

                ilios.global.defaultAJAXFailureHandler(null, e);

                return;
            }

            // MAY RETURN THIS BLOCK
            if (parsedObject.error != null) {
                var msg
                    = ilios_i18nVendor
                    .getI18NString('offering_management.error.failed_busy_learner');

                ilios.alert.alert(msg + ": " + parsedObject.error);

                ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                    'offering_management.busy_learner_fetch');
                ilios.alert.updateServerInteractionProgress();

                return;
            }

            ilios.om.associatedLearners = [];
            learners = parsedObject.learners;
            for (var key in learners) {
                learner = learners[key];

                if (learner.user_id != null) {
                    model = new UserModel(learner);
                    displayName = model.getFormattedName(ilios.utilities.UserNameFormatEnum.LAST_FIRST);
                }
                else {
                    displayName = learner.title;
                }

                offerings = learner.offerings;
                for (var key2 in offerings) {
                    offering = offerings[key2];

                    model = {};
                    model.displayName = displayName;
                    model.sessionTypeId = offering.session_type_id;
                    model.startDate = ilios.utilities.mySQLDateToDateObject(offering.start_date,
                        true);
                    model.endDate = ilios.utilities.mySQLDateToDateObject(offering.end_date,
                        true);

                    if (offering.recurring_event != null) {
                        recurringEventModel = new RecurringEventModel(offering.recurring_event);
                        recurringEventModel.setStartDate(model.startDate);

                        model.recurringEvent = recurringEventModel;
                    }

                    ilios.om.associatedLearners.push(model);
                }
            }

            ilios.om.calendar.renderBusyEvents(ilios.om.associatedLearners,
                ilios.om.learnersUniqueClassName);

            ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                'offering_management.busy_learner_fetch');
            ilios.alert.updateServerInteractionProgress();
        },

        failure: function (resultObject) {
            ilios.global.defaultAJAXFailureHandler(resultObject);
        }};

    ilios.alert.networkActivityI18NStrings.push('offering_management.busy_learner_fetch');
    ilios.alert.updateServerInteractionProgress();

    YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);
};
