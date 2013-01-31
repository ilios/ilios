/**
 * DEPENDENCY: YUI lib (at least YAHOO.util and its dependencies)
 * DEPENDENCY: scripts/ilios_ui.js
 * DEPENDENCY: scripts/ilios_utilities.js
 */

ilios.namespace('cm.session.ilm');


ilios.cm.session.ilm.instructorGroupDataSource = null;
ilios.cm.session.ilm.instructorGroupAutoCompleter = null;

ilios.cm.session.ilm.instructorsModel = [];

ilios.cm.session.ilm.inEditIndependentLearningModel = null;

ilios.cm.session.ilm.yuiILMDueDateCalendar = null;
ilios.cm.session.ilm.currentlySelectingILMDueDate = false;

/*
 * This method is invoked from the instructor group autocompleter to filter the list of available
 *  instructors / instructor groups based on their matching against the query string.
 *
 * Should be considered @private
 */
ilios.cm.session.ilm.instructorGroupsForQuery = function (queryString) {
    var rhett = ilios.cm.session.ilm.instructorsModel;

    if (queryString != '') {
        var i = 0;
        var len = ilios.cm.session.ilm.instructorsModel.length;
        var model = null;
        var stringToTest = null;

        rhett = [];

        for (; i < len; i++) {
            model = ilios.cm.session.ilm.instructorsModel[i];

            if (model instanceof UserModel) {
                stringToTest = model.getFormattedName(ilios.utilities.USER_NAME_FORMAT_LAST_FIRST).toLowerCase();
            }
            else {
                stringToTest = model.title.toLowerCase();
            }

            if (stringToTest.indexOf(queryString.toLowerCase()) != -1) {
                rhett.push(model);
            }
        }
    }

    return rhett;
};

ilios.cm.session.ilm.instructorGroupDataSource = new YAHOO.util.FunctionDataSource(ilios.cm.session.ilm.instructorGroupsForQuery);

/*
 * This method filters the instructors for the autocompleter; this should not be confused with
 *  the filtering which goes on via a user typed query string (and handled by the method in this
 *  file: ilios.cm.session.ilm.instructorGroupsForQuery ) -- this filtering is done pre-markup of
 *  the list and, here, takes out elements in the full list which have already been selected.
 *
 * TODO identical to ilios.om.lightbox.filterInstructors
 *
 * Should be considered @private
 */
ilios.cm.session.ilm.filterInstructors = function (queryString, fullResponse, parsedResponse,
                                                   callback) {
    var len = parsedResponse.results.length;
    var selectedList = document.getElementById('calendar_instructor_selected');
    var filteredResults =[];
    var i = 0;

    for (; i < len; i++) {
        if (! ilios.dom.iliosModeledLIElementsContainMatchingModel(selectedList,
                                                                   parsedResponse.results[i])) {
            filteredResults.push(parsedResponse.results[i]);
        }
    }

    parsedResponse.results = filteredResults;

    return parsedResponse;
};

/*
 * This is messaged when the user selects an instructor / instructor group from the list of them
 *  in the lightbox.
 *
 * Should be considered @private
 */
ilios.cm.session.ilm.handleInstructorGroupSelection = function (selectedModel) {
    var displayString = null;

    if (selectedModel instanceof UserModel) {
        displayString = selectedModel.getFormattedName(ilios.utilities.USER_NAME_FORMAT_LAST_FIRST);
    }
    else {
        displayString = selectedModel.title;
    }

    ilios.dom.addNewLIElementWithIliosModel(document.getElementById('calendar_instructor_selected'),
                                            selectedModel, displayString);

    ilios.cm.session.ilm.inEditIndependentLearningModel.addInstructor(selectedModel);

    ilios.cm.session.ilm.updateInstructorGroupTextField();
};

/*
 * This is messaged when the user clicks on an instructor / instructor group from the left pane of
 *  previously selected items (thereby semantically deselecting the item).
 *
 * Should be considered @private
 */
ilios.cm.session.ilm.handleInstructorGroupDeselection = function (event) {
    var target = ilios.utilities.getEventTarget(event);

    if (target.tagName.toLowerCase() === 'li') {
        var model = target.iliosModel;

        target.parentNode.removeChild(target);

        ilios.cm.session.ilm.inEditIndependentLearningModel.removeInstructor(model);

        ilios.cm.session.ilm.updateInstructorGroupTextField();

        ilios.cm.session.ilm.instructorGroupAutoCompleter.sendQuery('');

        return false;
    }

    return true;
};

/*
 * This method populates the read-only UI text field representing the instructors list with the
 *  contents of the current in-edit offering model.
 *
 * Should be considered @private
 */
ilios.cm.session.ilm.updateInstructorGroupTextField = function () {
    var element = document.getElementById('instructors_lightbox_textfield');

    element.innerHTML
            = ilios.cm.session.ilm.inEditIndependentLearningModel.getInstructorsAsFormattedText();
};

/*
 * This method either displays or hides the DIV in the lightbox which allows selection of
 *  instructors / instructor groups.
 *
 * Should be considered @protected
 */
ilios.cm.session.ilm.showInstructors = function (showSelectorDiv) {
    var element = document.getElementById('ilios_calendar_instructors_selector');

    ilios.utilities.toggleShowMoreOrLess('ilios_calendar_instructors_selector_div', null);

    if (showSelectorDiv) {
        (new YAHOO.util.Element(element)).setStyle('display', 'none');
        ilios.cm.session.ilm.instructorGroupAutoCompleter.sendQuery('');
    } else {
        (new YAHOO.util.Element(element)).setStyle('display', 'inline');
    }
};

ilios.cm.session.ilm.showILMLightbox = function (containerNumber) {
    var Element = YAHOO.util.Element;
    var element = null;
    var now = null;
    var sessionModel = ilios.cm.currentCourseModel.getSessionForContainer(containerNumber);
    var dueDate = null;
    var displayString, tmpArray, tmpModel, key;

    // MAY RETURN THIS BLOCK
    if (ilios.cm.currentCourseModel.isModelDirty()) {
        ilios.alert.alert(ilios_i18nVendor.getI18NString('course_management.error.independent_learning_edit'));

        return;
    } else if (ilios.utilities.arraySize(ilios.cm.currentCourseModel.getCohorts()) == 0) {
        ilios.alert.alert(ilios_i18nVendor.getI18NString('course_management.warning.independent_learning.no_cohorts'));

        return;
    } else if (ilios.utilities.arraySize(ilios.lg.picker.learnerTreeModel) == 0) {
        ilios.alert.alert(ilios_i18nVendor.getI18NString('course_management.warning.independent_learning.no_learners'));
        return;
    }

    // TODO check course_management.warning.independent_learning.no_instructors

    i18nStr = ilios_i18nVendor.getI18NString('general.terms.save');
    ilios.ui.hackilySetButtonTitleOnYUIDialog(i18nStr, ilios.cm.session.multipurposeSessionLightbox,
                                              0);

    element = document.getElementById('multipurpose_lightbox_title');
    element.innerHTML
          = ilios_i18nVendor.getI18NString('course_management.session.independent_learning.title');

    element = new Element(document.getElementById('multipurpose_instructors_div'));
    element.setStyle('display', 'block');

    element = new Element(document.getElementById('ilm_calendar_div'));
    element.setStyle('display', 'block');

    element = new Element(document.getElementById('multi_offering_calendar_div'));
    element.setStyle('display', 'none');

    element = new Element(document.getElementById('multi_offering_recurring_div'));
    element.setStyle('display', 'none');

    element = new Element(document.getElementById("learner_assignment_strategy_div"));
    element.setStyle("display", "none");

    ilios.cm.session.ilm.inEditIndependentLearningModel = sessionModel.getIndependentLearningModel().clone();

    ilios.lg.picker.constructOrResetLearnerTreeViews(sessionModel.getIndependentLearningModel()
                                                                               .getLearnerGroups());
    ilios.lg.picker.updateLearnerTextField();

    ilios.cm.session.ilm.updateInstructorGroupTextField();

    // populate left-hand side ("selected instructors") of instructor picker
    element = document.getElementById('calendar_instructor_selected');
    ilios.utilities.removeAllChildren(element);
    tmpArray = ilios.cm.session.ilm.inEditIndependentLearningModel.getInstructors();
    for (key in tmpArray) {
        tmpModel = tmpArray[key];

        if (tmpModel instanceof UserModel) {
            displayString = tmpModel.getFormattedName(ilios.utilities.USER_NAME_FORMAT_LAST_FIRST);
        } else {
            displayString = tmpModel.title;
        }

        ilios.dom.addNewLIElementWithIliosModel(element, tmpModel, displayString);
    }

    dueDate = ilios.cm.session.ilm.inEditIndependentLearningModel.getDueDate();
    if (dueDate != null) {
        ilios.cm.session.ilm.yuiILMDueDateCalendar.setYear(dueDate.getUTCFullYear());
        ilios.cm.session.ilm.yuiILMDueDateCalendar.setMonth(dueDate.getUTCMonth());

        ilios.cm.session.ilm.currentlySelectingILMDueDate = true;
        ilios.cm.session.ilm.yuiILMDueDateCalendar.select(dueDate);
        ilios.cm.session.ilm.currentlySelectingILMDueDate = false;

        ilios.cm.session.ilm.yuiILMDueDateCalendar.render();
    }

    ilios.cm.session.multipurposeSessionLightbox.cnumber = containerNumber;
    ilios.cm.session.multipurposeSessionLightbox.showingForMultiOffering = false;
    ilios.cm.session.multipurposeSessionLightbox.showDialogPane();
};

ilios.cm.session.ilm.userAcceptedILMLightboxChanges = function (dialog) {
    var containerNumber = ilios.cm.session.multipurposeSessionLightbox.cnumber;
    var sessionModel = ilios.cm.currentCourseModel.getSessionForContainer(containerNumber);
    var dueDate = ilios.cm.session.ilm.inEditIndependentLearningModel.getDueDate();

    if (dueDate != null) {
        var tf = document.getElementById(
                            ilios.cm.session.generateIdStringForSessionILMDueDate(containerNumber));

        tf.value = dueDate.format("m/d/yy");
    }

    ilios.cm.session.ilm.inEditIndependentLearningModel.setLearnerGroups(
                                                        ilios.lg.picker.getSelectedLearnerModels());

    sessionModel.setIndependentLearningModel(ilios.cm.session.ilm.inEditIndependentLearningModel);

    ilios.cm.session.multipurposeSessionLightbox.cancel();
};

/**
 * Click event handler callback function.
 * Finishes up the conversion of a "regular" course session to a Independent Learning (ILM) session.
 * @method continueWithILMCreation
 * @param {Event | null} event the triggered click event, or NULL if the function is invoked programatically
 * @param {Object} args callback arguments. Expects values keyed off by the following:
 *     "cnumber" ... the container number of the given session
 */
ilios.cm.session.ilm.continueWithILMCreation = function (event, args) {
    var containerNumber = args.cnumber;
    var sessionModel = ilios.cm.currentCourseModel.getSessionForContainer(containerNumber);
    var idStr = ilios.cm.session.generateIdStringForSessionILMGroupsLink(containerNumber);
    var element = document.getElementById(idStr);
    var dueDate = null;
    var existingILMModel = sessionModel.getIndependentLearningModel();

    ilios.dom.setElementEnabled(element, true);

    idStr = ilios.cm.session.generateIdStringForSessionMultiOfferingCreateLink(containerNumber);
    element = document.getElementById(idStr);
    ilios.dom.setElementEnabled(element, false);

    idStr = ilios.cm.session.generateIdStringForSessionILMDiv(containerNumber);
    element = new YAHOO.util.Element(document.getElementById(idStr));
    element.setStyle('display', '');

    idStr = ilios.cm.session.generateIdStringForSessionTypeSelector(containerNumber);
    element = document.getElementById(idStr);
    element.disabled = true;

    idStr = ilios.cm.session.generateIdStringForSessionILMHours(containerNumber);
    element = document.getElementById(idStr);

    if (existingILMModel != null) {
        dueDate = existingILMModel.getDueDate();

        element.value = existingILMModel.getHours();
    } else {
        dueDate
         = ilios.utilities.mySQLTimelessDateToDateObject(ilios.cm.currentCourseModel.getEndDate());

        element.value = 1;

        sessionModel.setILMAttributes(1, dueDate, null, null);
    }
    idStr = ilios.cm.session.generateIdStringForSessionILMDueDate(containerNumber);
    element = document.getElementById(idStr);
    element.value = dueDate.format("m/d/yy");

    // note: session save with non- -1 ilm db id nukes offerings

    if (event != null) {
        this.cancel();
    }
};

/**
 * Button click event handler function.
 * Cancels the conversion of a session into an ILM and closes out the confirmation dialog.
 * @method cancelILMCreation
 * @method {Event} event the click event
 * @param {Object} args the callback handler arguments object. Expects the checkbox-element
 * that was clicked as attributed keyed off by "checkbox"
 * @private
 */
ilios.cm.session.cancelILMCreation = function (event, args) {
    args.checkbox.checked = false; // un-tick the checkbox that triggered it all

    this.cancel();
};

ilios.cm.session.sessionILMCheckboxSelected = function (checkbox, containerNumber) {
    var sessionModel = ilios.cm.currentCourseModel.getSessionForContainer(containerNumber);

    if (checkbox.checked) {
        if (sessionModel.getOfferingCount() > 0) {
            var i18nStr = 'By selecting the Independent Learning option, you will remove ALL '
                            + 'previously entered calendar offerings for this session.';
            var continueStr = 'Are you sure you want to continue?';
            var yesStr = 'OK';
            var noStr = 'Cancel';

            ilios.alert.inform(('<p style="margin-bottom:9px; text-align:justify;">' + i18nStr
                    + '</p><center><b>' + continueStr + '</b></center>'),
                    yesStr, ilios.cm.session.ilm.continueWithILMCreation, {"cnumber": containerNumber},
                    noStr, ilios.cm.session.cancelILMCreation, {"checkbox": checkbox});
        } else {
            ilios.cm.session.ilm.continueWithILMCreation(null, {"cnumber": containerNumber});
        }
    }
    else {
        var idStr = ilios.cm.session.generateIdStringForSessionILMGroupsLink(containerNumber);
        var element = document.getElementById(idStr);

        ilios.dom.setElementEnabled(element, false);

        idStr = ilios.cm.session.generateIdStringForSessionMultiOfferingCreateLink(containerNumber);
        element = document.getElementById(idStr);
        ilios.dom.setElementEnabled(element, true);

        idStr = ilios.cm.session.generateIdStringForSessionILMDiv(containerNumber);
        element = new YAHOO.util.Element(document.getElementById(idStr));
        element.setStyle('display', 'none');

        idStr = ilios.cm.session.generateIdStringForSessionTypeSelector(containerNumber);
        element = document.getElementById(idStr);
        element.disabled = false;

        sessionModel.setILMAttributes(-1, null, null, null);
    }
};

ilios.cm.session.registerILMLightboxUIListeners = function () {
    var Event = YAHOO.util.Event;
    var element = document.getElementById('calendar_instructor_collapse_selector');
    var itemSelectHandler = null;

    ilios.cm.session.registerMultipurposeLightboxUIListeners();

    Event.addListener(element, 'click', function () {
        ilios.cm.session.ilm.showInstructors(false);
    });

    element = document.getElementById('calendar_instructor_selected');
    Event.addListener(element, 'click', function (event) {
        ilios.cm.session.ilm.handleInstructorGroupDeselection(event);
    });

    ilios.cm.session.ilm.instructorGroupAutoCompleter = new YAHOO.widget.AutoComplete(
        'calendar_instructor_ac_input', 'calendar_instructor_autolist',
        ilios.cm.session.ilm.instructorGroupDataSource);

    ilios.cm.session.ilm.instructorGroupAutoCompleter.alwaysShowContainer = true;
    ilios.cm.session.ilm.instructorGroupAutoCompleter.allowBrowserAutocomplete = false;
    ilios.cm.session.ilm.instructorGroupAutoCompleter.minQueryLength = 0;
    ilios.cm.session.ilm.instructorGroupAutoCompleter.maxResultsDisplayed = 2500;

    ilios.cm.session.ilm.instructorGroupAutoCompleter.autoHighlight = false;

    ilios.cm.session.ilm.instructorGroupAutoCompleter.target = element;

    ilios.cm.session.ilm.instructorGroupAutoCompleter.applyLocalFilter = true;
    ilios.cm.session.ilm.instructorGroupAutoCompleter.filterResults = ilios.cm.session.ilm.filterInstructors;

    ilios.cm.session.ilm.instructorGroupAutoCompleter.resultTypeList = false;

    ilios.cm.session.ilm.instructorGroupAutoCompleter.formatResult = function (resultDataObject, queryString, resultMatch) {
        if (resultDataObject instanceof UserModel) {
            var rhett = '<span title="' + resultDataObject.getEmailAddress() + '">';

            rhett += resultDataObject.getFormattedName(ilios.utilities.USER_NAME_FORMAT_LAST_FIRST);
            rhett += '</span>';

            return rhett;
        }

        return resultDataObject.title;
    };

    itemSelectHandler = function (selectionType, selectionArgs) {
        ilios.cm.session.ilm.handleInstructorGroupSelection(selectionArgs[2]);

        selectionArgs[1].parentNode.removeChild(selectionArgs[1]);

        document.getElementById('calendar_instructor_ac_input').value = '';
    };
    ilios.cm.session.ilm.instructorGroupAutoCompleter.itemSelectEvent.subscribe(itemSelectHandler);
};

ilios.cm.session.buildILMLightboxDOM = function () {
    ilios.cm.session.buildMultipurposeLightboxDOM();

    ilios.cm.session.ilm.yuiILMDueDateCalendar
                        = new YAHOO.widget.Calendar("ilmDueDateYUICal",
                                                    "ilm_lightbox_due_date_calendar",
                                                    { close: false } );
    ilios.cm.session.ilm.yuiILMDueDateCalendar.render();
    ilios.cm.session.ilm.yuiILMDueDateCalendar.selectEvent.subscribe(ilios.cm.session.ilmDueDateSelectionHandler,
                                                                     ilios.cm.session.ilm.yuiILMDueDateCalendar,
                                                                     true);
};

// @private
ilios.cm.session.ilmDueDateSelectionHandler = function (type, args, obj) {
    if (! ilios.cm.session.ilm.currentlySelectingILMDueDate) {
        var selected = args[0];

        // 'this' is the calendar instance
        ilios.cm.session.ilm.inEditIndependentLearningModel.setDueDate(this.toDate(selected[0]));
    }
};
