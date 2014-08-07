/**
 * Defines the following namespaces:
 *
 * ilios.cm.transaction
 *
 * Dependencies:
 *
 * YUI lib (at least YAHOO.util and its dependencies)
 * scripts/ilios_ui.js
 * scripts/ilios_utilities.js
 */

ilios.namespace('cm.transaction');

ilios.cm.transaction.needToSaveRecurrence = false;

ilios.cm.transaction.performCourseSave = function (shouldPublish, publishAsTBD) {
    var url = controllerURL + 'saveCourse',
        method = 'POST',
        paramString = '',
        modelArray = null,
        replacer = ilios.utilities.yahooJSONStringifyStateChangeListenerArgumentsReplacer,
        stringify = ilios.utilities.stringifyObjectAsArray;

    var ajaxCallback = {
            success: function (resultObject) {
                var parsedObject = null;
                var element = null;
                var objectives = null;
                var objective = null;
                var cNumber = 0;
                var objModel = null;
                var key = null;

                try {
                    parsedObject = YAHOO.lang.JSON.parse(resultObject.responseText);
                }
                catch (e) {
                    ilios.global.defaultAJAXFailureHandler(null, e);

                    return;
                }

                // MAY RETURN THIS BLOCK
                if (parsedObject.error != null) {
                    var msg
                          = ilios_i18nVendor.getI18NString('course_management.error.course_save');

                    ilios.alert.alert(msg + ": " + parsedObject.error);

                    return;
                }

//                element = document.getElementById('course_title').parentNode;
                element = YAHOO.util.Dom.getAncestorByClassName('course_title', 'level-1');
                YAHOO.util.Dom.removeClass(element, 'dirty_state');


                ilios.cm.currentCourseModel.setPublishedAsTBD(publishAsTBD);

                element = document.getElementById('parent_publish_status_text');
                ilios.cm.setPublishIdAndUpdateText(ilios.cm.currentCourseModel, element,
                                                   parsedObject.publish_event_id);


                objectives = parsedObject.objectives;
                for (key in objectives) {
                    objective = objectives[key];

                    cNumber = ilios.cm.currentCourseModel.getContainerForObjectiveWithId(objective.dbId);
                    if (cNumber == null) {
                        cNumber
                            = ilios.cm.currentCourseModel.getContainerForObjectiveWithTextMD5(objective.md5);

                        objModel = ilios.cm.currentCourseModel.getObjectiveForContainer(cNumber);
                        objModel.setDBId(objective.dbId);
                    }
                }

                objectives = ilios.cm.currentCourseModel.getObjectives();
                for (key in objectives) {
                    objectives[key].clearDirtyState();
                }

                ilios.cm.currentCourseModel.clearDirtyState();
                ilios.cm.loadedCourseModel = ilios.cm.currentCourseModel.clone();

                ilios.cm.transaction.loadStudentGroupTree();
                ilios.cm.transaction.loadInstructorsModel();

                ilios.cm.handleArchivingLinkVisibility();
                ilios.cm.handleRolloverLinkVisibility();
            },

            failure: function (resultObject) {
                ilios.global.defaultAJAXFailureHandler(resultObject);
            }};

    // MAY RETURN THIS BLOCK
    if (YAHOO.lang.trim(ilios.cm.currentCourseModel.getTitle()).length < 3) {
        var msg = ilios_i18nVendor.getI18NString('course_management.error.course_name_too_short');

        ilios.alert.alert(msg);

        return false;
    }

    paramString = 'course_id=' + ilios.cm.currentCourseModel.getDBId();
    paramString += '&external_id=' + ilios.cm.currentCourseModel.getExternalCourseId();
    paramString += '&should_publish=' + (shouldPublish ? 'true' : 'false');
    paramString += '&title=' + encodeURIComponent(ilios.cm.currentCourseModel.getTitle());
    paramString += '&course_level=' + ilios.cm.currentCourseModel.getCourseLevel();
    paramString += '&start_date=' + ilios.cm.currentCourseModel.getStartDate();
    paramString += '&end_date=' + ilios.cm.currentCourseModel.getEndDate();
    paramString += '&publish_as_tbd=' + (publishAsTBD ? 'true' : 'false');

    paramString += '&clerkship_type_id=' + ilios.cm.currentCourseModel.getClerkshipTypeId();

    modelArray = ilios.cm.currentCourseModel.getCohorts();
    paramString += '&cohort='
                        + encodeURIComponent(stringify(modelArray, replacer));
    modelArray = ilios.cm.currentCourseModel.getDisciplines();
    paramString += '&discipline='
                        + encodeURIComponent(stringify(modelArray, replacer));
    modelArray = ilios.cm.currentCourseModel.getDirectors();
    paramString += '&director='
                        + encodeURIComponent(stringify(modelArray, replacer));
    modelArray = ilios.cm.currentCourseModel.getMeSHItems();
    paramString += '&mesh_term='
                        + encodeURIComponent(stringify(modelArray, replacer));

    // TODO: JH - remove after testing the decoupling for issue #205
    /*modelArray = ilios.cm.currentCourseModel.getLearningMaterials();
    paramString += '&learning_materials='
                        + encodeURIComponent(stringify(modelArray, replacer));*/
    modelArray = ilios.cm.currentCourseModel.getObjectives();
    paramString += '&objective='
                        + encodeURIComponent(stringify(modelArray, replacer));

    YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);

    return false;
};

/**
 * Click-event handler, tied to the "Save as Draft" button in the course container dialog.
 * Initiates the process of saving the currently loaded course in draft-mode.
 * @method saveCourseAsDraft
 */
ilios.cm.transaction.saveCourseAsDraft = function () {
    var saveAsDraftStr, continueStr, yesStr;
    //
    // Check if the current course is already in draft mode.
    // If so, then proceed with saving it directly.
    // Otherwise, throw a confirmation dialog up.
    //
    if (! ilios.cm.currentCourseModel.isPublished()) {
        ilios.cm.transaction.performCourseSave(false, false); // save as draft directly
        return;
    }

    // pull up a confirmation dialog
    saveAsDraftStr = ilios_i18nVendor.getI18NString('course_management.warning.published_course_save_as_draft');
    continueStr = ilios_i18nVendor.getI18NString('general.phrases.want_to_continue');
    yesStr = ilios_i18nVendor.getI18NString('general.terms.yes');

    ilios.alert.inform((saveAsDraftStr + '<br />' + continueStr ), yesStr,
        ilios.cm.transaction.continueSaveCourseAsDraft);
};

/**
 * Click-event handler, tied to the "yes" button in the "save published course as draft?" confirmation dialog.
 * Saves a given currently loaded course as draft.
 * @method continueSaveCourseAsDraft
 * @private
 * @see ilios.cm.transaction.performCourseSave()
 * @see ilios.cm.transaction.saveCourseAsDraft()
 */
ilios.cm.transaction.continueSaveCourseAsDraft = function () {
    this.hide();
    ilios.cm.transaction.performCourseSave(false, false);
};

ilios.cm.transaction.saveAllDirty = function () {
    var sessions, sessionModel;
    var firstChild = null;
    var collapserId = null;
    var keys = [];
    var isLast = false;
    var i, n, key;

    this.hide();

    if (ilios.cm.currentCourseModel.isModelDirty()
            || ((ilios.cm.currentCourseModel.getPublishEventId() != null)
                            && (ilios.cm.currentCourseModel.getPublishEventId() > 0))) {
        ilios.cm.transaction.performCourseSave(false, false);
    }

    sessions = ilios.cm.currentCourseModel.getSessions();
    for (key in sessions) {
        sessionModel = sessions[key];
        if (sessionModel.isModelDirty()
            || ((sessionModel.getPublishEventId() != null) && (sessionModel.getPublishEventId() > 0))) {
            keys.push(key);
        }
    }
    for (i = 0, n = keys.length; i < n; i++) {
        key = keys[i];
        collapserId = ilios.dom.childCollapsingContainerIdForContainerNumber(key);
        firstChild = document.getElementById(collapserId);
        isLast = (n === (i + 1));
        ilios.cm.transaction.performSessionSave(firstChild.firstChild.getAttribute('cnumber'), false, false, isLast);
    }
};

// This will be called from the review dialog panel
ilios.cm.transaction.continueMixedPublish = function () {
    var reviewObject;
    var containerNumber = null;
    var publishAsTBD = false;
    var isLast = false;
    var i, n;

    // save course
    reviewObject = ilios.cm.reviewDialogObjects.courseReviewObject;
    if (reviewObject) {
        publishAsTBD = (reviewObject.checkbox && (! reviewObject.checkbox.checked)) ? true : false;
        ilios.cm.transaction.performCourseSave(true, publishAsTBD);
    }

    // save sessions
    for (i = 0, n = ilios.cm.reviewDialogObjects.sessionReviewObjects.length; i < n; i++) {
        reviewObject = ilios.cm.reviewDialogObjects.sessionReviewObjects[i];
        publishAsTBD = (reviewObject.checkbox && (! reviewObject.checkbox.checked)) ? true : false;
        containerNumber = ilios.cm.currentCourseModel.getContainerForSession(reviewObject.iliosModel);
        isLast = (n === (i + 1));
        ilios.cm.transaction.performSessionSave(containerNumber, true, publishAsTBD, isLast);
    }
};

ilios.cm.transaction.performCoursePublish = function () {
    var publishability = ilios.cm.currentCourseModel.getPublishability();

    if (publishability == AbstractJavaScriptModelForm.prototype.CAN_BE_PUBLISHED) {
        ilios.cm.transaction.performCourseSave(true, false);
    }
    else if (publishability
                   == AbstractJavaScriptModelForm.prototype.MEETS_MINIMAL_PUBLISHING_REQUIREMENTS) {
        ilios.ui.onIliosEvent.fire({action: 'review_dialog_open', cnumber: 0, // cnumber doesn't matter here
                     review_type: 1});
    }
};

ilios.cm.transaction.publishAll = function () {
    var publishability = ilios.cm.currentCourseModel.getTreePublishability();
    var keys = [];
    var isLast = false;
    var i, n;

    this.hide();

    if (publishability == AbstractJavaScriptModelForm.prototype.CAN_BE_PUBLISHED) {
        var sessions = null;

        ilios.cm.transaction.performCourseSave(true, false);

        sessions = ilios.cm.currentCourseModel.getSessions();
        for (var key in sessions) {
            keys.push(key);
        }
        for (i = 0, n = keys.length; i < n; i++) {
            key = keys[i];
            isLast = (n === (i + 1));
            ilios.cm.transaction.performSessionSave(key, true, false, isLast);
        }

    } else if (publishability == AbstractJavaScriptModelForm.prototype.MEETS_MINIMAL_PUBLISHING_REQUIREMENTS) {
        ilios.ui.onIliosEvent.fire({action: 'review_dialog_open', cnumber: 0, review_type: 0});  // cnumber doesn't matter here
    }
};

// @private
ilios.cm.transaction.performSessionSave = function (containerNumber, shouldPublish, publishAsTBD, reloadLearnerGroupsOnSuccess) {
    var url = controllerURL + "saveSession",
        method = "POST",
        paramString = null,
        sessionModel = ilios.cm.currentCourseModel.getSessionForContainer(containerNumber),
        ajaxCallback = null,
        modelArray = null,
        errorString = sessionModel.saveAttemptWarningMessage(),
        replacer = ilios.utilities.yahooJSONStringifyStateChangeListenerArgumentsReplacer,
        stringify = ilios.utilities.stringifyObjectAsArray;
        reloadLearnerGroupsOnSuccess = reloadLearnerGroupsOnSuccess || false;

    if (errorString != null) {
        ilios.alert.alert(errorString);

        return;
    }

    ajaxCallback = {
            success: function (resultObject) {
                var parsedObject = null;
                var returnedContainerNumber = null;
                var model = null;
                var element = null;
                var objectives = null;
                var objective = null;
                var objModel = null;
                var cNumber = 0;

                try {
                    parsedObject = YAHOO.lang.JSON.parse(resultObject.responseText);
                }
                catch (e) {
                    ilios.global.defaultAJAXFailureHandler(null, e);

                    return;
                }

                // MAY RETURN THIS BLOCK
                if (parsedObject.error != null) {
                    var msg
                           = ilios_i18nVendor.getI18NString('course_management.error.session_save');

                    ilios.alert.alert(msg + ": " + parsedObject.error);

                    return;
                }

                returnedContainerNumber = parsedObject.container;

                model = ilios.cm.currentCourseModel.getSessionForContainer(returnedContainerNumber);
                model.setDBId(parsedObject.session_id);
                model.setPublishedAsTBD(publishAsTBD);

                objectives = parsedObject.objectives;
                for (var key in objectives) {
                    objective = objectives[key];

                    cNumber = model.getContainerForObjectiveWithId(objective.dbId);
                    if (cNumber == null) {
                        cNumber = model.getContainerForObjectiveWithTextMD5(objective.md5);

                        objModel = model.getObjectiveForContainer(cNumber);
                        objModel.setDBId(objective.dbId);
                    }
                }

                if (parsedObject.ilm_db_id != null) {
                    model.getIndependentLearningModel().setDBId(parsedObject.ilm_db_id);
                }

                element = document.getElementById('' + containerNumber + '_child_draft_text');
                ilios.cm.setPublishIdAndUpdateText(model, element, parsedObject.publish_event_id);

                // display the learning materials search link
                element = document.getElementById(ilios.cm.lm.generateIdStringForLearningMaterialSearchLink(containerNumber));
                if (element) {
                    element.setAttribute('style', 'display:inline');
                }

                model.clearDirtyState();

                // updated loaded model so any revert correctly features current server state
                ilios.cm.loadedCourseModel.addSessionForContainer(model.clone(), returnedContainerNumber);

                // reload offerings
                ilios.cm.transaction.loadOfferingsForSession(model.getDBId());

                // reload learner groups
                if (reloadLearnerGroupsOnSuccess) {
                    ilios.cm.transaction.refreshAssociatedLearnerGroupsListing();
                }
            },

            failure: function (resultObject) {
                ilios.global.defaultAJAXFailureHandler(resultObject);
            }};

    paramString = 'course_id=' + ilios.cm.currentCourseModel.getDBId() + '&session_id='
                        + sessionModel.getDBId() + '&session_type_id='
                        + sessionModel.getSessionTypeId() + '&cnumber=' + containerNumber
                        + '&should_publish=' + (shouldPublish ? 'true' : 'false')
                        + '&is_supplemental=' + (sessionModel.isSupplemental() ? 'true' : 'false')
                        + '&attire_required='
                                    + (sessionModel.isAttireRequired() ? 'true' : 'false')
                        + '&equipment_required='
                                    + (sessionModel.isEquipmentRequired() ? 'true' : 'false')
                        + '&publish_as_tbd=' + (publishAsTBD ? 'true' : 'false');

    paramString += '&title=' + encodeURIComponent(sessionModel.getTitle());
    paramString += '&description=' + encodeURIComponent(sessionModel.getDescription());

    modelArray = sessionModel.getDisciplines();
    paramString += '&discipline='
                        + encodeURIComponent(stringify(modelArray, replacer));
    modelArray = sessionModel.getMeSHItems();
    paramString += '&mesh_term='
                        + encodeURIComponent(stringify(modelArray, replacer));
    modelArray = sessionModel.getLearningMaterials();
    paramString += '&learning_materials='
                        + encodeURIComponent(stringify(modelArray, replacer));
    modelArray = sessionModel.getObjectives();
    paramString += '&objective='
                        + encodeURIComponent(stringify(modelArray, replacer));

    if (sessionModel.getIndependentLearningModel() != null) {
        var model = sessionModel.getIndependentLearningModel();
        var learnerIdString = '';

        paramString += '&ilm_db_id=' + model.getDBId();

        paramString += '&ilm_hours=' + model.getHours();
        paramString += '&due_date='
                            + ilios.utilities.dateObjectToMySQLFriendly(model.getDueDate(), false);

        modelArray = model.getInstructors();
        paramString += '&ilm_instructors='
                           + encodeURIComponent(stringify(modelArray, replacer));

        modelArray = model.getLearnerGroups();
        for (var key in modelArray) {
            if (learnerIdString.length > 0) {
                learnerIdString += ',';
            }

            learnerIdString += modelArray[key].getDBId();
        }
        paramString += '&ilm_learners=' + learnerIdString;
    }

    YAHOO.util.Connect.initHeader("Content-Type",
                                  "application/x-www-form-urlencoded; charset=UTF-8");
    YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);
};

/**
 * Click-event handler, tied to the "Save as Draft" button in the session container dialog.
 * Initiates the process of saving a given session in draft-mode.
 * @method saveSessionDraft
 * @param {Event} event The click event.
 */
ilios.cm.transaction.saveSessionDraft = function (event) {
    var target = ilios.utilities.getEventTarget(event);
    var containerNumber = target.parentNode.parentNode.parentNode.parentNode.getAttribute('cnumber');
    var sessionModel = ilios.cm.currentCourseModel.getSessionForContainer(containerNumber);
    var saveAsDraftStr, continueStr, yesStr;
    //
    // Check if the given session is already in draft mode.
    // If so, then proceed with saving it directly.
    // Otherwise, throw a confirmation dialog up.
    //
    if (! sessionModel.isPublished()) {
        ilios.cm.transaction.performSessionSave(containerNumber, false, false, true); // save as draft directly
        return;
    }

    // pull up a confirmation dialog
    saveAsDraftStr = ilios_i18nVendor.getI18NString('course_management.warning.published_session_save_as_draft');
    continueStr = ilios_i18nVendor.getI18NString('general.phrases.want_to_continue');
    yesStr = ilios_i18nVendor.getI18NString('general.terms.yes');

    ilios.alert.inform((saveAsDraftStr + '<br />' + continueStr ), yesStr,
        ilios.cm.transaction.continueSaveSessionDraft, {"cnumber": containerNumber});
};

/**
 * Click-event handler, tied to the "yes" button in the "save published session as draft?" confirmation dialog.
 * Saves a given published session as draft.
 * @method continueSaveSessionDraft
 * @param {Event} event The click event.
 * @param {Object} args The event handler arguments object. It expects values keyed off by the following:
 *     "cnumber" ... the session container number
 * @private
 * @see ilios.cm.transaction.performSessionSave()
 * @see ilios.cm.transaction.saveSessionDraft()
 */
ilios.cm.transaction.continueSaveSessionDraft = function (event, args) {
    var containerNumber = args.cnumber;
    this.hide(); // "this" is the confirmation dialog. hide it.
    ilios.cm.transaction.performSessionSave(containerNumber, false, false, true);
};

ilios.cm.transaction.publishSession = function (event) {
    var target = ilios.utilities.getEventTarget(event);
    var containerNumber = target.parentNode.parentNode.parentNode.parentNode.getAttribute('cnumber');
    var model = ilios.cm.currentCourseModel.getSessionForContainer(containerNumber);
    var publishability = model.getPublishability();

    if (publishability== AbstractJavaScriptModelForm.prototype.CAN_BE_PUBLISHED) {
        ilios.cm.transaction.performSessionSave(containerNumber, true, false, true);
    }
    else if (publishability
                   == AbstractJavaScriptModelForm.prototype.MEETS_MINIMAL_PUBLISHING_REQUIREMENTS) {
        ilios.ui.onIliosEvent.fire({action: 'review_dialog_open', cnumber: containerNumber,
                     review_type: 2});
    }
};

ilios.cm.transaction.deleteSession = function (event) {
    var target = ilios.utilities.getEventTarget(event);
    var containerNumber = target.getAttribute('cnumber');
    var sessionModel = ilios.cm.currentCourseModel.getSessionForContainer(containerNumber);
    var deleteSessionStr = null;
    var continueStr = null;
    var yesStr = null;

    // MAY RETURN THIS BLOCK
    if (sessionModel.getDBId() == -1) {
        // Session was never saved, so needn't be nuked on the server side
        ilios.cm.currentCourseModel.removeSessionForContainer(containerNumber);

        ilios.cm.session.removeSessionUIWithContainerNumber(containerNumber);

        return;
    }

    deleteSessionStr = ilios_i18nVendor.getI18NString('course_management.warning.session_delete');
    continueStr = ilios_i18nVendor.getI18NString('general.phrases.want_to_continue');
    yesStr = ilios_i18nVendor.getI18NString('general.terms.yes');

    ilios.alert.inform(('<p style="margin-bottom:9px; text-align:justify;">' + deleteSessionStr
                + '</p><center><b>' + continueStr + '</b></center>'), yesStr,
            ilios.cm.transaction.continueSessionDelete, {"cnumber": containerNumber});
};

/**
 * Button-click event handler, tied to the "ok" button in the "delete session" confirmation dialog.
 * Deletes a given session.
 * @method continueSessionDelete
 * @param {Event} event the click event
 * @param {Object} args the event handler arguments object, expects values keyed off by the following:
 *     "cnumber" ... the session container number
 * @private
 */
ilios.cm.transaction.continueSessionDelete = function (event, args) {
    var containerNumber = args.cnumber;
    var sessionModel = ilios.cm.currentCourseModel.getSessionForContainer(containerNumber);
    var url = controllerURL + "deleteSession";
    var method = "POST";
    var paramString = "session_id=" + sessionModel.getDBId() + "&cnumber=" + containerNumber;
    var ajaxCallback = {
            success: function (resultObject) {
                var parsedObject = null;
                var returnedContainerNumber = null;

                try {
                    parsedObject = YAHOO.lang.JSON.parse(resultObject.responseText);
                }
                catch (e) {
                    ilios.global.defaultAJAXFailureHandler(null, e);

                    return;
                }

                // MAY RETURN THIS BLOCK
                if (parsedObject.error != null) {
                    var msg
                         = ilios_i18nVendor.getI18NString('course_management.error.session_delete');

                    ilios.alert.alert(msg + ": " + parsedObject.error);

                    return;
                }

                returnedContainerNumber = parsedObject.container;

                ilios.cm.currentCourseModel.removeSessionForContainer(returnedContainerNumber);

                // updated loaded model so any revert correctly features current server state
                ilios.cm.loadedCourseModel.removeSessionForContainer(returnedContainerNumber);

                ilios.cm.session.removeSessionUIWithContainerNumber(returnedContainerNumber);

                ilios.cm.transaction.refreshAssociatedLearnerGroupsListing();
            },

            failure: function (resultObject) {
                ilios.global.defaultAJAXFailureHandler(resultObject);
            }};

    this.hide();

    YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);
};

/*
 * This is called on an accept from a 'dirty are you sure' intervention done during a request
 *  for change reversion, if the user chooses 'yes, continue revert'
 *
 * @private
 */
ilios.cm.transaction.continueRevert = function () {
    var element = null;

    this.hide();

    element = document.getElementById(ilios.cm.generateIdStringForObjectivesContainer(-1));
    ilios.utilities.removeAllChildren(element);

    ilios.cm.populateCourseAndSetEnable(ilios.cm.loadedCourseModel.getTitle(),
                                        ilios.cm.loadedCourseModel.getStartDate(),
                                        ilios.cm.loadedCourseModel.getEndDate(),
                                        ilios.cm.loadedCourseModel.getYear(),
                                        ilios.cm.loadedCourseModel.getDBId(),
                                        ilios.cm.loadedCourseModel.getExternalCourseId(),
                                        ilios.cm.loadedCourseModel.getUniqueId(),
                                        ilios.cm.loadedCourseModel.getCourseLevel(),
                                        ilios.cm.loadedCourseModel.getPublishEventId(),
                                        ilios.cm.loadedCourseModel.getCohorts(),
                                        ilios.cm.loadedCourseModel.getCompetencies(),
                                        ilios.cm.loadedCourseModel.getDisciplines(),
                                        ilios.cm.loadedCourseModel.getDirectors(),
                                        ilios.cm.loadedCourseModel.getMeSHItems(),
                                        ilios.cm.loadedCourseModel.getObjectives(),
                                        ilios.cm.loadedCourseModel.getSessions(),
                                        ilios.cm.loadedCourseModel.getAssociatedLearners(),
                                        ilios.cm.loadedCourseModel.isLocked(),
                                        ilios.cm.loadedCourseModel.isPublishedAsTBD());
};

/*
 * Messaged from the revert UI button.
 */
ilios.cm.transaction.revertChanges = function () {
    if (ilios.cm.currentCourseModel.isModelDirty()) {
        var dirtyStr = ilios_i18nVendor.getI18NString('general.warning.dirty_state_lose_changes');
        var continueStr = ilios_i18nVendor.getI18NString('general.phrases.want_to_continue');
        var yesStr = ilios_i18nVendor.getI18NString('general.terms.yes');

        ilios.alert.inform(('<p style="margin-bottom:9px; text-align:justify;">' + dirtyStr + '</p><center><b>'
                    + continueStr + '</b></center>'), yesStr, ilios.cm.transaction.continueRevert);

        return;
    }
};

ilios.cm.transaction.returnToDashboard = function () {
    this.cancel();

    window.location.href = '/ilios.php/dashboard_controller';
};

/**
 * Button-click event handler, tied to the "no" button in the "lock/archive course" confirmation dialog.
 * Locks the current course.
 * @method continueCourseLock
 * @param {Event} event the click event
 * @param {Object} args the event handler arguments object.
 * @private
 */
ilios.cm.transaction.continueCourseLock = function (event, args) {
    var url = controllerURL + 'lockCourse';
    var method = "POST";
    var model = ilios.cm.currentCourseModel;
    var element = document.getElementById('archive_radio_lock_and_archive');
    var willArchive = (model.isLocked() || element.checked);
    var paramString = 'course_id=' + model.getDBId()
                            + '&archive=' + (willArchive ? 'true' : 'false');
    var ajaxCallback = {
            success: function (resultObject) {
                var parsedObject = null;
                var msg = null;

                try {
                    parsedObject = YAHOO.lang.JSON.parse(resultObject.responseText);
                }
                catch (e) {
                    ilios.global.defaultAJAXFailureHandler(null, e);

                    return;
                }

                // MAY RETURN THIS BLOCK
                if (parsedObject.error != null) {
                    msg = ilios_i18nVendor.getI18NString('course_management.error.archiving');

                    ilios.alert.alert(msg + ": " + parsedObject.error);

                    return;
                }

                if (willArchive) {
                    var okStr = ilios_i18nVendor.getI18NString('general.terms.ok');

                    msg = ilios_i18nVendor.getI18NString('course_management.archiving.file_finished');

                    ilios.alert.alert(msg, okStr, ilios.cm.transaction.returnToDashboard);
                }
                else {
                    model.setLocked(true);
                    ilios.cm.setDisplayAsLocked();
                }
            },

            failure: function (resultObject) {
                ilios.global.defaultAJAXFailureHandler(resultObject);
            }};

    this.cancel();  // (close the popup)

    YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);
};

ilios.cm.transaction.lockArchiveCourse = function () {
    var element = document.getElementById('archive_radio_lock_and_archive');
    var model = ilios.cm.currentCourseModel;
    var warningStr = (model.isLocked() || element.checked)
                        ? ilios_i18nVendor.getI18NString('course_management.archiving.confirm.lock_and_archive')
                        : ilios_i18nVendor.getI18NString('course_management.archiving.confirm.lock');
    var continueStr = ilios_i18nVendor.getI18NString('general.phrases.want_to_continue');
    var yesStr = ilios_i18nVendor.getI18NString('general.terms.yes');
    var noStr = ilios_i18nVendor.getI18NString('general.terms.cancel');

    ilios.cm.archivingDialog.cancel();

    ilios.alert.inform(('<p style="margin-bottom:9px; text-align:justify;">' + warningStr
                + '</p><center><b>' + continueStr + '</b></center>'),
            yesStr, ilios.cm.transaction.continueCourseLock, null, noStr);

};

ilios.cm.transaction.performMultiOfferingSave = function (lightboxDialog) {
    var recurring = ilios.cm.session.mo.lightboxStateIsRecurring();
    var strategy = ilios.cm.session.mo.getSelectedLearnerGroupAssignmentStrategy();
    var containerNumber = lightboxDialog.cnumber;
    var startDate = ilios.utilities.dateObjectToMySQLFriendly(ilios.cm.session.mo.getTimeMarker(true), true);
    var endDate = ilios.utilities.dateObjectToMySQLFriendly(ilios.cm.session.mo.getTimeMarker(false), true);
    var gids = [];
    var selectedModels = [];
    var i, n;

    if ("roots" === strategy) { // get parent groups
        selectedModels = ilios.lg.picker.getSelectedLearnerModelsRootsFromTree(ilios.lg.picker.learnerTreeModel);
    } else { // get subgroups
        selectedModels = ilios.lg.picker.getSelectedLearnerModelsLeavesFromTree(ilios.lg.picker.learnerTreeModel);
    }

    // extact group ids
    for (i = 0, n = selectedModels.length; i < n; i++) {
        gids.push(selectedModels[i].getDBId());
    }

    ilios.cm.transaction.performOfferingSave(gids.join(","), containerNumber, startDate, endDate,
        (recurring ? ilios.cm.session.mo.inEditRecurringEventModel : null));
};

// @private
ilios.cm.transaction.performOfferingSave = function (gidsString, containerNumber, startDate, endDate, recurringEventModel) {
    var url = controllerURL + 'multiOfferingSave';
    var method = "POST";
    var recurring = (recurringEventModel != null);
    var sessionModel = ilios.cm.currentCourseModel.getSessionForContainer(containerNumber);
    var paramString = "session_id=" + sessionModel.getDBId()
        + "&start_date=" + encodeURIComponent(startDate) + "&end_date=" + encodeURIComponent(endDate)
        + "&cnumber=" + containerNumber
        + "&is_recurring=" + (recurring ? 'true' : 'false');

    var ajaxCallback = {
        success: function (resultObject) {
            var parsedObject = null;
            var model = null;

            try {
                parsedObject = YAHOO.lang.JSON.parse(resultObject.responseText);
            } catch (e) {
                ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,'course_management.saving_offerings');
                ilios.alert.updateServerInteractionProgress();

                ilios.global.defaultAJAXFailureHandler(null, e);

                return;
            }

            // MAY RETURN THIS BLOCK
            if (parsedObject.error != null) {
                var msg = ilios_i18nVendor.getI18NString('offering_management.error.failed_save');

                ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings, 'course_management.saving_offerings');
                ilios.alert.updateServerInteractionProgress();

                ilios.alert.alert(msg + ": " + parsedObject.error);

                return;
            }

            model = ilios.cm.currentCourseModel.getSessionForContainer(parsedObject.container);
            // model.removeAllOfferings();
            ilios.cm.transaction.loadOfferingsForSession(model.getDBId());

            ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings, 'course_management.saving_offerings');
            ilios.alert.updateServerInteractionProgress();

            ilios.cm.transaction.refreshAssociatedLearnerGroupsListing();

            if (ilios.cm.transaction.needToSaveRecurrence && recurring) {
                ilios.cm.transaction.createEventsResultingFromRecurrence(parsedObject.container);
            }
        },

        failure: function (resultObject) {
            ilios.global.defaultAJAXFailureHandler(resultObject);
        }
    };

    paramString += "&gids=" + gidsString;

    if (recurring) {
        ilios.cm.transaction.needToSaveRecurrence = true;

        recurringEventModel.generateMySQLEndDate();

        paramString += "&recurring_event=" + encodeURIComponent(YAHOO.lang.JSON.stringify(recurringEventModel));
    }

    ilios.alert.networkActivityI18NStrings.push('course_management.saving_offerings');
    ilios.alert.updateServerInteractionProgress();

    YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);
};

/*
 * This is called on a successful save of an offering where its recurrence has been saved for the
 *  first time.
 *
 * Should be considered @private
 */
ilios.cm.transaction.createEventsResultingFromRecurrence = function (containerNumber) {
    var currentDate = new Date(ilios.cm.session.mo.getTimeMarker(true).getTime());
    var strategy = ilios.cm.session.mo.getSelectedLearnerGroupAssignmentStrategy();
    var currentDay = currentDate.getDay();
    var calendarEndDate = ilios.cm.session.mo.getTimeMarker(false);
    var currentEndDate = new Date(calendarEndDate.getTime());
    var patternEndDate
                = new Date(ilios.cm.session.mo.inEditRecurringEventModel.getEndDate().getTime());
    var dayPattern = ilios.cm.session.mo.inEditRecurringEventModel.getRawEventDays();
    var startEndDayDelta =
            Math.floor((currentEndDate.getTime() - currentDate.getTime()) / (1000 * 60 * 60 * 24));
    var saveStartDate = null;
    var saveEndDate = null;
    var gids = [];
    var selectedModels =[];
    var i, n;

    if ("roots" === strategy) { // get parent groups
        selectedModels = ilios.lg.picker.getSelectedLearnerModelsRootsFromTree(ilios.lg.picker.learnerTreeModel);
    } else { // get subgroups
        selectedModels = ilios.lg.picker.getSelectedLearnerModelsLeavesFromTree(ilios.lg.picker.learnerTreeModel);
    }

    // extact group ids
    for (i = 0, n = selectedModels.length; i < n; i++) {
        gids.push(selectedModels[i].getDBId());
    }

    gids = gids.join(",");

    ilios.cm.transaction.needToSaveRecurrence = false;
    ilios.cm.session.mo.inEditRecurringEventModel = null;

    patternEndDate.setHours(calendarEndDate.getHours(),
                            calendarEndDate.getMinutes(),
                            calendarEndDate.getSeconds(),
                            calendarEndDate.getMilliseconds());
    currentDate.setDate(currentDate.getDate()
                            + ilios.cm.transaction.getDayDeltaToNextRecurrence(currentDay,
                                                                               dayPattern));

    while (currentDate <= patternEndDate) {
        currentEndDate.setFullYear(currentDate.getFullYear(), currentDate.getMonth(),
                                   currentDate.getDate());
        currentEndDate.setDate(currentEndDate.getDate() + startEndDayDelta);

        saveStartDate = ilios.utilities.dateObjectToMySQLFriendly(currentDate, true);
        saveEndDate = ilios.utilities.dateObjectToMySQLFriendly(currentEndDate, true);

        ilios.cm.transaction.performOfferingSave(gids, containerNumber, saveStartDate, saveEndDate,
                                                 null);

        currentDay = currentDate.getDay();
        currentDate.setDate(currentDate.getDate()
                                + ilios.cm.transaction.getDayDeltaToNextRecurrence(currentDay,
                                                                                   dayPattern));
    }
};

/*
 * This is a helper method to calculate how many days are between the current day (day, not date)
 *  and the next day (day, not date) of an offering as defined by the pattern.
 *
 * Should be considered @private
 */
ilios.cm.transaction.getDayDeltaToNextRecurrence = function (currentDay, dayPattern) {
    var rhett = (currentDay + 1);

    while (dayPattern[(rhett % 7)] == 0) {
        rhett++;
    }

    return (rhett - currentDay);
};

ilios.cm.transaction.loadOfferingsForSession = function (sessionId) {
    var url = controllerURL + "getOfferingsForSession";
    var method = "POST";
    var paramString = "session_id=" + sessionId;
    var ajaxCallback = {
            success: function (resultObject) {
                var parsedObject = null;
                var sessionModel = null;
                var offeringModel = null;
                var i, n;

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

                sessionModel = ilios.cm.currentCourseModel.getSessionWithId(sessionId);


                if (sessionModel) {

                    sessionModel.removeAllOfferings();
                    sessionModel.setOfferingCount(-1);

                    for (i = 0, n = parsedObject.length; i < n; i++) {
                        offeringModel = new OfferingModel(parsedObject[i]);
                        sessionModel.addOffering(offeringModel);
                    }

                    var cNumber = ilios.cm.currentCourseModel.getContainerForSession(sessionModel);
                    var element = document.getElementById(ilios.cm.session.generateIdStringForSessionOfferingEnumerationList(cNumber));

                    sessionModel.sortOfferings();

                    element.innerHTML = sessionModel.getOfferingsEnumerationFormattedHTML();

                    element = document.getElementById(ilios.cm.session.generateIdStringForSessionOfferingsLink(cNumber));
                    element.innerHTML = ilios.cm.session.generateSessionOfferingsLinkText(sessionModel.getOfferingCount());
                    ilios.cm.session.updatePublishButtonForSession(sessionModel, cNumber);
                }

                ilios.cm.updatePublishAllUI();

                ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings, 'general.phrases.loading_offerings');
                ilios.alert.updateServerInteractionProgress();
            },

            failure: function (resultObject) {
                ilios.global.defaultAJAXFailureHandler(resultObject);
            }};

    ilios.alert.networkActivityI18NStrings.push('general.phrases.loading_offerings');
    ilios.alert.updateServerInteractionProgress();

    YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);
};

/*
 * This does an asynchronous load of all of the objectives associated to cohorts assigned to the
 *  course..
 *
 * TODO this must be invoked each time the course cohorts changes - in addition for looking for
 *  breakages due to cohort removal
 */
ilios.cm.transaction.loadCohortObjectives = function () {
    var url = controllerURL + "getCohortObjectives";
    var method = "POST";
    var paramString = null;
    var i,n;
    var model;
    var cohortIds = [];
    var cohorts = ilios.cm.currentCourseModel.getCohorts();
    var ajaxCallback = {
            success: function (resultObject) {
                var parsedObject = null;
                var pcObjectiveModel = null;
                var cohortObjectiveDto;
                var objectiveArray = null;
                var element = null;
                var i, j, n, o;

                try {
                    parsedObject = YAHOO.lang.JSON.parse(resultObject.responseText);
                }
                catch (e) {
                    ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                                                           'course_management.loading_objectives');
                    ilios.alert.updateServerInteractionProgress();

                    ilios.global.defaultAJAXFailureHandler(null, e);

                    return;
                }

                // MAY RETURN THIS BLOCK
                if (parsedObject.error != null) {
                    var msg
                        = ilios_i18nVendor.getI18NString('course_management.error.objective_load');

                    ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                                                           'course_management.loading_objectives');
                    ilios.alert.updateServerInteractionProgress();

                    ilios.alert.alert(msg + ": " + parsedObject.error);

                    return;
                }

                ilios.cm.programCohortObjectives = [];
                for (i = 0, n = parsedObject.length; i < n; i++) {
                    cohortObjectiveDto = parsedObject[i];
                    pcObjectiveModel = new Object();
                    pcObjectiveModel.title = cohortObjectiveDto.title;
                    pcObjectiveModel.cohortId = cohortObjectiveDto.cohort_id;
                    pcObjectiveModel.belongsToActiveSchool = cohortObjectiveDto.is_active_school;
                    objectiveArray = cohortObjectiveDto.objectives;
                    pcObjectiveModel.objectives = [];
                    for (j = 0, o = objectiveArray.length; j < o; j++) {
                        pcObjectiveModel.objectives.push(new ObjectiveModel(objectiveArray[j]));
                    }
                    ilios.cm.programCohortObjectives.push(pcObjectiveModel);
                }

                ilios.cm.repopulateListedCourseCompetencies(false);

                ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                                                       'course_management.loading_objectives');
                ilios.alert.updateServerInteractionProgress();
            },

            failure: function (resultObject) {
                ilios.global.defaultAJAXFailureHandler(resultObject);
            }};


    if (cohorts.length == 0) {
        return;
    }

    paramString = "cohort_id=";
    for (i = 0, n = cohorts.length; i < n; i++) {
        model = cohorts[i];

        if (model) {
            cohortIds.push(model.cohortId);
        }
    }

    paramString += cohortIds.join();

    ilios.alert.networkActivityI18NStrings.push('course_management.loading_objectives');
    ilios.alert.updateServerInteractionProgress();

    YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);
};

/*
 * This does an asynchronous load of the student group tree. We unfortunately need call this
 *  expensive thing mid-page lifespan since it depends on the cohorts associated to the course;
 *  we optimize its cost by launching this asynchronously on a successful course save, and after
 *  the initial course load.
 *
 * Should be considered @protected
 */
ilios.cm.transaction.loadStudentGroupTree = function () {
    var url = controllerURL + "getStudentGroupTrees";
    var method = "POST";
    var paramString = "course_id=" + ilios.cm.currentCourseModel.getDBId();
    var ajaxCallback = {
            success: function (resultObject) {
                var parsedObject = null;
                var returnedProgramModel = null;
                var returnedGroupModel = null;
                var topLevelModel = null;

                try {
                    parsedObject = YAHOO.lang.JSON.parse(resultObject.responseText);
                }
                catch (e) {
                    ilios.global.defaultAJAXFailureHandler(null, e);

                    return;
                }

                // MAY RETURN THIS BLOCK
                if (parsedObject.error != null) {
                    var msg
                     = ilios_i18nVendor.getI18NString('course_management.error.student_group_load');

                    ilios.alert.alert(msg + ": " + parsedObject.error);

                    return;
                }

                ilios.lg.picker.learnerTreeModel = new Array();
                for (var key in parsedObject) {
                    returnedProgramModel = parsedObject[key];

                    topLevelModel = new Object();
                    topLevelModel.program_title = returnedProgramModel.title;
                    topLevelModel.groups = new Array();

                    for (var key2 in returnedProgramModel.groups) {
                        returnedGroupModel = returnedProgramModel.groups[key2];

                        topLevelModel.groups.push(
                            ilios.cm.session.mo.recursivelyBuildSimplifiedGroupModel(returnedGroupModel));
                    }

                    ilios.lg.picker.learnerTreeModel.push(topLevelModel);
                }
            },

            failure: function (resultObject) {
                ilios.global.defaultAJAXFailureHandler(resultObject);
            }};

    YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);
};

/*
 * Similar to the design function of ilios.cm.transaction.loadStudentGroupTree, this does an
 *  asynchronous load of the instructor list. We need call this mid-page lifespan since it, too,
 *  depends on the cohorts associated to the course; we optimize its cost by launching it
 *  asynchronously on a successful course save, and after the initial course load.
 *
 * Should be considered @protected
 */
ilios.cm.transaction.loadInstructorsModel = function () {
    var url = controllerURL + "getAvailableInstructors";
    var method = "POST";
    var paramString = "course_id=" + ilios.cm.currentCourseModel.getDBId();
    var ajaxCallback = {
            success: function (resultObject) {
                var parsedObject = null;
                var model = null;
                var dbObjectRepresentation = null;
                var anArray = null;
                var key = null;

                try {
                    parsedObject = YAHOO.lang.JSON.parse(resultObject.responseText);
                }
                catch (e) {
                    ilios.global.defaultAJAXFailureHandler(null, e);

                    return;
                }

                // MAY RETURN THIS BLOCK
                if (parsedObject.error != null) {
                    var msg
                     = ilios_i18nVendor.getI18NString('course_management.error.instructor_load');

                    ilios.alert.alert(msg + ": " + parsedObject.error);

                    return;
                }

                ilios.cm.session.ilm.instructorsModel = new Array();

                anArray = parsedObject.instructor_groups;
                for (key in anArray) {
                    dbObjectRepresentation = anArray[key];

                    model = new Object();
                    model.isGroup = true;
                    model.dbId = dbObjectRepresentation.instructor_group_id;
                    model.title = dbObjectRepresentation.title;

                    ilios.cm.session.ilm.instructorsModel.push(model);
                }

                anArray = parsedObject.faculty;
                for (key in anArray) {
                    dbObjectRepresentation = anArray[key];

                    model = new UserModel(dbObjectRepresentation);
                    model.isGroup = false;

                    ilios.cm.session.ilm.instructorsModel.push(model);
                }
            },

            failure: function (resultObject) {
                ilios.global.defaultAJAXFailureHandler(resultObject);
            }};

    YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);
};

// @private
ilios.cm.transaction.refreshAssociatedLearnerGroupsListing = function () {
    var url = controllerURL + "getLearnerGroupIdsAndTitles";
    var method = "POST";
    var paramString = "course_id=" + ilios.cm.currentCourseModel.getDBId();
    var ajaxCallback = {
            success: function (resultObject) {
                var parsedObject = null;
                var element = null;

                try {
                    parsedObject = YAHOO.lang.JSON.parse(resultObject.responseText);
                }
                catch (e) {
                    ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                                                           'course_management.refreshing_associated_groups');
                    ilios.alert.updateServerInteractionProgress();

                    ilios.global.defaultAJAXFailureHandler(null, e);

                    return;
                }

                // MAY RETURN THIS BLOCK
                if (parsedObject.error != null) {
                    var msg
                     = ilios_i18nVendor.getI18NString(
                                                'course_management.error.associated_group_refresh');

                    ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                                                           'course_management.refreshing_associated_groups');
                    ilios.alert.updateServerInteractionProgress();

                    ilios.alert.alert(msg + ": " + parsedObject.error);

                    return;
                }

                ilios.cm.currentCourseModel.setAssociatedLearners(parsedObject.learners);
                element = document.getElementById('course_associated_learners');
                element.innerHTML = ilios.cm.currentCourseModel.getAssociatedLearnersAsFormattedText();

                ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                                                       'course_management.refreshing_associated_groups');
                ilios.alert.updateServerInteractionProgress();
            },

            failure: function (resultObject) {
                ilios.global.defaultAJAXFailureHandler(resultObject);
            }};

    ilios.alert.networkActivityI18NStrings.push('course_management.refreshing_associated_groups');
    ilios.alert.updateServerInteractionProgress();

    YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);
};

ilios.cm.transaction.associateLearningMaterial = function (learningMaterialId, dbId, isCourseDBId) {
    var url = learningMaterialsControllerURL + 'associateLearningMaterial';
    var method = "POST";
    var paramString = "learning_material_id=" + learningMaterialId + "&"
                        + (isCourseDBId ? "course_id" : "session_id") + "=" + dbId;
    var ajaxCallback = {
            success: function (resultObject) {
                var parsedObject = null;
                var containerNumber = ilios.cm.lm.learningMaterialDialog.cnumber;

                try {
                    parsedObject = YAHOO.lang.JSON.parse(resultObject.responseText);
                }
                catch (e) {
                    ilios.global.defaultAJAXFailureHandler(null, e);

                    return;
                }

                // MAY RETURN THIS BLOCK
                if (parsedObject.error != null) {
                    var msg = ilios_i18nVendor.getI18NString('learning_material.error.associate');
                    var isCourse = (containerNumber == -1);
                    var model = isCourse ? ilios.cm.currentCourseModel
                                         : ilios.cm.currentCourseModel.getSessionForContainer(containerNumber);
                    var learningMaterialId = parsedObject.learning_material_id;

                    model.removeLearningMaterialWithId(learningMaterialId);

                    ilios.alert.alert(msg + ": " + parsedObject.error);

                    return;
                }

                //TODO: JH - need to comment/document better...

                //JH - need to drop the populateLearningMaterialList and
                //work with newly-set up div elements...
                containerNumber = ilios.cm.lm.learningMaterialDialog.cnumber;

                isCourse = (containerNumber == -1);
                model = isCourse ? ilios.cm.currentCourseModel
                    : ilios.cm.currentCourseModel.getSessionForContainer(containerNumber);

                learningMaterialId = parsedObject.learning_material_id;
                //add the learning material to new div-based list in Course/session...
                ilios.cm.lm.addNewLearningMaterialToDom(containerNumber, learningMaterialId);
            },

            failure: function (resultObject) {
                ilios.global.defaultAJAXFailureHandler(resultObject);
            }};

    YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);
};

ilios.cm.transaction.disassociateLearningMaterial = function (learningMaterialId, dbId,
                                                              isCourseDBId) {
    var url = learningMaterialsControllerURL + 'disassociateLearningMaterial';
    var method = "POST";
    var paramString = "learning_material_id=" + learningMaterialId + "&"
                                + (isCourseDBId ? "course_id" : "session_id") + "=" + dbId;
    var ajaxCallback = {
            success: function (resultObject) {
                var parsedObject = null;
                var containerNumber = null;
                var learningMaterialId = null;
                var model = null;
                var isCourse = false;

                try {
                    parsedObject = YAHOO.lang.JSON.parse(resultObject.responseText);
                }
                catch (e) {
                    ilios.global.defaultAJAXFailureHandler(null, e);

                    return;
                }

                // MAY RETURN THIS BLOCK
                if (parsedObject.error != null) {
                    var msg
                           = ilios_i18nVendor.getI18NString('learning_material.error.disassociate');

                    ilios.alert.alert(msg + ": " + parsedObject.error);

                    return;
                }

                containerNumber = ilios.cm.lm.learningMaterialDialog.cnumber;

                isCourse = (containerNumber == -1);
                model = isCourse ? ilios.cm.currentCourseModel
                                 : ilios.cm.currentCourseModel.getSessionForContainer(containerNumber);

                learningMaterialId = parsedObject.learning_material_id;
            },

            failure: function (resultObject) {
                ilios.global.defaultAJAXFailureHandler(resultObject);
            }};

    YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);
};

ilios.cm.transaction.modifyLearningMaterial = function (statusSelectorElement) {
    var learningMaterialId = ilios.common.lm.learningMaterialsDetailsModel.getDBId();
    var statusId = statusSelectorElement.options[statusSelectorElement.selectedIndex].value;
    var url = learningMaterialsControllerURL + 'modifyLearningMaterial';
    var method = "POST";
    var paramString = "learning_material_id=" + learningMaterialId + "&status_id=" + statusId;
    var ajaxCallback = {
            success: function (resultObject) {
                var parsedObject = null;
                var sessions = null;
                var model = null;

                try {
                    parsedObject = YAHOO.lang.JSON.parse(resultObject.responseText);
                }
                catch (e) {
                    ilios.global.defaultAJAXFailureHandler(null, e);

                    return;
                }

                // MAY RETURN THIS BLOCK
                if (parsedObject.error != null) {
                    var msg = ilios_i18nVendor.getI18NString('learning_material.error.modify');

                    ilios.alert.alert(msg + ": " + parsedObject.error);

                    return;
                }

                model = ilios.cm.currentCourseModel.getLearningMaterialForId(
                                                                parsedObject.learning_material_id);
                if (model != null) {
                    model.setStatusId(parsedObject.status_id);
                }

                sessions = ilios.cm.currentCourseModel.getSessions();
                for (var key in sessions) {
                    model
                        = sessions[key].getLearningMaterialForId(parsedObject.learning_material_id);

                    if (model != null) {
                        model.setStatusId(parsedObject.status_id);
                    }
                }
            },

            failure: function (resultObject) {
                ilios.global.defaultAJAXFailureHandler(resultObject);
            }};

    YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);
};

ilios.cm.transaction.handleAddLearningMaterialUploadClick = function (uploadButtonElement) {
    var containerNumber = ilios.cm.lm.learningMaterialDialog.cnumber;
    var isCourse = (containerNumber == -1);
    var model = isCourse ? ilios.cm.currentCourseModel
                         : ilios.cm.currentCourseModel.getSessionForContainer(containerNumber);
    var url = learningMaterialsControllerURL + 'uploadLearningMaterial';
    var method = "POST";
    var uploadCallback = {
            upload: function (resultObject) {
                var element = null;
                var parsedObject
                        = ilios.utilities.getParsedResponseObjectFromFormUploadResponseText(
                                                                        resultObject.responseText);
                var learningMaterialModel = null;
                var meshItems = null;

                if (parsedObject.error != null) {
                    var msg = ilios_i18nVendor.getI18NString('learning_material.failed_upload');

                    ilios.alert.alert(msg + ": " + parsedObject.error);

                    return;
                }

                element = document.getElementById('alm_transaction_status');
                element.innerHTML
                            = ilios_i18nVendor.getI18NString('learning_material.successful_upload');


                learningMaterialModel = new LearningMaterialModel(parsedObject);
                learningMaterialModel.setOwningUserName(adminUserDisplayName);
                learningMaterialModel.setOwningUserId(currentUserId);
                learningMaterialModel.setRequired(true);

                meshItems = ilios.cm.lm.almLearningMaterialModel.getMeSHItems();
                for (var key in meshItems) {
                    learningMaterialModel.addMeSHItem(meshItems[key]);
                }

                model.addLearningMaterial(learningMaterialModel);
                //TODO: JH - add comments to change...
                ilios.cm.lm.addNewLearningMaterialToDom(containerNumber, learningMaterialModel.dbId);
                ilios.cm.lm.learningMaterialLightboxIsDirty = false;

                ilios.cm.lm.almLearningMaterialModel = null;
                ilios.cm.lm.resetAddLearningMaterialsDialog(ilios.cm.lm.learningMaterialDialog,
                                                            false);
            },

            failure: function (resultObject) {
                ilios.global.defaultAJAXFailureHandler(resultObject);
            }};
    var element = document.getElementById('alm_filepath_value');
    var dataMissing = false;

    if ((element != null) && (document.getElementById('alm_filepath_field') != null)) {
        element.value = document.getElementById('alm_filepath_field').value;
    }

    dataMissing = ((document.getElementById('alm_title').value.length == 0)
                    || (document.getElementById('alm_description').value.length == 0));

    if (! dataMissing) {
        switch (ilios.cm.lm.currentlyShowingTab) {
            case 1:
                dataMissing = ((! document.getElementById('alm_copyright_checkbox').checked)
                                    && (document.getElementById('alm_copyright_rationale')
                                                                               .value.length == 0));

                break;
            case 2:
                element = document.getElementById('alm_web_link');

                break;
            case 3:
                element = document.getElementById('alm_citation');

                break;
        }
    }

    if (! dataMissing) {
        dataMissing = (element.value.length == 0);
    }

    if (dataMissing) {
        var msg = ilios_i18nVendor.getI18NString('learning_material.warning.missing_information');

        ilios.alert.alert(msg);

        return;
    }
    else {
        var uploadForm = document.getElementById('alm_upload_form');

        document.getElementById('alm_displayed_tab_value').value = ilios.cm.lm.currentlyShowingTab;

        YAHOO.util.Connect.setForm(uploadForm, true, true);
        YAHOO.util.Connect.asyncRequest(method, url, uploadCallback, '');
    }
};

ilios.cm.transaction.searchLearningMaterials = function () {
    var url = learningMaterialsControllerURL + "getLearningMaterialDescriptorsForSearch";
    var method = "POST";
    var paramString = "search_string=" + encodeURIComponent(document.getElementById('alm_search_textfield').value);
    var ajaxCallback = {
            success: function (resultObject) {
                var parsedObject = null;
                var element = null;
                var learningMaterial = null;
                var model = null;
                var isCourse = false;
                var containerNumber = null;

                try {
                    parsedObject = YAHOO.lang.JSON.parse(resultObject.responseText);
                }
                catch (e) {
                    ilios.global.defaultAJAXFailureHandler(null, e);

                    return;
                }

                // MAY RETURN THIS BLOCK
                if (parsedObject.error != null) {
                    var msg = ilios_i18nVendor.getI18NString('learning_material.error.search');

                    ilios.alert.alert(msg + ": " + parsedObject.error);

                    return;
                }

                containerNumber = ilios.cm.lm.learningMaterialDialog.cnumber;

                isCourse = (containerNumber == -1);
                model = isCourse ? ilios.cm.currentCourseModel
                                 : ilios.cm.currentCourseModel.getSessionForContainer(containerNumber);

                element = document.getElementById('alm_search_results_ul');
                ilios.utilities.removeAllChildren(element);

                for (var key in parsedObject) {
                    learningMaterial = new LearningMaterialModel(parsedObject[key]);

                    if (! model.containsLearningMaterial(learningMaterial)) {
                        element.appendChild(ilios.cm.lm.createListElementForLearningMaterial(learningMaterial,
                                                                                             true,
                                                                                             null));
                    }
                }
            },

            failure: function (resultObject) {
                ilios.global.defaultAJAXFailureHandler(resultObject);
            }};

    YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);
};

// @private
ilios.cm.transaction.addCourseFromModalPanelResponse = function (type, args) {
    if ((args[0].event == 'ajaxresponse') && (args[0].object == 'modal_dialog_panel')) {
        if (args[0].action == 'succeed') {
            var parsedObject = null;

            try {
                parsedObject = YAHOO.lang.JSON.parse(args[0].data);
            }
            catch (e) {
                ilios.global.defaultAJAXFailureHandler(null, e);

                return;
            }

            if (args[0].target == "course_add_picked") {
                var sessionContainer = null;

                if (parsedObject.error != null) {
                    var i18nStr
                         = ilios_i18nVendor.getI18NString('course_management.error.course_create');

                    ilios.alert.alert(i18nStr + ": " + parsedObject.error);

                    return;
                }
                // force page reloading
                var newUrl = window.location.protocol + "//" + window.location.host
                    + window.location.pathname + "?course_id=" + parsedObject.course_id;
                window.location.href = newUrl;
                return;
            }
        }
        else {
            ilios.alert.alert(ilios_i18nVendor.getI18NString('course_management.error.course_create'));

            return;
        }
    }
};

ilios.ui.onIliosEvent.subscribe(ilios.cm.transaction.addCourseFromModalPanelResponse);


/*
 * POSTS learning material updates to controller for update
 *
 * @param object model the course/session
 * @param int lmDbId the learning material DbId
 * @param string isCourse 'true' if it is a course, 'false' if it is a session
 * @param int courseOrSessionDbId the dbId of the course or session
 */

ilios.cm.transaction.updateLearningMaterial = function (model, lmDbId, isCourse, courseOrSessionDbId, cnumber, lmnumber) {
    var url = learningMaterialsControllerURL + 'updateLearningMaterial';
    var method = "POST",
            paramString = '',
            replacer = ilios.utilities.yahooJSONStringifyStateChangeListenerArgumentsReplacer,
            stringify = ilios.utilities.stringifyObjectAsArray;

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

            // MAY RETURN THIS BLOCK
            if (parsedObject.error != null) {
                var msg
                    = ilios_i18nVendor.getI18NString('learning_material.error.learning_material_update');

                ilios.alert.alert(msg + ": " + parsedObject.error);

                return;
            }

            //get the container/lm number to easily update the 'Add Mesh' button total
            var lmnumber = parsedObject.lmnumber;
            var cnumber = parsedObject.cnumber;
            var meshTotal = parsedObject.meshTotal;
            console.log('mesh total' + meshTotal);
            var lmDbId = parsedObject.lmDbId;

            //update the mesh count
            ilios.cm.lm.updateLearningMaterialMeSHCount(cnumber, lmnumber, meshTotal);
       },

        failure: function (resultObject) {
            ilios.global.defaultAJAXFailureHandler(resultObject);
        }};

    paramString += 'is_course=' + isCourse;
    paramString += '&course_id=' + courseOrSessionDbId;
    paramString += '&container_number=' + cnumber;
    paramString += '&lm_number=' + lmnumber;
    paramString += '&lmDbId=' + lmDbId;
    var modelArray = model.getLearningMaterials();
    paramString += '&learning_materials='
        + encodeURIComponent(stringify(modelArray, replacer));
    YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);

    return false;
};