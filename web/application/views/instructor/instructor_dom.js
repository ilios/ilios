/**
 * DEPENDENCIES:
 * 		YUI lib (at least YAHOO.widget and its dependencies)
 * 		scripts/ilios_dom.js
 * 		scripts/ilios_ui.js
 * 		instructor_transaction.js
 */

ilios.igm.instructorGroupModels = new Array();
ilios.igm.nextContainerNumber = 1;

ilios.igm.dirtyStateListener = {

	modelChanged: function (model, mockedThis) {
		var containerNumber = -1;
		var collapserId = null;
		var element = null;

		for (var key in ilios.igm.instructorGroupModels) {
			if (ilios.igm.instructorGroupModels[key] == model) {
				containerNumber = key;

				break;
			}
		}

		collapserId = ilios.dom.childCollapsingContainerIdForContainerNumber(containerNumber);
		element = document.getElementById(collapserId).parentNode;
		if (model.isModelDirty()) {
			(new YAHOO.util.Element(element)).addClass('dirty_state');
		}
		else {
			(new YAHOO.util.Element(element)).removeClass('dirty_state');
		}

		element = document.getElementById(ilios.igm.generateIdForSaveButton(containerNumber));
		ilios.dom.setElementEnabled(element, model.isModelDirty());

		element = document.getElementById(ilios.igm.generateIdForMemberCount(containerNumber));
		element.innerHTML = model.getMemberCount();

		element
		  = document.getElementById(ilios.igm.generateIdForSelectedInstructorsDiv(containerNumber));
		element.innerHTML = ilios.igm.getDivFormattedMemberListForGroupModel(model);
	}

};

ilios.igm.buildGroupModel = function (groupId, title, users) {
	var groupModel = new InstructorGroupModel();

	groupModel.setDBId(groupId);
	groupModel.setTitle(title);

	if (users != null) {
		var len = users.length;
		var i = 0;
		var userModel = null;
		var loadedUser = null;

		for (; i < len; i++) {
			loadedUser = users[i];

			userModel = new UserModel(loadedUser);

			groupModel.addUser(userModel);
		}
	}

	groupModel.clearDirtyState();

	return groupModel;
};

ilios.igm.createGroupUI = function (groupContainer, containerNumber) {
    var formDOMElement = new YAHOO.util.Element(document.createElement('form'), {
        method: 'POST',
        action: ''
    });
    var newGroupDOMTree = ilios.dom.buildChildContainerDOMTree(containerNumber, null, null,
        null, null, null, ilios.igm.deleteGroup, ilios.igm.handleGroupDivCollapse,
        ilios.igm.handleGroupDivCollapse, formDOMElement, ilios.igm.groupContentGenerator);

    var element = null;

    formDOMElement.get('element').setAttribute('cnumber', containerNumber);

    groupContainer.appendChild(newGroupDOMTree.get('element'));

    ilios.igm.registerUIListenersForContainerNumber(containerNumber);
};

ilios.igm.getDivFormattedMemberListForGroupModel = function (groupModel) {
    var rhett = '';
    var arr = [];
    var users = groupModel.getUsers();
    
    for (var key in users) {
        arr.push(users[key].getFormattedName(ilios.utilities.USER_NAME_FORMAT_LAST_FIRST));
    }
    arr.sort(function(a, b) {
        return a.localeCompare(b);
    });
    rhett = arr.join("<br />");
    return rhett;
};

ilios.igm.registerUIListenersForContainerNumber = function (containerNumber) {
    var element = document.getElementById(ilios.igm.generateIdForGroupName(containerNumber));

    YAHOO.util.Event.addListener(element, 'keyup', function () {
        var title = document.getElementById(ilios.igm.generateIdForGroupName(containerNumber));
        var groupModel = ilios.igm.instructorGroupModels[containerNumber];
        groupModel.setTitle(title.value);
    });
};

ilios.igm.handleGroupDivCollapse = function (containerNumber, summaryTextDiv) {
    var groupModel = ilios.igm.instructorGroupModels[containerNumber];
    var i18nStr = ilios_i18nVendor.getI18NString('general.phrases.total_members');
    summaryTextDiv.innerHTML = groupModel.getTitle()
        + '<span class="membercount">'
        + i18nStr + ': <span class="read_only_data">'
        + groupModel.getMemberCount() + '</span></span>';
};

ilios.igm.generateIdForAssociatedCoursesDiv = function (containerNumber) {
	return '' + containerNumber + '_igm_associated_courses';
};

ilios.igm.generateIdForGroupName = function (containerNumber) {
	return '' + containerNumber + '_igm_group_name';
};

ilios.igm.generateIdForMemberCount = function (containerNumber) {
	return '' + containerNumber + '_igm_total_members';
};

ilios.igm.generateIdForSelectedInstructorsDiv = function (containerNumber) {
	return '' + containerNumber + '_igm_faculty_picker_selected_text_list';
};

ilios.igm.generateIdForAddMembersLink = function (containerNumber) {
	return '' + containerNumber + '_igm_add_members';
};

ilios.igm.generateIdForEditMembershipLink = function (containerNumber) {
	return '' + containerNumber + '_igm_edit_membership';
};

ilios.igm.generateIdForSaveButton = function (containerNumber) {
	return '' + containerNumber + '_igm_save_button';
};

ilios.igm.toggleAssociatedCoursesVisibility = function (widget) {
    var Element = YAHOO.util.Element;
	var idStr = ilios.igm.generateIdForAssociatedCoursesDiv(widget.cnumber);
	var containerElement = document.getElementById(idStr);
	var container = new Element(containerElement);
	var widgetElement = new Element(widget);

	if (container.getStyle('display') != 'none') {
		container.setStyle('display', 'none');

		widgetElement.removeClass('expanded_widget');
		widgetElement.addClass('collapsed_widget');
	}
	else {
		container.setStyle('display', 'block');

		widgetElement.removeClass('collapsed_widget');
		widgetElement.addClass('expanded_widget');

		if (! containerElement.hasLoaded) {
			ilios.igm.loadAssociatedCoursesForContainerNumber(widget.cnumber);
		}
	}
};

ilios.igm.groupContentGenerator = function (parentElement, containerNumber) {
    var Element = YAHOO.util.Element;
	var scratchLabel = null;
	var scratchInput = null;
	var scratchElement = null;
	var textNode = null;
	var titleId = ilios.igm.generateIdForGroupName(containerNumber);
	var groupModel = ilios.igm.instructorGroupModels[containerNumber];
	var i18nStr = ilios_i18nVendor.getI18NString('instructor_groups.group.group_name');
	var rowElement, actionCol, actionElement;

    //save button row
	rowElement = ilios.dom.createEntityContainerInputRow();
	scratchInput = document.createElement('button');
	scratchInput.setAttribute('id', ilios.igm.generateIdForSaveButton(containerNumber));
	scratchInput.setAttribute('class', 'small radius button');
	scratchInput.setAttribute('disabled', 'true');
	scratchInput.setAttribute('onclick',
							  "ilios.igm.handleSaveForContainerNumber(" + containerNumber
							  												+ "); return false;");
	textNode = document.createTextNode(ilios_i18nVendor.getI18NString('general.terms.save'));
	scratchInput.appendChild(textNode);
	rowElement.appendChild(scratchInput);

	parentElement.appendChild(rowElement);


    //group title row
	rowElement = ilios.dom.createEntityContainerInputRow();
    //label column
	scratchLabel = new Element(document.createElement('label'), {'for': titleId});
	textNode = document.createTextNode(i18nStr + ': ');
	scratchLabel.appendChild(textNode);
	ilios.dom.createLabelCol(rowElement, scratchLabel.get('element'));
	//data column
	scratchInput = new Element(document.createElement('input'),
								{'id': titleId, type: 'text', size: '50',
								 value: groupModel.getTitle()});
	ilios.dom.createDataCol(rowElement, scratchInput.get('element'));
	//no actions
	scratchInput = document.createElement('span');
	ilios.dom.createActionCol(rowElement, scratchInput);

	parentElement.appendChild(rowElement);

    //member list row
    rowElement = ilios.dom.createEntityContainerInputRow();
	i18nStr = ilios_i18nVendor.getI18NString('instructor_groups.group.member_list');
	titleId = ilios.igm.generateIdForSelectedInstructorsDiv(containerNumber);

	//label column
	scratchLabel = document.createElement('label');
	textNode = document.createTextNode(i18nStr + ': ');
	scratchLabel.appendChild(textNode);
	ilios.dom.createLabelCol(rowElement, scratchLabel);
	//data column
	scratchInput = document.createElement('div');
	scratchInput.setAttribute('id', titleId);
	scratchInput.setAttribute('class', 'read_only_data scroll_list');
	scratchInput.innerHTML = ilios.igm.getDivFormattedMemberListForGroupModel(groupModel);
	ilios.dom.createDataCol(rowElement, scratchInput);
	//action column

	// Add Members to this Group (now called "Manage Group Membership")
	   //i18nStr = ilios_i18nVendor.getI18NString('general.phrases.manage_membership');
	i18nStr = ilios_i18nVendor.getI18NString('general.terms.edit');
	actionElement = document.createElement('a');
	actionElement.setAttribute('class', 'tiny radius button');
	actionElement.setAttribute('href', '');
	actionElement.setAttribute('title', 'Manage Current Membership');
	actionElement.setAttribute('id', ilios.igm.generateIdForAddMembersLink(containerNumber));
	actionElement.setAttribute('onclick',
							  "ilios.ui.onIliosEvent.fire({action: 'default_dialog_open', "
													+   "event: 'faculty_picker_show_dialog', "
													+   "container_number: " + containerNumber
													+ "}); return false;");
	textNode = document.createTextNode(i18nStr);
	actionElement.appendChild(textNode);
	//ilios.dom.createActionCol(rowElement, scratchLabel);
	actionCol = document.createElement('div');
	actionCol.setAttribute('class', 'actions column');
	actionCol.appendChild(actionElement);


	   //i18nStr = ilios_i18nVendor.getI18NString('general.phrases.add_members');
	i18nStr = ilios_i18nVendor.getI18NString('general.phrases.add_new');
	actionElement = document.createElement('a');
	actionElement.setAttribute('class', 'tiny radius button');
	actionElement.setAttribute('href', '');
	actionElement.setAttribute('title', 'Add/Upload New Members to the Instructor List');
	actionElement.setAttribute('id', ilios.igm.generateIdForEditMembershipLink(containerNumber));
	actionElement.setAttribute('onclick',
							  "ilios.ui.onIliosEvent.fire({action: 'em_dialog_open', "
													+   "event: 'add_new_members_picker_show_dialog', "
													+   "container_number: " + containerNumber
													+ "}); return false;");
	textNode = document.createTextNode(i18nStr);
	actionElement.appendChild(textNode);
	actionCol.appendChild(actionElement);
	rowElement.appendChild(actionCol);
	parentElement.appendChild(rowElement);

    //total members row
	rowElement = ilios.dom.createEntityContainerInputRow();
	i18nStr = ilios_i18nVendor.getI18NString('general.phrases.total_members');
    //label column
	scratchLabel = document.createElement('label');
	textNode = document.createTextNode(i18nStr + ': ');
	scratchLabel.appendChild(textNode);
	ilios.dom.createLabelCol(rowElement, scratchLabel);

    //data column
	scratchInput = document.createElement('span');
	scratchInput.setAttribute('id', ilios.igm.generateIdForMemberCount(containerNumber));
	scratchInput.setAttribute('class', 'read_only_data');
	scratchInput.innerHTML = groupModel.getMemberCount();
	ilios.dom.createDataCol(rowElement, scratchInput);

    //action column
	//no actions
	scratchInput = document.createElement('span');
	ilios.dom.createActionCol(rowElement, scratchInput);

	parentElement.appendChild(rowElement);

	// associated courses row
    rowElement = ilios.dom.createEntityContainerInputRow();
	i18nStr = ilios_i18nVendor.getI18NString('instructor_groups.group.associated_courses');
    //label column
	scratchLabel = document.createElement('label');
	textNode = document.createTextNode(i18nStr + ': ');
	scratchLabel.appendChild(textNode);
	ilios.dom.createLabelCol(rowElement, scratchLabel);
    //data column
	scratchInput = document.createElement('div');
	scratchInput.setAttribute('id', ilios.igm.generateIdForAssociatedCoursesDiv(containerNumber));
	scratchInput.setAttribute('class', 'scroll_list');
	scratchInput.hasLoaded = false;
	ilios.dom.createDataCol(rowElement, scratchInput);
	ilios.igm.loadAssociatedCoursesForContainerNumber(containerNumber);


	//scratchInput.hasLoaded = false;
	//scratchElement.appendChild(scratchInput);

    //action column
	//no actions
	scratchInput = document.createElement('span');
	ilios.dom.createActionCol(rowElement, scratchInput);

	parentElement.appendChild(rowElement);
};

ilios.igm.resetAddNewMembersDialog = function (dialog) {
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

/**
 * <code>window.onbeforeunload</code> event handler.
 * Checks if any of the instructor groups loaded on the page are in dirty state, and returns a warning if applicable.
 *
 * @method windowWillClose
 * @param {Event} e The unload event.
 * @return {String|undefined} A warning message if any instructor group model is in a dirty state.
 *
 * @link https://developer.mozilla.org/en-US/docs/Web/API/Window.onbeforeunload
 */
ilios.igm.windowWillClose = function (e) {
    var i, n, msg, model;
    for (i = 0, n = ilios.igm.instructorGroupModels.length; i < n; i++) {
        model = ilios.igm.instructorGroupModels[i];
        // extra typecheck needed here b/c of crappy "sparse array" implementation.
        // @todo fix this by refactoring ilios.igm.instructorGroupModels as Object instead of Array. [ST 2013/10/18]
        if (typeof model !== 'undefined' && model.isModelDirty()) {
            msg = ilios_i18nVendor.getI18NString('general.warning.dirty_state_lose_changes');
            e.returnValue = msg;
            return msg;
        }
    }
};
