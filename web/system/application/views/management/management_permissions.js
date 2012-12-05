/*
 * DEPENDENCY: YUI lib (at least YAHOO.util and its dependencies)
 * DEPENDENCY: DHTMLX Scheduler
 * DEPENDENCY: scripts/ilios_ui.js
 * DEPENDENCY: scripts/ilios_utilities.js
 */

ilios.namespace('management.permissions');

ilios.management.permissions.userAutoCompleter = null;

ilios.management.permissions.selectedPermissionPrograms = null;
ilios.management.permissions.selectedPermissionSchools = null;
ilios.management.permissions.selectedUsers = null;


ilios.management.permissions.startPermissionsWorkflow = function () {
    var container = document.getElementById('management_center_content');
    var element = document.createElement('div');

    element.setAttribute('style',
                         'text-align: center; width: 100%; margin: 9px 0px; font-weight: bold;');
    element.innerHTML = ilios_i18nVendor.getI18NString('management.permissions.choose_users');

    ilios.utilities.removeAllChildren(container);
    container.appendChild(element);

    ilios.management.displayOptionAsSelected('permissions_li');
    ilios.management.permissions.populateUserPickerDOM(container,
                                                       ilios.management.permissions.permissionsWorkflowStep2,
                                                       ilios.management.permissions.endWorkflow);

    ilios.alert.networkActivityI18NStrings.push('management.permissions.fetching_users');
	ilios.alert.updateServerInteractionProgress();

    ilios.management.permissions.userAutoCompleter.sendQuery('');
};

ilios.management.permissions.permissionsWorkflowStep2 = function () {
    var Event = YAHOO.util.Event;
    var userCount = ilios.management.permissions.selectedUsers.length;
    var container = document.getElementById('management_center_content');
    var subContainer = null;
    var element = document.createElement('div');
    var model = null;
    var courseAccessText = ilios_i18nVendor.getI18NString('management.permissions.change_courses');
    var programAccessText = ilios_i18nVendor.getI18NString('management.permissions.change_programs');
    var schoolAccessText = ilios_i18nVendor.getI18NString('management.permissions.change_schools');

    element.setAttribute('style', 'text-align: left; width: 100%; margin: 3px 0px; font-weight: bold;');
    element.innerHTML = ilios_i18nVendor.getI18NString('management.permissions.permissions_for');

    ilios.utilities.removeAllChildren(container);
    container.appendChild(element);

    for (var i = 0; i < userCount; i++) {
        model = ilios.management.permissions.selectedUsers[i];

        element = document.createElement('div');
        element.setAttribute('class', 'read_only_data');
        element.setAttribute('style', 'text-align: left; width: 100%; margin: 2px 46px;');
        element.innerHTML = model.getFormattedName(ilios.utilities.USER_NAME_FORMAT_LAST_FIRST)
                                + ' (' + model.getEmailAddress() + ")";

        container.appendChild(element);
    }

    // School Access Div
    subContainer = document.createElement('div');
    subContainer.setAttribute('style', 'margin-top: 12px; margin-left: 46px;');
    if (userCount == 1) {
        element = document.createElement('span');
        element.setAttribute('style', 'font-weight: bold; margin-right: 3px;');
        element.innerHTML
            = ilios_i18nVendor.getI18NString('management.permissions.current_schools') + ': ';
        subContainer.appendChild(element);
    }
    else {
        schoolAccessText += ' '
                           + ilios_i18nVendor.getI18NString('general.phrases.for_all_listed_users');
    }
    element = document.createElement('a');
    element.setAttribute('href', '');
    element.setAttribute('onclick', 'return false;');
    element.setAttribute('id', 'school_choosing_link');
    element.innerHTML = schoolAccessText;
    Event.addListener(element, 'click', function () {
	IEvent.fire({
	    action: 'default_dialog_open',
            event: 'school_picker_show_dialog',
            container_number: -1
	});
        return false;
    });
    subContainer.appendChild(element);
    container.appendChild(subContainer);
    subContainer = document.createElement('div');
    subContainer.setAttribute('style', 'margin-left: 92px; margin-bottom: 46px;');
    subContainer.setAttribute('class', 'read_only_data');
    subContainer.setAttribute('id', 'current_school_permissions_div');
    subContainer.needsEmptying = false;
    if (userCount == 1) {
        element = document.createElement('div');
        element.innerHTML = ilios_i18nVendor.getI18NString('general.terms.none');
        subContainer.appendChild(element);
        subContainer.needsEmptying = true;
    }
    container.appendChild(subContainer);

    // Program Access div
    subContainer = document.createElement('div');
    subContainer.setAttribute('style', 'margin-top: 12px; margin-left: 46px;');
    if (userCount == 1) {
        element = document.createElement('span');
        element.setAttribute('style', 'font-weight: bold; margin-right: 3px;');
        element.innerHTML
            = ilios_i18nVendor.getI18NString('management.permissions.current_programs') + ': ';
        subContainer.appendChild(element);
    }
    else {
        programAccessText += ' '
                           + ilios_i18nVendor.getI18NString('general.phrases.for_all_listed_users');
    }
    element = document.createElement('a');
    element.setAttribute('href', '');
    element.setAttribute('onclick', 'return false;');
    element.setAttribute('id', 'program_choosing_link');
    element.innerHTML = programAccessText;
    Event.addListener(element, 'click', function () {
        IEvent.fire({
            action: 'default_dialog_open',
            event: 'program_picker_show_dialog',
            container_number: -1
        });
        return false;
    });
    subContainer.appendChild(element);
    container.appendChild(subContainer);
    subContainer = document.createElement('div');
    subContainer.setAttribute('style', 'margin-left: 92px; margin-bottom: 46px;');
    subContainer.setAttribute('class', 'read_only_data');
    subContainer.setAttribute('id', 'current_program_permissions_div');
    subContainer.needsEmptying = false;
    if (userCount == 1) {
        element = document.createElement('div');
        element.innerHTML = ilios_i18nVendor.getI18NString('general.terms.none');
        subContainer.appendChild(element);
        subContainer.needsEmptying = true;
    }
    container.appendChild(subContainer);

    // Course Access div
    subContainer = document.createElement('div');
    subContainer.setAttribute('style', 'margin-top: 12px; margin-left: 46px;');
    if (userCount == 1) {
        element = document.createElement('span');
        element.setAttribute('style', 'font-weight: bold; margin-right: 3px;');
        element.innerHTML
            = ilios_i18nVendor.getI18NString('management.permissions.current_courses') + ': ';
        subContainer.appendChild(element);
    }
    else {
        courseAccessText += ' '
                            + ilios_i18nVendor.getI18NString('general.phrases.for_all_listed_users');
    }
    element = document.createElement('a');
    element.setAttribute('href', '');
    element.setAttribute('onclick', 'return false;');
    element.setAttribute('id', 'course_choosing_link');
    element.innerHTML = courseAccessText;
    Event.addListener(element, 'click', function () {
        IEvent.fire({
            action: 'default_dialog_open',
            event: 'course_picker_show_dialog',
            container_number: -1
        });
        return false;
    });
    subContainer.appendChild(element);
    container.appendChild(subContainer);
    subContainer = document.createElement('div');
    subContainer.setAttribute('style', 'margin-left: 92px; margin-bottom: 46px;');
    subContainer.setAttribute('class', 'read_only_data');
    subContainer.setAttribute('id', 'current_course_permissions_div');
    subContainer.needsEmptying = false;
    if (userCount == 1) {
        element = document.createElement('div');
        element.innerHTML = ilios_i18nVendor.getI18NString('general.terms.none');
        subContainer.appendChild(element);
        subContainer.needsEmptying = true;
    }
    container.appendChild(subContainer);


    element = document.createElement('div');
    element.setAttribute('style', 'margin-top: 48px;');
    container.appendChild(element);

    ilios.management.appendContinueOrCancelButtons(container, null,
                                                   ilios.management.permissions.endWorkflow,
                                                   'permissions_workflow_2_continue_button',
                                                   'general.terms.finished');

    ilios.management.permissions.selectedPermissionSchools = new Array();
    ilios.management.permissions.selectedPermissionPrograms = new Array();
    ilios.management.selectedPermissionCourses = new Array();

    if (userCount == 1) {
        ilios.management.transaction.getPermissionsForUser(ilios.management.permissions.selectedUsers[0]);
    }
};

ilios.management.permissions.disableStep2ChoosingLinks = function () {
    var element = document.getElementById('course_choosing_link');

    ilios.dom.setEnableForAElement(element, false);

    element = document.getElementById('program_choosing_link');
    ilios.dom.setEnableForAElement(element, false);

    element = document.getElementById('school_choosing_link');
    ilios.dom.setEnableForAElement(element, false);
};

ilios.management.permissions.populateUserPickerDOM = function (parentContainer, continueAction,
                                                               cancelAction) {
    var container = document.createElement('div');
    var element = document.createElement('ul');
    var itemSelectHandler = null;

    container.setAttribute('style', 'float:left; width: 46%; overflow: auto;');
    element.setAttribute('class', 'picked');
    element.setAttribute('style', 'height: 321px;');
    element.setAttribute('id', 'permissions_selected_users');
	YAHOO.util.Event.addListener(element, 'click', function (e) {
        ilios.management.permissions.handleUserDeselection(e);
    });
    container.appendChild(element);
    parentContainer.appendChild(container);


    container = document.createElement('div');
    container.setAttribute('style', 'float: right; width: 52%; height: 321px; margin-top: 12px; margin-right: 9px; border: 1px #9393A3 solid; background-color: #EDF5FF;');
    container.setAttribute('id', 'permissions_ac_div');
    element = document.createElement('input');
    element.setAttribute('style', 'margin-top: 3px; margin-left: 3px; width: 93%;');
    element.setAttribute('id', 'permissions_ac_input');
    element.setAttribute('type', 'text');
    container.appendChild(element);

    element = document.createElement('div');
    element.setAttribute('style', 'margin-top: 15px; height: 282px;');
    element.setAttribute('class', 'autolist');
    element.setAttribute('id', 'permissions_autolist');
    container.appendChild(element);

    parentContainer.appendChild(container);


    element = document.createElement('div');
    element.setAttribute('style', 'clear: both; margin-bottom: 64px;');
    parentContainer.appendChild(element);


    ilios.management.appendContinueOrCancelButtons(parentContainer, continueAction, cancelAction,
                                                   'permissions_user_picker_continue_button',
                                                   'general.terms.cancel');

	ilios.management.permissions.userAutoCompleter
					= new YAHOO.widget.AutoComplete('permissions_ac_input', 'permissions_autolist',
													ilios.management.userDataSource);

	ilios.management.permissions.userAutoCompleter.alwaysShowContainer = true;
	ilios.management.permissions.userAutoCompleter.allowBrowserAutocomplete = false;
	ilios.management.permissions.userAutoCompleter.minQueryLength = 0;
	ilios.management.permissions.userAutoCompleter.maxResultsDisplayed = 2500;

	ilios.management.permissions.userAutoCompleter.autoHighlight = false;

	ilios.management.permissions.userAutoCompleter.applyLocalFilter = true;
	ilios.management.permissions.userAutoCompleter.filterResults
                                                        = ilios.management.permissions.filterUsers;

	ilios.management.permissions.userAutoCompleter.resultTypeList = false;

	ilios.management.permissions.userAutoCompleter.formatResult = function (resultDataObject,
                                                                            queryString,
                                                                            resultMatch) {
        var rhett = '<span title="' + resultDataObject.getEmailAddress()
                                + '" style="cursor: pointer;">';

		rhett += resultDataObject.getFormattedName(ilios.utilities.USER_NAME_FORMAT_LAST_FIRST);
		rhett += '</span>';

		return rhett;
	};

	itemSelectHandler = function (selectionType, selectionArgs) {
		ilios.management.permissions.handleUserSelection(selectionArgs[2]);

		selectionArgs[1].parentNode.removeChild(selectionArgs[1]);

		document.getElementById('permissions_ac_input').value = '';
	};
	ilios.management.permissions.userAutoCompleter.itemSelectEvent.subscribe(itemSelectHandler);

    ilios.management.permissions.selectedUsers = new Array();
};

ilios.management.permissions.handleUserSelection = function (selectedModel) {
	var displayString = selectedModel.getFormattedName(ilios.utilities.USER_NAME_FORMAT_LAST_FIRST);

	ilios.dom.addNewLIElementWithIliosModel(document.getElementById('permissions_selected_users'),
											selectedModel, displayString);

	ilios.management.permissions.selectedUsers.push(selectedModel);

    ilios.dom.setElementEnabled(document.getElementById('permissions_user_picker_continue_button'),
                                true);
};

ilios.management.permissions.handleUserDeselection = function (event) {
	var target = ilios.utilities.getEventTarget(event);

	if (target.tagName.toLowerCase() === 'li') {
		var model = target.iliosModel;

		target.parentNode.removeChild(target);

        ilios.utilities.removeElementWithValue(ilios.management.permissions.selectedUsers, model);

        ilios.dom.setElementEnabled(document.getElementById('permissions_user_picker_continue_button'),
                                    (ilios.management.permissions.selectedUsers.length > 0));

        ilios.alert.networkActivityI18NStrings.push('management.permissions.fetching_users');
    	ilios.alert.updateServerInteractionProgress();

        ilios.management.permissions.userAutoCompleter.sendQuery('');

		return false;
	}

    return true;
};

ilios.management.permissions.filterUsers = function (queryString, fullResponse, parsedResponse,
                                                     callback) {
	var len = parsedResponse.results.length;
	var selectedList = document.getElementById('permissions_selected_users');
	var filteredResults = new Array();
	var i = 0;
    var model = null;

	for (; i < len; i++) {
        model = new UserModel(parsedResponse.results[i]);

		if (! ilios.dom.iliosModeledLIElementsContainMatchingModel(selectedList, model)) {
			filteredResults.push(model);
		}
	}

	parsedResponse.results = filteredResults;

    ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
							   			   'management.permissions.fetching_users');
	ilios.alert.updateServerInteractionProgress();

	return parsedResponse;
};

ilios.management.permissions.endWorkflow = function () {
    var element = document.getElementById('management_center_content');
    var spaceFiller = document.createElement('div');

    spaceFiller.setAttribute('style', 'height: 580px;');

    ilios.utilities.removeAllChildren(element);
    element.appendChild(spaceFiller);

    ilios.management.permissions.userAutoCompleter = null;
    ilios.management.permissions.selectedUsers = null;

    ilios.management.displayOptionAsSelected(null);
};

// @private
ilios.management.permissions.populateCourseAndProgramFromPermissionObjects = function (permissionObjects) {
    var i = 0;
    var len = permissionObjects.length;
    var permission = null;
    var coursePermissions = new Array();
    var programPermissions = new Array();
    var schoolPermissions = new Array();

    for (i = 0; i < len; i++) {
        permission = new PermissionModel(permissionObjects[i]);

        if (permission.getUnderlyingObject() != null) {
            if (permission.getUnderlyingObjectName() == 'course') {
                coursePermissions.push(permission);
            }
            else if (permission.getUnderlyingObjectName() == 'program') {
                programPermissions.push(permission);
            }
            else if (permission.getUnderlyingObjectName() == 'school') {
                schoolPermissions.push(permission);
            }
        }
    }

    len = coursePermissions.length;
    if (ilios.management.selectedPermissionCourses.length == 0) {
        var dates = null;
        var cObject = null;

        for (i = 0; i < len; i++) {
            cObject = coursePermissions[i].getUnderlyingObject();
            dates = ilios.management.getStartAndEndDateObjectsFromPermissionCourseDBObject(cObject);

            // fyi: SimpleCourseModel is defined via the autogenerated code
            //          via course_picker_include.php
            permission = new SimpleCourseModel();
            permission.setTitle(coursePermissions[i].getTitle());
            permission.setDBId(coursePermissions[i].getUnderlyingObjectDBId());

            permission.year = parseInt(cObject.year);
            permission.startDate = dates[0];
            permission.endDate = dates[1];


            ilios.management.selectedPermissionCourses.length++;
            ilios.management.selectedPermissionCourses[permission.dbId] = permission;
        }
    }
    ilios.management.permissions.populatePermissions('current_course_permissions_div',
                                                     coursePermissions);

    len = programPermissions.length;
    if (ilios.management.permissions.selectedPermissionPrograms.length == 0) {
        for (i = 0; i < len; i++) {
            // fyi: SimpleProgramModel is defined via the autogenerated code
            //          via program_picker_include.php
            permission = new SimpleProgramModel();
                permission.setTitle(programPermissions[i].getTitle());
            permission.setDBId(programPermissions[i].getUnderlyingObjectDBId());

            ilios.management.permissions.selectedPermissionPrograms.length++;
            ilios.management.permissions.selectedPermissionPrograms[permission.dbId] = permission;
        }
    }

    ilios.management.permissions.populatePermissions('current_program_permissions_div',
                                                     programPermissions);

    len = schoolPermissions.length;
    if (ilios.management.permissions.selectedPermissionSchools.length == 0) {
        for (i = 0; i < len; i++) {
            // fyi: SimpleSchoolModel is defined via the autogenerated code
            //          via school_picker_include.php
            permission = new SimpleSchoolModel();
                permission.setTitle(schoolPermissions[i].getTitle());
            permission.setDBId(schoolPermissions[i].getUnderlyingObjectDBId());

            ilios.management.permissions.selectedPermissionSchools.length++;
            ilios.management.permissions.selectedPermissionSchools[permission.dbId] = permission;
        }
    }
    ilios.management.permissions.populatePermissions('current_school_permissions_div',
                                                     schoolPermissions);
};

// @private
ilios.management.permissions.populatePermissions = function (containerIdStr, permissionModels) {
    var container = document.getElementById(containerIdStr);
    var element = null;
    var modelsToDisplay = null;
    var len = 0;

    if (container.needsEmptying) {
        ilios.utilities.removeAllChildren(container);
        container.needsEmptying = false;
    }

    if (container.permissionModels != null) {
        modelsToDisplay
           = ilios.management.permissions
                             .addPermissionModelsAndReturnArrayToDisplay(container.permissionModels,
                                                                         permissionModels);
    }
    else {
        container.permissionModels = permissionModels;
        modelsToDisplay = permissionModels;
    }

    len = modelsToDisplay.length;
    for (var i = 0; i < len; i++) {
        element = document.createElement('div');
        element.innerHTML = modelsToDisplay[i].getDisplayHTML();
        container.appendChild(element);
    }
};

// @private
ilios.management.permissions.addPermissionModelsAndReturnArrayToDisplay = function (preExisting,
                                                                                    newModels) {
    var rhett = new Array();
    var i = 0;
    var j = 0;
    var len = newModels.length;
    var len2 = 0;
    var model = null;
    var shouldAdd = false;

    for (i = 0; i < len; i++) {
        model = newModels[i];

        shouldAdd = true;
        len2 = preExisting.length;
        for (j = 0; j < len2; j++) {
            if (model.getDisplayHTML() == preExisting[j].getDisplayHTML()) {
                shouldAdd = false;

                break;
            }
        }

        if (shouldAdd) {
            rhett.push(model);
        }

        // We still want to add it to the pre-existing since it will be for a different user id,
        //      in all plausible use cases
        preExisting.push(model);
    }

    return rhett;
};
