/**
 * Defines the following namespaces:
 *
 * ilios.cm.cs
 *
 * Dependencies:
 *
 * YUI lib (at least YAHOO.util and its dependencies)
 * scripts/ilios_ui.js
 * scripts/ilios_utilities.js
 */

ilios.namespace('cm.cs');

ilios.cm.cs.displayCourseSearchPanel = function () {
    if ((ilios.cm.currentCourseModel != null)
            && (ilios.cm.currentCourseModel.isModelDirty()
                            || ilios.cm.currentCourseModel.containsDirtySessions())) {
        var notification = ilios_i18nVendor.getI18NString('general.warning.dirty_state_lose_changes');
        var continueStr = ilios_i18nVendor.getI18NString('general.phrases.want_to_continue');
        var yesStr = ilios_i18nVendor.getI18NString('general.terms.yes');

        ilios.alert.inform(('<p style="margin-bottom:9px; text-align:justify;">' + notification
                + '</p><center><b>' + continueStr + '</b></center>'), yesStr,
               ilios.cm.cs.continueDisplayCourseSearchPanel);

        return;
    }

    ilios.cm.cs.courseSearchDialog.showDialogPane();
};

// @private
ilios.cm.cs.continueDisplayCourseSearchPanel = function () {
    this.cancel();      // (alert panel dismiss)

    ilios.cm.cs.courseSearchDialog.showDialogPane();
};

ilios.cm.cs.performCourseSearch = function (searchTerms) {
    var searchInfoElement = document.getElementById('course_search_status');

    if (YAHOO.lang.trim(searchTerms).length < 2) {
        searchInfoElement.innerHTML = ilios_i18nVendor.getI18NString('general.error.query_length');
    }
    else {
        var url = controllerURL + "getCourseListForQuery";
        var method = "POST";
        var paramString = "query=" + searchTerms;
        var ajaxCallback = {
            success: function (resultObject) {
                var parsedObject = null;
                var courseModel = null;
                var searchResultsContainer = null;
                var element = null;

                try {
                    parsedObject = YAHOO.lang.JSON.parse(resultObject.responseText);
                }
                catch (e) {
                    document.getElementById('course_search_status').innerHTML
                                      = ilios_i18nVendor.getI18NString('general.error.must_retry');

                    return;
                }

                searchResultsContainer = document.getElementById('course_search_results_list');
                ilios.utilities.removeAllChildren(searchResultsContainer);

                for (var key in parsedObject) {
                    element = document.createElement('li');
                    courseModel = new CourseModel(parsedObject[key]);
                    element.iliosModel = courseModel;
                    element.innerHTML = '<span class="title">' + courseModel.getTitle() + '</span> '
                                            + '<span class="year">' + courseModel.getYear()
                                            + ' - ' + (parseInt(courseModel.getYear()) + 1) + '</span>'
                                            + '<span class="date">('
                                            + courseModel.getStartDateAsDateObject().format('m/d/yyyy')
                                            + ' - '
                                            + courseModel.getEndDateAsDateObject().format('m/d/yyyy')
                                            + ')</span>';
                    YAHOO.util.Event.addListener(element, 'click',
                                       function (e) { ilios.cm.cs.courseSelected(this); });
                    searchResultsContainer.appendChild(element);
                }

                if (parsedObject.length == 0) {
                    document.getElementById('course_search_status').innerHTML
                                = ilios_i18nVendor.getI18NString('general.phrases.search.no_match');
                }
                else {
                    document.getElementById('course_search_status').innerHTML = '';
                }
            },

            failure: function (resultObject) {
                ilios.global.defaultAJAXFailureHandler(resultObject);

                document.getElementById('course_search_status').innerHTML
                                         = ilios_i18nVendor.getI18NString('general.error.must_retry');

                return;
            }};

        searchInfoElement.innerHTML = ilios_i18nVendor.getI18NString('general.terms.searching')
                                            + '&hellip;';

        YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);
    }
};

/**
 *  Does the same thing as performCourseSearch, but is utilized for the calendar searches
 *  as it does not limit the returned items to ones that do not have the 'archived' bit set
 *
 *  @param searchTerms
 */

ilios.cm.cs.performCourseSearchForCalendar = function (searchTerms) {
    var searchInfoElement = document.getElementById('course_search_status');

    if (YAHOO.lang.trim(searchTerms).length < 2) {
        searchInfoElement.innerHTML = ilios_i18nVendor.getI18NString('general.error.query_length');
    }
    else {
        var url = controllerURL + "getCourseListForQueryForCalendar";
        var method = "POST";
        var paramString = "query=" + searchTerms;
        var ajaxCallback = {
            success: function (resultObject) {
                var parsedObject = null;
                var courseModel = null;
                var searchResultsContainer = null;
                var element = null;

                try {
                    parsedObject = YAHOO.lang.JSON.parse(resultObject.responseText);
                }
                catch (e) {
                    document.getElementById('course_search_status').innerHTML
                        = ilios_i18nVendor.getI18NString('general.error.must_retry');

                    return;
                }

                searchResultsContainer = document.getElementById('course_search_results_list');
                ilios.utilities.removeAllChildren(searchResultsContainer);

                for (var key in parsedObject) {
                    element = document.createElement('li');
                    courseModel = new CourseModel(parsedObject[key]);
                    element.iliosModel = courseModel;
                    element.innerHTML = '<span class="title">' + courseModel.getTitle() + '</span> '
                        + '<span class="year">' + courseModel.getYear()
                        + ' - ' + (parseInt(courseModel.getYear()) + 1) + '</span>'
                        + '<span class="date">('
                        + courseModel.getStartDateAsDateObject().format('m/d/yyyy')
                        + ' - '
                        + courseModel.getEndDateAsDateObject().format('m/d/yyyy')
                        + ')</span>';
                    YAHOO.util.Event.addListener(element, 'click',
                        function (e) { ilios.cm.cs.courseSelected(this); });
                    searchResultsContainer.appendChild(element);
                }

                if (parsedObject.length == 0) {
                    document.getElementById('course_search_status').innerHTML
                        = ilios_i18nVendor.getI18NString('general.phrases.search.no_match');
                }
                else {
                    document.getElementById('course_search_status').innerHTML = '';
                }
            },

            failure: function (resultObject) {
                ilios.global.defaultAJAXFailureHandler(resultObject);

                document.getElementById('course_search_status').innerHTML
                    = ilios_i18nVendor.getI18NString('general.error.must_retry');

                return;
            }};

        searchInfoElement.innerHTML = ilios_i18nVendor.getI18NString('general.terms.searching')
            + '&hellip;';

        YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);
    }
};

ilios.cm.cs.emptySearchDialogForViewing = function () {
    var element = document.getElementById('course_search_terms');

    element.value = '';

    element = document.getElementById('course_search_results_list');
    ilios.utilities.removeAllChildren(element);
    element = document.getElementById('course_search_terms');
    element.focus();
};

ilios.cm.cs.courseSelected = function (divElement) {
    var model = divElement.iliosModel; // CourseModel
    // construct new course url and reload page with it.
    var newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + "?course_id=" + model.getDBId();
    window.location.href = newUrl;
};
