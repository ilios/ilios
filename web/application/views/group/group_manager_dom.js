/**
 * DEPENDENCIES:
 *         YUI lib (at least YAHOO.widget and its dependencies)
 *         scripts/ilios_dom.js
 *         scripts/ilios_ui.js
 *         scripts/models/program_cohort_model.js
 *         group_manager_transaction.js
 */

ilios.gm.currentModel = null;

// this is the root view group (the group which has editable subgroups in view)
ilios.gm.currentRootViewGroup = null;

ilios.gm.inEditInstructorArray = null;

ilios.gm.dirtyStateListener = {

    updatingParentChain: false,
    rootCauseForModelChangeCascade: null,

    // here we need do:
    //        set group name, instructor names, location name, enrollment counts to reflect
    //                    associated model
    //        set unassigned count up the chain to the master group
    //        enabled/disable save button
    //        ? call model changed on subgroups ?
    // the dirty logic here is influenced by the logic of the use cases: a clean event would
    //    only ever be fired when the entire model state has gone clean (due to a save).
    modelChanged: function (model, mockedThis) {
        var parentModel = model.getParentGroup();
        var modelIsRoot = (parentModel == null);
        var containerNumber
            = (modelIsRoot ? ilios.gm.currentModel.getRootGroup().getContainerNumberForSubgroup(model)
                           : parentModel.getContainerNumberForSubgroup(model));
        var dirty = model.isModelDirty();
        var subgroups = null;
        var element = null;
        var subgroup = null;

        if (this.rootCauseForModelChangeCascade == null) {
            this.rootCauseForModelChangeCascade = model;
        }


        // walk up the parent chain as appropriate, communicating the dirty state
        if ((! modelIsRoot) && (parentModel.isModelDirty() != dirty)
                            && (this.rootCauseForModelChangeCascade == model)) {
            this.updatingParentChain = true;

            while (parentModel != null) {
                if (dirty) {
                    parentModel.setDirtyAndNotify();
                }
                else {
                    parentModel.clearDirtyState();
                }

                parentModel = parentModel.getParentGroup();
            }

            this.updatingParentChain = false;
        }


        element = modelIsRoot ? document.getElementById('cohort_form').firstElementChild
                              : model.domElement;
        if (element != null) {    // would be null if dealing with subgroups not rendered
            if (dirty) {
                (new YAHOO.util.Element(element)).addClass('dirty_state');
            }
            else {
                (new YAHOO.util.Element(element)).removeClass('dirty_state');
            }
        }

        if ((containerNumber != -1)
                && (((! ilios.gm.subgroup.isPresentlyCollapsed) && ilios.gm.isModelInView(model))
                                || (ilios.gm.subgroup.isPresentlyCollapsed
                                                        && ilios.gm.subgroup.indexPathCanEdit(model.indexPath)))) {
            element = document.getElementById(ilios.gm.generateIdForEnrollment(containerNumber));

            element.innerHTML = model.getEnrollment();
        }


        // if we're not updating up the chain, then walk the tree downwards setting dirty state
        if (! this.updatingParentChain) {
            subgroups = model.getSubgroups();
            for (var key in subgroups) {
                subgroup = subgroups[key];

                if (subgroup.isModelDirty() != dirty) {
                    if (dirty) {
                        subgroup.setDirtyAndNotify();
                    }
                    else {
                        subgroup.clearDirtyState();
                    }
                }
            }
        }

        // if true, this is the end of model change event cascade
        if (this.rootCauseForModelChangeCascade == model) {
            element = document.getElementById('save_all_dirty_to_draft');

            ilios.dom.setElementEnabled(element, dirty);

            this.rootCauseForModelChangeCascade = null;
        }
    }

};

// @private
ilios.gm.isModelInView = function (model) {
    var rhett = ((model.domElement != null) && (model.domElement.parentNode != null));

    if (rhett) {
        var yElement = new YAHOO.util.Element(model.domElement);
        var display = yElement.getStyle('display');
        var visibility = yElement.getStyle('visibility');

        rhett = ((display != 'none') && (visibility != 'hidden'));
    }

    return rhett;
};

// @private
ilios.gm.buildGroupModelTree = function (groupId, title, users, instructors, room, subgroups,
                                         courses) {
    var groupModel = new GroupModel();
    var len = 0;
    var i = 0;

    groupModel.setDBId(groupId);
    groupModel.setTitle(title);

    if (users != null) {
        var userModel = null;
        var loadedUser = null;

        len = users.length;

        for (i = 0; i < len; i++) {
            loadedUser = users[i];

            userModel = new StudentModel(loadedUser);

            groupModel.addUser(userModel);
        }
    }

    if ((instructors != null) && (instructors.length > 0)) {
        groupModel.setInstructorsFromDBArray(instructors);
    }

    if ((courses != null) && (courses.length > 0)) {
        groupModel.setAssociatedCourses(courses);
    }

    if (room != null) {
        groupModel.setLocation(room);
    }

    if (subgroups != null) {
        var subgroupModel = null;
        var loadedSubgroup = null;

        len = subgroups.length;

        for (i = 0; i < len; i++) {
            loadedSubgroup = subgroups[i];

            subgroupModel = ilios.gm.buildGroupModelTree(loadedSubgroup.group_id,
                                                         loadedSubgroup.title,
                                                         loadedSubgroup.users,
                                                         loadedSubgroup.instructors,
                                                         loadedSubgroup.location,
                                                         loadedSubgroup.subgroups,
                                                         loadedSubgroup.courses);

            groupModel.addSubgroup(subgroupModel);

            subgroupModel.clearDirtyState();
            subgroupModel.addStateChangeListener(ilios.gm.dirtyStateListener, null);
        }
    }

    groupModel.clearDirtyState();

    return groupModel;
};

ilios.gm.createGroupUI = function (groupContainer, containerNumber, groupModel) {
    var formDOMElement = new YAHOO.util.Element(document.createElement('form'), {method: 'POST', action: ''});
    var newGroupDOMTree = ilios.dom.buildChildContainerDOMTree(containerNumber, null, null,
                                                               null, null, null,
                                                               ilios.gm.transaction.deleteGroup,
                                                               ilios.gm.handleGroupDivCollapse,
                                                               null,
                                                                  formDOMElement,
                                                                  ilios.gm.groupContentGenerator);
    var element = null;

    formDOMElement.get('element').setAttribute('cnumber', containerNumber);

    if (groupModel.isModelDirty()) {
        newGroupDOMTree.addClass('dirty_state');
    }

    if (ilios.gm.subgroup.isPresentlyCollapsed) {
        var totalIndent
                = groupModel.indexPath.length * ilios.gm.subgroup.subContainerPixelIndentIncrement;

        newGroupDOMTree.setStyle('margin-left', '' + totalIndent + 'em');
    }

    groupContainer.appendChild(newGroupDOMTree.get('element'));
    groupModel.domElement = newGroupDOMTree.get('element');

    element = document.getElementById(ilios.gm.generateIdForManageSubgroupsCount(containerNumber));
    element.innerHTML =  groupModel.getSubgroupCount();

    ilios.gm.registerUIListenersForContainerNumber(containerNumber);

    // if indexPath is null, it's because these are fresh off handle subgroup load and the
    //        enable group generation will be handled there
    canAutoGenerateGroups = ((groupModel.getSubgroupCount() == 0)
                                 && (groupModel.indexPath != null)
                                 && (groupModel.indexPath.length == 1));
    ilios.gm.enableGroupGenerationDiv(containerNumber, canAutoGenerateGroups);
};

/*
 * @param if groupContainer is null, then no modification to the current DOM will be done
 */
ilios.gm.createSubgroupModelAndUI = function (groupContainer, parentGroup, groupId, title, users,
                                                 instructors, room, subgroups, courses,
                                                 dummyIndexPath) {
    var groupModel = ilios.gm.buildGroupModelTree(groupId, title, users, instructors, room,
                                                  subgroups, courses);
    var containerNumber = 0;
    var canAutoGenerateGroups = false;

    // this must be added prior to ilios.gm.groupContentGenerator invoked in the DOM creation below
    //
    // since adding a group is an action that goes through the server, it makes no sense to
    //        note the model as being dirty
    containerNumber = parentGroup.addSubgroup(groupModel, true);

    groupModel.clearDirtyState();

    groupModel.addStateChangeListener(ilios.gm.dirtyStateListener, null);

    if (groupContainer != null) {
        if ((typeof dummyIndexPath != 'undefined') && (dummyIndexPath != null)) {
            // we do this so the groupModel is layed out correctly; the index paths should be marked
            //        for rebuilding prior to or post this call so that the group model will be
            //        populated with a real index path. This dummy index path need only have the
            //        same length as the one it will eventually have calculated for it (so, for
            //        example, a peer pre-existing model could lend its index path).
            groupModel.indexPath = dummyIndexPath;
        }

        ilios.gm.createGroupUI(groupContainer, containerNumber, groupModel);

        ilios.dom.collapseChildForContainerNumber(containerNumber, 1, ilios.gm.handleGroupDivCollapse);
    }

    return groupModel;
};

ilios.gm.getCohortDisplayText = function () {
    return ilios.gm.currentModel.getProgramTitle() + " - " + ilios.gm.currentModel.getCohortTitle();
};

/**
 * Called from ilios.ui.handleProgramCohortSelectionDialogDisplay
 *
 * @param cohort an object returned by the controller method getCohortProgramTreeContent
 * @param programTitle
 * @return an instance of a model -- this instance will be attached to the tree node so that it may
 *                 be referenced later
 */
ilios.ui.buildNodeModel = function (cohort, programTitle) {
    var rhett = new ProgramCohortModel(programTitle, cohort.title, cohort.cohort_id,
                                       cohort.program_year_id, cohort.start_year, new GroupModel());

    rhett.setProgramShortTitle(cohort.program_short_title);
    rhett.setProgramDuration(cohort.program_duration);
    rhett.setCurrentEnrollment(cohort.enrollment);

    return rhett;
};


/**
 * Handles user selections in the cohort picker dialog.
 * Called from <code>ilios.ui.handleProgramCohortSelectionDialogDisplay</code>
 * @method ilios.ui.handleProgramCohortDialogSelection
 *
 */
ilios.ui.handleProgramCohortDialogSelection = function (clickObject) {
    var node = clickObject.node; // TextNode
    var model = node.iliosModel; // ProgramCohortModel

    if (! node.parentDialog) {
        return;
    }
    // todo: force use to confirm their choice before reloading the page
    // in case that there are any pending modifications to the model

    // if ((ilios.gm.currentModel != null) && (ilios.gm.currentModel.getRootGroup().isModelDirty())) {
    //     ilios.alert.alert("The model was dirty; we will offer you the chance to cancel out of this "
    //         + "action in the future.");
    // }
    node.parentDialog.cancel();
    // reload page with selected cohort
    var newUrl = window.location.protocol + "//" + window.location.host
        + window.location.pathname + "?cohort_id=" + model.getCohortId();
    window.location.href = newUrl;
}

/**
 * Loads group data for a given cohort via server callbacks
 * and populates the UI with it.
 * @method ilios.gm.loadAndDisplayGroupsForCohort
 * @param {ProgramCohortModel} the cohort model
 * @todo completely refactor this turd. [ST 2012/07/12]
 */
ilios.gm.loadAndDisplayGroupsForCohort = function (model) {
    var element = null;
    var canAutoGenerateGroups = true;
    var xmlHTTPRequest = null;
    var url = null;
    var method = null;
    var paramString = null;

    ilios.gm.currentModel = model;
    rootGroup = ilios.gm.currentModel.getRootGroup();
    ilios.gm.currentRootViewGroup = rootGroup;

    element = document.getElementById('program_cohort_title');
    element.innerHTML = ilios.gm.getCohortDisplayText();

    element = document.getElementById('program_title_short');
    element.innerHTML = ilios.gm.currentModel.getProgramShortTitle();

    element = document.getElementById('current_enrollment');
    element.innerHTML = ilios.gm.currentModel.getCurrentEnrollment();


    // we don't want to AJAX this one lest the user be left with a half-state waiting on a tardy
    //            asynchronous reply
    // TODO SJAX UI blocking display
    xmlHTTPRequest = ilios.utilities.getXMLHttpRequest();
    if (xmlHTTPRequest == null) {
        return;
    }


    url = controllerURL + 'getUsersForCohort';
    method = 'GET';
    paramString = 'cohort_id=' + ilios.gm.currentModel.getCohortId() + '&rnd=' + new Date().getTime();
    xmlHTTPRequest.open(method, url + '?' + paramString, false);
    if ('undefined' !== typeof xmlHTTPRequest.overrideMimeType) {
        xmlHTTPRequest.overrideMimeType("text/plain");
    }
    xmlHTTPRequest.setRequestHeader("X-Requested-With", "XMLHttpRequest");
    xmlHTTPRequest.send(null);

    try {
        parsedObject = YAHOO.lang.JSON.parse(xmlHTTPRequest.responseText);
    }
    catch (e) {
        ilios.global.defaultAJAXFailureHandler(null, e);

        return;
    }

    if (parsedObject.error != null) {
        var i18nStr = ilios_i18nVendor.getI18NString('groups.error.user_tree_load');

        ilios.alert.alert(i18nStr + ': ' + parsedObject.error);

        return;
    }

    len = parsedObject.XHRDS.length;
    for (var i = 0; i < len; i++) {
        model = new StudentModel(parsedObject.XHRDS[i]);

        rootGroup.addUser(model);
    }
    rootGroup.clearDirtyState();


    url = controllerURL + 'getMasterGroupsForCohort';
    method = 'GET';
    paramString = 'cohort_id=' + ilios.gm.currentModel.getCohortId() + '&rnd=' + new Date().getTime();

    // todo figure out why POST and send()ing the paramString doesn't transmit the param correctly
    xmlHTTPRequest.open(method, url + '?' + paramString, false);
    if ('undefined' !== typeof xmlHTTPRequest.overrideMimeType) {
        xmlHTTPRequest.overrideMimeType("text/plain");
    }
    xmlHTTPRequest.send(null);

    try {
        parsedObject = YAHOO.lang.JSON.parse(xmlHTTPRequest.responseText);
    }
    catch (e) {
        ilios.global.defaultAJAXFailureHandler(null, e);

        return;
    }

    ilios.gm.handleSubgroupLoad(parsedObject.XHRDS, ilios.gm.currentModel.getRootGroup());

    element = document.getElementById('all_edit_member_link');
    ilios.dom.setElementEnabled(element, true);

    element = document.getElementById('save_all_dirty_to_draft');
    ilios.dom.setElementEnabled(element, false);

    element = new YAHOO.util.Element(document.getElementById('expand_groups_link'));
    element.setStyle('display', 'inline');
    element.addClass('groups_collapsed');
    element.get('element').innerHTML = ilios_i18nVendor.getI18NString('groups.expand_groups');

    element = document.getElementById('general_new_add_group_link');
    ilios.dom.setElementEnabled(element, true);


    rootGroup.addStateChangeListener(ilios.gm.dirtyStateListener, null);

    ilios.gm.subgroup.indexPathsNeedRebuilding = true;
};

/*
* @param parsedObject should be the return from the server-side call getMasterGroupsForCohort
*                 (or from autogenerateSubGroups which, itself, later calls getMasterGroupsForCohort)
*/
ilios.gm.handleSubgroupLoad = function (parsedObject, parentGroupModel) {
    var groupContainer = null;
    var subgroup = null;
    var model = null;
    var containerNumber = -1;
    var canAutoGenerateGroups = false;
    var subgroupsAreMasterGroups = (parentGroupModel == ilios.gm.currentModel.getRootGroup());

    if (parsedObject.error != null) {
        var i18nStr = ilios_i18nVendor.getI18NString('groups.error.subgroup_load');

        ilios.alert.alert(i18nStr + ': ' + parsedObject.error);

        return;
    }

    if (subgroupsAreMasterGroups) {
        groupContainer = document.getElementById('group_container');
        ilios.utilities.removeAllChildren(groupContainer);
    }
    else {
        parentGroupModel.removeAllUsers(true);
    }

    for (var key in parsedObject) {
        subgroup = parsedObject[key];

        model = ilios.gm.createSubgroupModelAndUI(groupContainer, parentGroupModel,
                                                  subgroup.group_id, subgroup.title, subgroup.users,
                                                  subgroup.instructors, subgroup.location,
                                                  subgroup.subgroups, subgroup.courses);

        if (groupContainer != null) {
            containerNumber = parentGroupModel.getContainerNumberForSubgroup(model);
            canAutoGenerateGroups = (model.getSubgroupCount() == 0);
            ilios.gm.enableGroupGenerationDiv(containerNumber, canAutoGenerateGroups);
        }
    }

    parentGroupModel.clearDirtyState();
};

ilios.gm.setPageTitle = function (pageTitle) {
    var element = document.getElementById('page_title');

    element.innerHTML = '> ' + pageTitle;
};

ilios.gm.setAddNewGroupText = function (linkText) {
    var element = document.getElementById('general_new_add_group_link');

    element.innerHTML = linkText;
};

ilios.gm.displayInstructorPicker = function (containerNumber) {
    var element = document.getElementById('instructor_picker_selected');
    var groupModel = ilios.gm.currentRootViewGroup.getSubgroupForContainerNumber(containerNumber);
    var instructors = groupModel.getInstructors();
    var tmpModel = null;

    ilios.utilities.removeAllChildren(element);
    for (var key in instructors) {
        tmpModel = instructors[key];

        if (tmpModel instanceof UserModel) {
            displayString = tmpModel.getFormattedName(ilios.utilities.USER_NAME_FORMAT_LAST_FIRST);
        }
        else {
            displayString = tmpModel.title;
        }

        ilios.dom.addNewLIElementWithIliosModel(element, tmpModel, displayString);
    }

    ilios.gm.inEditInstructorArray = groupModel.getInstructors().concat();

    instructorPickerDialog.cnumber = containerNumber;
    instructorGroupAutoCompleter.sendQuery('');
    instructorPickerDialog.showDialogPane();
};

/*
 * This is messaged when the user selects an instructor / instructor group from the list of them
 *     in the picker.
 *
 * Should be considered @protected
 */
ilios.gm.handleInstructorGroupSelection = function (selectedModel) {
    var containerNumber = instructorPickerDialog.cnumber;
    var groupModel = ilios.gm.currentRootViewGroup.getSubgroupForContainerNumber(containerNumber);
    var displayString = null;

    if (selectedModel instanceof UserModel) {
        displayString = selectedModel.getFormattedName(ilios.utilities.USER_NAME_FORMAT_LAST_FIRST);
    }
    else {
        displayString = selectedModel.title;
    }

    ilios.dom.addNewLIElementWithIliosModel(document.getElementById('instructor_picker_selected'),
                                            selectedModel, displayString);

    ilios.gm.inEditInstructorArray.push(selectedModel);
};

/*
 * This is messaged when the user clicks on an instructor / instructor group from the left pane of
 *     previously selected items (thereby semantically deselecting the item).
 *
 * Should be considered @protected
 */
ilios.gm.handleInstructorGroupDeselection = function (event) {
    var target = ilios.utilities.getEventTarget(event);

    if (target.tagName.toLowerCase() === 'li') {
        var model = target.iliosModel;
        target.parentNode.removeChild(target);
        ilios.utilities.removeElementWithValue(ilios.gm.inEditInstructorArray, model);
        document.getElementById('instructor_picker_ac_input').value = '';
        instructorGroupAutoCompleter.sendQuery('');
        return false;
    }
    return true;
};

ilios.gm.userAcceptedInstructorPickerChanges = function () {
    var containerNumber = instructorPickerDialog.cnumber;
    var groupModel = ilios.gm.currentRootViewGroup.getSubgroupForContainerNumber(containerNumber);
    var element = document.getElementById(ilios.gm.generateIdForInstructors(containerNumber));

    groupModel.setInstructors(ilios.gm.inEditInstructorArray);
    ilios.gm.inEditInstructorArray = null;

    element.innerHTML = groupModel.getInstructorsAsFormattedText();
};

ilios.gm.userCanceledInstructorPickerChanges = function () {
    ilios.gm.inEditInstructorArray = null;
};

/*
 * Call via onDOMReady
 */
ilios.gm.registerUIElements = function () {
    var element = document.getElementById('save_all_dirty_to_draft');

    YAHOO.util.Event.addListener(element, 'click', ilios.gm.transaction.saveGroupModel);
    ilios.dom.setElementEnabled(element, false);

    element = document.getElementById('general_new_add_group_link');
    ilios.dom.setEnableForAElement(element, false);

    element = document.getElementById('all_edit_member_link');
    ilios.dom.setEnableForAElement(element, false);
};

// @private
ilios.gm.registerUIListenersForContainerNumber = function (containerNumber) {
    var Event = YAHOO.util.Event;
    var element = document.getElementById(ilios.gm.generateIdForGroupName(containerNumber));

    Event.addListener(element, 'keyup', function () {
        var title = document.getElementById(
        ilios.gm.generateIdForGroupName(containerNumber));
        var groupModel = ilios.gm.currentRootViewGroup.getSubgroupForContainerNumber(containerNumber);
        groupModel.setTitle(title.value);
    });

    element = document.getElementById(ilios.gm.generateIdForRoom(containerNumber));
    Event.addListener(element, 'keyup', function () {
        var title = document.getElementById(
        ilios.gm.generateIdForRoom(containerNumber));
        var groupModel = ilios.gm.currentRootViewGroup.getSubgroupForContainerNumber(containerNumber);
        groupModel.setLocation(title.value);
    });
};

ilios.gm.collapseOrExpandGroups = function (groupIsLoading, forceCollapse) {
    var element = new YAHOO.util.Element(document.getElementById('expand_groups_link'));
    var groups = ilios.gm.currentRootViewGroup.getSubgroups();
    var collapseValue = 1;
    var collapseValueToUse = -1;

    if (element.hasClass('groups_collapsed') && (! groupIsLoading) && (! forceCollapse)) {
        element.removeClass('groups_collapsed');
        element.get('element').innerHTML = ilios_i18nVendor.getI18NString('general.phrases.collapse_all');

        collapseValue = 2;        // expand no matter what
    }
    else {
        element.addClass('groups_collapsed');
        element.get('element').innerHTML = ilios_i18nVendor.getI18NString('general.phrases.expand_all');
    }

    for (var key in groups) {
        collapseValueToUse = collapseValue;

        if (groupIsLoading && false) { // (sessions[key].getDBId() == loadedSessionIdToFocusOn)) {
            collapseValueToUse = 2;
        }

        ilios.dom.collapseChildForContainerNumber(key, collapseValueToUse, ilios.gm.handleGroupDivCollapse);
    }
};

ilios.gm.handleGroupDivCollapse = function (containerNumber, summaryTextDiv) {
    var groupModel = ilios.gm.currentRootViewGroup.getSubgroupForContainerNumber(containerNumber);
    var i18nStr = ilios_i18nVendor.getI18NString('general.phrases.total_members');
    summaryTextDiv.innerHTML = groupModel.getTitle()
        + '<span class="membercount">'
        + i18nStr + ': <span class="read_only_data">'
        + groupModel.getEnrollment() + '</span></span>';
};

ilios.gm.generateIdForGroupName = function (containerNumber) {
    return '' + containerNumber + '_group_name';
};

ilios.gm.generateIdForGroupNameLabel = function (containerNumber) {
    return '' + containerNumber + '_group_name_label';
};

ilios.gm.generateIdForInstructors = function (containerNumber) {
    return '' + containerNumber + '_instructor';
};

ilios.gm.generateIdForRoom = function (containerNumber) {
    return '' + containerNumber + '_location';
};

ilios.gm.generateIdForEnrollment = function (containerNumber) {
    return '' + containerNumber + '_total_members';
};

ilios.gm.generateIdForAutogenerateDiv = function (containerNumber) {
    return '' + containerNumber + '_autogenerate_groups_div';
};

ilios.gm.generateIdForAutogenerateButton = function (containerNumber) {
    return '' + containerNumber + '_autogenerate_group_button';
};

ilios.gm.generateIdForAutogenerateTextfield = function (containerNumber) {
    return '' + containerNumber + '_autogenerate_group_textfield';
};

ilios.gm.generateIdForAddMembersLink = function (containerNumber) {
    return '' + containerNumber + '_add_members';
};

ilios.gm.generateIdForManageSubgroupsCount = function (containerNumber) {
    return '' + containerNumber + '_manage_subgroups_sum';
};

// @private
ilios.gm.groupContentGenerator = function (parentElement, containerNumber) {
    var scratchLabel = null;
    var scratchInput = null;
    var scratchElement = null;
    var textNode = null;
    var titleId = ilios.gm.generateIdForGroupName(containerNumber);
    var groupModel = ilios.gm.currentRootViewGroup.getSubgroupForContainerNumber(containerNumber);
    var i18nStr = ilios_i18nVendor.getI18NString('groups.group.group_name');
    var rowElement, labelCol, dataCol, actionCol;


    if (ilios.gm.currentRootViewGroup.getParentGroup() != null) {
        rowElement = ilios.dom.createEntityContainerInputRow();
        //label column
        scratchLabel = document.createElement('label');
        textNode = document.createTextNode(ilios_i18nVendor.getI18NString('groups.group.parentgroup') + ':');
        scratchLabel.appendChild(textNode);
        ilios.dom.createLabelCol(rowElement, scratchLabel);
        //data column
        scratchInput = document.createElement('span');
        textNode = document.createTextNode(ilios.gm.currentRootViewGroup.getTitle());
        scratchInput.appendChild(textNode);
        ilios.dom.createDataCol(rowElement, scratchInput);

        //action column
        //no actions
        scratchInput = document.createElement('span');
        ilios.dom.createActionCol(rowElement, scratchInput);
        parentElement.appendChild(rowElement);
    }

    //group name row
    rowElement = ilios.dom.createEntityContainerInputRow();

    scratchLabel = document.createElement('label');
    scratchLabel.setAttribute('id', ilios.gm.generateIdForGroupNameLabel(containerNumber));
    textNode = document.createTextNode(i18nStr + ':');
    scratchLabel.appendChild(textNode);
    ilios.dom.createLabelCol(rowElement, scratchLabel);

    scratchInput = document.createElement('input');
    scratchInput.setAttribute('id', titleId);
    scratchInput.setAttribute('type', 'text');
    scratchInput.value = groupModel.getTitle();
    ilios.dom.createDataCol(rowElement, scratchInput);

    //action column
    //no actions
    scratchInput = document.createElement('span');
    ilios.dom.createActionCol(rowElement, scratchInput);
    parentElement.appendChild(rowElement);


    //default instructor row
    i18nStr = ilios_i18nVendor.getI18NString('groups.default_instructor');
    titleId = ilios.gm.generateIdForInstructors(containerNumber);
    rowElement = ilios.dom.createEntityContainerInputRow();

    scratchLabel = document.createElement('label');
    textNode = document.createTextNode(i18nStr + ': ');
    scratchLabel.appendChild(textNode);
    ilios.dom.createLabelCol(rowElement, scratchLabel);

    scratchInput = document.createElement('div');
    scratchInput.setAttribute('id', titleId);
    scratchInput.setAttribute('class', 'read_only_data');
    scratchInput.innerHTML = groupModel.getInstructorsAsFormattedText();
    ilios.dom.createDataCol(rowElement, scratchInput);

    i18nStr = ilios_i18nVendor.getI18NString('general.terms.edit');
    scratchInput = document.createElement('a');
    scratchInput.setAttribute('class', 'tiny radius button');
    scratchInput.setAttribute('href', '');
    scratchInput.setAttribute('onclick',
                              "ilios.gm.displayInstructorPicker(" + containerNumber + "); return false;");
    textNode = document.createTextNode(i18nStr);
    scratchInput.appendChild(textNode);
    ilios.dom.createActionCol(rowElement, scratchInput);

    parentElement.appendChild(rowElement);


    //default location row
    i18nStr = ilios_i18nVendor.getI18NString('groups.default_location');
    titleId = ilios.gm.generateIdForRoom(containerNumber);
    rowElement = ilios.dom.createEntityContainerInputRow();

    scratchLabel = document.createElement('label');
    textNode = document.createTextNode(i18nStr + ': ');
    scratchLabel.appendChild(textNode);
    ilios.dom.createLabelCol(rowElement, scratchLabel);

    scratchInput = document.createElement('input');
    scratchInput.setAttribute('id', titleId);
    scratchInput.setAttribute('type', 'text');
    scratchInput.value = groupModel.getLocation();
    ilios.dom.createDataCol(rowElement, scratchInput);

    //action column
    //no actions
    scratchInput = document.createElement('span');
    ilios.dom.createActionCol(rowElement, scratchInput);
    parentElement.appendChild(rowElement);

    //associated courses row
    i18nStr = ilios_i18nVendor.getI18NString('general.phrases.associated_courses');

    rowElement = ilios.dom.createEntityContainerInputRow();

    scratchLabel = document.createElement('label');
    textNode = document.createTextNode(i18nStr + ': ');
    scratchLabel.appendChild(textNode);
    ilios.dom.createLabelCol(rowElement, scratchLabel);

    scratchInput = document.createElement('div');
    scratchInput.setAttribute('class', 'read_only_data');
    scratchInput.innerHTML = groupModel.getAssociatedCoursesAsDisplayString();
    ilios.dom.createDataCol(rowElement, scratchInput);

    //action column
    //no actions
    scratchInput = document.createElement('span');
    ilios.dom.createActionCol(rowElement, scratchInput);
    parentElement.appendChild(rowElement);


    // enrollment total & modification links div
    i18nStr = ilios_i18nVendor.getI18NString('general.phrases.total_members');
    rowElement = ilios.dom.createEntityContainerInputRow();

    scratchLabel = document.createElement('label');
    textNode = document.createTextNode(i18nStr + ': ');
    scratchLabel.appendChild(textNode);
    ilios.dom.createLabelCol(rowElement, scratchLabel);

    scratchInput = document.createElement('span');
    scratchInput.setAttribute('id', ilios.gm.generateIdForEnrollment(containerNumber));
    scratchInput.setAttribute('class', 'read_only_data');
    scratchInput.innerHTML = groupModel.getEnrollment();
    ilios.dom.createDataCol(rowElement, scratchInput);

    // "Manage Group Membership"
    //i18nStr = ilios_i18nVendor.getI18NString('general.phrases.manage_membership');
    i18nStr = ilios_i18nVendor.getI18NString('general.terms.edit');
    scratchInput = document.createElement('a');
    scratchInput.setAttribute('class', 'tiny radius button');
    scratchInput.setAttribute('href', '');
    scratchInput.setAttribute('id', ilios.gm.generateIdForAddMembersLink(containerNumber));
    scratchInput.setAttribute('onclick',
                              "ilios.gm.mm.managedContainerNumber = " + containerNumber
                                      + "; ilios.ui.onIliosEvent.fire({action: 'gen_dialog_open', "
                                                +   "event: 'manage_member_picker_show_dialog', "
                                                +   "container_number: " + containerNumber
                                        + "}); return false;");
    textNode = document.createTextNode(i18nStr);
    scratchInput.appendChild(textNode);
    ilios.dom.createActionCol(rowElement, scratchInput);

    parentElement.appendChild(rowElement);

    // subgroup management div
    //i18nStr = ilios_i18nVendor.getI18NString('groups.group.manage_subgroups');
    i18nStr = ilios_i18nVendor.getI18NString('groups.group.subgroups');
    rowElement = ilios.dom.createEntityContainerInputRow();

    scratchLabel = document.createElement('label');
    textNode = document.createTextNode(i18nStr + ': ');
    scratchLabel.appendChild(textNode);
    ilios.dom.createLabelCol(rowElement, scratchLabel);

    // subgroup count
    scratchInput = document.createElement('span');
    scratchInput.setAttribute('id', ilios.gm.generateIdForManageSubgroupsCount(containerNumber));
    scratchInput.setAttribute('class', 'read_only_data');
    textNode = document.createTextNode(' 0');
    scratchInput.appendChild(textNode);
    ilios.dom.createDataCol(rowElement, scratchInput);

    //i18nStr = ilios_i18nVendor.getI18NString('groups.group.manage_subgroups');
    i18nStr = ilios_i18nVendor.getI18NString('general.terms.edit');
    scratchInput = document.createElement('a');
    scratchInput.setAttribute('class', 'tiny radius button');
    scratchInput.setAttribute('href', '');
    scratchInput.setAttribute('onclick',
                              'ilios.gm.subgroup.collapseGroupForSubgrouping(this, event); return false;');
    textNode = document.createTextNode(i18nStr);
    scratchInput.appendChild(textNode);
    ilios.dom.createActionCol(rowElement, scratchInput);

    parentElement.appendChild(rowElement);

};

ilios.gm.enableGroupGenerationDiv = function (containerNumber, enabled) {
    var Event = YAHOO.util.Event;
    var divId = ilios.gm.generateIdForAutogenerateDiv(containerNumber);
    var element = document.getElementById(divId);

    if (enabled) {
        var formElement = null;
        var inputElement = null;
        var textNode = null;
        var i18nStr = ilios_i18nVendor.getI18NString('general.terms.create');
        var collapserId = ilios.dom.childCollapsingContainerIdForContainerNumber(containerNumber);
        var autogenButtonId = ilios.gm.generateIdForAutogenerateButton(containerNumber);
        var autogenTextfieldId = ilios.gm.generateIdForAutogenerateTextfield(containerNumber);
        var rowEl, labelCol, dataCol, actionCol;

        // MAY RETURN THIS BLOCK (is the div already there because we previously loaded a
        //                                program-cohort that had no groups? todo - re-enable?)
        if (element != null) {
            return;
        }

        formElement = document.createElement('form');
        rowEl = ilios.dom.createEntityContainerInputRow();
        rowEl.setAttribute('id', divId);


        labelElement = document.createElement('label');
        labelCol = ilios.dom.createLabelCol(rowEl, labelElement);

        textNode = document.createTextNode(i18nStr + ' ');
        labelElement = document.createElement('label');
        labelElement.appendChild(textNode);
        dataCol = ilios.dom.createDataCol(rowEl, labelElement);

        inputElement = document.createElement('input');
        inputElement.setAttribute('type', 'text');
        inputElement.setAttribute('style', 'width: 28px;');
        inputElement.setAttribute('value', '');
        inputElement.setAttribute('id', autogenTextfieldId);
        inputElement.cnumber = containerNumber;
        dataCol.appendChild(inputElement);

        i18nStr = ilios_i18nVendor.getI18NString('general.phrases.groups_for_this');
        textNode = document.createTextNode(' ' + i18nStr + ' ');
        dataCol.appendChild(textNode);


        i18nStr = ilios_i18nVendor.getI18NString('general.terms.generate');
        inputElement = document.createElement('button');
        inputElement.setAttribute('class', 'small radius button');
        inputElement.setAttribute('id', autogenButtonId);
        inputElement.setAttribute('disabled', 'disabled');
        inputElement.setAttribute('onclick', 'return false;');
        inputElement.cnumber = containerNumber;
        textNode = document.createTextNode(i18nStr);
        inputElement.appendChild(textNode);
        actionCol = ilios.dom.createActionCol(rowEl, inputElement);

        formElement.appendChild(rowEl);
        document.getElementById(collapserId).appendChild(formElement);

        element = document.getElementById(autogenTextfieldId);
        Event.addListener(element, 'keyup', function () {
            var generateButton = document.getElementById(ilios.gm.generateIdForAutogenerateButton(this.cnumber));
            var disable = false;
            var currentText = this.value;

            disable = ((currentText.length == 0) || isNaN(currentText) || (parseInt(currentText) < 1));

            ilios.dom.setElementEnabled(generateButton, (! disable));
        });

        element = document.getElementById(autogenButtonId);
        Event.addListener(element, 'click', ilios.gm.transaction.autogenerateSubgroups);
    } else {
        if (element != null) {
            element.parentNode.removeChild(element);
        }

        element = null;
    }
};

ilios.gm.resetAddNewMembersDialog = function (dialog) {
    var element = document.getElementById('em_last_name');

    element.value = '';

    element = document.getElementById('em_first_name');
    element.value = '';

    element = document.getElementById('em_middle_name');
    element.value = '';

    element = document.getElementById('em_phone');
    element.value = '';

    element = document.getElementById('em_email');
    element.value = '';

    element = document.getElementById('em_uc_id');
    element.value = '';

    element = document.getElementById('em_csv_file_field');
    element.value = '';

    element = document.getElementById('em_transaction_status');
    element.innerHTML = '';
};
