/**
 * DEPENDENCY: YUI lib (at least YAHOO.util and its dependencies)
 * DEPENDENCY: scripts/ilios_dom.js
 * DEPENDENCY: scripts/ilios_ui.js
 * DEPENDENCY: scripts/ilios_utilities.js
 * DEPENDENCY: program_manager_transaction.js
 * DEPENDENCY: program_model.js
 * DEPENDENCY: program_year_model.js
 */

ilios.pm.loadedProgramModel = null;
ilios.pm.currentProgramModel = null;

/*
 * This is a non-associative array of StewardModel instances which represents a non-hierarchial
 *  version of the school-department tree.
 */
ilios.pm.schoolDepartmentTreeModel = null;

ilios.pm.placeHolderNodeTitle = "Place holder";

/*
 * @return true if the program model is dirty (including any of its program year models)
 */
// @private
ilios.pm.currentModelIsDirty = function () {
    var programYearArray = null;

    if (ilios.pm.currentProgramModel.isModelDirty()) {
        return true;
    }

    programYearArray = ilios.pm.currentProgramModel.getProgramYearArray();
    for (var key in programYearArray) {
        if (programYearArray[key].isModelDirty()) {
            return true;
        }
    }

    return false;
};

ilios.pm.windowWillClose = function (e) {
    if ((ilios.pm.currentProgramModel != null) && ilios.pm.currentModelIsDirty()) {
        var notification = ilios_i18nVendor.getI18NString('general.warning.dirty_state_lose_changes');

        e.returnValue = notification; // most browsers

        return notification; // safari
    }
};

ilios.pm.displayAddNewProgramDialog = function () {
    if ((ilios.pm.currentProgramModel != null) && ilios.pm.currentModelIsDirty()) {
        var notification = ilios_i18nVendor.getI18NString('general.warning.dirty_state_lose_changes');
        var continueStr = ilios_i18nVendor.getI18NString('general.phrases.want_to_continue');
        var yesStr = ilios_i18nVendor.getI18NString('general.terms.yes');

        ilios.alert.inform(('<p style="margin-bottom:9px; text-align:justify;">' + notification
                + '</p><center><b>' + continueStr + '</b></center>'), yesStr,
               ilios.pm.continueDisplayAddNewProgramDialog);

        return;
    }

    ilios.ui.onIliosEvent.fire({ action: 'default_dialog_open', event: 'add_new_program' });
};

ilios.pm.continueDisplayAddNewProgramDialog = function () {
    this.cancel();      // (alert panel dismiss)

    ilios.ui.onIliosEvent.fire({ action: 'default_dialog_open', event: 'add_new_program' });
};

/*
 * This is called on an accept from a 'dirty are you sure' intervention done during a request
 *  for change reversion, if the user chooses 'yes, continue revert'
 */
// @private
ilios.pm.continueRevert = function () {
    this.hide();

    ilios.pm.populateProgramAndSetEnable(ilios.pm.loadedProgramModel.getTitle(),
                                         ilios.pm.loadedProgramModel.getShortTitle(),
                                         ilios.pm.loadedProgramModel.getDuration(),
                                         ilios.pm.loadedProgramModel.getDBId(),
                                         ilios.pm.loadedProgramModel.getPublishEventId(),
                                         true, true, false);
};

/*
 * Messaged from the revert UI button.
 */
ilios.pm.revertChanges = function () {
    if (ilios.pm.currentModelIsDirty()) {
        var dirtyStr = ilios_i18nVendor.getI18NString('general.warning.dirty_state_lose_changes');
        var continueStr = ilios_i18nVendor.getI18NString('general.phrases.want_to_continue');
        var yesStr = ilios_i18nVendor.getI18NString('general.terms.yes');

        ilios.alert.inform(('<p style="margin-bottom:9px; text-align:justify;">' + dirtyStr + '</p><center><b>'
                    + continueStr + '</b></center>'), yesStr, ilios.pm.continueRevert);

        return;
    }
};

/*
 * Call via onDOMReady
 */
ilios.pm.registerProgramUIListeners = function () {
    var Event = YAHOO.util.Event;
    var element = document.getElementById('program_title');

    ilios.pm.transaction.loadSchoolDepartmentTree();

    Event.addListener(element, 'keyup', function () {
        if (! document.getElementById('short_title').hasAttribute('disabled')) {
            var newTitle = document.getElementById('program_title').value;
            ilios.pm.currentProgramModel.setTitle(newTitle);
        }
    });

    element = document.getElementById('short_title');
    Event.addListener(element, 'keyup', function () {
        var newTitle = document.getElementById('short_title').value;
        ilios.pm.currentProgramModel.setShortTitle(newTitle);
    });

    element = document.getElementById('duration_selector');
    Event.addListener(element, 'change', function () {
        // avoiding using a local variable due to garbage collection
        // on closure -- makes for ugly column obeyance though
        ilios.pm.currentProgramModel.setDuration(
            document.getElementById('duration_selector').options[document.getElementById('duration_selector').selectedIndex].value);
    });
};

ilios.pm.removeProgramYearAssociatedToContainer = function (containerNumber) {
    var collapserId = ilios.dom.childCollapsingContainerIdForContainerNumber(containerNumber);
    var firstChildElement = document.getElementById(collapserId);

    if (firstChildElement != null) {
        var container = firstChildElement.parentNode;
        var model = ilios.pm.currentProgramModel.getProgramYearForContainerNumber(containerNumber);

        container.parentNode.removeChild(container);
        ilios.pm.currentProgramModel.removeProgramYear(model);
    }
};

ilios.pm.dirtyStateListener = {

    modelChanged: function (model, mockedThis) {
        var Element = YAHOO.util.Element;
        var enabled = model.isModelDirty();
        var element = null;

        if (model == ilios.pm.currentProgramModel) {
            element = document.getElementById('draft_button');
            ilios.dom.setElementEnabled(element, enabled);

            element = document.getElementById('publish_button');
            ilios.dom.setElementEnabled(element,
                                        ilios.utilities.canPublishModelItem(ilios.pm.currentProgramModel));

            element = document.getElementById('reset_button');
            ilios.dom.setElementEnabled(element, enabled);

            element = YAHOO.util.Dom.getElementsByClassName('level-1', 'div', document.getElementById('program_form'))[0];
            if (enabled) {
                (new Element(element)).addClass('dirty_state');
            }
            else {
                (new Element(element)).removeClass('dirty_state');
            }
        } else {
            var containerNumber = ilios.pm.currentProgramModel.getContainerNumberForProgramYear(model);

            // should always be true
            if (containerNumber > 0) {
                var idStr = ilios.dom.childCollapsingContainerIdForContainerNumber(containerNumber);
                var directorString = null;
                var collapseTrio = null;

                element = document.getElementById(idStr).parentNode;
                if (enabled) {
                    (new Element(element)).addClass('dirty_state');
                }
                else {
                    (new Element(element)).removeClass('dirty_state');
                }

                element = document.getElementById('' + containerNumber + '_child_save');
                ilios.dom.setElementEnabled(element, enabled);

                element = document.getElementById('' + containerNumber + '_child_publish');
                enabled = ilios.utilities.canPublishModelItem(model);
                ilios.dom.setElementEnabled(element, enabled);

                directorString = model.getDirectorsAsFormattedText();
                idStr = ilios.pm.generateIdStringForDirectorContent(containerNumber);
                element = document.getElementById(idStr);
                collapseTrio = ilios.pm.getCollapseTrioForFirstChildLevelDiv(element);
                collapseTrio[0].innerHTML = ilios.lang.ellipsisedOfLength(directorString, 75);
                collapseTrio[1].innerHTML = directorString;

                if (ilios.global.preferencesModel.showProgramYearArchiving()
                            && model.isPublished()) {
                    idStr = ilios.pm.generateIdStringForArchivingDiv(containerNumber);
                    element = new Element(document.getElementById(idStr));
                    element.setStyle('display', (model.isModelDirty() ? 'none' : 'block'));
                }
            }
        }
    }

};

/**
 * Dirty-state change-listener for objective models.
 * @property objectiveDirtyStateListener
 * @type {Object}
 */
ilios.pm.objectiveDirtyStateListener = {

    /**
     * Updates the objective model's display if the model has changed.
     * @method modelChanged
     * @param {ObjectiveModel} model the objective model
     * @param {Object} obj state-change-listener arguments. Parameters assumed:
     *     "containerId": DOM element Id for the objective description container corresponding to the given objective model.
     */
    modelChanged: function (model, obj) {
        var container = null;
        var o = YAHOO.lang.isObject(obj) || {};
        if (obj.containerId) {
            container = document.getElementById(obj.containerId);
            if (container) {
                container.innerHTML = model.getTitle();
            }
        }
    }
};

/**
 * In addition to setting up the UI, this resets the loaded and current model instances.
 *
 * todo this seems like a plausible tie in point for a dirty check / user alert to state dumping
 * todo need to make sure to save pre-existing program year associations in certain situations
 *
 * @param title if null, a blank string is inserted
 * @param shortTitle if null, a blank string is inserted
 * @param duration if null, the first option in the select pulldown is selected
 * @param programId if null, 0 is used
 * @param publishEventId if null, or -1, then the program is assumed to be in draft mode
 * @param enabled if true, all UI widgets are enabled; if false, only the title field is enabled
 * @param shouldFetchProgramYears if true, program years associated to the programId are fetched
 *                  from the server
 * @param shouldSaveProgramYearState if true, the program year state of the ilios.pm.currentProgramModel
 *                  will be saved and re-set into the program models. if both this and
 *                  shouldFetchProgramYears is true, unpredictable behaviour will arise.
 */
ilios.pm.populateProgramAndSetEnable = function (title, shortTitle, duration, programId,
                                                 publishEventId, enabled, shouldFetchProgramYears,
                                                 shouldSaveProgramYearState) {
    var element;
    var previousProgramYearState = null;
    var previousNextContainerNumber = -1;

    if (shouldSaveProgramYearState) {
        previousProgramYearState = ilios.pm.currentProgramModel.getProgramYearArray();
        previousNextContainerNumber = ilios.pm.currentProgramModel.getNextContainerNumber();
    }

    if (ilios.pm.currentProgramModel != null) {
        ilios.pm.currentProgramModel.clearDirtyState();
    }

    ilios.pm.loadedProgramModel = new ProgramModel();
    ilios.pm.currentProgramModel = new ProgramModel();

    ilios.pm.loadedProgramModel.setDBId((programId != null) ? programId : 0);
    ilios.pm.currentProgramModel.setDBId((programId != null) ? programId : 0);

    // main container form
    element = document.getElementById('program_title');
    element.value = ((title != null) ? title : "");
    element.removeAttribute('disabled');
    ilios.pm.loadedProgramModel.setTitle(title);
    ilios.pm.currentProgramModel.setTitle(title);

    element = document.getElementById('short_title');
    element.value = ((shortTitle != null) ? shortTitle : "");
    ilios.dom.setElementEnabled(element, enabled);
    ilios.pm.loadedProgramModel.setShortTitle(shortTitle);
    ilios.pm.currentProgramModel.setShortTitle(shortTitle);

    element = document.getElementById('duration_selector');
    ilios.utilities.selectOptionWithValue(element, duration);
    ilios.dom.setElementEnabled(element, enabled);
    ilios.pm.loadedProgramModel.setDuration((duration != null) ? duration : 0);
    ilios.pm.currentProgramModel.setDuration((duration != null) ? duration : 0);


    element = document.getElementById('draft_button');
    element.setAttribute('disabled', 'true');

    element = document.getElementById('publish_button');
    ilios.dom.setElementEnabled(element, ((publishEventId == null) || (publishEventId == -1)));

    element = document.getElementById('reset_button');
    element.setAttribute('disabled', 'true');

    element = document.getElementById('parent_publish_status_text');
    if ((publishEventId != null) && (publishEventId != -1)){
        element.innerHTML = '<span class="status is-published">' + ilios_i18nVendor.getI18NString('general.terms.published') + '</span>';

        ilios.pm.loadedProgramModel.setPublishEventId(publishEventId);
        ilios.pm.currentProgramModel.setPublishEventId(publishEventId);
    }
    else {
        element.innerHTML = '<span class="status is-draft">' + ilios_i18nVendor.getI18NString('general.terms.draft') + '</span>';

        ilios.pm.loadedProgramModel.setPublishEventId(-1);
        ilios.pm.currentProgramModel.setPublishEventId(-1);
    }

    element = document.getElementById('expand_program_years_link');
    element.setAttribute('style', 'display: inline;');

    element = document.getElementById('add_new_program_year_link');
    ilios.dom.setElementEnabled(element, enabled);

    if (previousProgramYearState != null) {
        ilios.pm.loadedProgramModel.setProgramYearArray(previousProgramYearState);
        ilios.pm.currentProgramModel.setProgramYearArray(previousProgramYearState);

        ilios.pm.loadedProgramModel.setNextContainerNumber(previousNextContainerNumber);
        ilios.pm.currentProgramModel.setNextContainerNumber(previousNextContainerNumber);
    }

    if (shouldFetchProgramYears && (programId != null)) {
        ilios.pm.transaction.loadProgramYearsForProgramId(programId);
    }
    else {
        ilios.pm.loadedProgramModel.clearDirtyState();
        ilios.pm.currentProgramModel.clearDirtyState();

        ilios.pm.currentProgramModel.addStateChangeListener(ilios.pm.dirtyStateListener, null);
    }

    YAHOO.util.Dom.setStyle("edit_program_inputfields", "display", "block");

    ilios.pm.cs.programSearchDialog.cancel();
};

ilios.pm.collapseOrExpandProgramYears = function (collapseRegardless) {
    var element = new YAHOO.util.Element(document.getElementById('expand_program_years_link'));
    var programYears = ilios.pm.currentProgramModel.getProgramYearArray();
    var containerNumber = 0;
    var collapseValue = 1;
    var collapseValueToUse = -1;

    if (element.hasClass('program_years_collapsed') && (! collapseRegardless)) {
        element.removeClass('program_years_collapsed');
        element.get('element').innerHTML
                     = ilios_i18nVendor.getI18NString('general.phrases.collapse_all');

        collapseValue = 2;      // expand no matter what
    }
    else {
        element.addClass('program_years_collapsed');
        element.get('element').innerHTML
                        = ilios_i18nVendor.getI18NString('general.phrases.expand_all');
    }

    for (var key in programYears) {
        collapseValueToUse = collapseValue;

        containerNumber
                = ilios.pm.currentProgramModel.getContainerNumberForProgramYear(programYears[key]);

        ilios.dom.collapseChildForContainerNumber(containerNumber, collapseValueToUse,
            ilios.pm.handleProgramYearDivCollapse);
    }
};

/**
 * Finalizes matriculation summary text in collapsible title-bar div of respective program year container
 * @method ilios.pm.handleProgramYearDivCollapse
 * @param {Number} containerNumber
 * @param {Object} summaryTextDiv
 */

ilios.pm.handleProgramYearDivCollapse = function (containerNumber, summaryTextDiv) {
    var programYearStr = ilios_i18nVendor.getI18NString('general.phrases.matriculation_year');
    var graduatingClassOfStr = ilios_i18nVendor.getI18NString('general.phrases.graduating_class_of');
    var model = ilios.pm.currentProgramModel.getProgramYearForContainerNumber(containerNumber);
    var duration = ilios.pm.currentProgramModel.duration;
    var startYear = model.getStartYear();
    var graduatingClassOfString = ilios.pm.getGraduatingClassOfString(startYear, duration);
    var yearText = startYear + "-" + (startYear + 1);
    summaryTextDiv.innerHTML = programYearStr + ': ' + yearText + " ("+graduatingClassOfString+")";
};

ilios.pm.getCollapseTrioForFirstChildLevelDiv = function (firstChildLevelDiv) {
    var rhett = [];
    var parent = firstChildLevelDiv.parentNode.parentNode;
    var children = parent.getElementsByClassName('py_summary_shpeel');

    rhett.push(children[0]);

    children = parent.getElementsByClassName('py_total_text_div');
    rhett.push(children[0]);

    children = parent.getElementsByClassName('collapsed_widget');
    if (children.length == 0) {
        children = parent.getElementsByClassName('expanded_widget');
    }
    rhett.push(children[0]);

    return rhett;
};

/*
 * @param shouldToggle if true, the current visibility will be toggled; if false, the div will be
 *                          made visible
 *
 * @private
 */
ilios.pm.setCollapsedDivVisibility = function (widgetDiv, shouldToggle) {
    var Element = YAHOO.util.Element;
    var collapseTrio = ilios.pm.getCollapseTrioForFirstChildLevelDiv(widgetDiv);
    var summaryDiv = new Element(collapseTrio[0]);
    var verboseDiv = new Element(collapseTrio[1]);
    var collapseWidgetDiv = new Element(collapseTrio[2]);

    if ((verboseDiv.getStyle('display') != 'none') && shouldToggle) {
        collapseWidgetDiv.removeClass('expanded_widget');
        collapseWidgetDiv.addClass('collapsed_widget');
        summaryDiv.setStyle('display', ((summaryDiv.tagName == 'DIV') ? 'block' : 'inline'));
        verboseDiv.setStyle('display', 'none');
    } else {
        collapseWidgetDiv.removeClass('collapsed_widget');
        collapseWidgetDiv.addClass('expanded_widget');
        summaryDiv.setStyle('display', 'none');
        verboseDiv.setStyle('display', 'block');
    }
};

/*
 * @param parentElement
 * @param contentId expected to arrive already uniqued across the DOM
 * @param labelText
 * @param searchEventName
 * @param textFieldSize
 * @param containerNumber
 * @param dialogAction if defined, this will be used as the event action fired to trigger the dialog
 *                          open; this defaults to 'default_dialog_open'
 *
 * @private
 */
ilios.pm.appendProgramYearComponentToDOM = function (parentElement, contentId, labelText,
                                                     searchEventName, textFieldSize,
                                                     containerNumber, dialogAction) {
    var Event = YAHOO.util.Event;
    var Element = YAHOO.util.Element;
    //var containingDiv = new Element(document.createElement('div'));
    var scratchInput = null;
    var scratchElement = null;
    var textNode = null;
    //var i18nStr = ilios_i18nVendor.getI18NString('general.terms.search');
    var i18nStr = ilios_i18nVendor.getI18NString('general.terms.edit');
    var rowEl, labelCol, dataCol, actionCol;

    if (typeof dialogAction == 'undefined') {
        dialogAction = 'default_dialog_open';
    }

    rowEl = ilios.dom.createEntityContainerInputRow();

    //label column
    scratchElement = document.createElement('div');
    scratchElement.setAttribute('class', 'collapsed_widget');
    Event.addListener(scratchElement, 'click', function () {
        ilios.pm.setCollapsedDivVisibility(this, true);
    });
    labelCol = ilios.dom.createLabelCol(rowEl, scratchElement);

    textNode = document.createElement('label');
    textNode.innerHTML = labelText;
    labelCol.appendChild(textNode);

    //data column
    scratchInput = document.createElement('div');
    scratchInput.setAttribute('id', contentId);
    scratchInput.setAttribute('class', 'read_only_data py_summary_shpeel');
    Event.addListener(scratchInput, 'click', function () {
        ilios.pm.setCollapsedDivVisibility(this, true);
    });
    dataCol = ilios.dom.createDataCol(rowEl, scratchInput);

    scratchInput = document.createElement('div');
    scratchInput.setAttribute('id', (contentId + '_full'));
    scratchInput.setAttribute('class', 'read_only_data py_total_text_div');
    scratchInput.setAttribute('style', 'display:none');
    dataCol.appendChild(scratchInput);

    //action column
    if (searchEventName != null) {
        var parentDOMElement = parentElement.get('element');

        scratchElement = new Element(document.createElement('a'), {href: ''});
        scratchElement.addClass('tiny button radius');
        scratchElement.get('element').setAttribute('onclick', 'return false;');
        scratchElement.addListener('click', function (e) {
            IEvent.fire({
                action: dialogAction,
                event: searchEventName,
                container_number: containerNumber
            });
        }, null, this);
        textNode = document.createTextNode(i18nStr);
        scratchElement.appendChild(textNode);
        actionCol = ilios.dom.createActionCol(rowEl, scratchElement.get('element'));


        if (! parentDOMElement.hideOnLockDivs) {
            parentDOMElement.hideOnLockDivs = [];
        }
        parentDOMElement.hideOnLockDivs.push(scratchElement);
    }

    parentElement.appendChild(rowEl);
};

// @private
ilios.pm.appendObjectivesBlockToDOM = function (parentElement, containerNumber) {
    var Element = YAHOO.util.Element;
    var contentId = ilios.pm.generateIdStringForObjectiveContent(containerNumber);
    var labelText = ilios_i18nVendor.getI18NString('general.phrases.learning_objectives') + ' ';
    var scratchInput = null;
    var scratchElement = null;
    var textNode = null;
    var parentDOMElement = parentElement.get('element');
    var rowEl, labelCol, dataCol, actionCol;

    rowEl = ilios.dom.createEntityContainerInputRow();

    //label column
    scratchElement = document.createElement('div');
    scratchElement.setAttribute('class', 'collapsed_widget');
    scratchElement.setAttribute('id', ilios.pm.generateIdStringForObjectiveCollapseWidget(containerNumber));
    YAHOO.util.Event.addListener(scratchElement, 'click', function () {
        ilios.pm.setCollapsedDivVisibility(this, true);
    });
    labelCol = ilios.dom.createLabelCol(rowEl, scratchElement);

    textNode = document.createElement('label');
    textNode.innerHTML = labelText;
    scratchElement = document.createElement('span');
    scratchElement.setAttribute('id', ilios.pm.generateIdStringForObjectiveCountSpan(containerNumber));
    scratchElement.innerHTML = '(0)';
    textNode.appendChild(scratchElement);
    labelCol.appendChild(textNode);


    //data column

    //this doesn't seem to be used for objectives?...
    scratchElement = document.createElement('div');
    scratchElement.setAttribute('class', 'py_summary_shpeel');
    dataCol = ilios.dom.createDataCol(rowEl, scratchElement);

    scratchInput = document.createElement('div');
    scratchInput.setAttribute('id', contentId);
    scratchInput.setAttribute('class', 'py_total_text_div');
    scratchInput.setAttribute('style', 'display:none');
    dataCol.appendChild(scratchInput);


    //action column
    scratchInput = new Element(document.createElement('a'), {href: ''});
    scratchInput.addClass('tiny radius button');
    scratchInput.get('element').setAttribute('onclick', 'return false;');
    scratchInput.addListener('click', function (e) {
        ilios.pm.addNewObjective(containerNumber);
    }, null, this);
    textNode = document.createTextNode(ilios_i18nVendor.getI18NString('general.phrases.add_objective'));
    scratchInput.appendChild(textNode);

    if (! parentDOMElement.hideOnLockDivs) {
        parentDOMElement.hideOnLockDivs = [];
    }
    parentDOMElement.hideOnLockDivs.push(new Element(scratchInput));
    actionCol = ilios.dom.createActionCol(rowEl, scratchInput.get('element'));

    parentElement.appendChild(rowEl);
};

ilios.pm.generateIdStringForProgramYearSelect = function (containerNumber) {
    return '' + containerNumber + '_' + 'program_year_title';
};

ilios.pm.generateIdStringForArchivingDiv = function (containerNumber) {
    return '' + containerNumber + '_' + 'archiving_link_div';
};

ilios.pm.generateIdStringForCompetencyContent = function (containerNumber) {
    return '' + containerNumber + '_' + 'competency_picker_selected_text_list';
};

ilios.pm.generateIdStringForDisciplineContent = function (containerNumber) {
    return '' + containerNumber + '_' + 'discipline_picker_selected_text_list';
};

ilios.pm.generateIdStringForDirectorContent = function (containerNumber) {
    return '' + containerNumber + '_' + 'director_picker_selected_text_list';
};

ilios.pm.generateIdStringForObjectiveCollapseWidget = function (containerNumber) {
    return '' + containerNumber + '_' + 'objective_collapser';
};

ilios.pm.generateIdStringForObjectiveContent = function (containerNumber) {
    return '' + containerNumber + '_' + 'objective_listing';
};

ilios.pm.generateIdStringForObjectiveCountSpan = function (containerNumber) {
    return '' + containerNumber + '_' + 'objective_count_span';
};

ilios.pm.generateIdStringForObjectiveTextArea = function (containerNumber, objectiveNumber) {
    return '' + containerNumber + '_' + objectiveNumber + '_objectives_container';
};

ilios.pm.generateIdStringForObjectiveMeSHLink = function (containerNumber, objectiveNumber) {
    return '' + containerNumber + '_' + objectiveNumber + '_objectives_mesh_link';
};

ilios.pm.generateIdStringForStewardContent = function (containerNumber) {
    return '' + containerNumber + '_' + 'steward_picker_selected_text_list';
};

/*
 * @return ilios.pm.currentProgramModel.getDuration() + FUDGE_FACTOR where FUDGE_FACTOR currently is 2
 *
 * @private
 */
ilios.pm.getMaximumProgramYearCount = function () {
    var programDuration = ilios.pm.currentProgramModel.getDuration();

    return parseInt(programDuration, 10) + 2;
};

// @private
ilios.pm.handleProgramYearStartYearSelect = function (containerNumber) {
    var titleId = ilios.pm.generateIdStringForProgramYearSelect(containerNumber);
    var selectElement = document.getElementById(titleId);

    if (selectElement != null) {
        var option = selectElement.options[selectElement.selectedIndex];
        var startYear = parseInt(option.value, 10);
        var model = ilios.pm.currentProgramModel.getProgramYearForContainerNumber(containerNumber);

        model.setStartYear(startYear);
    }
};

/**
 * setGraduatingClassOfText
 * When an academic year is selected/changed in a program year container, calculate and set the 'Graduating Class of XXXX' string
 *  and add it to the appropriate places: next to the year selector AND in the collapsible title div.
 */

ilios.pm.setGraduatingClassOfText = function (containerNumber, academicStartYear) {
	var matriculationYearSummary = document.getElementById(containerNumber+'_matriculation_year_summary_text');
	var collapseSummaryTextSelector = document.getElementById(containerNumber+'_collapse_summary_text');
	var duration = ilios.pm.currentProgramModel.getDuration();
    var currentYearSelector = document.getElementById(containerNumber+'_program_year_title');
    academicStartYear = academicStartYear || currentYearSelector.options[currentYearSelector.selectedIndex].value;

    //calculate the graduation year based on the duration and get the i18N string
    var graduatingClassOfString = ilios.pm.getGraduatingClassOfString(academicStartYear, duration);
    var yearText = academicStartYear + "-" + (parseInt(academicStartYear, 10) + 1);
    //wrap the text in parentheses..
    graduatingClassString = "("+graduatingClassOfString+")";
    //update the collapse div
    collapseSummaryTextSelector.innerHTML = "Matriculation year: "+yearText+" "+graduatingClassString;
    //and update the text next to the selector...
    matriculationYearSummary.innerHTML = "&nbsp;"+graduatingClassString;
};

ilios.pm.programYearContentGenerator = function (parentElement, containerNumber) {
    var Element = YAHOO.util.Element;
    var scratchLabel = null;
    var scratchInput = null;
    var scratchOption = null;
    var text = null;
    var startYear = 0;
    var textNode = null;
    var currentYear = (new Date()).getFullYear();
    var i = 0;
    var len = ilios.pm.getMaximumProgramYearCount();
    var titleId = ilios.pm.generateIdStringForProgramYearSelect(containerNumber);
    var i18nStr = ilios_i18nVendor.getI18NString('general.phrases.academic_year');
    var elem, rowEl, labelCol, dataCol, actionCol;
    var previousProgramYearModel = ilios.pm.currentProgramModel;

    // Matriculation Year
    rowEl = ilios.dom.createEntityContainerInputRow();

    //label column
    scratchLabel = new Element(document.createElement('label'), {
        'for': titleId
    });
    textNode = document.createTextNode(i18nStr + " ");
    scratchLabel.appendChild(textNode);
    labelCol = ilios.dom.createLabelCol(rowEl, scratchLabel.get('element'));

    //data column
    scratchInput = new Element(document.createElement('select'), {
        id: titleId
    });
    scratchInput.addListener('change', function (e) {
        ilios.pm.handleProgramYearStartYearSelect(containerNumber);
        ilios.pm.setGraduatingClassOfText(containerNumber);
    });
    i = -len;   // we want to offer the same number of years forwards and backwards in time
    for (; i <= len; i++) {
        startYear = currentYear + i;
        text = "" + startYear + "-" + (startYear + 1);

        scratchOption = document.createElement('option');
        scratchOption.setAttribute('value', startYear);
        if (i == 0) {
            scratchOption.setAttribute('selected', 'selected');
        }

        textNode = document.createTextNode(text);
        scratchOption.appendChild(textNode);

        scratchInput.appendChild(scratchOption);
    }

    dataCol = ilios.dom.createDataCol(rowEl, scratchInput.get('element'));
    parentElement.appendChild(rowEl);

    //Initialize the 'matriculation_year_summary_text' div...
    scratchElement = document.createElement('div');
    scratchElement.setAttribute('id', (containerNumber+'_matriculation_year_summary_text'));
    scratchElement.setAttribute('class', 'matriculation_year_summary_text');
    scratchElement.setAttribute('style', 'display:inline-block');
    dataCol.appendChild(scratchElement);

    // Competencies
    i18nStr = ilios_i18nVendor.getI18NString('general.terms.competencies') + ' ';
    ilios.pm.appendProgramYearComponentToDOM(parentElement,
        ilios.pm.generateIdStringForCompetencyContent(containerNumber),
        i18nStr, 'competency_picker_show_dialog', '517', containerNumber, 'gen_dialog_open');

    // Objectives
    ilios.pm.appendObjectivesBlockToDOM(parentElement, containerNumber);

    // Disciplines
    i18nStr = ilios_i18nVendor.getI18NString('general.terms.topics') + ' ';
    ilios.pm.appendProgramYearComponentToDOM(parentElement,
        ilios.pm.generateIdStringForDisciplineContent(containerNumber),
        i18nStr, 'discipline_picker_show_dialog', '517', containerNumber);

    // Directors
    i18nStr = ilios_i18nVendor.getI18NString('program_management.directors') + ' ';
    ilios.pm.appendProgramYearComponentToDOM(parentElement,
        ilios.pm.generateIdStringForDirectorContent(containerNumber),
        i18nStr, 'director_picker_show_dialog', '517', containerNumber);

    // Schools & departments
    i18nStr = ilios_i18nVendor.getI18NString('program_management.stewards') + ' ';
    ilios.pm.appendProgramYearComponentToDOM(parentElement,
        ilios.pm.generateIdStringForStewardContent(containerNumber),
        i18nStr, 'steward_picker_show_dialog', '366', containerNumber, 'gen_dialog_open');

    if (ilios.global.preferencesModel.showProgramYearArchiving()) {
        scratchInput = document.createElement('a');
        scratchInput.setAttribute('href', '');
        scratchInput.setAttribute('onclick', 'return false;');
        YAHOO.util.Event.addListener(scratchInput, 'click', function (e) {
            IEvent.fire({
                action: 'archive_dialog_open',
                container_number: containerNumber}
            );
        });
        i18nStr = ilios_i18nVendor.getI18NString('general.terms.archive');
        scratchInput.innerHTML = i18nStr;
        scratchInput.setAttribute('class', 'small secondary radius button');
        scratchInput.setAttribute('id', ilios.pm.generateIdStringForArchivingDiv(containerNumber));
        scratchInput.setAttribute('style', 'display:none');
        //we set a placeholder li.archiver in ilios.dom.buildChildContainerDOMTree()
        elem = YAHOO.util.Dom.getElementsByClassName('archiver', 'li', parentElement)[0]; //works, but could be cleaner
        elem.appendChild(scratchInput);
    }
};

ilios.pm.addNewProgramYear = function () {
    var str;
    var userCanAdd = ((ilios.pm.currentProgramModel.getDBId() != null)
        && (ilios.pm.currentProgramModel.getDBId() != '')
        && (parseInt(ilios.pm.currentProgramModel.getDBId()) > 0));

    if (userCanAdd) {
        var container = document.getElementById('program_year_container');
        var newProgramYearDOMTree = null;
        var formDOMElement = new YAHOO.util.Element(document.createElement('form'), {
            method: 'POST',
            action: ''
        });
        var containerNumber = ilios.pm.currentProgramModel.getNextContainerNumber();
        var programYearModel = null;

        formDOMElement.get('element').setAttribute('cnumber', containerNumber);

        newProgramYearDOMTree = ilios.dom.buildChildContainerDOMTree(containerNumber, null, null,
           ilios.pm.transaction.saveProgramYearAsDraft, null, ilios.pm.transaction.saveProgramYearAndPublish,
           ilios.pm.transaction.deleteProgramYear, ilios.pm.handleProgramYearDivCollapse, null,
           formDOMElement, ilios.pm.programYearContentGenerator, ['archiver']);

        container.appendChild(newProgramYearDOMTree.get('element'));

        if ((containerNumber == 1) || (ilios.pm.currentProgramModel.getProgramYearCount() == 0)) {

        	//set the newStartYear equal to the current year for the matriculation summary...
        	var newStartYear = (new Date()).getFullYear();

            programYearModel = new ProgramYearModel();

            programYearModel.addStateChangeListener(ilios.pm.dirtyStateListener, null);

            ilios.pm.currentProgramModel.addProgramYear(programYearModel, ('' + containerNumber));

            ilios.pm.handleProgramYearStartYearSelect(containerNumber);
        } else {
            var elementId = ilios.pm.generateIdStringForProgramYearSelect(containerNumber);
            var selectElement = document.getElementById(elementId);
            var textListContent = null;
            var modelArray = null;
            var currentYear = (new Date()).getFullYear();
            var newStartYear = 0;
            var maximumProgramYears = ilios.pm.getMaximumProgramYearCount();
            var i = containerNumber - 1;
            var previousProgramYearModel = null;
            var scratchElement = null;
            var collapseTrio = null;
            var titleId = null;
            var length = 0;

            for (; i > 0; i--) {
            	 previousProgramYearModel = ilios.pm.currentProgramModel.getProgramYearForContainerNumber(i);

                if (previousProgramYearModel != null) {
                    programYearModel = previousProgramYearModel.clone();
                    break;
                }
            }

            newStartYear = programYearModel.getStartYear() + 1;
            if (newStartYear >= (currentYear + maximumProgramYears)) {
                newStartYear = currentYear + maximumProgramYears - 1;
            }
            programYearModel.setStartYear(newStartYear);

            ilios.utilities.selectOptionWithValue(selectElement, programYearModel.getStartYear());

            modelArray = programYearModel.getCompetencyArray();
            if (modelArray != null) {
                titleId = ilios.pm.generateIdStringForCompetencyContent(containerNumber);
                scratchElement = document.getElementById(titleId);
                collapseTrio = ilios.pm.getCollapseTrioForFirstChildLevelDiv(scratchElement);
                str = ilios.competencies.generateSummaryStringForSelectedCompetencies(modelArray);
                collapseTrio[0].innerHTML = ilios.lang.ellipsisedOfLength(str, 75);
                str = ilios.competencies.generateListHTMLForSelectedCompetencies(modelArray);
                collapseTrio[1].innerHTML = str;
            }

            modelArray = programYearModel.getDisciplineArray();
            if (modelArray != null) {
                textListContent = ilios.utilities.delimitedStringOfTitledObjects(modelArray, ';');

                titleId = ilios.pm.generateIdStringForDisciplineContent(containerNumber);
                scratchElement = document.getElementById(titleId);
                collapseTrio = ilios.pm.getCollapseTrioForFirstChildLevelDiv(scratchElement);
                collapseTrio[0].innerHTML = ilios.lang.ellipsisedOfLength(textListContent, 75);
                collapseTrio[1].innerHTML = textListContent;
            }

            modelArray = programYearModel.getDirectors();
            if (modelArray != null) {
                textListContent = ilios.utilities.delimitedStringOfTitledObjects(modelArray, ';');

                titleId = ilios.pm.generateIdStringForDirectorContent(containerNumber);
                scratchElement = document.getElementById(titleId);
                collapseTrio = ilios.pm.getCollapseTrioForFirstChildLevelDiv(scratchElement);
                collapseTrio[0].innerHTML = ilios.lang.ellipsisedOfLength(textListContent, 75);
                collapseTrio[1].innerHTML = textListContent;
            }

            modelArray = programYearModel.getStewardArray();
            if (modelArray != null) {
                textListContent = '';

                for (var key in modelArray) {
                    if (textListContent.length > 0) {
                        textListContent += '; ';
                    }

                    textListContent += modelArray[key].getStewardTitle();
                }

                titleId = ilios.pm.generateIdStringForStewardContent(containerNumber);
                scratchElement = document.getElementById(titleId);
                collapseTrio = ilios.pm.getCollapseTrioForFirstChildLevelDiv(scratchElement);
                collapseTrio[0].innerHTML = ilios.lang.ellipsisedOfLength(textListContent, 75);
                collapseTrio[1].innerHTML = textListContent;
            }

            modelArray = programYearModel.getObjectives();
            length = programYearModel.getObjectiveCount();
            if (length > 0) {
                var idStr = ilios.pm.generateIdStringForObjectiveContent(containerNumber);
                var objectiveContainer = document.getElementById(idStr);

                for (var j = 0; j <= length; j++) {
                    if (typeof modelArray[j] != 'undefined') {
                        ilios.pm.buildAndPopulateObjective(containerNumber, j, null, modelArray[j], objectiveContainer);
                    }
                }
            }

            ilios.pm.currentProgramModel.addProgramYear(programYearModel, ('' + containerNumber));

            ilios.pm.updateObjectiveCountText(containerNumber);

            programYearModel.addStateChangeListener(ilios.pm.dirtyStateListener, null);

            programYearModel.setDirtyAndNotify();
        }
        ilios.pm.setGraduatingClassOfText(containerNumber, newStartYear);
    }
};

/*
 * @param model if non-null, the objectiveModel will be added to it
 */
ilios.pm.buildAndPopulateObjective = function (containerNumber, objectiveNumber, model,
                                               objectiveModel, container) {
    var newObjectiveContainer = document.createElement('div');
    var scratchElement = null;
    var scratchString = null;
    var scratchInput = null;
    var parentDOMFormElement = container.parentNode.parentNode.parentNode;
    var Event = YAHOO.util.Event;
    var Element = YAHOO.util.Element;


    newObjectiveContainer.setAttribute('class', 'objective_container');
    newObjectiveContainer.setAttribute('cnumber', containerNumber);
    newObjectiveContainer.setAttribute('onumber', objectiveNumber);


    // Delete widget
    scratchElement = new Element(document.createElement('div'));
    scratchElement.addClass('delete_widget icon-cancel');
    scratchElement.get('element').setAttribute('cnumber', containerNumber);
    scratchElement.get('element').setAttribute('onumber', objectiveNumber);
    scratchElement.get('element').setAttribute('title', ilios_i18nVendor.getI18NString("general.phrases.delete_objective"));
    scratchElement.addListener('click', ilios.pm.deleteObjective, null, this);
    newObjectiveContainer.appendChild(scratchElement.get('element'));
    parentDOMFormElement.hideOnLockDivs.push(scratchElement);

    // objective description container
    scratchElement = document.createElement('div');
    scratchString = ilios.pm.generateIdStringForObjectiveTextArea(containerNumber, objectiveNumber);
    scratchElement.setAttribute('class', 'objective_description_container');
    scratchElement.setAttribute('id', scratchString);
    scratchElement.innerHTML = objectiveModel.getTitle();
    newObjectiveContainer.appendChild(scratchElement);

    if (! parentDOMFormElement.objectivesDescriptionContainers) {
        parentDOMFormElement.objectivesDescriptionContainers = [];
    }
    // register click event handler on objective description container
    Event.addListener(scratchElement, "click", function (e) { // pop up the "edit objective" dialog
        ilios.pm.eot.inEditObjectiveModel = objectiveModel;
        IEvent.fire({
            action: 'eot_dialog_open',
            cnumber: containerNumber
        });
    });
    parentDOMFormElement.objectivesDescriptionContainers.push(scratchElement);

    objectiveModel.addStateChangeListener(ilios.pm.objectiveDirtyStateListener, {containerId : scratchString});

    scratchString = ilios.pm.generateIdStringForObjectiveMeSHLink(containerNumber, objectiveNumber);
    scratchInput = document.createElement('a');
    scratchInput.setAttribute('id', scratchString);
    scratchInput.setAttribute('class', 'mesh_btn tiny secondary radius button');
    scratchInput.setAttribute('href', '');
    scratchInput.setAttribute('onclick', 'return false;');
    Event.addListener(scratchInput, 'click', function (e) {
        IEvent.fire({
            action: 'mesh_picker_dialog_open',
            model_in_edit: objectiveModel,
            cnumber: containerNumber
        });
        return false;
    });
    scratchInput.innerHTML = ilios.pm.meshLinkText(objectiveModel);
    newObjectiveContainer.appendChild(scratchInput);
    ilios.utilities.appendClearingDivToContainer(newObjectiveContainer);

    parentDOMFormElement.hideOnLockDivs.push(new Element(scratchInput));

    container.appendChild(newObjectiveContainer);

    if (model != null) {
        model.addObjectiveForContainer(objectiveModel, objectiveNumber);
    }

    ilios.pm.updateObjectiveCountText(containerNumber);
};

ilios.pm.deleteObjective = function (event) {
    var target = ilios.utilities.getEventTarget(event);
    var deleteObjectiveStr = ilios_i18nVendor.getI18NString("general.warning.delete_objective");
    var yesStr = ilios_i18nVendor.getI18NString('general.terms.yes');
    var args = {
        "cnumber": target.getAttribute("cnumber"),
        "onumber": target.getAttribute("onumber")
    }
    ilios.alert.inform(deleteObjectiveStr, yesStr, ilios.pm.continueDeletingObjective, args);
};

/**
 * "Click" event handler function for the "delete objective" confirmation dialog's "OK" button.
 * @method ilios.pm.continueDeletingObjective
 * @param {Event} event
 * @param {Object} args handler arguments object, expected attributes:
 *     "cnumber" ... the objective's container number
 *     "onumber" ... the id of the objective to delete
 */
ilios.pm.continueDeletingObjective = function(event, args) {
    var containerNumber = args.cnumber;
    var objectiveNumber = args.onumber;
    var model = ilios.pm.currentProgramModel.getProgramYearForContainerNumber(containerNumber);
    var objectiveDescriptionContainerId = ilios.pm.generateIdStringForObjectiveTextArea(containerNumber, objectiveNumber);
    var element = document.getElementById(objectiveDescriptionContainerId).parentNode;

    element.parentNode.removeChild(element);
    model.removeObjectiveForContainer(objectiveNumber);
    ilios.pm.updateObjectiveCountText(containerNumber);
    this.hide();
}

/**
 * Initiates the addition of a new objective to a program year by firing up the "objective" dialog.
 * @method addNewObjective
 * @param {String} containerNumber the program year display container id
 */
ilios.pm.addNewObjective = function (containerNumber) {
    ilios.pm.eot.inEditObjectiveModel = null; // reset the objective model
    // fire up the objective dialog
    IEvent.fire({
        action: 'eot_dialog_open',
        cnumber: containerNumber
    });
};

/**
 * Creates the display for a given objective model in a given program year container.
 * @method addNewObjectiveToDom
 * @param {String} containerNumber the program year display container id
 * @param {ObjectiveModel} model the objective model
 */
ilios.pm.addNewObjectiveToDom = function (containerNumber, objectiveModel) {
    var model = ilios.pm.currentProgramModel.getProgramYearForContainerNumber(containerNumber);
    var objectiveNumber = model.getNextObjectiveNumber();
    var containerId = ilios.pm.generateIdStringForObjectiveContent(containerNumber);
    var container = document.getElementById(containerId);
    var collapseWidget = document.getElementById(ilios.pm.generateIdStringForObjectiveCollapseWidget(containerNumber));

    ilios.pm.buildAndPopulateObjective(containerNumber, objectiveNumber, model, objectiveModel, container);

    ilios.pm.setCollapsedDivVisibility(collapseWidget, false);
};

ilios.pm.updateObjectiveCountText = function (containerNumber) {
    var model = ilios.pm.currentProgramModel.getProgramYearForContainerNumber(containerNumber);

    if (model != null) {
        var idString = ilios.pm.generateIdStringForObjectiveCountSpan(containerNumber);
        var element = document.getElementById(idString);

        element.innerHTML = '(' + ilios.utilities.objectPropertyCount(model.getObjectives()) + ')';
    }
};

// @private
ilios.pm.updateObjectiveMeSHCounts = function (model, containerNumber) {
    var objectives = model.getObjectives();
    var idString = null;

    for (var key in objectives) {
        idString = ilios.pm.generateIdStringForObjectiveMeSHLink(containerNumber, key);
        document.getElementById(idString).innerHTML = ilios.pm.meshLinkText(objectives[key]);
    }
};

// @private
ilios.pm.meshLinkText = function (model) {
    var rhett = ilios_i18nVendor.getI18NString('general.phrases.add_mesh');

    rhett += " (" + model.getMeSHItems().length + ")";

    return rhett;
};

ilios.pm.resetEditObjectiveTextDialog = function (dialog) {
    var pulldown = document.getElementById('eot_competency_pulldown');
    var containerNumber = dialog.cnumber;

    ilios.pm.buildObjectiveTextDialogPulldownOptions(pulldown, containerNumber);

    if (ilios.pm.eot.inEditObjectiveModel) {
        dialog.eotEditor.setEditorHTML(ilios.pm.eot.inEditObjectiveModel.getDescription());
        ilios.utilities.selectOptionWithValue(pulldown, ilios.pm.eot.inEditObjectiveModel.getCompetencyId());
    } else {
        dialog.eotEditor.setEditorHTML('');
    }
};

/**
 * Populates a dropdown box with options for the available competencies associated with the given program year.
 * @method buildObjectiveTextDialogPulldownOptions
 * @param {HTMLElement} pulldown the "select" element to append options to.
 * @param {Number} containerNumber the program year id
 */
ilios.pm.buildObjectiveTextDialogPulldownOptions = function (pulldown, containerNumber) {
    var model = ilios.pm.currentProgramModel.getProgramYearForContainerNumber(containerNumber);
    var competencies = model.getCompetencyArray();
    var option = document.createElement('option');
    var competencyModel, parentCompetencyModel;
    var optionTitle = null;
    var tmpObjs = [];
    var i, n, tmpObj;

    ilios.utilities.removeAllChildren(pulldown);

    option.value = '0';
    option.innerHTML = '(' + ilios_i18nVendor.getI18NString('general.terms.none') + ')';
    pulldown.appendChild(option);

    // create a list of temporary competency objects that can be sorted and converted into DOM nodes.
    for (var key in competencies) {
        competencyModel = competencies[key];
        tmpObj = {};
        tmpObj.title = competencyModel.getCompetencyTitle() || '';
        tmpObj.dbId =  competencyModel.getDBId();
        tmpObj.parentTitle = '';
        if (competencyModel.isSubDomain()) {
            parentCompetencyModel = ilios.competencies.getCompetency(competencyModel.getParentCompetencyId());
            tmpObj.parentTitle = parentCompetencyModel.getCompetencyTitle() || '';
        }
        tmpObjs.push(tmpObj);
    }

    // sort by parent comp. title and comp.title
    tmpObjs.sort(function (a, b) {
        var rhett = a.parentTitle.localeCompare(b.parentTitle);
        if (! rhett) {
            rhett = a.title.localeCompare(b.title);
        }
        return rhett;
    });

    // create option nodes
    for (i = 0, n = tmpObjs.length; i < n; i++) {
        tmpObj = tmpObjs[i];
        option = document.createElement('option');
        option.value = tmpObj.dbId;
        if (tmpObj.parentTitle) {
             option.innerHTML = tmpObj.title + ' (' + tmpObj.parentTitle + ')';
        } else {
            option.innerHTML = tmpObj.title;
        }
        pulldown.appendChild(option);
    }
};

ilios.pm.alterViewToReflectReadOnlyState = function () {
    var Element = YAHOO.util.Element;
    var programYears = ilios.pm.currentProgramModel.getProgramYearArray();
    var containerNumber = 0;
    var element = null;

    for (var key in programYears) {
        containerNumber = ilios.pm.currentProgramModel.getContainerNumberForProgramYear(programYears[key]);
        ilios.pm.alterProgramYearUIToReflectLockedState(containerNumber, false);
    }

    element = new Element(document.getElementById('draft_button'));
    element.setStyle('display', 'none');

    element = new Element(document.getElementById('publish_button'));
    element.setStyle('display', 'none');

    element = new Element(document.getElementById('reset_button'));
    element.setStyle('display', 'none');

    element = document.getElementById('program_title');
    element.disabled = 'disabled';

    element = document.getElementById('short_title');
    element.disabled = 'disabled';

    element = document.getElementById('duration_selector');
    element.disabled = 'disabled';

    element = document.getElementById('add_new_program_year_link');
    element.disabled = 'disabled';
};

ilios.pm.alterProgramYearUIToReflectLockedState = function (containerNumber, updateDraftText) {
    var Element = YAHOO.util.Element;
    var idStr = ilios.dom.childCollapsingContainerIdForContainerNumber(containerNumber);
    var collapsedContainer = document.getElementById(idStr);
    var i = 0;
    var n = 0;

    if (collapsedContainer != null) {
        var container = new Element(collapsedContainer.parentNode);
        var model = ilios.pm.currentProgramModel.getProgramYearForContainerNumber(containerNumber);
        var element = null;
        var childElement = null;
        var formElement = collapsedContainer.getElementsByTagName('form').item(0);
        var containerDeleteDiv = collapsedContainer.parentNode.getElementsByClassName('delete_widget').item(0);


        container.setStyle('background-color', '#C5C5C5');

        idStr = ilios.pm.generateIdStringForArchivingDiv(containerNumber);
        element = document.getElementById(idStr);
        if (element != null) { // would be null if the user doesn't have archiving turned on
            childElement = element.childNodes[0];
            childElement.innerHTML = ilios_i18nVendor.getI18NString('general.terms.file');
        }

        (new Element(containerDeleteDiv)).setStyle('display', 'none');

        for (var key in formElement.hideOnLockDivs) {
            element = formElement.hideOnLockDivs[key];
            element.setStyle('display', 'none');
        }

        if (YAHOO.lang.isArray(formElement.objectivesDescriptionContainers)) {
            for (i = 0, n = formElement.objectivesDescriptionContainers.length; i < n; i++) {
                element = formElement.objectivesDescriptionContainers[i];
                YAHOO.util.Event.removeListener(element, 'click');
            }
        }

        element = new Element(document.getElementById('' + containerNumber + '_child_save'));
        element.setStyle('display', 'none');

        element = new Element(document.getElementById('' + containerNumber + '_child_publish'));
        element.setStyle('display', 'none');

        if (updateDraftText) {
            element = document.getElementById('' + containerNumber + '_child_draft_text');
            element.innerHTML = '<span class="status is-locked">' + ilios_i18nVendor.getI18NString('general.terms.locked') + '</span>';
        }

        idStr = ilios.pm.generateIdStringForProgramYearSelect(containerNumber);
        childElement = document.getElementById(idStr);
        element = childElement.parentNode;
        element.removeChild(childElement);
        childElement = document.createElement('span');
        childElement.setAttribute('style', 'font-weight: bold; font-size: 9pt;')
        childElement.innerHTML = ('' + model.getStartYear() + '-' + (model.getStartYear() + 1));
        element.appendChild(childElement);
    }
};

ilios.common.picker.mesh.handleMeSHPickerSave = function (dialogPanel) {
    if (ilios.mesh.meshInEditModel.compareTo(ilios.mesh.meshInEditReferenceModel) != 0) {
        var model = ilios.pm.currentProgramModel.getProgramYearForContainerNumber(dialogPanel.cnumber);

        ilios.mesh.meshInEditReferenceModel.replaceContentWithModel(ilios.mesh.meshInEditModel, true);

        ilios.pm.updateObjectiveMeSHCounts(model, dialogPanel.cnumber);
    }

    if ((ilios.mesh.currentEditQueryUIDPairSelections != null)
        && (ilios.mesh.currentEditQueryUIDPairSelections.length > 0)) {
        ilios.mesh.saveMeSHSearchSelections();
    }

    ilios.mesh.meshInEditModel = null;
    ilios.mesh.meshInEditReferenceModel = null;

    ilios.mesh.lastMeSHSearchQuery = null;
    ilios.mesh.currentEditQueryUIDPairSelections = null;
};

/**
 * Returns the 'Graduating Class of XXXX' string when given the startYear and duration of a program
 *
 * @method ilios.pm.getGraduatingClassOfString
 * @param {Number} startYear the starting year of the academic/matriculation year of the program
 * @param {Number} duration the duration of the program
 * @return {String} rhett 'Graduating Class of XXXX' string
 */

ilios.pm.getGraduatingClassOfString = function (startYear, duration) {
	var graduatingClassOfStr = ilios_i18nVendor.getI18NString('general.phrases.graduating_class_of');
	var rhett = graduatingClassOfStr+" "+(parseInt(startYear) + parseInt(duration));
    return rhett;
}
