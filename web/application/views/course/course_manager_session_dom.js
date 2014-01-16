/**
 * DEPENDENCY: YUI lib (at least YAHOO.util and its dependencies)
 * DEPENDENCY: scripts/ilios_ui.js
 * DEPENDENCY: scripts/ilios_utilities.js
 */

ilios.namespace('cm.session');


ilios.cm.session.sessionContainerCount = 0;

ilios.cm.session.collapseOrExpandSessions = function (courseIsLoading) {
    var element = new YAHOO.util.Element(document.getElementById('expand_sessions_link'));
    var collapseValue = 1;
    var sessions = ilios.cm.currentCourseModel.getSessions();
    var collapseValueToUse = -1;

    if (element.hasClass('sessions_collapsed') && (! courseIsLoading)) {
        element.removeClass('sessions_collapsed');
        element.get('element').innerHTML
                            = ilios_i18nVendor.getI18NString('general.phrases.collapse_all');
        collapseValue = 2;
    }
    else {
        element.addClass('sessions_collapsed');
        element.get('element').innerHTML
                            = ilios_i18nVendor.getI18NString('general.phrases.expand_all');
    }

    for (var key in sessions) {
        collapseValueToUse = collapseValue;

        if (courseIsLoading && (sessions[key].getDBId() == ilios.cm.loadedSessionIdToDisplay)) {
            collapseValueToUse = 2;
        }

        ilios.dom.collapseChildForContainerNumber(key, collapseValueToUse,ilios.cm.session.setSessionContainerHeaderTitle,ilios.cm.session.setSessionContainerHeaderTitle);
    }
};

ilios.cm.session.generateSessionOfferingsLinkText = function (offeringCount) {
    var i18nStr = ilios_i18nVendor.getI18NString('course_management.go_to_calendar');

    return i18nStr + ' (' + offeringCount + ')';
};

ilios.cm.session.generateIdStringForSessionAttireCheckbox = function (containerNumber) {
    return '' + containerNumber + '_session_attire_checkbox';
};

ilios.cm.session.generateIdStringForSessionCoreRadio = function (containerNumber) {
    return '' + containerNumber + '_session_core_radio';
};

ilios.cm.session.generateIdStringForSessionDescription = function (containerNumber) {
    return '' + containerNumber + '_session_description';
};

ilios.cm.session.generateIdStringForSessionDescriptionEditButton = function (containerNumber) {
    return '' + containerNumber + '_session_description_edit_button';
};

ilios.cm.session.generateIdStringForSessionEquipmentCheckbox = function (containerNumber) {
    return '' + containerNumber + '_session_equipment_checkbox';
};

ilios.cm.session.generateIdStringForSessionGroups = function (containerNumber) {
    return '' + containerNumber + '_session_groups';
};

ilios.cm.session.generateIdStringForSessionILMCheckbox = function (containerNumber) {
    return '' + containerNumber + '_session_ilm_checkbox';
};

ilios.cm.session.generateIdStringForSessionILMDiv = function (containerNumber) {
    return '' + containerNumber + '_session_ilm_div';
};

ilios.cm.session.generateIdStringForSessionILMDueDate = function (containerNumber) {
    return '' + containerNumber + '_session_ilm_due_date_tf';
};

ilios.cm.session.generateIdStringForSessionILMHours = function (containerNumber) {
    return '' + containerNumber + '_session_ilm_hours_tf';
};

ilios.cm.session.generateIdStringForSessionILMGroupsLink = function (containerNumber) {
    return '' + containerNumber + '_session_ilm_groups_link';
};

ilios.cm.session.generateIdStringForSessionMultiOfferingCreateLink = function (containerNumber) {
    return '' + containerNumber + '_session_multi_offering_link';
};

ilios.cm.session.generateIdStringForSessionOfferingsLink = function (containerNumber) {
    return '' + containerNumber + '_session_offerings_link';
};

ilios.cm.session.generateIdStringForSessionOfferingEnumerationList = function (containerNumber) {
    return '' + containerNumber + '_session_offerings_enumeration';
};

ilios.cm.session.generateIdStringForSessionOfferingExpandWidget = function (containerNumber) {
    return '' + containerNumber + '_session_offerings_expand';
};

ilios.cm.session.generateIdStringForSessionSupplementalRadio = function (containerNumber) {
    return '' + containerNumber + '_session_supplemental_radio';
};

ilios.cm.session.generateIdStringForSessionTitle = function (containerNumber) {
    return '' + containerNumber + '_session_title';
};

ilios.cm.session.generateIdStringForSessionTypeSelector = function (containerNumber) {
    return '' + containerNumber + '_session_type_selector';
};

// @private
ilios.cm.session.appendMeSHBlock = function (parentElement, containerNumber, labelString) {
    var clickFunction = function (e) {
        var sessionModel = ilios.cm.currentCourseModel.getSessionForContainer(containerNumber);
        IEvent.fire({
            action: 'mesh_picker_dialog_open',
            model_in_edit: sessionModel
        });
    };

    return ilios.cm.createAttributeDisplayBlock(parentElement, containerNumber, labelString,
        clickFunction, 'mesh_terms_picker_selected_text_list', false, '');
};

ilios.cm.session.resetEditSessionObjectiveDialog = function (dialog) {
    var objectivesDiv = document.getElementById('eso_parent_objectives_div');
    var courseObjectives = ilios.cm.currentCourseModel.getObjectives();
    var courseObjective = null;
    var parentObjectives = [];

    var selected = false;
    if (ilios.cm.inEditObjectiveModel) {
        parentObjectives = ilios.cm.inEditObjectiveModel.getParentObjectives();
        document.getElementById("eso_textarea").value = ilios.cm.inEditObjectiveModel.getDescription();
        dialog.esoEditor.setEditorHTML(ilios.cm.inEditObjectiveModel.getDescription());
    } else {
        document.getElementById("eso_textarea").value = "";
        dialog.esoEditor.setEditorHTML("");
    }

    ilios.utilities.removeAllChildren(objectivesDiv);
    for (var i = 0; i < courseObjectives.length; i++) {
        courseObjective = courseObjectives[i];
        if (courseObjective != null) {
            selected = ilios.utilities.arrayContains(parentObjectives, courseObjective.getDBId());
            objectivesDiv.appendChild(ilios.cm.session.domForEditSessionCourseObjective(courseObjective, selected));
            ilios.utilities.appendClearingDivToContainer(objectivesDiv);
        }
    }
};

ilios.cm.session.domForEditSessionCourseObjective = function (objectiveModel, selected) {
    var rhett = document.createElement('div');
    var textDiv = document.createElement('div');
    var checkboxDiv = document.createElement('div');
    var checkbox = document.createElement('input');

    rhett.setAttribute('style', 'position: relative; margin: 0px;');

    checkboxDiv.setAttribute('style', 'float: left; margin: 1px 0px; width: 22px;');
    checkboxDiv.setAttribute('class', 'objective_checkbox_div');
    textDiv.setAttribute('style', 'float: auto; padding: 3px 6px 0; width: 95%;');

    checkbox.setAttribute('type', 'checkbox');
    if (selected) {
        checkbox.setAttribute('checked', 'yes');
    }
    checkbox.iliosModel = objectiveModel;
    checkboxDiv.appendChild(checkbox);

    textDiv.innerHTML = objectiveModel.getDescription();

    rhett.appendChild(checkboxDiv);
    rhett.appendChild(textDiv);

    return rhett;
};

ilios.cm.session.getArrayOfCurrentlySelectedSessionParentObjectives = function () {
    var Element = YAHOO.util.Element;
    var rhett = [];
    var objectivesDiv = document.getElementById('eso_parent_objectives_div');
    var children = objectivesDiv.children;
    var objective = null;
    var checkBoxDiv = null;
    var checkbox = null;

    for (var i = 0; i < children.length; i += 2) {
        objective = new Element(children[i]);
        checkboxDiv = objective.getElementsByClassName('objective_checkbox_div');
        checkbox = checkboxDiv[0].children[0];

        if (checkbox.checked) {
            rhett.push(checkbox.iliosModel.getDBId());
        }
    }

    return rhett;
};

// @private
ilios.cm.session.setOfferingEnumerationDivVisibility = function (containerNumber, widgetDiv, shouldToggle) {
    var Element = YAHOO.util.Element;
    var yElement = null;
    var idString = null;
    var div = null;

    if (ilios.cm.currentCourseModel == null) {
        return;
    }

    if (widgetDiv == null) {
        idString = ilios.cm.session.generateIdStringForSessionOfferingExpandWidget(containerNumber);

        widgetDiv = document.getElementById(idString);
    }

    yElement = new Element(widgetDiv);
    idString = ilios.cm.session.generateIdStringForSessionOfferingEnumerationList(containerNumber);
    div = new Element(document.getElementById(idString));
    if ((div.getStyle('display') != 'none') && shouldToggle) {
        yElement.removeClass('expanded_widget');
        yElement.addClass('collapsed_widget');
        div.setStyle('display', 'none');
    } else {
        yElement.removeClass('collapsed_widget');
        yElement.addClass('expanded_widget');
        div.setStyle('display', 'block');
    }
};

/*
 * (hideous thick coding -- unclear how to make it more readable
 */
// @private
ilios.cm.session.sessionContentGenerator = function (parentElement, containerNumber) {
    var scratchLabel = null;
    var scratchInput = null;
    var scratchOption = null;
    var scratchElement = null;
    var scratchContainer = null;
    var subContainer = null;
    var subSubContainer = null;
    var text = null;
    var i18nStr = ilios_i18nVendor.getI18NString('course_management.session.session_title');
    var len = ilios.cm.loadedSessionTypes.length;
    var i = 0;
    var sessionType = null;
    var rowElement, labelCol, dataCol, actionCol, count;
    var scratchElementId = null;
    var Event = YAHOO.util.Event;
    var Element = YAHOO.util.Element;

    //session title
    rowElement = ilios.dom.createEntityContainerInputRow();
    scratchLabel = document.createElement('label');
    text = document.createTextNode(i18nStr);
    scratchLabel.appendChild(text);
    ilios.dom.createLabelCol(rowElement, scratchLabel);
    //data column
    scratchInput = document.createElement('input');
    scratchInput.setAttribute('id', ilios.cm.session.generateIdStringForSessionTitle(containerNumber));
    scratchInput.setAttribute('type', 'text');
    Event.addListener(scratchInput, 'keyup', function () {
        var element = document.getElementById(ilios.cm.session.generateIdStringForSessionTitle(containerNumber));
        var sessionModel = ilios.cm.currentCourseModel.getSessionForContainer(containerNumber);
        sessionModel.setTitle(element.value);
    });

    ilios.dom.createDataCol(rowElement, scratchInput);
    //no actions
    scratchInput = document.createElement('span');
    ilios.dom.createActionCol(rowElement, scratchInput);

    parentElement.appendChild(rowElement);

    //session type
    i18nStr = ilios_i18nVendor.getI18NString('course_management.session.session_type');
    rowElement = ilios.dom.createEntityContainerInputRow();
    //label column
    scratchLabel = document.createElement('label');
    text = document.createTextNode(i18nStr);
    scratchLabel.appendChild(text);
    ilios.dom.createLabelCol(rowElement, scratchLabel);
    //data column
    scratchInput = new Element(document.createElement('select'), {id:
        ilios.cm.session.generateIdStringForSessionTypeSelector(containerNumber)
    });
    scratchInput.addListener('change', function (e) {
        ilios.cm.session.sessionTypeSelected(containerNumber);
    });

    for (i = 0; i < len; i++) {
        sessionType = ilios.cm.loadedSessionTypes[i];
        scratchOption = document.createElement('option');
        scratchOption.setAttribute('value', sessionType.dbId);
        if (i == 0) {
            scratchOption.setAttribute('selected', 'true');
        }
        textNode = document.createTextNode(sessionType.title);
        scratchOption.appendChild(textNode);
        scratchInput.appendChild(scratchOption);
    }
    ilios.dom.createDataCol(rowElement, scratchInput.get('element'));
    //no actions
    scratchElement = document.createElement('span');
    ilios.dom.createActionCol(rowElement, scratchElement);

    parentElement.appendChild(rowElement);

    //ILM
    rowElement = ilios.dom.createEntityContainerInputRow();
    //no label
    scratchLabel = document.createElement('span');
    ilios.dom.createLabelCol(rowElement, scratchLabel);
    //data column
    scratchElement = document.createElement('label');
    scratchInput = document.createElement('input');
    scratchInput.setAttribute('type', 'checkbox');
    scratchInput.setAttribute('name', 'independent_learning');
    scratchInput.setAttribute('id', ilios.cm.session.generateIdStringForSessionILMCheckbox(containerNumber));
    Event.addListener(scratchInput, 'click', function () {
        ilios.cm.session.sessionILMCheckboxSelected(this, containerNumber);
    });
    i18nStr = ilios_i18nVendor.getI18NString('course_management.session.independent_learning');
    scratchElement.appendChild(scratchInput);
    scratchElement.appendChild(document.createTextNode(i18nStr));
    dataCol = ilios.dom.createDataCol(rowElement, scratchElement);

    //add in the ILM due date - clean this up later
    subContainer = document.createElement('span');
    subContainer.setAttribute('class', 'margin-l');
    subContainer.setAttribute('style', 'display:none;');
    subContainer.setAttribute('id', ilios.cm.session.generateIdStringForSessionILMDiv(containerNumber));
    scratchInput = document.createElement('input');
    scratchInput.setAttribute('style', 'width: 3em;');
    scratchInput.setAttribute('type', 'text');
    scratchInput.setAttribute('name', 'ilm_hours');
    scratchInput.setAttribute('value', '1');
    scratchInput.setAttribute('id', ilios.cm.session.generateIdStringForSessionILMHours(containerNumber));
    Event.addListener(scratchInput, 'keyup', function () {
        var hourIdString = ilios.cm.session.generateIdStringForSessionILMHours(containerNumber);
        var hourTF = document.getElementById(hourIdString);
        if (ilios.utilities.isNumber(hourTF.value)) {
            var sessionModel = ilios.cm.currentCourseModel.getSessionForContainer(containerNumber);
            var ilmModel = sessionModel.getIndependentLearningModel();
            ilmModel.setHours(hourTF.value);
        }
    });

    subContainer.appendChild(scratchInput);
    i18nStr = ' ' + ilios_i18nVendor.getI18NString('general.terms.hours').toLowerCase() + ' ';
    subContainer.appendChild(document.createTextNode(i18nStr));

    subSubContainer = document.createElement('span');
    subSubContainer.setAttribute('style', 'display: inline-block;');
    i18nStr = ' ' + ilios_i18nVendor.getI18NString('general.phrases.due_by').toLowerCase() + ' ';
    subSubContainer.appendChild(document.createTextNode(i18nStr));

    scratchInput = document.createElement('input');
    scratchInput.setAttribute('style', 'width: 6em;');
    scratchInput.setAttribute('type', 'text');
    scratchInput.setAttribute('name', 'ilm_hours');
    scratchInput.setAttribute('id', ilios.cm.session.generateIdStringForSessionILMDueDate(containerNumber));
    scratchInput.disabled = true;
    subSubContainer.appendChild(scratchInput);
    subContainer.appendChild(subSubContainer);

    subSubContainer = document.createElement('div');
    subSubContainer.setAttribute('class', 'calendar_button');
    subSubContainer.setAttribute('style', 'display: inline-block;');
    Event.addListener(subSubContainer, 'click',
                       function () {ilios.cm.session.ilm.showILMLightbox(containerNumber);});
    ilios.cm.uiElementsToHideOnLockedView.push(new Element(subSubContainer));
    subContainer.appendChild(subSubContainer);

    //scratchContainer.appendChild(subContainer);
    dataCol.appendChild(subContainer);

    //action column
    scratchInput = document.createElement('button');
    scratchInput.setAttribute('class', 'tiny radius button');
    scratchInput.setAttribute('id', ilios.cm.session.generateIdStringForSessionILMGroupsLink(containerNumber));
    Event.addListener(scratchInput, 'click', function (e) {
        Event.preventDefault(e);
        ilios.cm.session.ilm.showILMLightbox(containerNumber);
    });
    i18nStr = ' ' + ilios_i18nVendor.getI18NString('general.phrases.add_groups');
    scratchInput.appendChild(document.createTextNode(i18nStr));
    ilios.cm.uiElementsToHideOnLockedView.push(new Element(scratchInput));
    ilios.dom.createActionCol(rowElement, scratchInput);

    parentElement.appendChild(rowElement);

    //Offerings
    rowElement = ilios.dom.createEntityContainerInputRow();
    //label
    scratchLabel = document.createElement('label');
    i18nStr = ilios_i18nVendor.getI18NString('general.terms.offerings');
    text = document.createTextNode(i18nStr);
    scratchLabel.appendChild(text);
    ilios.dom.createLabelCol(rowElement, scratchLabel);

    //data column
    scratchInput = document.createElement('div');
    scratchInput.setAttribute('class', 'read_only_data scroll_list');
    scratchInput.setAttribute('id', ilios.cm.session.generateIdStringForSessionOfferingEnumerationList(containerNumber));
    ilios.dom.createDataCol(rowElement, scratchInput);

    //action column
    //button 1
    scratchInput = document.createElement('button');
    scratchInput.setAttribute('class', 'small radius button');
    scratchInput.setAttribute('id', ilios.cm.session.generateIdStringForSessionOfferingsLink(containerNumber));
    scratchInput.setAttribute('onclick', 'return false;');
    Event.addListener(scratchInput, 'click', function () {
        ilios.cm.session.editOfferingsForContainer(containerNumber);
    });
    text = document.createTextNode(ilios.cm.session.generateSessionOfferingsLinkText(0));
    scratchInput.appendChild(text);
    actionCol = ilios.dom.createActionCol(rowElement, scratchInput);

    //button 2
    scratchInput = document.createElement('button');
    scratchInput.setAttribute('class', 'small radius button');
    scratchInput.setAttribute('id', ilios.cm.session.generateIdStringForSessionMultiOfferingCreateLink(containerNumber));
    Event.addListener(scratchInput, 'click', function (e) {
        Event.preventDefault(e);
        ilios.cm.session.mo.showMultiOfferingLightbox(containerNumber);
    });
    i18nStr = ilios_i18nVendor.getI18NString('course_management.create_multi_offering');
    text = document.createTextNode(i18nStr);
    scratchInput.appendChild(text);
    ilios.cm.uiElementsToHideOnLockedView.push(new Element(scratchInput));
    actionCol.appendChild(scratchInput);

    parentElement.appendChild(rowElement);

    //groups
    i18nStr = ilios_i18nVendor.getI18NString('general.terms.groups');
    rowElement = ilios.dom.createEntityContainerInputRow();
    //label col
    scratchLabel = document.createElement('label');
    text = document.createTextNode(i18nStr);
    scratchLabel.appendChild(text);
    labelCol = ilios.dom.createLabelCol(rowElement, scratchLabel);

    //data col
    scratchInput = document.createElement('div');
    scratchInput.setAttribute('id',
                              ilios.cm.session.generateIdStringForSessionGroups(containerNumber));
    scratchInput.setAttribute('class', 'read_only_data scroll_list');
    dataCol = ilios.dom.createDataCol(rowElement, scratchInput);

    //no actions
    scratchInput = document.createElement('span');
    actionCol = ilios.dom.createActionCol(rowElement, scratchInput);

    parentElement.appendChild(rowElement);

    //core/supplemental
    i18nStr = ilios_i18nVendor.getI18NString('general.terms.curriculum_type');
    rowElement = ilios.dom.createEntityContainerInputRow();
    //label column
    scratchLabel = document.createElement('label');
    text = document.createTextNode(i18nStr);
    scratchLabel.appendChild(text);
    labelCol = ilios.dom.createLabelCol(rowElement, scratchLabel);
    //data column
    scratchInput = document.createElement('input');
    scratchInput.setAttribute('type', 'radio');
    scratchInput.setAttribute('name', 'core_or_supplement');
    scratchInput.setAttribute('value', 'core');
    scratchInput.setAttribute('id', ilios.cm.session.generateIdStringForSessionCoreRadio(containerNumber));
    Event.addListener(scratchInput, 'click', function () {
        ilios.cm.session.supplementalCoreRadioChanged(containerNumber, this, false);
    });
    scratchLabel = document.createElement('label');
    scratchLabel.setAttribute('for', scratchInput.getAttribute('id'));
    scratchLabel.setAttribute('id', (scratchInput.getAttribute('id') + '_label'));
    i18nStr = ilios_i18nVendor.getI18NString('general.terms.core');
    scratchLabel.appendChild(scratchInput);
    scratchLabel.appendChild(document.createTextNode(i18nStr));
    dataCol = ilios.dom.createDataCol(rowElement, scratchLabel);

    dataCol.appendChild(document.createElement('br'));

    scratchInput = document.createElement('input');
    scratchInput.setAttribute('type', 'radio');
    scratchInput.setAttribute('name', 'core_or_supplement');
    scratchInput.setAttribute('value', 'supplemental');
    scratchInput.setAttribute('id', ilios.cm.session.generateIdStringForSessionSupplementalRadio(containerNumber));
    scratchInput.setAttribute('checked', 'true');
    Event.addListener(scratchInput, 'click', function () {
        ilios.cm.session.supplementalCoreRadioChanged(containerNumber, this, true);
    });
    scratchLabel = document.createElement('label');
    scratchLabel.setAttribute('for', scratchInput.getAttribute('id'));
    scratchLabel.setAttribute('id', (scratchInput.getAttribute('id') + '_label'));
    i18nStr = ilios_i18nVendor.getI18NString('general.terms.supplemental');
    scratchLabel.appendChild(scratchInput);
    scratchLabel.appendChild(document.createTextNode(i18nStr));
    dataCol.appendChild(scratchLabel);
    //action column - no actions
    scratchInput = document.createElement('span');
    actionCol = ilios.dom.createActionCol(rowElement, scratchInput);

    parentElement.appendChild(rowElement);

    //special requirements
    i18nStr = ilios_i18nVendor.getI18NString('course_management.session.special_requirements');
    rowElement = ilios.dom.createEntityContainerInputRow();
    //label column
    scratchLabel = document.createElement('label');
    text = document.createTextNode(i18nStr);
    scratchLabel.appendChild(text);
    ilios.dom.createLabelCol(rowElement, scratchLabel);
    //data column
    scratchInput = document.createElement('input');
    scratchInput.setAttribute('type', 'checkbox');
    scratchInput.setAttribute('name', 'attire_and_equipment');
    scratchInput.setAttribute('value', 'attire');
    scratchInput.setAttribute('id', ilios.cm.session.generateIdStringForSessionAttireCheckbox(containerNumber));
    Event.addListener(scratchInput, 'click', function () {
        ilios.cm.session.attireCheckboxChanged(containerNumber);
    });
    scratchLabel = document.createElement('label');
    scratchLabel.appendChild(scratchInput);
    i18nStr = ilios_i18nVendor.getI18NString('course_management.session.attire');
    scratchLabel.appendChild(document.createTextNode(i18nStr));
    dataCol = ilios.dom.createDataCol(rowElement, scratchLabel);

    dataCol.appendChild(document.createElement('br'));

    scratchInput = document.createElement('input');
    scratchInput.setAttribute('type', 'checkbox');
    scratchInput.setAttribute('name', 'attire_and_equipment');
    scratchInput.setAttribute('value', 'equipment');
    scratchInput.setAttribute('id', ilios.cm.session.generateIdStringForSessionEquipmentCheckbox(containerNumber));
    Event.addListener(scratchInput, 'click', function () {
        ilios.cm.session.equipmentCheckboxChanged(containerNumber);
    });
    scratchLabel = document.createElement('label');
    scratchLabel.appendChild(scratchInput);
    i18nStr = ilios_i18nVendor.getI18NString('course_management.session.equipment');
    scratchLabel.appendChild(document.createTextNode(i18nStr));
    dataCol.appendChild(scratchLabel);
    //action column - no actions
    scratchInput = document.createElement('span');
    actionCol = ilios.dom.createActionCol(rowElement, scratchInput);

    parentElement.appendChild(rowElement);

    //description
    rowElement = ilios.dom.createEntityContainerInputRow();
    i18nStr = ilios_i18nVendor.getI18NString('general.terms.description');
    //label column
    scratchLabel= document.createElement('label');
    scratchLabel.appendChild(document.createTextNode(i18nStr));
    ilios.dom.createLabelCol(rowElement, scratchLabel);
    //data column
    scratchElement = document.createElement('div');
    scratchElement.setAttribute('class', 'session_description');
    scratchElementId = ilios.cm.session.generateIdStringForSessionDescription(containerNumber);
    scratchElement.setAttribute('id', scratchElementId);
    dataCol = ilios.dom.createDataCol(rowElement, scratchElement);

    // action column
    i18nStr = ilios_i18nVendor.getI18NString('general.terms.edit');
    scratchInput = new Element(document.createElement('a'), {href: ''});
    scratchInput.get('element').setAttribute('onclick', 'return false;');
    scratchElementId = ilios.cm.session.generateIdStringForSessionDescriptionEditButton(containerNumber);
    scratchInput.get('element').setAttribute('id', scratchElementId);
    scratchInput.addClass('tiny radius button');
    text = document.createTextNode(i18nStr);
    scratchInput.appendChild(text);
    actionCol = ilios.dom.createActionCol(rowElement, scratchInput.get('element'));
    ilios.cm.uiElementsToHideOnLockedView.push(scratchInput);
    parentElement.appendChild(rowElement);

    //disciplines
    i18nStr = ilios_i18nVendor.getI18NString('general.terms.topics');
    scratchElement = ilios.cm.appendAttributeDisplayBlock(parentElement, containerNumber, i18nStr,
        'discipline_picker_show_dialog', 'discipline_picker_selected_text_list', false, '');
    ilios.cm.uiElementsToHideOnLockedView.push(scratchElement);

    //MeSH
    i18nStr = ilios_i18nVendor.getI18NString('general.phrases.mesh_terms');
    scratchElement = ilios.cm.session.appendMeSHBlock(parentElement, containerNumber, i18nStr);
    ilios.cm.uiElementsToHideOnLockedView.push(scratchElement);

    //Learning Material
    rowElement = ilios.dom.createEntityContainerInputRow();
    i18nStr = ilios_i18nVendor.getI18NString('course_management.learning_materials.title');

    //label column
    subContainer = document.createElement('div');
    subContainer.setAttribute('class', 'collapsed_widget');
    subContainer.setAttribute('id', ilios.cm.lm.generateIdStringForLearningMaterialExpandWidget(containerNumber));
    Event.addListener(subContainer, 'click', function () {
        ilios.cm.lm.setLearningMaterialDivVisibility(containerNumber, this, true);
    });
    labelCol = ilios.dom.createLabelCol(rowElement, subContainer);

    text = document.createTextNode(i18nStr);
    scratchLabel = document.createElement('label');
    scratchLabel.appendChild(text);
    count = document.createElement('span');
    count.setAttribute('id', ilios.cm.lm.generateIdStringForLearningMaterialCount(containerNumber));
    scratchLabel.appendChild(count);
    labelCol.appendChild(scratchLabel);

    //data column
    subContainer = document.createElement('div');
    subContainer.setAttribute('class', 'scroll_list');
    subContainer.setAttribute('style', 'display: none;');
    subSubContainer = document.createElement('ul');
    subSubContainer.setAttribute('class', 'learning_material_list');
    subSubContainer.setAttribute('id', ilios.cm.lm.generateIdStringForLearningMaterialList(containerNumber));
    subContainer.appendChild(subSubContainer);
    dataCol = ilios.dom.createDataCol(rowElement, subContainer);

    //action column
    i18nStr = ilios_i18nVendor.getI18NString('general.terms.add');
    scratchInput = new Element(document.createElement('a'), {
            href: '',
            id : ilios.cm.lm.generateIdStringForLearningMaterialSearchLink(containerNumber)
        });
    scratchInput.get('element').setAttribute('onclick', 'return false;');
    scratchInput.get('element').setAttribute('style', 'display: none;'); // not displayed by default
    scratchInput.addClass('tiny radius button');
    scratchInput.addListener('click', function (e) {
        IEvent.fire({
            action: 'alm_dialog_open',
            container_number: containerNumber
        });
    }, null, this);
    text = document.createTextNode(i18nStr);
    scratchInput.appendChild(text);
    ilios.cm.uiElementsToHideOnLockedView.push(scratchInput);
    actionCol = ilios.dom.createActionCol(rowElement, scratchInput.get('element'));

    parentElement.appendChild(rowElement);

    //Objectives
    rowElement = ilios.dom.createEntityContainerInputRow();

    //label column
    subContainer = document.createElement('div');
    subContainer.setAttribute('class', 'collapsed_widget');
    subContainer.setAttribute('id', ilios.cm.generateIdStringForObjectivesContainerExpandWidget(containerNumber));
    Event.addListener(subContainer, 'click', function () {
        ilios.cm.setObjectiveDivVisibility(containerNumber, this, true);
    });
    labelCol = ilios.dom.createLabelCol(rowElement, subContainer);

    scratchLabel = document.createElement('label');
    scratchLabel.setAttribute('id', ilios.cm.generateIdStringForObjectivesContainerLabel(containerNumber));
    scratchLabel.innerHTML = ilios_i18nVendor.getI18NString('general.terms.objectives') + ' (0)';
    labelCol.appendChild(scratchLabel);

    //data column
    scratchContainer = document.createElement('div');
    scratchContainer.setAttribute('id',
                                  ilios.cm.generateIdStringForObjectivesContainer(containerNumber));
    scratchContainer.setAttribute('style', 'display: none;');
    dataCol = ilios.dom.createDataCol(rowElement, scratchContainer);

    //action column
    i18nStr = ilios_i18nVendor.getI18NString('general.phrases.add_objective');
    scratchInput = new Element(document.createElement('a'), {href: ''});
    scratchInput.addClass('tiny radius button');
    scratchInput.get('element').setAttribute('onclick', 'return false;');
    scratchInput.addListener('click', function (e) {
        ilios.cm.addNewSessionObjective(containerNumber);
    }, null, this);
    text = document.createTextNode(i18nStr);
    scratchInput.appendChild(text);
    ilios.cm.uiElementsToHideOnLockedView.push(scratchInput);

    ilios.dom.createActionCol(rowElement, scratchInput.get('element'));

    parentElement.appendChild(rowElement);
};

// @private
ilios.cm.session.setSessionContainerHeaderTitle = function (containerNumber, summaryTextDiv) {
    var sessionModel = ilios.cm.currentCourseModel.getSessionForContainer(containerNumber);
    var title = (sessionModel.getTitle() != null) ? sessionModel.getTitle() : '';
    summaryTextDiv.innerHTML = title;
};

ilios.cm.session.userRequestsSessionAddition = function () {
    var containerNumber = 0;
    var sessionModel = null;

    // MAY RETURN THIS BLOCK
    if (ilios.cm.currentCourseModel == null) {
        return;
    }

    containerNumber = ++ilios.cm.session.sessionContainerCount;

    if (containerNumber == 1) {
        sessionModel = new SessionModel();
    }
    else {
        // todo clone last session model like done with program years?
        sessionModel = new SessionModel();
    }

    // KLUDGE!
    // default the session type to be the first one on the list of available session types
    sessionModel.setSessionTypeId(ilios.cm.loadedSessionTypes[0].dbId);

    ilios.cm.session.buildAndPopulateSession(containerNumber, ilios.cm.currentCourseModel,
                                             sessionModel);

    sessionModel.addStateChangeListener(ilios.cm.dirtyStateListener, null);
};

ilios.cm.session.buildAndPopulateSession = function (containerNumber, model, sessionModel, isLocked) {
    var Event = YAHOO.util.Event;
    var Element = YAHOO.util.Element;
    var container = document.getElementById('session_container');
    var newSessionDOMTree = null;
    var formDOMElement = new Element(document.createElement('form'), {method: 'POST', action: ''});
    var objectives = null;
    var objectivesContainerId = ilios.cm.generateIdStringForObjectivesContainer(containerNumber);
    var objectivesContainer = null;
    var element = null;
    var deselectIdString = null;
    var selectIdStringBase = null;
    var elementId = null;
    var isLocked = isLocked || false;

    formDOMElement.get('element').setAttribute('cnumber', containerNumber);

    element = document.createElement('div');
    element.setAttribute('class', 'yellow_warning_icon');
    element.setAttribute('id', ilios.cm.generateIdStringForPublishWarning(containerNumber));
    element.setAttribute('style', 'display: none;');

    newSessionDOMTree = ilios.dom.buildChildContainerDOMTree(containerNumber, null, null,
        ilios.cm.transaction.saveSessionDraft, element, ilios.cm.transaction.publishSession,
        ilios.cm.transaction.deleteSession, ilios.cm.session.setSessionContainerHeaderTitle,
        ilios.cm.session.setSessionContainerHeaderTitle, formDOMElement,
        ilios.cm.session.sessionContentGenerator);

    container.appendChild(newSessionDOMTree.get('element'));

    model.addSessionForContainer(sessionModel, containerNumber);

    element = document.getElementById(
                                ilios.cm.session.generateIdStringForSessionTitle(containerNumber));
    element.value = sessionModel.getTitle();

    elementId = ilios.cm.session.generateIdStringForSessionDescription(containerNumber);
    element = document.getElementById(elementId);
    element.innerHTML = sessionModel.getDescription();
    //rig up the dirty state listener
    sessionModel.addStateChangeListener(ilios.cm.sessionDescriptionDirtyStateListener, {containerId : elementId});

    elementId = ilios.cm.session.generateIdStringForSessionDescriptionEditButton(containerNumber);
    element = document.getElementById(elementId);
    if (! isLocked) {
        // register onclick event handler on edit button to bring up modal dialog
        Event.addListener(element, 'click', function (e) {
            Event.preventDefault(e);
            IEvent.fire({
                action: 'esd_dialog_open',
                model: sessionModel
            });
            return false;
        });
    }
    element = document.getElementById(ilios.cm.session.generateIdStringForSessionOfferingsLink(containerNumber));
    element.innerHTML = ilios.cm.session.generateSessionOfferingsLinkText(sessionModel.getOfferingCount());

    element = document.getElementById(ilios.cm.session.generateIdStringForSessionOfferingEnumerationList(containerNumber));
    element.innerHTML = sessionModel.getOfferingsEnumerationFormattedHTML();

    if (sessionModel.isSupplemental()) {
        deselectIdString = ilios.cm.session.generateIdStringForSessionCoreRadio(containerNumber) + "_label";
        selectIdStringBase = ilios.cm.session.generateIdStringForSessionSupplementalRadio(containerNumber);
    } else {
        deselectIdString = ilios.cm.session.generateIdStringForSessionSupplementalRadio(containerNumber) + "_label";
        selectIdStringBase = ilios.cm.session.generateIdStringForSessionCoreRadio(containerNumber);
    }
    element = new Element(document.getElementById(deselectIdString));
    element.setStyle('font-weight', 'normal');

    element = new Element(document.getElementById(selectIdStringBase + '_label'));
    element.setStyle('font-weight', 'bold');

    element = document.getElementById(selectIdStringBase);
    element.setAttribute('checked', 'true');

    element = document.getElementById(
                         ilios.cm.session.generateIdStringForSessionILMGroupsLink(containerNumber));
    ilios.dom.setElementEnabled(element, false);

    if (sessionModel.isAttireRequired()) {
        element = document.getElementById(
                        ilios.cm.session.generateIdStringForSessionAttireCheckbox(containerNumber));
        element.setAttribute('checked', 'true');
    }

    if (sessionModel.isEquipmentRequired()) {
        element = document.getElementById(
                     ilios.cm.session.generateIdStringForSessionEquipmentCheckbox(containerNumber));
        element.setAttribute('checked', 'true');
    }

    element = document.getElementById(
                          ilios.cm.session.generateIdStringForSessionTypeSelector(containerNumber));
    ilios.utilities.selectOptionWithValue(element, sessionModel.getSessionTypeId());

    element = document.getElementById('' + containerNumber
                                                        + '_discipline_picker_selected_text_list');
    element.innerHTML
            = ilios.utilities.delimitedStringOfTitledObjects(sessionModel.getDisciplines(), ';');

    element = document.getElementById('' + containerNumber
                                                        + '_mesh_terms_picker_selected_text_list');
    element.innerHTML = sessionModel.getMeSHItemsAsFormattedText();

    objectives = sessionModel.getObjectives();
    objectivesContainer = document.getElementById(objectivesContainerId);
    for (var key in objectives) {
        ilios.cm.buildAndPopulateObjective(containerNumber, key, null, objectives[key], objectivesContainer, isLocked);
    }

    ilios.cm.session.setSessionsSummaryText();

    element = document.getElementById('' + containerNumber + '_child_draft_text');
    ilios.cm.updatePublishStateText(sessionModel, element);

    if (sessionModel.getIndependentLearningModel() != null) {
        element = document.getElementById(
                           ilios.cm.session.generateIdStringForSessionILMCheckbox(containerNumber));
        element.checked = true;

        ilios.cm.session.ilm.continueWithILMCreation(null, {"cnumber": containerNumber});
    }

    ilios.cm.session.updatePublishButtonForSession(sessionModel, containerNumber);

    ilios.cm.lm.populateLearningMaterialList(containerNumber);
    // only display the learning materials search link if the corresponding session model has been saved yet
    if (-1 !== sessionModel.getDBId()) { // check db record id
        element = document.getElementById(ilios.cm.lm.generateIdStringForLearningMaterialSearchLink(containerNumber));
        if (element) {
            element.setAttribute('style', 'display:inline');
        }
    }
    ilios.cm.updateObjectiveCountText(containerNumber);
};

ilios.cm.session.updatePublishButtonForSession = function (sessionModel, containerNumber) {
    var coursePublishability = ilios.cm.currentCourseModel.getPublishability();
    var publishability = sessionModel.getPublishability();
    var element = document.getElementById('' + containerNumber + '_child_publish');
    var needsPublishing = ilios.utilities.modelItemNeedsPublishing(sessionModel);
    var enable = ((publishability != sessionModel.CANNOT_BE_PUBLISHED)
                                        && (coursePublishability != ilios.cm.currentCourseModel.CANNOT_BE_PUBLISHED)
                                        && (sessionModel.isPublishedAsTBD() || needsPublishing));
    var idStr = ilios.cm.generateIdStringForPublishWarning(containerNumber);

    ilios.dom.setElementEnabled(element, enable);

    //element = new YAHOO.util.Element(document.getElementById(idStr));
    // if (it doesn't need publishing, or it's publishability bla bla) AND it's not published as tbd
    if ((! sessionModel.isPublishedAsTBD())
            && ((! needsPublishing)
                       || (publishability != sessionModel.MEETS_MINIMAL_PUBLISHING_REQUIREMENTS))) {
        //element.setStyle('display', 'none');
        YAHOO.util.Dom.removeClass(element, 'icon-warning');
    }
    else {
        //element.setStyle('display', 'inline-block');
        YAHOO.util.Dom.addClass(element, 'icon-warning');
    }
};

/**
 * This should be called after the associated session model has been removed from its course model.
 */
ilios.cm.session.removeSessionUIWithContainerNumber = function (containerNumber) {
    var idString = ilios.dom.childCollapsingContainerIdForContainerNumber(containerNumber);
    var element = document.getElementById(idString).parentNode;

    element.parentNode.removeChild(element);

    ilios.cm.session.setSessionsSummaryText();
};

ilios.cm.session.setSessionsSummaryText = function () {
    var sessionCount = ilios.utilities.arraySize(ilios.cm.currentCourseModel.getSessions());
    var element = document.getElementById('sessions_summary');
    var i18nString
            = ilios_i18nVendor.getI18NString('course_management.notification.sessions_exist');
    var countString = (sessionCount == 0) ? ilios_i18nVendor.getI18NString('general.terms.no')
                                            : ('' + sessionCount);

    element.innerHTML = ' ' + countString + ' ' + i18nString;
};

// @private
ilios.cm.session.editOfferingsForContainer = function (containerNumber) {
    var sessionModel = ilios.cm.currentCourseModel.getSessionForContainer(containerNumber);
    var sessionId = sessionModel.getDBId();

    if (ilios.cm.currentCourseModel.isModelDirty() || (sessionId == -1)) {
        var i18nString = ilios_i18nVendor.getI18NString('course_management.error.offering_launch');

        ilios.alert.alert(i18nString);
    } else {
        var url = offeringControllerURL + "?session_id=" + sessionId;

        // Search previous sessions for start date if current session does not have any offering.
        if (0 >= sessionModel.offeringCount) {
            var sessions = ilios.cm.currentCourseModel.getSessions();
            var startDate = ilios.cm.currentCourseModel.getStartDateObjectForNewSession();

            url = url + "&start_date=" + ilios.utilities.dateObjectToMySQLFriendly(startDate, false);
        }

        window.location = url;
    }
};

// @private
ilios.cm.session.sessionTypeSelected = function (containerNumber) {
    var sessionModel = ilios.cm.currentCourseModel.getSessionForContainer(containerNumber);
    var idString = ilios.cm.session.generateIdStringForSessionTypeSelector(containerNumber);
    var element = document.getElementById(idString);

    sessionModel.setSessionTypeId(element.options[element.selectedIndex].value);
};

// @private
ilios.cm.session.supplementalCoreRadioChanged = function (containerNumber, inputElement,
                                                          supplementalChosen) {
    var sessionModel = ilios.cm.currentCourseModel.getSessionForContainer(containerNumber);

    ilios.ui.radioButtonSelected(inputElement);

    sessionModel.setSupplemental(supplementalChosen);
};

// @private
ilios.cm.session.attireCheckboxChanged = function (containerNumber) {
    var sessionModel = ilios.cm.currentCourseModel.getSessionForContainer(containerNumber);
    var element = document.getElementById(
                        ilios.cm.session.generateIdStringForSessionAttireCheckbox(containerNumber));

    sessionModel.setAttireRequired(element.checked);
};

// @private
ilios.cm.session.equipmentCheckboxChanged = function (containerNumber) {
    var sessionModel = ilios.cm.currentCourseModel.getSessionForContainer(containerNumber);
    var element = document.getElementById(
                     ilios.cm.session.generateIdStringForSessionEquipmentCheckbox(containerNumber));

    sessionModel.setEquipmentRequired(element.checked);
};

ilios.cm.session.alterSessionUIToReflectLockedState = function (session) {
    var containerNumber = ilios.cm.currentCourseModel.getContainerForSession(session);
    var idStr = "" + containerNumber + "_child_draft_text";
    var element = document.getElementById(idStr);
    var parent = new YAHOO.util.Element(element.parentNode);

    element.innerHTML = '<span class="status is-locked">' + ilios_i18nVendor.getI18NString('general.terms.locked') + '</span>';

    parent.setStyle('background-color', '#C5C5C5');
    parent.get('element').removeChild((parent.getElementsByClassName('delete_widget'))[0]);

    idStr = ilios.cm.session.generateIdStringForSessionTitle(containerNumber);
    element = document.getElementById(idStr);
    element.disabled = 'disabled';

    idStr = ilios.cm.session.generateIdStringForSessionAttireCheckbox(containerNumber);
    element = document.getElementById(idStr);
    element.disabled = 'disabled';

    idStr = ilios.cm.session.generateIdStringForSessionCoreRadio(containerNumber);
    element = document.getElementById(idStr);
    element.disabled = 'disabled';

    idStr = ilios.cm.session.generateIdStringForSessionDescriptionEditButton(containerNumber);
    element = document.getElementById(idStr);
    element.disabled = 'disabled';

    idStr = ilios.cm.session.generateIdStringForSessionEquipmentCheckbox(containerNumber);
    element = document.getElementById(idStr);
    element.disabled = 'disabled';

    idStr = ilios.cm.session.generateIdStringForSessionILMCheckbox(containerNumber);
    element = document.getElementById(idStr);
    element.disabled = 'disabled';

    idStr = ilios.cm.session.generateIdStringForSessionILMHours(containerNumber);
    element = document.getElementById(idStr);
    element.disabled = 'disabled';

    idStr = ilios.cm.session.generateIdStringForSessionOfferingsLink(containerNumber);
    element = document.getElementById(idStr);
    ilios.dom.setElementEnabled(element, false);

    idStr = ilios.cm.session.generateIdStringForSessionSupplementalRadio(containerNumber);
    element = document.getElementById(idStr);
    element.disabled = 'disabled';

    idStr = ilios.cm.session.generateIdStringForSessionTypeSelector(containerNumber);
    element = document.getElementById(idStr);
    element.disabled = 'disabled';
};

ilios.cm.session.reorderSessionDivs = function () {
    var element = document.getElementById('session_container');
    var children = element.childNodes;
    var sortingArray = new Array();
    var divCount = children.length;
    var i = 0;

    for (i = 0; i < divCount; i++) {
        sortingArray.push(children[i]);
    }
    sortingArray.sort(ilios.cm.session.sessionDivComparator);

    ilios.utilities.removeAllChildren(element);
    for (i = 0; i < divCount; i++) {
        element.appendChild(sortingArray[i]);
    }
};

// @private
ilios.cm.session.sessionDivComparator = function (div1, div2) {
    var selector = document.getElementById('session_ordering_selector');
    var selectedIndex = selector.selectedIndex;
    var cn1 = div1.getAttribute('cnumber');
    var cn2 = div2.getAttribute('cnumber');

/*
    if (selectedIndex == 0) {
        return parseInt(cn1) - parseInt(cn2);
    } else {
*/
        var sm1 = ilios.cm.currentCourseModel.getSessionForContainer(cn1);
        var sm2 = ilios.cm.currentCourseModel.getSessionForContainer(cn2);
        var fe1 = null;
        var fe2 = null;

        switch (selectedIndex) {
            case 0:
                return sm1.getTitle().localeCompare(sm2.getTitle());
            case 1:
                return sm2.getTitle().localeCompare(sm1.getTitle());
            case 2:
                fe1 = sm1.getFirstEventStart();
                fe2 = sm2.getFirstEventStart();
                return fe1 - fe2;
            case 3:
                fe1 = sm1.getFirstEventStart();
                fe2 = sm2.getFirstEventStart();
                return fe2 - fe1;
        }
/*
    }
    return 0;
*/
};
