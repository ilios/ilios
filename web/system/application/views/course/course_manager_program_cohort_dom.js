/**
 * Program-cohort picker functionality for course management.
 *
 * DEPENDENCY: YUI lib (at least YAHOO.util and its dependencies)
 * DEPENDENCY: scripts/ilios_ui.js
 * DEPENDENCY: scripts/ilios_utilities.js
 * DEPENDENCY: scripts/models/program_cohort_model.js
 */

/**
 * @property {Array} programCohortWorkingArray holds the current selection of program cohorts
 */
ilios.cm.programCohortWorkingArray = [];

/**
 * @property {HTMLElement} programCohortSelectListElement the currently selected list item in the picker dialog
 */
ilios.cm.programCohortSelectListElement = null;

/**
 * Called from ilios.ui.handleProgramCohortSelectionDialogDisplay
 *
 * @param {Object} cohort an object returned by the controller method getCohortProgramTreeContent
 * @param {String} programTitle
 * @return {ProgramCohortModel} an instance of a model -- this instance will be attached to the tree node so that it may
 *              be referenced later
 */
ilios.ui.buildNodeModel = function (cohort, programTitle) {
    var rhett = new ProgramCohortModel(programTitle, cohort.title, cohort.cohort_id,
        cohort.program_year_id, cohort.start_year, null, cohort.is_active_school);

    rhett.setProgramShortTitle(cohort.program_short_title);
    rhett.setProgramDuration(cohort.program_duration);
    rhett.setCurrentEnrollment(cohort.enrollment);

    return rhett;
};

/**
 * Called from ilios.ui.handleProgramCohortSelectionDialogDisplay
 * @param {Object} clickObject
 */
ilios.ui.handleProgramCohortDialogSelection = function (clickObject) {
    var node = clickObject.node;
    var pickedProgramCohortModel = null;
    var liElement = null;

    if (! node.parentDialog) {
        // this is a program parent node
        return false;
    }


    pickedProgramCohortModel = node.iliosModel;

    // refuse selection for cohorts that don't belong to the currently active school
    if (! pickedProgramCohortModel.belongsToActiveSchool()) {
        return false;
    }

    ilios.cm.programCohortWorkingArray.push(pickedProgramCohortModel);

    liElement = ilios.cm.addProgramCohortToSelection(pickedProgramCohortModel);

    liElement.parentTreeNode = node.parent;
    liElement.removedTreeNode = node;

    ilios.ui.programCohortDialogTreeView.popNode(node);
    ilios.ui.programCohortDialogTreeView.draw();

    // returning false prevents the default action of a click (setting focus, etc)
    // which would cause an error since we just got rid of the node
    // which was clicked upon
    return false;
};

ilios.cm.handleProgramCohortDialogDeselection = function (event) {
    var target = ilios.utilities.getEventTarget(event);
    var i, n;

    if (target.tagName.toLowerCase() === 'li') {
        var model = target.iliosModel;

        if (! model.belongsToActiveSchool()) {
            return false;
        }
        var inserted = false;
        var parentTreeNode = target.parentTreeNode;
        if (! parentTreeNode) {
            return false;
        }
        var children = target.parentTreeNode.children;
        var removedLabel = target.removedTreeNode.label;
        var selectedCohorts = ilios.cm.programCohortWorkingArray;


        // append the removed tree node from the selected cohort tree
        // onto the correct sub-branch in the "available cohorts" tree
        for (var i = 0; (i < children.length); i++) { // find the right location
            var childLabel = children[i].label;
            if (childLabel.localeCompare(removedLabel) > 0) {
                target.removedTreeNode.insertBefore(children[i]);
                inserted = true;
                break;
            }
        }
        // fallback:
        // if the node has not been inserted above
        // then simply latch it on to the end of the branch
        if (! inserted) {
            target.removedTreeNode.appendTo(target.parentTreeNode);
        }

        // remove from "selected cohorts" model array
        for (i = 0, n = selectedCohorts.length; i < n; i++) {
            if (model.getCohortId() === selectedCohorts[i].getCohortId()) {
                selectedCohorts.splice(i, 1);
                break;
            }
        }

        ilios.cm.programCohortSelectListElement.removeChild(target);
        ilios.ui.programCohortDialogTreeView.draw();
        return false;
    }

    return true;
};

// @private
ilios.cm.addProgramCohortToSelection = function (programCohortModel) {
    var liElement = document.createElement('li');
    var textNode = document.createTextNode(programCohortModel.getDisplayText());

    liElement.appendChild(textNode);
    liElement.iliosModel = programCohortModel;

    ilios.cm.programCohortSelectListElement.appendChild(liElement);

    return liElement;
};

// @private
ilios.cm.findAndRemoveProgramCohortNodeForModel = function (programCohortModel, liElement) {
    var rootNode = ilios.ui.programCohortDialogTreeView.getRoot();
    var nodeToPop = ilios.cm.recursiveNodeSearchForModel(rootNode, programCohortModel);

    if (nodeToPop != null) {
        liElement.parentTreeNode = nodeToPop.parent;
        liElement.removedTreeNode = nodeToPop;

        ilios.ui.programCohortDialogTreeView.popNode(nodeToPop);
        ilios.ui.programCohortDialogTreeView.draw();
    }
};

// @private
ilios.cm.recursiveNodeSearchForModel = function (node, programCohortModel) {
    var children = null;
    var len = 0;
    var rhett = null;

    if ((node.iliosModel != null) && (node.iliosModel.compareTo(programCohortModel) == 0)) {
        return node;
    }

    children = node.children;
    len = children.length;
    for (var i = 0; i < len; i++) {
        rhett = ilios.cm.recursiveNodeSearchForModel(children[i], programCohortModel);

        if (rhett != null) {
            return rhett;
        }
    }

    return rhett;
};

// @private
ilios.cm.finishProgramCohortSelectionDialogDisplay = function (dialog) {
    var liElement = null;
    var yElement = new YAHOO.util.Element(document.getElementById('steward_indeterminate_div'));
    var courseCohorts = ilios.cm.currentCourseModel.getCohorts();
    var i, n;

    // create a shallow copy of the course's program cohorts
    ilios.cm.programCohortWorkingArray = courseCohorts.slice(0);

    ilios.utilities.removeAllChildren(ilios.cm.programCohortSelectListElement);
    for (i = 0, n = ilios.cm.programCohortWorkingArray.length; i < n; i++) {
        liElement = ilios.cm.addProgramCohortToSelection(ilios.cm.programCohortWorkingArray[i]);
        ilios.cm.findAndRemoveProgramCohortNodeForModel(ilios.cm.programCohortWorkingArray[i], liElement);
    }

    yElement.setStyle('display', 'none');
};

ilios.cm.courseManagementHandleProgramCohortSelectionDialogDisplay = function (dialog) {
    var yElement = new YAHOO.util.Element(document.getElementById('steward_indeterminate_div'));

    if (ilios.cm.programCohortSelectListElement == null) {
        ilios.cm.programCohortSelectListElement = document.getElementById(dialog.selectListId);
    }

    yElement.setStyle('display', 'block');

    ilios.ui.handleProgramCohortSelectionDialogDisplay(dialog,
                                                       ilios.cm
                                                         .finishProgramCohortSelectionDialogDisplay);
};

/*
 * This will get messaged when the user clicks the submit button on the dialog (this
 *      button is currently display-text'd as "Done")
 *
 * @see ilios.dom.generateGenericDialogMarkupAndWireContent
 */
ilios.cm.handleProgramCohortDialogSubmit = function () {
    var denyModification = false;

    // check for missing cohorts
    if (ilios.utilities.arraySize(ilios.cm.currentCourseModel.getAssociatedLearners()) > 0) {
        denyModification
            = ilios.utilities.arrayHasElementsMissingInArray(ilios.cm.currentCourseModel.getCohorts(),
                                                             ilios.cm.programCohortWorkingArray);
    }

    if (denyModification) {
        var msg = ilios_i18nVendor.getI18NString('course_management.error.program_cohort_deletion');

        ilios.alert.alert(msg);
    }
    else {
        ilios.cm.currentCourseModel.setCohorts(ilios.cm.programCohortWorkingArray);

        ilios.cm.refreshCohortData();
    }

    ilios.cm.programCohortWorkingArray = null;
};

ilios.cm.refreshCohortData = function () {
    var dataSource = ilios.cm.cohortDataTable.getDataSource();

    ilios.cm.transaction.loadCohortObjectives();

    dataSource.sendRequest('', { success: ilios.cm.cohortDataTable.onDataReturnInitializeTable,
                                 failure: ilios.cm.cohortDataTable.onDataReturnInitializeTable,
                                 scope: ilios.cm.cohortDataTable } );
};

ilios.cm.getCohortTableData = function () {
    if (ilios.cm.currentCourseModel == null) {
        return [];
    }

    return ilios.cm.currentCourseModel.getCohortDataTableArray();
};
