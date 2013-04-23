/**
 * DEPENDENCY: YUI lib (at least YAHOO.util and its dependencies)
 * DEPENDENCY: scripts/ilios_ui.js
 * DEPENDENCY: scripts/ilios_utilities.js
 * DEPENDENCY: program_manager_dom.js
 * DEPENDENCY: program_model.js
 * DEPENDENCY: program_year_model.js
 */

ilios.namespace('pm.transaction');

/*
 * TODO since this method is async, we should give feedback to the user that something is
 *          happening
 */
ilios.pm.transaction.performProgramSave = function (shouldPublish) {
    var url = controllerURL + "saveProgram";
    var method = 'POST';
    var paramString = "publish=" + (shouldPublish ? "true" : "false")
                        + "&program_id=" + ilios.pm.currentProgramModel.getDBId();
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
                          = ilios_i18nVendor.getI18NString('program_management.error.program_save');

                    ilios.alert.alert(msg + ": " + parsedObject.error);

                    return;
                }

                ilios.pm.populateProgramAndSetEnable(parsedObject.title, parsedObject.short_title,
                                                     parsedObject.duration, parsedObject.pid,
                                                     (parsedObject.publish == "-1"
                                                        ? null
                                                        : parsedObject.publish),
                                                     true, false, true);
            },

            failure: function (resultObject) {
                ilios.global.defaultAJAXFailureHandler(resultObject);
            }};
    var str = null;
    var element = null;
    var msg = null;

    str = document.getElementById('program_title').value;
    if (str.length == 0) {
        msg = ilios_i18nVendor.getI18NString('program_management.error.no_program_title');

        ilios.alert.alert(msg);

        return false;
    }
    paramString += '&program_title=' + escape(str);

    str = document.getElementById('short_title').value;
    if ((str.length < 1) || (str.length > 10)) {
        msg = ilios_i18nVendor.getI18NString('program_management.error.bad_short_title');

        ilios.alert.alert(msg);

        return false;
    }
    paramString += '&short_title=' + escape(str);

    element = document.getElementById('duration_selector');
    paramString += '&duration=' + element.options[element.selectedIndex].value;

    YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);

    return false;
};

/*
 * This is called when the user has clicked on the Save All UI button and then further ok'd
 *  the action via a modal inform choice panel.
 */
ilios.pm.transaction.saveAllDirty = function () {
    var programYearArray = null;
    var programYear = null;
    var containerNumber = 0;
    var firstChild = null;
    var collapserId = null;

    this.hide();

    if (ilios.pm.currentProgramModel.isModelDirty()) {
        ilios.pm.transaction.performProgramSave(false);
    }

    programYearArray = ilios.pm.currentProgramModel.getProgramYearArray();
    for (var key in programYearArray) {
        programYear = programYearArray[key];

        if (programYear.isModelDirty()) {
            containerNumber
                      = ilios.pm.currentProgramModel.getContainerNumberForProgramYear(programYear);

            collapserId = ilios.dom.childCollapsingContainerIdForContainerNumber(containerNumber);
            firstChild = document.getElementById(collapserId);
            ilios.pm.transaction.performProgramYearSave(firstChild.firstChild.getAttribute('cnumber'),
                                                        false);
        }
    }
};

/*
 * This is called when the user has clicked on the Publish All UI button and then further ok'd
 *  the action via a modal inform choice panel.
 */
ilios.pm.transaction.publishAll = function () {
    var programYearArray = null;
    var programYear = null;
    var containerNumber = 0;
    var firstChild = null;
    var collapserId = null;

    this.hide();

    if (ilios.utilities.canPublishModelItem(ilios.pm.currentProgramModel)) {
        ilios.pm.transaction.performProgramSave(true);
    }

    programYearArray = ilios.pm.currentProgramModel.getProgramYearArray();
    for (var key in programYearArray) {
        programYear = programYearArray[key];

        if (ilios.utilities.canPublishModelItem(programYear)) {
            containerNumber
                    = ilios.pm.currentProgramModel.getContainerNumberForProgramYear(programYear);

            collapserId = ilios.dom.childCollapsingContainerIdForContainerNumber(containerNumber);
            firstChild = document.getElementById(collapserId);
            ilios.pm.transaction.performProgramYearSave(firstChild.firstChild.getAttribute('cnumber'),
                                                        true);
        }
    }
};


/**
 * This is only called once per page load (or rather it should only need be such)
 */
ilios.pm.transaction.loadSchoolDepartmentTree = function () {
    var url = null;
    var method = null;
    var paramString = null;
    var ajaxCallback = null;

    if (ilios.pm.schoolDepartmentTreeModel != null) {
        return;
    }

    url = controllerURL + "getSchoolTree";
    method = "POST";
    paramString = "";
    ajaxCallback = {
            success: function (resultObject) {
                var parsedObject = null;
                var schoolModel = null;
                var departmentModel = null;
                var tempSchool = null;
                var tempDepartment = null;

                try {
                    parsedObject = YAHOO.lang.JSON.parse(resultObject.responseText);
                }
                catch (e) {
                    ilios.global.defaultAJAXFailureHandler(null, e);

                    return;
                }

                ilios.pm.schoolDepartmentTreeModel = [];

                for (var key in parsedObject) {
                    tempSchool = parsedObject[key];

                    schoolModel = new StewardModel(tempSchool.title, -1, tempSchool.school_id);
                    ilios.pm.schoolDepartmentTreeModel.push(schoolModel);

                    for (var key2 in tempSchool.departments) {
                        tempDepartment = tempSchool.departments[key2];

                        departmentModel = new StewardModel(tempDepartment.title,
                                                           tempSchool.school_id,
                                                           tempDepartment.department_id);

                        ilios.pm.schoolDepartmentTreeModel.push(departmentModel);
                    }
                }
            },

            failure: function (resultObject) {
                ilios.global.defaultAJAXFailureHandler(resultObject);
            }};


    YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);
};


/**
 * TODO Since this load is asynchronous - we really need some way (a modal indeterminate progress
 *  sort of thing) to keep the user from interacting with the UI until the transaction is over.
 */
ilios.pm.transaction.loadProgramYearsForProgramId = function (programId) {
    var url = controllerURL + "getProgramYears";
    var method = "POST";
    var paramString = "program_id=" + programId;
    var ajaxCallback = {
            success: function (resultObject) {
                var Element = YAHOO.util.Element;
                var parsedObject = null;
                var programYearContainer = document.getElementById('program_year_container');
                var i = 0;
                var len = 0;
                var modelTree = null;
                var childArray = null;
                var j = 0;
                var childLen = 0;
                var childModel = null;
                var childModelArray = null;
                var arrayedChildModel = null;
                var newProgramYearDOMTree = null;
                var formDOMElement = null;
                var programYearModel = null;
                var titleId = null;
                var scatchElement = null;
                var textListContent = null;
                var enable = false;
                var draftStr = ilios_i18nVendor.getI18NString('general.terms.draft');
                var publishedStr = ilios_i18nVendor.getI18NString('general.terms.published');
                var collapseTrio = null;
                var idString = null;
                var str;
                var containerNumber;

                try {
                    parsedObject = YAHOO.lang.JSON.parse(resultObject.responseText);
                }
                catch (e) {
                    ilios.global.defaultAJAXFailureHandler(null, e);

                    return;
                }

                ilios.utilities.removeAllChildren(programYearContainer);

                len = parsedObject.years.length;
                for (; i < len; i++) {
                    modelTree = parsedObject.years[i];

                    formDOMElement = new Element(document.createElement('form'),
                                                  {method: 'POST', action: ''});

                    containerNumber = i + 1;
                    formDOMElement.get('element').setAttribute('cnumber', containerNumber);

                    newProgramYearDOMTree
                        = ilios.dom.buildChildContainerDOMTree(containerNumber, null, null,
                                                               ilios.pm.transaction.saveProgramYearAsDraft,
                                                               null,
                                                               ilios.pm.transaction.saveProgramYearAndPublish,
                                                               ilios.pm.transaction.deleteProgramYear,
                                                               ilios.pm.handleProgramYearDivCollapse,
                                                               null,
                                                               formDOMElement,
                                                               ilios.pm.programYearContentGenerator,
                                                               ['archiver']);

                    // possible DOM race condition
                    programYearContainer.appendChild(newProgramYearDOMTree.get('element'));

                    programYearModel = new ProgramYearModel();
                    programYearModel.setDBId(modelTree['program_year_id']);
                    programYearModel.setStartYear(modelTree['start_year']);
                    programYearModel.setPublishEventId(modelTree['publish_event_id']);


                    titleId = ilios.pm.generateIdStringForProgramYearSelect(containerNumber);
                    scatchElement = document.getElementById(titleId);
                    ilios.utilities.selectOptionWithValue(scatchElement, modelTree['start_year']);

                    childArray = modelTree.competency;
                    j = 0;
                    if (childArray != null) {
                        childLen = childArray.length;
                        childModelArray = [];
                        for (; j < childLen; j++) {
                            arrayedChildModel = childArray[j];

                            childModel = new CompetencyModel(arrayedChildModel['title'],
                                                             arrayedChildModel['parent_competency_id'],
                                                             arrayedChildModel['competency_id']);

                            childModelArray.push(childModel);
                        }
                        titleId = ilios.pm.generateIdStringForCompetencyContent(containerNumber);
                        scratchElement = document.getElementById(titleId);
                        collapseTrio = ilios.pm.getCollapseTrioForFirstChildLevelDiv(scratchElement);
                        str = ilios.competencies.generateSummaryStringForSelectedCompetencies(childModelArray);
                        collapseTrio[0].innerHTML = ilios.lang.ellipsisedOfLength(str, 75);
                        str = ilios.competencies.generateListHTMLForSelectedCompetencies(childModelArray);
                        collapseTrio[1].innerHTML = str;
                        programYearModel.setCompetencyArray(childModelArray);
                    }

                    childArray = modelTree.objectives;
                    j = 0;
                    if (childArray != null) {
                        var objectiveNumber = 0;
                        var contentElement = null;

                        idString = ilios.pm.generateIdStringForObjectiveContent(containerNumber);
                        contentElement = document.getElementById(idString);

                        childLen = childArray.length;
                        childModelArray = [];
                        for (; j < childLen; j++) {
                            arrayedChildModel = childArray[j];

                            childModel = new ObjectiveModel(arrayedChildModel);

                            objectiveNumber = programYearModel.getNextObjectiveNumber();

                            ilios.pm.buildAndPopulateObjective(containerNumber, objectiveNumber,
                                                               programYearModel, childModel,
                                                               contentElement);
                        }
                    }

                    childArray = modelTree.discipline;
                    j = 0;
                    if (childArray != null) {
                        childLen = childArray.length;
                        childModelArray = [];
                        textListContent = '';
                        for (; j < childLen; j++) {
                            arrayedChildModel = childArray[j];

                            childModel = new DisciplineModel();
                            childModel.setDBId(arrayedChildModel['discipline_id']);
                            childModel.setTitle(arrayedChildModel['title']);
                            childModel.clearDirtyState();

                            if (textListContent.length > 0) {
                                textListContent += '; ';
                            }
                            textListContent += arrayedChildModel['title'];

                            childModelArray.push(childModel);
                        }
                        titleId = ilios.pm.generateIdStringForDisciplineContent(containerNumber);
                        scratchElement = document.getElementById(titleId);
                        collapseTrio = ilios.pm.getCollapseTrioForFirstChildLevelDiv(scratchElement);
                        collapseTrio[0].innerHTML = ilios.lang.ellipsisedOfLength(textListContent, 75);
                        collapseTrio[1].innerHTML = textListContent;
                        programYearModel.setDisciplineArray(childModelArray);
                    }

                    childArray = modelTree.director;
                    j = 0;
                    if (childArray != null) {
                        childLen = childArray.length;
                        childModelArray = [];
                        textListContent = '';
                        for (; j < childLen; j++) {
                            arrayedChildModel = childArray[j];

                            childModel = new UserModel(arrayedChildModel);
                            childModel.clearDirtyState();

                            if (textListContent.length > 0) {
                                textListContent += '; ';
                            }
                            textListContent += childModel.getFormattedName(ilios.utilities.USER_NAME_FORMAT_LAST_FIRST);

                            childModelArray[childModel.getUniqueKey()] = childModel;
                            childModelArray.length++;
                        }
                        titleId = ilios.pm.generateIdStringForDirectorContent(containerNumber);
                        scratchElement = document.getElementById(titleId);
                        collapseTrio = ilios.pm.getCollapseTrioForFirstChildLevelDiv(scratchElement);
                        collapseTrio[0].innerHTML = ilios.lang.ellipsisedOfLength(textListContent, 75);
                        collapseTrio[1].innerHTML = textListContent;
                        programYearModel.setDirectors(childModelArray);
                    }

                    // @see models/program_year.php - getStewardsForProgramYear(...)
                    childArray = modelTree.steward;
                    j = 0;
                    if (childArray != null) {
                        childLen = childArray.length;
                        childModelArray = [];
                        textListContent = '';
                        for (; j < childLen; j++) {
                            arrayedChildModel = childArray[j];

                            childModel = new StewardModel(arrayedChildModel['title'],
                                                          arrayedChildModel['parent_school_id'],
                                                          arrayedChildModel['row_id']);

                            if (textListContent.length > 0) {
                                textListContent += '; ';
                            }
                            textListContent += childModel.getStewardTitle();

                            childModelArray[childModel.getStewardTitle()] = childModel;
                            childModelArray.length++;
                        }
                        titleId = ilios.pm.generateIdStringForStewardContent(containerNumber);
                        scratchElement = document.getElementById(titleId);
                        collapseTrio = ilios.pm.getCollapseTrioForFirstChildLevelDiv(scratchElement);
                        collapseTrio[0].innerHTML = ilios.lang.ellipsisedOfLength(textListContent, 75);
                        collapseTrio[1].innerHTML = textListContent;
                        programYearModel.setStewardArray(childModelArray);
                    }

                    programYearModel.setLocked(modelTree['locked'] == '1');

                    if (ilios.global.preferencesModel.showProgramYearArchiving()
                                                        && programYearModel.isPublished()) {
                        idString = ilios.pm.generateIdStringForArchivingDiv(containerNumber);

                        scratchElement = new Element(document.getElementById(idString));
                        scratchElement.setStyle('display', 'block');
                    }

                    programYearModel.clearDirtyState();

                    ilios.pm.loadedProgramModel.addProgramYear(programYearModel, ('' + containerNumber));
                    ilios.pm.currentProgramModel.addProgramYear(programYearModel, ('' + containerNumber));

                    scratchElement = document.getElementById('' + containerNumber + '_child_publish');
                    enable = ilios.utilities.canPublishModelItem(programYearModel);
                    ilios.dom.setElementEnabled(scratchElement, enable);

                    scratchElement = document.getElementById('' + containerNumber + '_child_draft_text');
                    if (! programYearModel.isPublished()) {
                        scratchElement.innerHTML = '<span class="status is-draft">' + draftStr + '</span>';
                    }
                    else {
                        scratchElement.innerHTML = '<span class="status is-published">' + publishedStr + '</span>';
                    }

                    programYearModel.addStateChangeListener(ilios.pm.dirtyStateListener, null);

                    ilios.pm.updateObjectiveCountText(containerNumber);

                    if (programYearModel.isLocked()) {
                        ilios.pm.alterProgramYearUIToReflectLockedState(containerNumber, true);
                    }
                }

                ilios.pm.loadedProgramModel.clearDirtyState();
                ilios.pm.currentProgramModel.clearDirtyState();

                ilios.pm.collapseOrExpandProgramYears(true);

                if (! parsedObject.school_owns_program) {
                    ilios.pm.alterViewToReflectReadOnlyState();
                } else {
                    ilios.pm.currentProgramModel.addStateChangeListener(ilios.pm.dirtyStateListener, null);
                }
            },

            failure: function (resultObject) {
                ilios.global.defaultAJAXFailureHandler(resultObject);
            }};

    YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);
};

// @private
ilios.pm.transaction.performProgramYearSave = function (containerNumber, shouldPublish) {
    var replacer = ilios.utilities.yahooJSONStringifyStateChangeListenerArgumentsReplacer,
        stringify = ilios.utilities.yahooJSONStringForAssociativeArray,
        url = controllerURL + "saveProgramYear",
        method = "POST",
        paramString = null,
        pymArray = null;

    var programYearModel = ilios.pm.currentProgramModel.getProgramYearForContainerNumber(containerNumber);

    var ajaxCallback = {
            success: function (resultObject) {
                var parsedObject = null;
                var returnedContainerNumber = 0;
                var model = null;
                var element = null;
                var objectives = null;
                var objective = null;
                var objModel = null;
                var cNumber = null;

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
                     = ilios_i18nVendor.getI18NString('program_management.error.program_year_save');

                    ilios.alert.alert(msg + ": " + parsedObject.error);

                    return;
                }

                returnedContainerNumber = parsedObject.container;

                model
                    = ilios.pm.currentProgramModel.getProgramYearForContainerNumber(returnedContainerNumber);
                model.setDBId(parsedObject.pyid);
                model.setPublishEventId(parsedObject.publish);

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

                model.clearDirtyState();

                element = document.getElementById('' + containerNumber + '_child_draft_text');
                if ((parsedObject.publish == null) || (parseInt(parsedObject.publish) < 1)) {
                    element.innerHTML = '<span class="status is-draft">' + ilios_i18nVendor.getI18NString('general.terms.draft') + '</span>';
                }
                else {
                    element.innerHTML = '<span class="status is-published">' + ilios_i18nVendor.getI18NString('general.terms.published') + '</span>';
                }
            },

            failure: function (resultObject) {
                ilios.global.defaultAJAXFailureHandler(resultObject);
            }};


    paramString = "program_id=" + ilios.pm.currentProgramModel.getDBId() + "&program_year_id="
                        + programYearModel.getDBId() + "&start_year="
                        + programYearModel.getStartYear() + "&cnumber=" + containerNumber
                        + "&publish=" + (shouldPublish ? "true" : "false");

    pymArray = programYearModel.getCompetencyArray();
    paramString += "&competency=" + escape(stringify(pymArray, replacer));
    pymArray = programYearModel.getObjectives();
    paramString += "&objective=" + escape(stringify(pymArray, replacer));
    pymArray = programYearModel.getDisciplineArray();
    paramString += "&discipline=" + escape(stringify(pymArray,replacer));
    pymArray = programYearModel.getDirectors();
    paramString += "&director=" + escape(stringify(pymArray, replacer));
    pymArray = programYearModel.getStewardArray();
    paramString += "&steward=" + escape(stringify(pymArray, replacer));

    YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);
};

ilios.pm.transaction.saveProgramYearAsDraft = function (event) {
    var target = ilios.utilities.getEventTarget(event);
    var cnumber =  target.parentNode.parentNode.parentNode.parentNode.getAttribute('cnumber'); // fffffffuuuuuuuuuuuu
    ilios.pm.transaction.performProgramYearSave(cnumber, false);
};

ilios.pm.transaction.saveProgramYearAndPublish = function (event) {
    var target = ilios.utilities.getEventTarget(event);
    var cnumber =  target.parentNode.parentNode.parentNode.parentNode.getAttribute('cnumber'); // todo: delegate this event
    ilios.pm.transaction.performProgramYearSave(cnumber, true);
};

/**
 * Button-click event-handler callback function.
 * Locks a given program year.
 * @method continueProgramYearLock
 * @method {Event} event the click event
 * @method {Object} args the event handler arguments object. Expected attributes:
 *     "cnumber" ... the program year's container number
 * @private
 */
ilios.pm.transaction.continueProgramYearLock = function (event, args) {
    var containerNumber = args.cnumber;
    var url = controllerURL + 'lockProgramYear';
    var method = "POST";
    var model = ilios.pm.currentProgramModel.getProgramYearForContainerNumber(containerNumber);
    var element = document.getElementById('archive_radio_lock_and_archive');
    var willArchive = (model.isLocked() || element.checked);
    var paramString = 'program_year_id=' + model.getDBId()
                            + '&archive=' + (willArchive ? 'true' : 'false');
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
                    var msg = ilios_i18nVendor.getI18NString('program_management.error.archiving');

                    ilios.alert.alert(msg + ": " + parsedObject.error);

                    return;
                }

                if (willArchive) {
                    ilios.pm.removeProgramYearAssociatedToContainer(containerNumber);
                }
                else {
                    ilios.pm.alterProgramYearUIToReflectLockedState(containerNumber);
                    model.setLocked(true);
                }
            },

            failure: function (resultObject) {
                ilios.global.defaultAJAXFailureHandler(resultObject);
            }};

    this.cancel();  // (close the popup)

    YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);
};

ilios.pm.transaction.lockArchiveProgramYear = function () {
    var containerNumber = ilios.pm.archivingDialog.cnumber;
    var element = document.getElementById('archive_radio_lock_and_archive');
    var model = ilios.pm.currentProgramModel.getProgramYearForContainerNumber(containerNumber);
    var warningStr = (model.isLocked() || element.checked)
                        ? ilios_i18nVendor.getI18NString('program_management.archiving.confirm.lock_and_archive')
                        : ilios_i18nVendor.getI18NString('program_management.archiving.confirm.lock');
    var continueStr = ilios_i18nVendor.getI18NString('general.phrases.want_to_continue');
    var yesStr = ilios_i18nVendor.getI18NString('general.terms.yes');
    var noStr = ilios_i18nVendor.getI18NString('general.terms.cancel');

    ilios.pm.archivingDialog.cancel();

    ilios.alert.inform(('<p style="margin-bottom:9px; text-align:justify;">' + warningStr
                + '</p><center><b>' + continueStr + '</b></center>'),
            yesStr, ilios.pm.transaction.continueProgramYearLock, {"cnumber": containerNumber}, noStr);

};

/**
 * Button-click event-handler callback function.
 * Deletes a given program year.
 * @method continueProgramYearDelete
 * @method {Event} event the click event
 * @method {Object} args the event handler arguments object. Expected attributes:
 *     "cnumber" ... the program year's container number
 * @private
 */
ilios.pm.transaction.continueProgramYearDelete = function (event, args) {
    var containerNumber = args.cnumber;
    var url = null;
    var method = null;
    var paramString = null;
    var programYearModel
                = ilios.pm.currentProgramModel.getProgramYearForContainerNumber(containerNumber);
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
                    var msg = ilios_i18nVendor.getI18NString('program_management.error.program_year_delete');
                    ilios.alert.alert(msg + ": " + parsedObject.error);

                    return;
                }

                ilios.pm.removeProgramYearAssociatedToContainer(parseInt(parsedObject.container));
            },

            failure: function (resultObject) {
                ilios.global.defaultAJAXFailureHandler(resultObject);
            }};

    this.hide();

    if ((programYearModel.getDBId() == -1) || (programYearModel.getDBId() == null)) {
        ilios.pm.removeProgramYearAssociatedToContainer(containerNumber);

        return;
    }

    url = controllerURL + "deleteProgramYear";
    method = "POST";

    paramString = "program_year_id=" + programYearModel.getDBId() + "&cnumber=" + containerNumber;

    YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);
};

ilios.pm.transaction.deleteProgramYear = function (event) {
    var target = ilios.utilities.getEventTarget(event);
    var containerNumber = target.getAttribute('cnumber');
    var deletePYStr
                = ilios_i18nVendor.getI18NString('program_management.warning.program_year_delete');
    var continueStr = ilios_i18nVendor.getI18NString('general.phrases.want_to_continue');
    var yesStr = ilios_i18nVendor.getI18NString('general.terms.yes');

    ilios.alert.inform(('<p style="margin-bottom:9px; text-align:justify;">' + deletePYStr
                + '</p><center><b>' + continueStr + '</b></center>'),
            yesStr, ilios.pm.transaction.continueProgramYearDelete, {"cnumber": containerNumber});
};
