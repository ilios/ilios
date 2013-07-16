/**
 * Functionality for interacting with the server backend commonly used in dashboard- and calendar-pages.
 *
 * Defines the following namespaces:
 *
 * ilios.home.transaction
 *
 * Dependencies:
 *
 * YUI libs
 * scripts/ilios_ui.js
 * scripts/ilios_utilities.js
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
