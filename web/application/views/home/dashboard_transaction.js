/**
 * This file contains all of the server transactions related to the offering management page; all
 *  transactions reflect that they are midst action via the display of an indeterminate progress
 *  indicator on the page.
 */

/*
 * DEPENDENCY: scripts/ilios_ui.js
 * DEPENDENCY: scripts/ilios_utilities.js
 * DEPENDENCY: home/dashboard_calendar_support.js
 */



ilios.namespace('home.transaction');


ilios.home.transaction.loadedOfferingModels = [];

ilios.home.transaction.midLoadCourseHolder = null;

ilios.home.transaction.activeRequests = [];


/**
 * This method is called via onDomReady and loads all calendar events (except for Session
 *  Independent Learning events) for the user viewing the home page.
 */
ilios.home.transaction.loadAllOfferings = function (filters) {
    var url = controllerURL;
    var method = "POST";
    var paramString = '';

    //onDOMReady uses the Custom Event signature, with the object
    //passed in as the third argument:
    if (arguments.length > 2) {
        filters = arguments[2];
    }

    if (null != filters) {
        paramString = "filters=" + YAHOO.lang.JSON.stringify( filters );
    }

    var ajaxCallback = {
            success: function (resultObject) {
                var parsedObject = null;
                var offerings = null;
                var offering = null;
                var offeringModel = null;
                var i = 0;
                var len = 0;
                var model = null;
                var courseLoadModel = null;

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

                ilios.home.transaction.loadedOfferingModels = [];

                if (! pageLoadedForStudent) {
                    ilios.home.transaction.midLoadCourseHolder = [];
                }

                offerings = parsedObject.offerings;
                len = offerings.length;
                for (; i < len; i++) {
                    offering = offerings[i];

                    if (! pageLoadedForStudent
                        && (typeof ilios.home.transaction.midLoadCourseHolder[offering.course_id] == "undefined" )) {

                    	courseLoadModel = {};
                        courseLoadModel.title = offering.course_title;
                        courseLoadModel.id = offering.course_id;
                        courseLoadModel.instructor = true;

                        ilios.home.transaction.midLoadCourseHolder[offering.course_id]
                                                                                = courseLoadModel;
                    }

                    offeringModel = new CalendarItemModel(offering);

                    ilios.home.transaction.loadedOfferingModels.push(offeringModel);
                }

                var filters = null;

                if ('' != resultObject.argument.filtersParamString) {
                    // Assuming the first token is 'filters'
                    filters = YAHOO.lang.JSON.parse(resultObject.argument.filtersParamString.split("=")[1]);
                }

                ilios.home.transaction.loadAllSILMs(filters);

                ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                                                       'general.phrases.loading_offerings');
                ilios.alert.updateServerInteractionProgress();

                ilios.home.transaction.activeRequests['loadAllOfferings'] = null;
            },

            failure: function (resultObject) {
                ilios.global.defaultAJAXFailureHandler(resultObject);

                ilios.home.transaction.activeRequests['loadAllOfferings'] = null;
            },

            argument: {filtersParamString: paramString}
    };

    url += pageLoadedForStudent ? 'getOfferingsForLearnerDashboard'
                                : 'getOfferingsForNonLearnerDashboard';

    // Abort any previous request.
    if ((typeof ilios.home.transaction.activeRequests['loadAllOfferings'] != undefined) &&
        (ilios.home.transaction.activeRequests['loadAllOfferings'] != null)) {

        var request = ilios.home.transaction.activeRequests['loadAllOfferings'];
        if (YAHOO.util.Connect.isCallInProgress(request)) {
            YAHOO.util.Connect.abort(request);
            ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                                                   'general.phrases.loading_offerings');
            ilios.alert.updateServerInteractionProgress();
        }
    }

    ilios.alert.networkActivityI18NStrings.push('general.phrases.loading_offerings');
    ilios.alert.updateServerInteractionProgress();

    ilios.home.transaction.midLoadCourseHolder = null;

    ilios.home.transaction.activeRequests['loadAllOfferings'] = YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);
};

/**
 * This method is called via ilios.home.transaction.loadAllOfferings and loads all SILM calendar
 *  events for the user viewing the home page. Do Not Call this method directly.
 *
 * @private
 */
ilios.home.transaction.loadAllSILMs = function (filters) {
    var url = controllerURL;
    var method = "POST";
    var paramString = '';

    //onDOMReady uses the Custom Event signature, with the object
    //passed in as the third argument:
    if (arguments.length > 2) {
        filters = arguments[2];
    }

    if (null != filters) {
        paramString = "filters=" + YAHOO.lang.JSON.stringify( filters );
    }

    var ajaxCallback = {
            success: function (resultObject) {
                var parsedObject = null;
                var silms = null;
                var silm = null;
                var offeringModel = null;
                var i = 0;
                var len = 0;
                var model = null;
                var courseLoadModel = null;
                var dateStr = null;
                var uniqueId = 0;

                try {
                    parsedObject = YAHOO.lang.JSON.parse(resultObject.responseText);
                }
                catch (e) {
                    ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                                                           'general.phrases.loading_silms');
                    ilios.alert.updateServerInteractionProgress();

                    ilios.global.defaultAJAXFailureHandler(null, e);

                    return;
                }

                // MAY RETURN THIS BLOCK
                if (parsedObject.error != null) {
                    var msg
                          = ilios_i18nVendor.getI18NString('offering_management.error.failed_load');

                    ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                                                           'general.phrases.loading_silms');
                    ilios.alert.updateServerInteractionProgress();

                    ilios.alert.alert(msg + ": " + parsedObject.error);

                    return;
                }

                var paramString = resultObject.argument.filtersParamString;

                silms = parsedObject.silms;
                len = silms.length;
                for (; i < len; i++) {
                    silm = silms[i];

                    if (! pageLoadedForStudent
                        && (typeof ilios.home.transaction.midLoadCourseHolder[silm.course_id] == 'undefined')) {
                        courseLoadModel = new Object();

                        courseLoadModel.title = silm.course_title;
                        courseLoadModel.id = silm.course_id;
                        courseLoadModel.instructor = true;
                        ilios.home.transaction.midLoadCourseHolder[silm.course_id] = courseLoadModel;
                    }

                    offeringModel = new CalendarItemModel();

                    offeringModel.courseId = silm.course_id;
                    offeringModel.courseTitle = silm.course_title;
                    offeringModel.sessionId = silm.session_id;
                    offeringModel.sessionTitle = silm.session_title;
                    offeringModel.recentlyUpdated = (silm.recently_updated == '1');

                    offeringModel.ilmEvent = true;

                    dateStr = silm.due_date + ' 17:00:00';
                    offeringModel.startDate = ilios.utilities.mySQLDateToDateObject(dateStr, false);
                    dateStr = silm.due_date + ' 17:30:00';
                    offeringModel.endDate = ilios.utilities.mySQLDateToDateObject(dateStr, false);

                    offeringModel.sessionTypeCssClass = silm.session_type_css_class;

                    ilios.home.transaction.loadedOfferingModels.push(offeringModel);
                }

                if (! pageLoadedForStudent && ! isCalendarView && ('' == paramString)) {
                    ilios.home.transaction.loadCoursesAndProgramsForUserAsDirector();
                }

                ilios.home.calendar.resetCurrentCalendarViewToStart();

                ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                                                       'general.phrases.loading_silms');
                ilios.alert.updateServerInteractionProgress();

                ilios.home.transaction.activeRequests['loadAllSILMs'] = null;
            },

            failure: function (resultObject) {
                ilios.global.defaultAJAXFailureHandler(resultObject);
                ilios.home.transaction.activeRequests['loadAllSILMs'] = null;
            },

            argument: {filtersParamString: paramString}
    };

    // Abort any previous request.
    if ((typeof ilios.home.transaction.activeRequests['loadAllSILMs'] != undefined) &&
        (ilios.home.transaction.activeRequests['loadAllSILMs'] != null)) {
        var request = ilios.home.transaction.activeRequests['loadAllSILMs'];
        if (YAHOO.util.Connect.isCallInProgress(request)) {
            YAHOO.util.Connect.abort(request);
            ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                                                   'general.phrases.loading_silms');
            ilios.alert.updateServerInteractionProgress();
        }
    }

    url += pageLoadedForStudent ? 'getSessionILMsForLearnerDashboard'
                                : 'getSessionILMsForNonLearnerDashboard';

    ilios.alert.networkActivityI18NStrings.push('general.phrases.loading_silms');
    ilios.alert.updateServerInteractionProgress();

    ilios.home.transaction.activeRequests['loadAllSILMs'] = YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);
};

// @private
ilios.home.transaction.loadCoursesAndProgramsForUserAsDirector = function () {
    var url = controllerURL + 'getCoursesForUserAsDirector';
    var method = "POST";
    var ajaxCallback = {
            success: function (resultObject) {
                var parsedObject = null;
                var course = null;
                var courseLoadModel = null;
                var container = null;
                var ulElement = null;
                var liElement = null;
                var aElement = null;
                var html = null;
                var loadedSortedCourses = null;
                var key = null;

                try {
                    parsedObject = YAHOO.lang.JSON.parse(resultObject.responseText);
                }
                catch (e) {
                    ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                                                           'general.phrases.loading_courses');
                    ilios.alert.updateServerInteractionProgress();

                    ilios.global.defaultAJAXFailureHandler(null, e);

                    return;
                }

                // MAY RETURN THIS BLOCK
                if (parsedObject.error != null) {
                    var msg
                          = ilios_i18nVendor.getI18NString('offering_management.error.failed_load');

                    ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                                                           'general.phrases.loading_courses');
                    ilios.alert.updateServerInteractionProgress();

                    ilios.alert.alert(msg + ": " + parsedObject.error);

                    return;
                }

                for (key in parsedObject.courses) {
                    course = parsedObject.courses[key];

                    if (course.course_id in ilios.home.transaction.midLoadCourseHolder) {
                        courseLoadModel
                                    = ilios.home.transaction.midLoadCourseHolder[course.course_id];
                    }
                    else {
                        courseLoadModel = new Object();

                        courseLoadModel.title = course.title;
                        courseLoadModel.id = course.course_id;
                        ilios.home.transaction.midLoadCourseHolder[course.course_id]
                                                                                = courseLoadModel;
                    }

                    courseLoadModel.director = true;
                }

                loadedSortedCourses = new Array();
                for (key in ilios.home.transaction.midLoadCourseHolder) {
                    loadedSortedCourses.push(ilios.home.transaction.midLoadCourseHolder[key]);
                }
                loadedSortedCourses.sort(ilios.home.transaction.titledObjectComparator);

                container = document.getElementById('course_widget_content');
                if (container) {
                    ilios.utilities.removeAllChildren(container);

                    ulElement = document.createElement('ul');

                    for (var i = 0; i < loadedSortedCourses.length; i++) {
                        liElement = document.createElement('li');

                        courseLoadModel = loadedSortedCourses[i];

                        aElement = document.createElement('a');
                        aElement.setAttribute('href', (courseManagementURL
                                                            + '?course_id=' + courseLoadModel.id));

                        html = courseLoadModel.title
                            + '<span style="font-size: 7pt; color: #a1a3a3;">';
                        if (courseLoadModel.director) {
                            html += ' [D]';
                        }
                        if (courseLoadModel.instructor) {
                            html += ' [I]';
                        }
                        html += '</span>';

                        aElement.innerHTML = html;

                        liElement.appendChild(aElement);
                        ulElement.appendChild(liElement);
                    }

                    container.appendChild(ulElement);
                }
                ilios.home.transaction.loadProgramsForUserAsDirector();

                ilios.home.transaction.midLoadCourseHolder = null;

                ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                                                       'general.phrases.loading_courses');
                ilios.alert.updateServerInteractionProgress();

                ilios.home.transaction.activeRequests['loadCoursesAndProgramsForUserAsDirector'] = null;
            },

            failure: function (resultObject) {
                ilios.global.defaultAJAXFailureHandler(resultObject);
                ilios.home.transaction.activeRequests['loadCoursesAndProgramsForUserAsDirector'] = null;
            }};

    // Abort any previous request.
    if ((typeof ilios.home.transaction.activeRequests['loadCoursesAndProgramsForUserAsDirector'] != undefined) &&
        (ilios.home.transaction.activeRequests['loadCoursesAndProgramsForUserAsDirector'] != null)) {
        var request = ilios.home.transaction.activeRequests['loadCoursesAndProgramsForUserAsDirector'];
        if (YAHOO.util.Connect.isCallInProgress(request)) {
            YAHOO.util.Connect.abort(request);
            ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                                                   'general.phrases.loading_courses');
            ilios.alert.updateServerInteractionProgress();
        }
    }

    ilios.alert.networkActivityI18NStrings.push('general.phrases.loading_courses');
    ilios.alert.updateServerInteractionProgress();

    YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, "");
};

// @private
ilios.home.transaction.titledObjectComparator = function (obj1, obj2) {
    return obj1.title.localeCompare(obj2.title);
};

/**
 * This method is called via onDomReady and loads the recent activity of the user who is
 *  authenticated and viewing the home page.
 */
ilios.home.transaction.loadRecentActivity = function () {
    var url = controllerURL + 'getRecentActivity';
    var method = "POST";
    var ajaxCallback = {
            success: function (resultObject) {
                var parsedObject = null;
                var container = null;
                var activities = null;
                var activity = null;
                var ulElement = null;
                var liElement = null;
                var aElement = null;
                var jsDate = null;
                var dateString = null;

                try {
                    parsedObject = YAHOO.lang.JSON.parse(resultObject.responseText);
                }
                catch (e) {
                    ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                                                           'dashboard.loading_recent_activity');
                    ilios.alert.updateServerInteractionProgress();

                    ilios.global.defaultAJAXFailureHandler(null, e);

                    return;
                }

                // MAY RETURN THIS BLOCK
                if (parsedObject.error != null) {
                    var msg = ilios_i18nVendor.getI18NString('dashboard.error.activity_load');

                    ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                                                           'dashboard.loading_recent_activity');
                    ilios.alert.updateServerInteractionProgress();

                    ilios.alert.alert(msg + ": " + parsedObject.error);

                    return;
                }

                container = document.getElementById('recent_widget_content');
                ilios.utilities.removeAllChildren(container);

                ulElement = document.createElement('ul');

                activities = parsedObject.events;
                for (var i = 0; i < activities.length; i++) {
                    activity = activities[i];
                    jsDate = ilios.utilities.mySQLDateToDateObject(activity.time_stamp, true);
                    dateString = jsDate.format('mmm d yyyy, h:MM t');

                    liElement = document.createElement('li');

                    if (activity.relative_url.length > 0) {
                        aElement = document.createElement('a');
                        aElement.setAttribute('href', (baseURL + activity.relative_url));
                        aElement.innerHTML = activity.title
                                                + ' <span style="font-size: 7pt; color: #a1a3a3;"> ('
                                                + dateString + ')</span>';
                    }
                    else {
                        aElement = document.createElement('span');
                        aElement.setAttribute('style', 'color: #919393;');
                        aElement.innerHTML = activity.title
                                                + ' <span style="font-size: 7pt; color: #a1a3a3;"> ('
                                                + dateString + ')</span>';
                    }

                    liElement.appendChild(aElement);
                    ulElement.appendChild(liElement);
                }

                container.appendChild(ulElement);

                ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                                                       'dashboard.loading_recent_activity');
                ilios.alert.updateServerInteractionProgress();
            },

            failure: function (resultObject) {
                ilios.global.defaultAJAXFailureHandler(resultObject);
            }};

    ilios.alert.networkActivityI18NStrings.push('dashboard.loading_recent_activity');
    ilios.alert.updateServerInteractionProgress();

    YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, "");
};

// @private
ilios.home.transaction.loadProgramsForUserAsDirector = function () {
    var url = controllerURL + 'getProgramsForUserAsDirector';
    var method = "POST";
    var paramString = '';
    var ajaxCallback = {
            success: function (resultObject) {
                var parsedObject = null;
                var container = null;
                var programs = null;
                var program = null;
                var programLoadModel = null;
                var ulElement = null;
                var liElement = null;
                var aElement = null;
                var html = null;
                var programLoadModels = null;
                var loadedSortedPrograms = null;
                var i = 0;

                try {
                    parsedObject = YAHOO.lang.JSON.parse(resultObject.responseText);
                }
                catch (e) {
                    ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                                                           'dashboard.loading_programs');
                    ilios.alert.updateServerInteractionProgress();

                    ilios.global.defaultAJAXFailureHandler(null, e);

                    return;
                }

                // MAY RETURN THIS BLOCK
                if (parsedObject.error != null) {
                    var msg = ilios_i18nVendor.getI18NString('dashboard.error.program_load');

                    ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                                                           'dashboard.loading_programs');
                    ilios.alert.updateServerInteractionProgress();

                    ilios.alert.alert(msg + ": " + parsedObject.error);

                    return;
                }

                container = document.getElementById('program_widget_content');
                container.setAttribute('style', 'display:none');
                if (container) {
                    ilios.utilities.removeAllChildren(container);

                    ulElement = document.createElement('ul');

                    programLoadModels = new Array();
                    programs = parsedObject.programs;
                    for (i = 0; i < programs.length; i++) {
                        program = programs[i];

                        if (program.program_id in programLoadModels) {
                            programLoadModel = programLoadModels[program.program_id];
                        }
                        else {
                            programLoadModel = new Object();

                            programLoadModel.title = program.title;
                            programLoadModel.id = program.program_id;
                            programLoadModels[program.program_id] = programLoadModel;
                        }

                        if (program.short_title) {
                            programLoadModel.program = true;
                        }
                        else {
                            programLoadModel.course = true;
                        }
                    }

                    loadedSortedPrograms = new Array();
                    for (var key in programLoadModels) {
                        loadedSortedPrograms.push(programLoadModels[key]);
                    }
                    loadedSortedPrograms.sort(ilios.home.transaction.titledObjectComparator);

                    for (i = 0; i < loadedSortedPrograms.length; i++) {
                        program = loadedSortedPrograms[i];

                        liElement = document.createElement('li');

                        aElement = document.createElement('a');
                        aElement.setAttribute('href', (programManagementURL + '?program_id='
                                                                            + program.id));

                        html = program.title + '<span style="font-size: 7pt; color: #a1a3a3;">';
                        if (program.program) {
                            html += ' [PY]';
                        }
                        if (program.course) {
                            html += ' [C]';
                        }
                        html += '</span>';

                        aElement.innerHTML = html;

                        liElement.appendChild(aElement);
                        ulElement.appendChild(liElement);
                    }

                    container.appendChild(ulElement);

                }

                ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                                                       'dashboard.loading_programs');
                ilios.alert.updateServerInteractionProgress();

                ilios.home.transaction.activeRequests['loadProgramsForUserAsDirector'] = null;
            },

            failure: function (resultObject) {
                ilios.global.defaultAJAXFailureHandler(resultObject);
                ilios.home.transaction.activeRequests['loadProgramsForUserAsDirector'] = null;
            }};

    // Abort any previous request.
    if ((typeof ilios.home.transaction.activeRequests['loadProgramsForUserAsDirector'] != undefined) &&
        (ilios.home.transaction.activeRequests['loadProgramsForUserAsDirector'] != null)) {
        var request = ilios.home.transaction.activeRequests['loadProgramsForUserAsDirector'];
        if (YAHOO.util.Connect.isCallInProgress(request)) {
            YAHOO.util.Connect.abort(request);
            ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                                                   'dashboard.loading_programs');
            ilios.alert.updateServerInteractionProgress();
        }
    }

    ilios.alert.networkActivityI18NStrings.push('dashboard.loading_programs');
    ilios.alert.updateServerInteractionProgress();

    YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);
};

ilios.home.transaction.saveArchivingPreferences = function () {
    var url = controllerURL + 'setArchivingPreferences';
    var paramString = 'py_archive=';
    var element = document.getElementById('ap_py_radio_active');

    paramString += element.checked ? 'true' : 'false';

    element = document.getElementById('ap_course_radio_active');
    paramString += '&course_archive=' + (element.checked ? 'true' : 'false');

    ilios.home.transaction.savePreference(url, paramString, ilios.home.preferences.apDialog);
};

ilios.home.transaction.saveRolloverPreference = function () {
    var url = controllerURL + 'setRolloverPreference';
    var paramString = 'course_rollover=';
    var element = document.getElementById('rp_radio_active');

    paramString += element.checked ? 'true' : 'false';

    ilios.home.transaction.savePreference(url, paramString, ilios.home.preferences.rpDialog);
};

// dialogToClose is depressingly anti-closure.. TODO
// @private
ilios.home.transaction.savePreference = function (url, paramString, dialogToClose) {
    var method = "POST";
    var ajaxCallback = {
            success: function (resultObject) {
                var parsedObject = null;

                try {
                    parsedObject = YAHOO.lang.JSON.parse(resultObject.responseText);
                }
                catch (e) {
                    ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                                                           'dashboard.saving_preferences');
                    ilios.alert.updateServerInteractionProgress();

                    dialogToClose.cancel();

                    ilios.global.defaultAJAXFailureHandler(null, e);

                    return;
                }

                // MAY RETURN THIS BLOCK
                if (parsedObject.error != null) {
                    var msg = ilios_i18nVendor.getI18NString('dashboard.error.preferences_save');

                    ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                                                           'dashboard.saving_preferences');
                    ilios.alert.updateServerInteractionProgress();

                    dialogToClose.cancel();

                    ilios.alert.alert(msg + ": " + parsedObject.error);

                    return;
                }

                ilios.global.preferencesModel.updateWithServerDispatchedObject(parsedObject.prefs);

                ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                                                       'dashboard.saving_preferences');
                ilios.alert.updateServerInteractionProgress();

                dialogToClose.cancel();
            },

            failure: function (resultObject) {
                dialogToClose.cancel();

                ilios.global.defaultAJAXFailureHandler(resultObject);
            }};

    ilios.alert.networkActivityI18NStrings.push('dashboard.saving_preferences');
    ilios.alert.updateServerInteractionProgress();

    YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);
};

ilios.home.transaction.saveReminder = function (reminderModel) {
    var url = controllerURL + 'addOrUpdateReminder';
    var method = "POST";
    var dueDate = ilios.utilities.dateObjectToMySQLFriendly(reminderModel.getDueDate(), true);
    var paramString = 'reminder_id=' + reminderModel.getDBId()
                            + '&note=' + escape(reminderModel.getNote())
                            + '&due=' + escape(dueDate)
                            + '&closed=' + (reminderModel.isClosed() ? 'true' : 'false');
    var ajaxCallback = {
            success: function (resultObject) {
                var parsedObject = null;

                try {
                    parsedObject = YAHOO.lang.JSON.parse(resultObject.responseText);
                }
                catch (e) {
                    ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                                                           'dashboard.saving_reminder');
                    ilios.alert.updateServerInteractionProgress();

                    ilios.global.defaultAJAXFailureHandler(null, e);

                    return;
                }

                // MAY RETURN THIS BLOCK
                if (parsedObject.error != null) {
                    var msg = ilios_i18nVendor.getI18NString('dashboard.error.reminder_save');

                    ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                                                           'dashboard.saving_reminder');
                    ilios.alert.updateServerInteractionProgress();

                    ilios.alert.alert(msg + ": " + parsedObject.error);

                    return;
                }

                ilios.home.transaction.loadReminderAlerts();

                ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                                                       'dashboard.saving_reminder');
                ilios.alert.updateServerInteractionProgress();
            },

            failure: function (resultObject) {
                ilios.global.defaultAJAXFailureHandler(resultObject);
            }};

    ilios.alert.networkActivityI18NStrings.push('dashboard.saving_reminder');
    ilios.alert.updateServerInteractionProgress();

    YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);
};

ilios.home.transaction.saveReport = function (reportModel) {
    var url = controllerURL + 'addReport';
    var method = "POST";
    var paramString = 'noun1=' + reportModel.getSubject();
    var ajaxCallback = {
        success: function (resultObject) {
            var parsedObject = null;

            try {
                parsedObject = YAHOO.lang.JSON.parse(resultObject.responseText);
            }
            catch (e) {
                ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                    'dashboard.saving_report');
                ilios.alert.updateServerInteractionProgress();

                ilios.global.defaultAJAXFailureHandler(null, e);

                return;
            }

            // MAY RETURN THIS BLOCK
            if (parsedObject.error != null) {
                var msg = ilios_i18nVendor.getI18NString('dashboard.error.report_save');

                ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                    'dashboard.saving_report');
                ilios.alert.updateServerInteractionProgress();

                ilios.alert.alert(msg + ": " + parsedObject.error);

                return;
            }

            ilios.home.transaction.loadReports();

            ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                'dashboard.saving_report');
            ilios.alert.updateServerInteractionProgress();
        },

        failure: function (resultObject) {
            ilios.global.defaultAJAXFailureHandler(resultObject);
        }};

    if (reportModel.hasPrepositionalObject()) {
        var poValues = reportModel.getPrepositionalObjectValues();
        var valueList = '';

        paramString += '&noun2=' + reportModel.getPrepositionalObject();

        for (var key in poValues) {
            if (valueList.length > 0) {
                valueList += ',';
            }

            valueList += poValues[key];
        }
        paramString += '&noun2_values=' + valueList;
    }

    if (reportModel.getTitle() != null) {
        paramString += '&title=' + reportModel.getTitle();
    }

    ilios.alert.networkActivityI18NStrings.push('dashboard.saving_report');
    ilios.alert.updateServerInteractionProgress();

    YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);
};

ilios.home.transaction.deleteSavedReport = function (reportModel) {
    var url = controllerURL + 'deleteReport';
    var method = "POST";
    var paramString = 'rid=' + reportModel.getDBId();
    var ajaxCallback = {
            success: function (resultObject) {
                var parsedObject = null;

                try {
                    parsedObject = YAHOO.lang.JSON.parse(resultObject.responseText);
                }
                catch (e) {
                    ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                                                           'dashboard.deleting_report');
                    ilios.alert.updateServerInteractionProgress();

                    ilios.global.defaultAJAXFailureHandler(null, e);

                    return;
                }

                // MAY RETURN THIS BLOCK
                if (parsedObject.error != null) {
                    var msg = ilios_i18nVendor.getI18NString('dashboard.error.report_delete');

                    ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                                                           'dashboard.deleting_report');
                    ilios.alert.updateServerInteractionProgress();

                    ilios.alert.alert(msg + ": " + parsedObject.error);
                    return;
                }

                ilios.home.transaction.loadReports();

                ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                                                       'dashboard.deleting_report');
                ilios.alert.updateServerInteractionProgress();
            },

            failure: function (resultObject) {
                ilios.global.defaultAJAXFailureHandler(resultObject);
            }};

    ilios.alert.networkActivityI18NStrings.push('dashboard.deleting_report');
    ilios.alert.updateServerInteractionProgress();

    YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);
};

ilios.home.transaction.runReport = function (reportModel) {
    var url = controllerURL + 'runReport';
    var method = "POST";
    var paramString = 'report_id=' + reportModel.getDBId();
    var ajaxCallback = {
            success: function (resultObject) {
                var parsedObject = null;
                var container = null;
                var divElement = null;
                var ulElement = null;
                var liElement = null;
                var aElement = null;
                var html = null;
                var key = null;

                try {
                    parsedObject = YAHOO.lang.JSON.parse(resultObject.responseText);
                }
                catch (e) {
                    ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                                                           'dashboard.running_report');
                    ilios.alert.updateServerInteractionProgress();

                    ilios.global.defaultAJAXFailureHandler(null, e);

                    return;
                }

                // MAY RETURN THIS BLOCK
                if (parsedObject.error != null) {
                    var msg = ilios_i18nVendor.getI18NString('dashboard.error.report_load');

                    ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                                                           'dashboard.running_report');
                    ilios.alert.updateServerInteractionProgress();

                    ilios.alert.alert(msg + ": " + parsedObject.error);

                    return;
                }

                container = document.getElementById('report_results_content');

                if (reportModel.getTitle() != null) {
                    html = reportModel.getTitle();
                } else {
                    html = ilios_i18nVendor.getI18NString('dashboard.report.result.header_1');
                    html += ' <span class="read_only_data">' + reportModel.getSubject().toUpperCase()
                        + '</span>';

                    if (reportModel.hasPrepositionalObject()) {
                        html += ' '
                            + ilios_i18nVendor.getI18NString('dashboard.report.result.header_2')
                            .toLowerCase();
                        html += ' <span class="read_only_data">'
                            + parsedObject.po_display_value.toUpperCase() + '</span>';
                    }
                }
                divElement = document.createElement('div');
                divElement.setAttribute('class','report-header');
                divElement.innerHTML = html;
                container.appendChild(divElement);

                ulElement = document.createElement('ul');

                if (parsedObject.list_type == 'text') {
                    if (parsedObject.text_items.length == 0) {
                        divElement = document.createElement('p');

                        divElement.innerHTML
                                    = ilios_i18nVendor.getI18NString('dashboard.report.no_results');
                        container.appendChild(divElement);
                    }
                    else {
                        for (key in parsedObject.text_items) {
                            liElement = document.createElement('li');
                            liElement.innerHTML = parsedObject.text_items[key];

                            ulElement.appendChild(liElement);
                        }
                    }
                }
                else {
                    if (parsedObject.link_items.length == 0) {
                        divElement = document.createElement('p');

                        divElement.innerHTML
                                    = ilios_i18nVendor.getI18NString('dashboard.report.no_results');
                        container.appendChild(divElement);
                    }
                    else {
                        for (key in parsedObject.link_items) {
                            liElement = document.createElement('li');
                            liElement.setAttribute('class', 'linked-item');

                            if (parsedObject.link_items[key].link == '') {
                                liElement.innerHTML = parsedObject.link_items[key].text;
                            } else {
                                aElement = document.createElement('a');
                                aElement.setAttribute('href', parsedObject.link_items[key].link);
                                aElement.innerHTML = parsedObject.link_items[key].text;

                                liElement.appendChild(aElement);
                            }
                            ulElement.appendChild(liElement);
                        }
                    }
                }

                container.appendChild(ulElement);

                ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                                                       'dashboard.running_report');
                ilios.alert.updateServerInteractionProgress();
            },

            failure: function (resultObject) {
                ilios.global.defaultAJAXFailureHandler(resultObject);
            }};

    ilios.alert.networkActivityI18NStrings.push('dashboard.running_report');
    ilios.alert.updateServerInteractionProgress();

    YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);
};

ilios.home.transaction.loadReminderAlerts = function () {
    var url = controllerURL + 'loadReminders';
    var method = "POST";
    var paramString = '';
    var ajaxCallback = {
            success: function (resultObject) {
                var Element = YAHOO.util.Element;
                var parsedObject = null;
                var container = null;
                var reminders = null;
                var reminder = null;
                var ulElement = null;
                var liElement = null;
                var aElement = null;
                var overdueContainer = null;
                var overdueAlertFound = false;
                var reminderModel = null;

                try {
                    parsedObject = YAHOO.lang.JSON.parse(resultObject.responseText);
                }
                catch (e) {
                    ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                                                           'dashboard.loading_reminder');
                    ilios.alert.updateServerInteractionProgress();

                    ilios.global.defaultAJAXFailureHandler(null, e);

                    return;
                }

                // MAY RETURN THIS BLOCK
                if (parsedObject.error != null) {
                    var msg = ilios_i18nVendor.getI18NString('dashboard.error.reminder_load');

                    ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                                                           'dashboard.loading_reminder');
                    ilios.alert.updateServerInteractionProgress();

                    ilios.alert.alert(msg + ": " + parsedObject.error);

                    return;
                }

                overdueContainer = new Element(document.getElementById('alerts_overdue_warning'));

                container = document.getElementById('alerts_widget_list_container');
                ilios.utilities.removeAllChildren(container);

                reminders = parsedObject.reminders;
                for (var key in reminders) {
                    reminder = reminders[key];
                    reminderModel = new ReminderModel(reminder);

                    if (reminderModel.isClosed()) {
                        continue;
                    }

                    liElement = document.createElement('li');

                    aElement = document.createElement('a');
                    aElement.setAttribute('href', '');
                    aElement.setAttribute('onclick', 'return false;');
                    aElement.setAttribute('title', reminder.note);
                    aElement.innerHTML = ilios.lang.ellipsisedOfLength(reminder.note, 26);
                    aElement.iliosModel = reminderModel;
                    YAHOO.util.Event.addListener(aElement, 'click', function () {
                        IEvent.fire({
                            action: 'ur_dialog_open',
                            reminder_model: this.iliosModel
                        });
                    });

                    liElement.appendChild(aElement);

                    aElement = document.createElement('span');
                    if (reminderModel.isOverdue()) {
                        overdueAlertFound = true;
                        aElement.setAttribute('style',
                                              'font-size: 8pt; font-weight: bold; color: #ee0a0a;');
                        aElement.innerHTML
                          = ' ('
                             + ilios_i18nVendor.getI18NString('general.terms.overdue').toLowerCase()
                             + ')';
                    }
                    else {
                        aElement.setAttribute('style', 'font-size: 8pt; color: #A1A3A3;');
                        aElement.innerHTML = ' (' + reminderModel.getDueDate().format('m/dd/yyyy')
                                                    + ')';
                    }
                    liElement.appendChild(aElement);

                    container.appendChild(liElement);
                }

                if (overdueAlertFound) {
                    overdueContainer.addClass('icon-warning');
                }
                else {
                    overdueContainer.removeClass('icon-warning');
                }

                ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings, 'dashboard.loading_reminder');
                ilios.alert.updateServerInteractionProgress();
            },

            failure: function (resultObject) {
                ilios.global.defaultAJAXFailureHandler(resultObject);
            }};

    ilios.alert.networkActivityI18NStrings.push('dashboard.loading_reminder');
    ilios.alert.updateServerInteractionProgress();

    YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);
};

ilios.home.transaction.loadReports = function () {
    var url = controllerURL + 'loadReports';
    var method = "POST";
    var paramString = '';
    var ajaxCallback = {
            success: function (resultObject) {
                var Event = YAHOO.util.Event;
                var parsedObject = null;
                var container = null;
                var reports = null;
                var report = null;
                var ulElement = null;
                var liElement = null;
                var aElement = null;
                var reportModel = null;
                var html = null;

                try {
                    parsedObject = YAHOO.lang.JSON.parse(resultObject.responseText);
                }
                catch (e) {
                    ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                                                           'dashboard.loading_report');
                    ilios.alert.updateServerInteractionProgress();

                    ilios.global.defaultAJAXFailureHandler(null, e);

                    return;
                }

                // MAY RETURN THIS BLOCK
                if (parsedObject.error != null) {
                    var msg = ilios_i18nVendor.getI18NString('dashboard.error.reminder_load');

                    ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                                                           'dashboard.loading_report');
                    ilios.alert.updateServerInteractionProgress();

                    ilios.alert.alert(msg + ": " + parsedObject.error);

                    return;
                }

                container = document.getElementById('reports_widget_list_container');
                ilios.utilities.removeAllChildren(container);

                reports = parsedObject.reports;
                for (var key in reports) {
                    report = reports[key];
                    reportModel = new ReportModel(report);

                    html = "";
                    if (reportModel.getTitle() != null) {
                        html += reportModel.getTitle();
                    } else {  // make report tile because user didn't enter
                        html += ilios_i18nVendor.getI18NString('dashboard.report.result.header_1') + " ";
                        html += reportModel.getSubject();
                        if (reportModel.hasPrepositionalObject()) {
                            html += " " + ilios_i18nVendor.getI18NString('dashboard.report.result.header_2') + " ";
                            html += reportModel.getPrepositionalObjectDisplayValues();
                        }
                    }

                    liElement = document.createElement('li');
                    liElement.setAttribute('class', 'dashboard_report_item');

                    aElement = document.createElement('span');
                    aElement.setAttribute('class', 'icon-cancel');
                    aElement.iliosModel = reportModel;
                    Event.addListener(aElement, 'click', function () {
                        ilios.home.transaction.deleteSavedReport(this.iliosModel);
                    });
                    liElement.appendChild(aElement);

                    aElement = document.createElement('a');
                    aElement.setAttribute('href', '');
                    aElement.setAttribute('target', '_new');
                    aElement.setAttribute('onclick', 'return false;');
                    Event.addListener(aElement, 'click', function () {
                        IEvent.fire({
                            action: 'report_results_dialog_open',
                            report: this.iliosModel
                        });
                    });
                    aElement.innerHTML = html;
                    aElement.iliosModel = reportModel;

                    liElement.appendChild(aElement);
                    container.appendChild(liElement);
                }

                ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                                                       'dashboard.loading_report');
                ilios.alert.updateServerInteractionProgress();
            },

            failure: function (resultObject) {
                ilios.global.defaultAJAXFailureHandler(resultObject);
            }};

    ilios.alert.networkActivityI18NStrings.push('dashboard.loading_report');
    ilios.alert.updateServerInteractionProgress();

    ilios.utilities.removeAllChildren(document.getElementById('reports_widget_list_container'));

    YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);
};
