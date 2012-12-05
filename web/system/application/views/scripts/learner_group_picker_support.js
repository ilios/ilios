/**
 * DEPENDENCY: YUI lib (at least YAHOO.util and its dependencies)
 * DEPENDENCY: scripts/ilios_ui.js
 * DEPENDENCY: scripts/ilios_utilities.js
 */

ilios.namespace('lg.picker');

// These three should be overridden if the DOM has a different elements to display these
//
    // these values are used in constructOrResetTreeViews()
ilios.lg.picker.learnerTreeDivId = 'learner_tree_view_div';
ilios.lg.picker.selectedLearnerTreeDivId = 'selected_learner_tree_view_div';
    // this value is used in ilios.lg.picker.updateLearnerTextField()
ilios.lg.picker.learnerTextFieldId = 'learner_list_lightbox_textfield';


ilios.lg.picker.learnerTreeView = null;
ilios.lg.picker.selectedLearnerTreeView = null;

ilios.lg.picker.learnerTreeModel = new Array();

ilios.lg.picker.constructOrResetLearnerTreeViews = function (selectedModels) {
    if (ilios.lg.picker.learnerTreeView == null) {
        ilios.lg.picker.learnerTreeView
                                    = new YAHOO.widget.TreeView(ilios.lg.picker.learnerTreeDivId);

        ilios.lg.picker.learnerTreeView.subscribe("clickEvent",
                                                  ilios.lg.picker.handleLearnerTreeClick);
    }

    if (ilios.lg.picker.selectedLearnerTreeView == null) {
        ilios.lg.picker.selectedLearnerTreeView
                            = new YAHOO.widget.TreeView(ilios.lg.picker.selectedLearnerTreeDivId);

        ilios.lg.picker.selectedLearnerTreeView.subscribe("clickEvent",
                                                          ilios.lg.picker.handleLearnerTreeDeselection);
    }
    else {
        var rootNode = ilios.lg.picker.selectedLearnerTreeView.getRoot();

        ilios.lg.picker.selectedLearnerTreeView.removeChildren(rootNode);
    }

    if (typeof selectedModels == 'undefined') {
        ilios.lg.picker.setUserSelectedStatusOnLeanerTree(ilios.lg.picker.learnerTreeModel, false);
    }
    else {
        ilios.lg.picker.selectLearnerModelsWithinTree(ilios.lg.picker.learnerTreeModel,
                                                      selectedModels);

        ilios.lg.picker.reconstructSelectedTree();
    }
    ilios.lg.picker.selectedLearnerTreeView.draw();

    ilios.lg.picker.reconstructLearnerTree();
    ilios.lg.picker.learnerTreeView.draw();
};

/*
 * This method populates the read-only UI text field representing the learners list with the
 *  contents of the current in-edit offering model.
 */
ilios.lg.picker.updateLearnerTextField = function () {
    var element = document.getElementById(ilios.lg.picker.learnerTextFieldId);
    var selectedModels = ilios.lg.picker.getSelectedLearnerModels();
    var text = '';

    for (var key in selectedModels) {
        if (text.length > 0) {
            text += '; ';
        }

        text += selectedModels[key].getGroupTitle();
    }

    element.innerHTML = text;
};

/**
 * Returns selected learner groups from the pool of available learner group trees.
 * @method getSelectedLearnerModels
 * @return {Array} a list of learner groups
 */
ilios.lg.picker.getSelectedLearnerModels = function () {
    var rhett = [];

    ilios.lg.picker.getSelectedLearnerModelsFromTree(ilios.lg.picker.learnerTreeModel, rhett);

    return rhett;
};

// @private
ilios.lg.picker.pruneTreeOfEmptyNodes = function (owningTree, parentNode) {
    var superNode = (parentNode == null) ? owningTree.getRoot() : parentNode;
    var children = superNode.children;
    var i = 0;
    var len = children.length;
    var node = null;
    var removeNode = false;

    for (; i < len; i++) {
        node = children[i];

        if (node.children.length > 0) {
            ilios.lg.picker.pruneTreeOfEmptyNodes(owningTree, node);
        }

        // to other distracted developers: this should NOT be 'else if (node.children.length == ...'
        if (node.children.length == 0) {
            if (node.iliosModel instanceof SimplifiedGroupModel) {
                removeNode = (ilios.utilities.arraySize(node.iliosModel.getSubgroups()) > 0);
            }
            else if (node.iliosModel != null) {
                removeNode = (ilios.utilities.arraySize(node.iliosModel.groups) > 0);
            }
            else {
                removeNode = true;
            }

            if (removeNode) {
                // TODO - if crashy-crash here, hold on to the "to remove nodes" until after
                //              the for loop
                owningTree.removeNode(node);
            }
        }
    }
};

// @private
ilios.lg.picker.reconstructSelectedTree = function () {
    var rootNode = ilios.lg.picker.selectedLearnerTreeView.getRoot();

    ilios.lg.picker.selectedLearnerTreeView.removeChildren(rootNode);

    ilios.lg.picker.buildTreeFromModel(rootNode, ilios.lg.picker.learnerTreeModel, true);

    ilios.lg.picker.pruneTreeOfEmptyNodes(ilios.lg.picker.selectedLearnerTreeView, null);

    ilios.lg.picker.selectedLearnerTreeView.render();
};

// @private
ilios.lg.picker.reconstructLearnerTree = function () {
    var rootNode = ilios.lg.picker.learnerTreeView.getRoot();

    ilios.lg.picker.learnerTreeView.removeChildren(rootNode);

    ilios.lg.picker.buildTreeFromModel(rootNode, ilios.lg.picker.learnerTreeModel, false);

    ilios.lg.picker.pruneTreeOfEmptyNodes(ilios.lg.picker.learnerTreeView, null);

    ilios.lg.picker.learnerTreeView.getRoot().collapseAll();
};

// @private
ilios.lg.picker.buildTreeFromModel = function (parentNode, subgroups, selectStatus) {
    var TextNode = YAHOO.widget.TextNode;
    var i = 0;
    var len = subgroups.length;
    var node = null;
    var model = null;

    for (; i < len; i++) {
        model = subgroups[i];

        if (model instanceof SimplifiedGroupModel) {
            if ((model.userSelected == selectStatus) || (ilios.utilities.arraySize(model.getSubgroups()) > 0)) {
                node = new TextNode(model.getGroupTitle(), parentNode, true);
                node.iliosModel = model;

                ilios.lg.picker.buildTreeFromModel(node, model.getSubgroups(), selectStatus);
            }
        } else {
            node = new TextNode(model.program_title, parentNode, true);

            ilios.lg.picker.buildTreeFromModel(node, model.groups, selectStatus);
        }
    }
};

// @private
ilios.lg.picker.setUserSelectedStatusOnLeanerTree = function (rootModel, flag) {
    var iterableSet = null;

    // MAY RETURN THIS BLOCK
    if (rootModel == null) {
        return;
    }

    if (rootModel instanceof SimplifiedGroupModel) {
        rootModel.userSelected = flag;

        iterableSet = rootModel.getSubgroups();
    }
    else if (rootModel.groups != null) {
        iterableSet = rootModel.groups;
    }
    else {
        iterableSet = rootModel;
    }

    for (var key in iterableSet) {
        ilios.lg.picker.setUserSelectedStatusOnLeanerTree(iterableSet[key], flag);
    }
};

// @private
// optimize: better traversal / selection check strategy
ilios.lg.picker.selectLearnerModelsWithinTree = function (rootModel, selectionGroup) {
    var iterableSet = null;

    if (rootModel instanceof SimplifiedGroupModel) {
        var flag = false;

        for (var key in selectionGroup) {
            if (selectionGroup[key].getDBId() == rootModel.getDBId()) {
                flag = true;

                break;
            }
        }
        rootModel.userSelected = flag;

        iterableSet = rootModel.getSubgroups();
    }
    else if (rootModel.groups != null) {
        iterableSet = rootModel.groups;
    }
    else {
        iterableSet = rootModel;
    }

    for (var key in iterableSet) {
        ilios.lg.picker.selectLearnerModelsWithinTree(iterableSet[key], selectionGroup);
    }
};

/**
 * Traverses a given array of learner-group trees and extracts the selected learner groups within.
 *
 * ACHTUNG!
 * The selected learner groups are extracted into a "flat" structure, no parent/child
 * relationship between selected groups and their respective selected sub-groups are expressed
 * in the structure of the result set.
 *
 * Example:
 * In a given group tree A -> B -> (C, D), group B and its subgroups C and D are selected.
 * This function would extract groups B, C and D; and return them in an array [B, C, D].
 *
 * @method getSelectedLearnerModelsFromTree
 * @param {Array} subgroups the list of groups to check
 * @param {Array} selected list of extracted selected learner groups
 * @private
 * @todo replace second "in/out" parameter with a function return value
 */
ilios.lg.picker.getSelectedLearnerModelsFromTree = function (subgroups, selected) {
    var node = null;
    var model = null;
    var i, n;

    for (i = 0, n = subgroups.length; i < n; i++) {
        model = subgroups[i];

        if (model instanceof SimplifiedGroupModel) {
            if (model.userSelected) {
                selected.push(model);
            }
            ilios.lg.picker.getSelectedLearnerModelsFromTree(model.getSubgroups(), selected);
        } else {
            ilios.lg.picker.getSelectedLearnerModelsFromTree(model.groups, selected);
        }
    }
};

/**
 * Traverses a given array of learner groups trees and extracts the selected learner groups within
 * but only the "roots" of each selected branch.
 * Selected sub-groups of a selected groups are not included.
 *
 * @method getSelectedLearnerModelsRootsFromTree
 * @param {Array} subgroups the list of groups to check
 * @return {Array} a list of selected groups
 * @static
 */
ilios.lg.picker.getSelectedLearnerModelsRootsFromTree = function (subgroups) {
    var node = null;
    var model = null;
    var selected = [];
    var i, n, sub;

    for (i = 0, n = subgroups.length; i < n; i++) {
        model = subgroups[i];
        if (model instanceof SimplifiedGroupModel) {
            sub = model.getSubgroups();
            if (model.userSelected) {
                selected.push(model);
            } else {
                selected = selected.concat(ilios.lg.picker.getSelectedLearnerModelsRootsFromTree(sub));
            }
        } else {
            selected = selected.concat(ilios.lg.picker.getSelectedLearnerModelsRootsFromTree(model.groups));
        }
    }
    return selected;
};

/**
 * Traverses a given array of learner groups trees and extracts the selected learner groups within
 * but only the "leaves" of each selected branch.
 * Selected parent-groups are not included.
 *
 * @method getSelectedLearnerModelsLeavesFromTree
 * @param {Array} subgroups the list of groups to check
 * @return {Array} a list of selected groups
 * @static
 */
ilios.lg.picker.getSelectedLearnerModelsLeavesFromTree = function (subgroups) {
    var node = null;
    var model = null;
    var selected = [];
    var i, n, sub;

    for (i = 0, n = subgroups.length; i < n; i++) {
        model = subgroups[i];
        if (model instanceof SimplifiedGroupModel) {
            sub = model.getSubgroups();
            if (model.userSelected && (! sub.length)) {
                selected.push(model);
            } else {
                selected = selected.concat(ilios.lg.picker.getSelectedLearnerModelsLeavesFromTree(sub));
            }
        } else {
            selected = selected.concat(ilios.lg.picker.getSelectedLearnerModelsLeavesFromTree(model.groups));
        }
    }
    return selected;
};

/*
 * This is messaged when the user selects a student group from the YAHOO tree UI in the lightbox.
 *
 * Should be considered @protected
 */
ilios.lg.picker.handleLearnerTreeClick = function (clickObject) {
    var node = clickObject.node;
    var nodeToPop = node;

    if ((! node.isGroup) && (node.getSiblings() == null)) {
        nodeToPop = node.parent;
    }

    if (node.iliosModel != null) {
        ilios.lg.picker.setUserSelectedStatusOnLeanerTree(node.iliosModel, true);

        ilios.lg.picker.learnerTreeView.popNode(node);
        ilios.lg.picker.learnerTreeView.draw();

        ilios.lg.picker.reconstructSelectedTree();

        ilios.lg.picker.updateLearnerTextField();
    }

    // returning false prevents the default action of a click (setting focus, etc)
    //              which would cause an error since we just got rid of the node
    //              which was clicked upon
    return false;
};

// @protected
ilios.lg.picker.handleLearnerTreeDeselection = function (clickObject) {
    var node = clickObject.node;
    var nodeToPop = node;

    if ((! node.isGroup) && (node.getSiblings() == null)) {
        nodeToPop = node.parent;
    }

    if (node.iliosModel != null) {
        ilios.lg.picker.setUserSelectedStatusOnLeanerTree(node.iliosModel, false);

        ilios.lg.picker.selectedLearnerTreeView.popNode(nodeToPop);
        ilios.lg.picker.selectedLearnerTreeView.draw();

        ilios.lg.picker.reconstructLearnerTree();
        ilios.lg.picker.learnerTreeView.draw();

        ilios.lg.picker.updateLearnerTextField();
    }

    // returning false prevents the default action of a click (setting focus, etc)
    //              which would cause an error since we just got rid of the node
    //              which was clicked upon
    return false;
};
