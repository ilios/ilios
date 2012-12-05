/**
 * DEPENDENCIES:
 *         YUI lib
 *         scripts/ilios_utilities.js
 */

ilios.namespace('management.transaction');

/**
 * Retrieves the permissions for a given user from the server via XHR
 * and updates the UI accordingly.
 * @method getPermissionsForUser
 * @param {UserModel} the user
 */
ilios.management.transaction.getPermissionsForUser = function (userModel) {
    var url = controllerURL + "getUserPermissions";
    var method = "POST";
    var paramString = "user_id=" + userModel.getDBId();
    var ajaxCallback = {
        success: function (resultObject) {
            var parsedObject = null;

            try {
                parsedObject = YAHOO.lang.JSON.parse(resultObject.responseText);
            }
            catch (e) {
                ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                                                       'management.permissions.fetching_permissions');
                ilios.alert.updateServerInteractionProgress();

                ilios.global.defaultAJAXFailureHandler(null, e);

                return;
            }

            if (parsedObject.error != null) {
                var msg = ilios_i18nVendor.getI18NString('management.error.loading_permissions');

                ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                                                       'management.permissions.fetching_permissions');
                ilios.alert.updateServerInteractionProgress();

                ilios.alert.alert(msg + ": " + parsedObject.error);

                return;
            }

            ilios.management.permissions.populateCourseAndProgramFromPermissionObjects(parsedObject.permissions);


            ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                                                   'management.permissions.fetching_permissions');
            ilios.alert.updateServerInteractionProgress();
        },

        failure: function (resultObject) {
               ilios.global.defaultAJAXFailureHandler(resultObject);
        }};

    ilios.alert.networkActivityI18NStrings.push('management.permissions.fetching_permissions');
    ilios.alert.updateServerInteractionProgress();

    YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);
};

ilios.management.transaction.setPermissionsForSelectedCourses = function () {
    ilios.management.transaction
            .setPermissionsForSelectedModels(ilios.management.selectedPermissionCourses,
                                             ilios.management.preDialogSelectedCourseModels,
                                             'current_course_permissions_div',
                                             ilios.management.transaction
                                                                   .setPermissionsForUserForCourse);
};

ilios.management.transaction.setPermissionsForSelectedPrograms = function () {
    ilios.management.transaction
            .setPermissionsForSelectedModels(ilios.management.permissions.selectedPermissionPrograms,
                                             ilios.management.preDialogSelectedProgramModels,
                                             'current_program_permissions_div',
                                             ilios.management.transaction
                                                                .setPermissionsForUserForProgram);
};

ilios.management.transaction.setPermissionsForSelectedSchools = function () {
    ilios.management.transaction
            .setPermissionsForSelectedModels(ilios.management.permissions.selectedPermissionSchools,
                                             ilios.management.preDialogSelectedSchoolModels,
                                             'current_school_permissions_div',
                                             ilios.management.transaction
                                                                .setPermissionsForUserForSchool);
};

// @private
ilios.management.transaction.setPermissionsForSelectedModels = function (selectedModels,
                                                                         priorSelection,
                                                                         containerIdStr,
                                                                         transactionFunction) {
    var userModel = null;
    var selectedModel = null;
    var replacePermissions = false;
    var idArray = null;
    var container = null;
    var key = null;

    if (ilios.utilities.associativeArrayEquality(selectedModels, priorSelection)) {
        return;
    }

    replacePermissions = (ilios.management.permissions.selectedUsers.length == 1);
    idArray = new Array();

    for (key in selectedModels) {
        selectedModel = selectedModels[key];

        idArray.push(selectedModel.getDBId());
    }

    container = document.getElementById(containerIdStr);
    container.needsEmptying = true;
    container.permissionModels = null;

    for (key in ilios.management.permissions.selectedUsers) {
        userModel = ilios.management.permissions.selectedUsers[key];

        transactionFunction(userModel.getDBId(), idArray, (! replacePermissions));
    }
};

// @param shouldAdd if true, the permission is added in the db, with rw=TT; if false, any permission
//    in the table for the userId and table 'school' will get nuked prior to setting the new
//    permissions
ilios.management.transaction.setPermissionsForUserForSchool = function (userId, schoolIds,
                                                                         shouldAdd) {
    ilios.management.transaction.setPermissionsForUser(userId, schoolIds, 'school', shouldAdd);
};

// @param shouldAdd if true, the permission is added in the db, with rw=TT; if false, any permission
//    in the table for the userId and table 'program' will get nuked prior to setting the new
//    permissions
ilios.management.transaction.setPermissionsForUserForProgram = function (userId, programIds,
                                                                         shouldAdd) {
    ilios.management.transaction.setPermissionsForUser(userId, programIds, 'program', shouldAdd);
};

// @param shouldAdd if true, the permission is added in the db, with rw=TT; if false, any permission
//    in the table for the userId and table 'course' will get nuked prior to setting the new
//    permissions
ilios.management.transaction.setPermissionsForUserForCourse = function (userId, courseIds,
                                                                        shouldAdd) {
    ilios.management.transaction.setPermissionsForUser(userId, courseIds, 'course', shouldAdd);
};

/*
 * @private
 */
ilios.management.transaction.setPermissionsForUser = function (userId, tableIds, tableName,
                                                               shouldAdd) {
    var url = controllerURL + "setUserPermissions";
    var method = "POST";
    var paramString = "user_id=" + userId + "&table_name=" + tableName
                        + "&replace=" + (shouldAdd ? "false" : "true");
    var ajaxCallback = {
        success: function (resultObject) {
            var parsedObject = null;

            try {
                parsedObject = YAHOO.lang.JSON.parse(resultObject.responseText);
            }
            catch (e) {
                ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                                                       'management.permissions.saving_permissions');
                ilios.alert.updateServerInteractionProgress();

                ilios.global.defaultAJAXFailureHandler(null, e);

                return;
            }

            if (parsedObject.error != null) {
                var msg = ilios_i18nVendor.getI18NString('management.error.saving_permissions');

                ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                                                       'management.permissions.saving_permissions');
                ilios.alert.updateServerInteractionProgress();

                ilios.alert.alert(msg + ": " + parsedObject.error);

                return;
            }

            ilios.management.permissions.populateCourseAndProgramFromPermissionObjects(parsedObject.permissions);

            ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                                                   'management.permissions.saving_permissions');
            ilios.alert.updateServerInteractionProgress();
        },

        failure: function (resultObject) {
               ilios.global.defaultAJAXFailureHandler(resultObject);
        }};
    var len = tableIds.length;
    var idStr = '';

    for (var i = 0; i < len; i++) {
        if (idStr != '') {
            idStr += ',';
        }

        idStr += tableIds[i];
    }
    paramString += '&ids=' + idStr;

    ilios.management.permissions.disableStep2ChoosingLinks();

    ilios.alert.networkActivityI18NStrings.push('management.permissions.saving_permissions');
    ilios.alert.updateServerInteractionProgress();

    YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);
};

/**
 * Retrieves users that have actionable sync exceptions via XHR.
 * On success, the retrieved user data is loaded into the appropriate container element.
 * On failure, an error message is displayed instead.
 *
 * @global controllerURL
 * @see ilios.management.user_accounts.buildAlertsDisplayDOM()
 */
ilios.management.transaction.loadUsersWithSyncExceptions = function () {
    var url = controllerURL + 'getUsersWithSyncExceptions';

    var callback = {
        success : function (o) {
            var response = null;
            try {
                // parse the JSON-formatted response body
                response = YAHOO.lang.JSON.parse(o.responseText);
            } catch (e) {
                ilios.global.defaultAJAXFailureHandler(null, e);
                return;
            }
            // check if an error was returned
            if (null != response.error) {
                var msg = ilios_i18nVendor.getI18NString('management.error.loading_users');
                ilios.alert.alert(msg + ':' + response.error);
            }
            // everything seems to be OK at this point,
            // so lets populate the container element with
            // the returned user data
            ilios.management.user_accounts.populateSyncExceptionsList(response.users);
        },
        failure : function (o) {
            ilios.global.defaultAJAXFailureHandler(o);
        }
    };
    // make the call
    YAHOO.util.Connect.asyncRequest('POST', url, callback);
};

/**
 * Posts action items for user sync exceptions to server for processing.
 * @param Object actionItems an object with of user-id/action pairs as property-names/values.
 */
ilios.management.transaction.processActionItems = function (actionItems) {
    var Dom = YAHOO.util.Dom;
    var url = controllerURL + "processActionItemsForUserSyncExceptions";
    var paramString = 'users=' +  escape(YAHOO.lang.JSON.stringify(actionItems));
    var btn = Dom.get("ua_process_queued_actions_btn");
    var callback = {
        success : function (o) {
            var parsedObject = null;
            Dom.setStyle('ua_indeterminate_progress', 'display', 'none');
            try {
                parsedObject = YAHOO.lang.JSON.parse(o.responseText);
            } catch (e) {
                ilios.global.defaultAJAXFailureHandler(e);
            }
            if (null != parsedObject.error) {
                var msg = ilios_i18nVendor.getI18NString('management.error.processing_action_items');
                ilios.alert.alert(msg + ": " + parsedObject.error);
                return;
            }
            // success!
            // update the sync exceptions count and rebuild the UI
            ilios.management.syncExceptionsCount = parsedObject.users_with_sync_exceptions_count;
            ilios.management.user_accounts.rebuildSyncExceptionsDisplayDOM();
        },
        failure : function (o) {
            ilios.global.defaultAJAXFailureHandler(o);
        }
    };

    YAHOO.util.Connect.asyncRequest('POST', url, callback, paramString);
};

// We handle this one differently to allow the server side to stream a potentially large set
//      of users as opposed to developing an in-memory array structure on the server which then
//      gets JSON'd back
ilios.management.transaction.loadCohortlessUsers = function () {
    var url = controllerURL + "getCohortlessStudentList";
    var dataSourceCallback = {
        success: function (requestObject, resultObject) {
            ilios.management.user_accounts.populateCohortlessUserList(resultObject.results);
        },

        failure: function (requestObject, resultObject) {
               ilios.global.defaultAJAXFailureHandler(resultObject);
        }};
    var xhrDataSource = new YAHOO.util.XHRDataSource(url);
    xhrDataSource.connMethodPost = true;
    xhrDataSource.responseType = YAHOO.util.XHRDataSource.TYPE_XML;
    xhrDataSource.responseSchema = {resultNode: "Result",
                                     fields: [ "first_name", "last_name", "middle_name", "email",
                                               "uc_uid", "cohort_name", "user_id" ]};

    xhrDataSource.sendRequest(null, dataSourceCallback);
};

ilios.management.transaction.performCohortAssociations = function (users, selectedCohortId) {
    var url = controllerURL + "performCohortAssociations";
    var method = "POST";
    var paramString = 'cohort_id=' + selectedCohortId + '&users=' + escape(YAHOO.lang.JSON.stringify(users));
    var ajaxCallback = {
        success: function (resultObject) {
            var parsedObject = null;
            var element = new YAHOO.util.Element(document.getElementById('ua_indeterminate_progress'));
            var divsToRemove = new Array();
            var children = null;
            var i = 0;
            var j = 0;
            var length = 0;
            var userCount = users.length;
            var model = null;

            element.setStyle('display', 'none');

            try {
                parsedObject = YAHOO.lang.JSON.parse(resultObject.responseText);
            }
            catch (e) {
                ilios.global.defaultAJAXFailureHandler(null, e);

                return;
            }

            if (parsedObject.error != null) {
                var msg = ilios_i18nVendor.getI18NString('management.error.saving_user_account');

                ilios.alert.alert(msg + ": " + parsedObject.error);

                return;
            }

            ilios.management.transaction.updateUserAccountAlertCounts();

            element = document.getElementById('ua_cohortless_user_list');
            children = element.childNodes;
            length = children.length;
            for (i = 0; i < length; i++) {
                model = children[i].iliosModel;
                if (model != null) {
                    for (j = 0; j < userCount; j++) {
                        if (model == users[j]) {
                            divsToRemove.push(children[i]);
                        }
                    }
                }
            }

            length = divsToRemove.length;
            for (i = 0; i < length; i++) {
                divsToRemove[i].parentNode.removeChild(divsToRemove[i]);
            }

            if (element.childNodes.length == 1) {
                var niceJobElement = document.createElement('div');

                ilios.utilities.removeAllChildren(element);

                niceJobElement.setAttribute('style',
                                            'margin-top: 152px; margin-left: 36%; font-weight: bold;');
                niceJobElement.innerHTML
                                  = ilios_i18nVendor.getI18NString('general.phrases.you_are_done');
                element.appendChild(niceJobElement);

                element = document.getElementById('ua_assign_cohortless_button');
                element.setAttribute('disabled', 'true');
            }

            element = document.getElementById('ua_assignment_result_status');
            element.innerHTML
                    = '' + userCount + ' '
                        + ilios_i18nVendor.getI18NString('management.user_accounts.associations_made');
        },

        failure: function (resultObject) {
               ilios.global.defaultAJAXFailureHandler(resultObject);
        }};

    YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);
};

// We handle this one differently to allow the server side to stream a potentially large set
//      of users as opposed to developing an in-memory array structure on the server which then
//      gets JSON'd back
ilios.management.transaction.performUserSearch = function (searchTerms) {
    var searchInfoElement = document.getElementById('user_search_status');

    if (searchTerms.trim().length < 2) {
        searchInfoElement.innerHTML = ilios_i18nVendor.getI18NString('general.error.query_length');
    }
    else {
        var url = controllerURL + "searchAllUsers?query=" + searchTerms;
        var dataSourceCallback = {
            success: function (requestObject, resultObject) {
                ilios.management.user_accounts.populateUserSearchResultList(resultObject.results);

                if (resultObject.results.length == 0) {
                    var createAccountButtonStr
                        = ilios_i18nVendor.getI18NString('management.user_accounts.create_button');
                    var cancelStr = ilios_i18nVendor.getI18NString('general.terms.cancel');
                    var createNewUserStr
                        = ilios_i18nVendor.getI18NString('general.phrases.search.no_match')
                            + '<br/><br/>'
                            + ilios_i18nVendor.getI18NString('management.user_accounts.create_new_1')
                            + ' ' + createAccountButtonStr + ' '
                            + ilios_i18nVendor.getI18NString('management.user_accounts.create_new_2')
                            + ' ' + cancelStr + '.';

                    document.getElementById('user_search_status').innerHTML
                                = ilios_i18nVendor.getI18NString('general.phrases.search.no_match');

                    ilios.alert.inform('<p style="margin-bottom:9px; text-align:justify;">'
                                            + createNewUserStr, createAccountButtonStr,
                                            ilios.management.user_accounts.createNewAccount, null,
                                            cancelStr);
                }
                else {
                    document.getElementById('user_search_status').innerHTML = '';
                }
            },

            failure: function (requestObject, resultObject) {
                ilios.global.defaultAJAXFailureHandler(resultObject);

                document.getElementById('user_search_status').innerHTML
                                       = ilios_i18nVendor.getI18NString('general.error.must_retry');
            }};
        var xhrDataSource = new YAHOO.util.XHRDataSource(url);

        xhrDataSource.responseType = YAHOO.util.XHRDataSource.TYPE_XML;
        xhrDataSource.responseSchema = {resultNode: "Result",
                                         fields: [ "first_name", "last_name", "middle_name", "email",
                                                   "uc_uid", "user_id", "phone",
                                                   "primary_school_id", "enabled", "user_sync_ignore"]};

        searchInfoElement.innerHTML = ilios_i18nVendor.getI18NString('general.terms.searching')
                                            + '&hellip;';

        xhrDataSource.sendRequest(null, dataSourceCallback);
    }
};

ilios.management.transaction.addOrUpdateUserAccount = function (addingUser, wasDisabled, isDisabled,
    userId, firstName, middleName, lastName, ucId, email, roleArray, secondaryCohortIds,
    wasSyncIgnored, isSyncIgnored, loginName, password) {
    var url = controllerURL + (addingUser ? "createUserAccount" : "updateUserAccount");
    var method = "POST";
    var paramString = 'user_id=' + userId + '&first_name=' + firstName
        + '&middle_name=' + middleName + '&last_name=' + lastName
        + '&uc_id=' + ucId + '&email=' + email + "&secondary_cohorts=" + secondaryCohortIds
        + '&login=' + loginName + '&password=' + password;

    var ajaxCallback = {
        success: function (resultObject) {
            var parsedObject = null;
            var element = new YAHOO.util.Element(document.getElementById('ua_indeterminate_progress'));
            var name = null;

            element.setStyle('display', 'none');

            try {
                parsedObject = YAHOO.lang.JSON.parse(resultObject.responseText);
            }
            catch (e) {
                ilios.global.defaultAJAXFailureHandler(null, e);

                return;
            }

            if (parsedObject.error != null) {
                var msg = ilios_i18nVendor.getI18NString('management.error.saving_user_account');
                if (YAHOO.lang.isArray(parsedObject.error)) {
                    msg = msg + ":<br>" + parsedObject.error.join("<br>");
                } else {
                    msg = msg + ":<br>" + parsedObject.error;
                }

                ilios.alert.alert(msg);

                return;
            }

            name = firstName + ((middleName != '') ? (' ' + middleName) : '') + ' ' + lastName;

            ilios.management.user_accounts.displayAccountUpdateResult(addingUser, wasDisabled,
                                                                      isDisabled, name, ucId);
        },

        failure: function (resultObject) {
               ilios.global.defaultAJAXFailureHandler(resultObject);
        }};
    var roleString = '';
    var length = roleArray.length;

    for (var i = 0; i < length; i++) {
        if (roleString != '') {
            roleString += ',';
        }

        roleString += roleArray[i];
    }

    paramString += '&roles=' + roleString;

    if (wasDisabled != isDisabled) {
        paramString += '&set_able=' + (isDisabled ? 'disable' : 'enable');
    }

    if (wasSyncIgnored != isSyncIgnored) {
        paramString += '&sync_ignored=' + (isSyncIgnored ? 'y' : 'n');
    }

    YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);
};


/**
 * Issues an XHR callback to retrieve the total numbers of cohortless students
 * and users with sync exceptions awaiting processing.
 */
ilios.management.transaction.updateUserAccountAlertCounts = function () {
    var url = controllerURL + "getUserAccountAlertCounts";
    var method = "POST";
    var paramString = '';
    var ajaxCallback = {
        success: function (resultObject) {
            var parsedObject = null;

            try {
                parsedObject = YAHOO.lang.JSON.parse(resultObject.responseText);
            }
            catch (e) {
                ilios.global.defaultAJAXFailureHandler(null, e);

                return;
            }

            if (parsedObject.error != null) {
                var msg = ilios_i18nVendor.getI18NString('management.error.saving_user_account');

                ilios.alert.alert(msg + ": " + parsedObject.error);

                return;
            }

            ilios.management.cohortlessUserCount = parsedObject.cohortless_user_count;
            ilios.management.syncExceptionsCount = parsedObject.users_with_sync_exceptions_count;
        },

        failure: function (resultObject) {
               ilios.global.defaultAJAXFailureHandler(resultObject);
        }};

    YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);
};
