/**
 * Program-cohort picker functionality for user management.
 *
 * DEPENDENCY: YUI lib (at least YAHOO.util and its dependencies)
 * DEPENDENCY: scripts/ilios_ui.js
 * DEPENDENCY: scripts/ilios_utilities.js
 * DEPENDENCY: scripts/models/school_program_cohort_model.js
 * DEPENDENCY: management/management_user_accounts.js
 */

/**
 * @property {Array} programCohortWorkingArray holds the current selection of program cohorts
 */
ilios.management.user_accounts.programCohortWorkingArray = [];


/**
 * @property {HTMLElement} programCohortSelectListElement the currently selected list item in the picker dialog
 */
ilios.management.user_accounts.programCohortSelectListElement = null;

/**
 * @property {TreeView} the program cohorts tree widget in the picker dialog
 */
ilios.management.user_accounts.programCohortDialogTreeView = null;

// @private
ilios.management.user_accounts.subscribedToPCLabelClick = false;

/**
 * Called from ilios.management.user_accounts.handleProgramCohortSelectionDialogDisplay
 * @param {Object} clickObject
 */
ilios.management.user_accounts.handleProgramCohortDialogSelection = function (clickObject) {
    var node = clickObject.node;
    var pickedProgramCohortModel = null;
    var liElement = null;

    if (! node.parentDialog) {
        // this is a program parent node
        return false;
    }


    pickedProgramCohortModel = node.iliosModel;

    ilios.management.user_accounts.programCohortWorkingArray.push(pickedProgramCohortModel);

    liElement = ilios.management.user_accounts.addProgramCohortToSelection(pickedProgramCohortModel);

    liElement.parentTreeNode = node.parent;
    liElement.removedTreeNode = node;

    ilios.management.user_accounts.programCohortDialogTreeView.popNode(node);
    ilios.management.user_accounts.programCohortDialogTreeView.draw();

    // returning false prevents the default action of a click (setting focus, etc)
    // which would cause an error since we just got rid of the node
    // which was clicked upon
    return false;
};

ilios.management.user_accounts.handleProgramCohortDialogDeselection = function (event) {
    var target = ilios.utilities.getEventTarget(event);
    var i, n;

    if (target.tagName.toLowerCase() === 'li') {
        var model = target.iliosModel;

        var inserted = false;
        var parentTreeNode = target.parentTreeNode;
        if (! parentTreeNode) {
            return false;
        }
        var children = target.parentTreeNode.children;
        var removedLabel = target.removedTreeNode.label;
        var selectedCohorts = ilios.management.user_accounts.programCohortWorkingArray;

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

        ilios.management.user_accounts.programCohortSelectListElement.removeChild(target);
        ilios.management.user_accounts.programCohortDialogTreeView.draw();
        return false;
    }

    return true;
};

// @private
ilios.management.user_accounts.addProgramCohortToSelection = function (programCohortModel) {
    var liElement = document.createElement('li');
    var textNode = document.createTextNode(programCohortModel.getDisplayText());

    liElement.appendChild(textNode);
    liElement.iliosModel = programCohortModel;

    ilios.management.user_accounts.programCohortSelectListElement.appendChild(liElement);

    return liElement;
};

// @private
ilios.management.user_accounts.findAndRemoveProgramCohortNodeForModel = function (programCohortModel, liElement) {
    var rootNode = ilios.management.user_accounts.programCohortDialogTreeView.getRoot();
    var nodeToPop = ilios.management.user_accounts.recursiveNodeSearchForModel(rootNode, programCohortModel);

    if (nodeToPop != null) {
        if (liElement) {
            liElement.parentTreeNode = nodeToPop.parent;
            liElement.removedTreeNode = nodeToPop;
        }

        ilios.management.user_accounts.programCohortDialogTreeView.popNode(nodeToPop);
        ilios.management.user_accounts.programCohortDialogTreeView.draw();
    }
};

// @private
ilios.management.user_accounts.recursiveNodeSearchForModel = function (node, programCohortModel) {
    var children = null;
    var len = 0;
    var rhett = null;

    if ((node.iliosModel != null) && (node.iliosModel.compareTo(programCohortModel) == 0)) {
        return node;
    }

    children = node.children;
    len = children.length;
    for (var i = 0; i < len; i++) {
        rhett = ilios.management.user_accounts.recursiveNodeSearchForModel(children[i], programCohortModel);

        if (rhett != null) {
            return rhett;
        }
    }

    return rhett;
};

ilios.management.user_accounts.programCohortDialogTreeDOMGenerator = function (parentDivId) {
    if (ilios.management.user_accounts.programCohortDialogTreeView != null) {
        ilios.management.user_accounts.programCohortDialogTreeView.destroy();

        ilios.management.user_accounts.programCohortDialogTreeView = null;

        ilios.management.user_accounts.subscribedToPCLabelClick = false;
    }

    ilios.management.user_accounts.programCohortDialogTreeView = new YAHOO.widget.TreeView(parentDivId);
};


// @private
ilios.management.user_accounts.finishProgramCohortSelectionDialogDisplay = function (dialog) {
    var liElement = null;
    var yElement = new YAHOO.util.Element(document.getElementById('steward_indeterminate_div'));
    var secondaryCohorts = ilios.management.user_accounts.currentUserModel.getSecondaryCohorts();
    var primaryCohort = ilios.management.user_accounts.currentUserModel.getPrimaryCohort();
    var i, n;

    // create a shallow copy of the course's program cohorts
    ilios.management.user_accounts.programCohortWorkingArray = secondaryCohorts.slice(0);

    ilios.utilities.removeAllChildren(ilios.management.user_accounts.programCohortSelectListElement);
    for (i = 0, n = ilios.management.user_accounts.programCohortWorkingArray.length; i < n; i++) {
        liElement = ilios.management.user_accounts.addProgramCohortToSelection(ilios.management.user_accounts.programCohortWorkingArray[i]);
        ilios.management.user_accounts.findAndRemoveProgramCohortNodeForModel(ilios.management.user_accounts.programCohortWorkingArray[i], liElement);
    }
    // remove primary cohort from tree of available cohorts
    if (primaryCohort) {
        ilios.management.user_accounts.findAndRemoveProgramCohortNodeForModel(primaryCohort);
    }

    yElement.setStyle('display', 'none');
};

ilios.management.user_accounts.secondaryProgramCohortSelectionDialogDisplay = function (dialog) {
    var yElement = new YAHOO.util.Element(document.getElementById('steward_indeterminate_div'));

    if (ilios.management.user_accounts.programCohortSelectListElement == null) {
        ilios.management.user_accounts.programCohortSelectListElement = document.getElementById(dialog.selectListId);
    }

    yElement.setStyle('display', 'block');

    ilios.management.user_accounts.handleProgramCohortSelectionDialogDisplay(dialog,
        ilios.management.user_accounts.finishProgramCohortSelectionDialogDisplay);
};

ilios.management.user_accounts.handleProgramCohortSelectionDialogDisplay = function (dialog, additionalHandler) {
    var TextNode = YAHOO.widget.TextNode;
    var rootNode = ilios.management.user_accounts.programCohortDialogTreeView.getRoot();
    var schoolCohorts = ilios.management.schoolCohorts;
    var schoolDto, cohortDto, cohortModel;
    var parentNode;
    var i, j, m, n;

    ilios.management.user_accounts.programCohortDialogTreeView.removeChildren(rootNode);

    for (i = 0, m = schoolCohorts.length; i < m; i++) {
        schoolDto = schoolCohorts[i];
        parentNode = new TextNode(schoolDto.school_title, rootNode, false);
        for (j = 0, n = schoolDto.program_cohorts.length; j < n; j++) {
            cohortDto = schoolDto.program_cohorts[j];
            cohortModel = new SchoolProgramCohortModel(
                cohortDto.program_title, cohortDto.cohort_title,
                cohortDto.cohort_id, cohortDto.py_id,
                cohortDto.start_year, cohortDto.owning_school_id);
            cohortNode = new TextNode(cohortModel.getDisplayText(), parentNode, false);
            cohortNode.parentDialog = dialog;
            cohortNode.iliosModel = cohortModel;
        }
    }

    if (! ilios.management.user_accounts.subscribedToPCLabelClick) {
        ilios.management.user_accounts.programCohortDialogTreeView.subscribe("clickEvent",
            ilios.management.user_accounts.handleProgramCohortDialogSelection);
        ilios.management.user_accounts.subscribedToPCLabelClick = true;
    }

    ilios.management.user_accounts.programCohortDialogTreeView.draw();

    if (additionalHandler != null) {
        additionalHandler(dialog);
    }
};


/*
 * This will get messaged when the user clicks the submit button on the dialog (this
 *      button is currently display-text'd as "Done")
 *
 * @see ilios.dom.generateGenericDialogMarkupAndWireContent
 */
ilios.management.user_accounts.handleProgramCohortDialogSubmit = function () {
    ilios.management.user_accounts.currentUserModel.setSecondaryCohorts(
        ilios.management.user_accounts.programCohortWorkingArray);
    // KLUGDE!
    // ham-fistedly update the display of secondary cohorts in the user account details view.
    // ideally, there would be some event listener subscribed to the user model
    // that gets invoked and refreshes the display when the secondary cohorts are updated.
    // however, this is currently not possible since we do not have a clean path
    // to unregister such a listener when we are done modifying a specific model.
    // @todo refactor when we revamp the user administration console
    ilios.management.user_accounts.populateUserSecondaryCohortsDisplay(
        ilios.management.user_accounts.currentUserModel, 'ua_account_secondary_cohorts');
    // cleanup
    ilios.management.user_accounts.programCohortWorkingArray = null;
};
