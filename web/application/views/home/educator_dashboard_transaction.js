/**
 * Educator-dashboard-specific functionality for interacting with the server backend.
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
 * home/dashboard_calendar_support.js
 */

ilios.namespace('home.transaction');

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
                jsDate = ilios.utilities.mySQLDateToDateObject(activity.created_at, true);
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

            if (! ilios.preferences.preferencesModel) {
                ilios.preferences.installPreferencesModel();
            }

            ilios.preferences.preferencesModel.updateWithServerDispatchedObject(parsedObject.prefs);

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
        + '&note=' + encodeURIComponent(reminderModel.getNote())
        + '&due=' + encodeURIComponent(dueDate)
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
            var ulElement;
            var liElement;
            var aElement;
            var spanElement;
            var overdueContainer = null;
            var overdueAlertFound = false;
            var reminderModel = null;
            var overdueText;
            var dateText;

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
                aElement.setAttribute('class', 'alert-list-item-text truncate');
                aElement.setAttribute('href', '');
                aElement.setAttribute('onclick', 'return false;');
                aElement.setAttribute('title', reminder.note);
                aElement.appendChild(document.createTextNode(reminder.note));
                aElement.iliosModel = reminderModel;
                YAHOO.util.Event.addListener(aElement, 'click', function () {
                    IEvent.fire({
                        action: 'ur_dialog_open',
                        reminder_model: this.iliosModel
                    });
                });

                spanElement = document.createElement('span');
                if (reminderModel.isOverdue()) {
                    overdueAlertFound = true;
                    spanElement.setAttribute('class', 'alert-due-date-late');
                    overdueText = ilios_i18nVendor.getI18NString('general.terms.overdue').toLowerCase();
                    spanElement.appendChild(document.createTextNode('(' + overdueText + ')'));
                }
                else {
                    spanElement.setAttribute('class', 'alert-due-date');
                    dateText = reminderModel.getDueDate().format('m/dd/yyyy');
                    spanElement.appendChild(document.createTextNode('(' + dateText + ')'));
                }
                liElement.appendChild(aElement);
                liElement.appendChild(spanElement);
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
