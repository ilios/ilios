/**
 * DEPENDENCIES:
 *      YUI lib (at least YAHOO.widget and its dependencies)
 *      scripts/ilios_dom.js
 *      scripts/ilios_utilities.js
 *      group_manager_dom.js
 *      group_model.js
 */

ilios.namespace('gm.mm');


ilios.gm.mm.ugtDialogTreeView = null;
ilios.gm.mm.subscribedToUGTLabelClick = false;

ilios.gm.mm.ugtDisplayModel = null; // this will be a group representation of the unselected
ilios.gm.mm.ugtSelectedModel = null; // this will be a group representation of the selected

ilios.gm.mm.selectedItemListElement = null;

// this will be set as part of the click action which requests the dialog to be shown
ilios.gm.mm.managedContainerNumber = 0;

// defined below after its dependent function is declared within the namespace
ilios.gm.mm.ugtDataSource = null;

/*
 * Invoked on both user-group member dialog show, and query forming for autocomplete for that dialog
 */
ilios.gm.mm.resetUserGroupTree = function (dialog) {
    // the case for a dialog panel display; not the case for an updated query generation
    var shouldResetSelectedModel = (dialog != null);

    if (ilios.gm.mm.ugtDialogTreeView != null) {
        var rootNode = ilios.gm.mm.ugtDialogTreeView.getRoot();

        ilios.gm.mm.ugtDialogTreeView.removeChildren(rootNode);
    }


    if (ilios.gm.mm.selectedItemListElement == null) {
        ilios.gm.mm.selectedItemListElement = document.getElementById(dialog.selectListId);
    }
    else if (shouldResetSelectedModel) {
        ilios.utilities.removeAllChildren(ilios.gm.mm.selectedItemListElement);
    }

    if (ilios.gm.mm.ugtDisplayModel != null) {
        ilios.gm.mm.ugtDisplayModel.destroy();

        ilios.gm.mm.ugtDisplayModel = null;

        if (shouldResetSelectedModel && (ilios.gm.mm.ugtSelectedModel != null)) {
            // the case for a dialog panel display; not the case for a query generation
            ilios.gm.mm.ugtSelectedModel.destroy();

            ilios.gm.mm.ugtSelectedModel = null;
        }
    }

    ilios.gm.mm.ugtDisplayModel = new GroupModel();

    if (shouldResetSelectedModel) {
        var containerNumber = dialog.containerNumber;
        var groupModel = ilios.gm.currentRootViewGroup.getSubgroupForContainerNumber(containerNumber);
        var anArray = groupModel.getSubgroups();
        var model = null;
        var liElement = null;
        var textNode = null;
        var yElement = null;
        var key = null;
        var children = null;
        var i, n;
        var sortingArray = [];

        ilios.gm.mm.ugtSelectedModel = new GroupModel();
        ilios.gm.mm.ugtSelectedModel.setDBId(groupModel.getDBId());
        ilios.gm.mm.ugtSelectedModel.setTitle(groupModel.getTitle());

        for (key in anArray) {
            model = anArray[key].clone();

            ilios.gm.mm.ugtSelectedModel.addSubgroup(model);

            liElement = document.createElement('li');
            yElement = new YAHOO.util.Element(liElement);
            yElement.addClass('tree_group_name');
            yElement.setStyle('cursor', 'default');
            yElement.setStyle('list-style', 'none inside url("")');
            yElement.setStyle('margin-bottom', '3px');

            textNode = document.createTextNode(model.getTitle());
            liElement.appendChild(textNode);

            liElement.iliosModel = model;
            liElement.isGroup = true;

            ilios.gm.mm.selectedItemListElement.appendChild(liElement);
        }

        anArray = groupModel.getUsers();
        for (key in anArray) {
            model = anArray[key].clone();

            ilios.gm.mm.ugtSelectedModel.addUser(model);

            liElement = document.createElement('li');
            liElement.setAttribute('title', model.getEmailAddress());

            textNode
             = document.createTextNode(model.getFormattedName(ilios.utilities.USER_NAME_FORMAT_LAST_FIRST));
            liElement.appendChild(textNode);

            liElement.iliosModel = model;

            ilios.gm.mm.selectedItemListElement.appendChild(liElement);
        }

        //
        // sort list items
        //
        children = ilios.gm.mm.selectedItemListElement.children;
        for (i = 0, n = children.length; i < n; i++) {
            sortingArray.push(children[i]);
        }

        sortingArray.sort(function(a, b) {
            var Dom = YAHOO.util.Dom;
            // figure out if the given list element represents a group or user
            // by checking its class attribute
            var aIsGroup = Dom.hasClass(a, 'tree_group_name');
            var bIsGroup = Dom.hasClass(b, 'tree_group_name');

            if (! (aIsGroup ^ bIsGroup)) { // compare group-to-group or user-to-user
                return (a.innerHTML.localeCompare(b.innerHTML));
            } else {
                return (aIsGroup ? -1 : 1); // sort groups before users
            }
        });

        ilios.utilities.removeAllChildren(ilios.gm.mm.selectedItemListElement);
        for (i = 0, n = sortingArray.length; i < n; i++) {
            ilios.gm.mm.selectedItemListElement.appendChild(sortingArray[i]);
        }
    }
};

/*
 * This is used with the YUI FunctionDataSource which is handed to the autocompleter in the
 *  Manage Members panel
 *
 * @private
 */
ilios.gm.mm.buildUGTDataSourceReturnForQuery = function (queryString) {
    var topLevelModel = ilios.gm.mm.getTopLevelGroupForCurrentRootView();
    var rhett = null;

    if (topLevelModel == ilios.gm.currentModel.getRootGroup()) {
        topLevelModel
                = ilios.gm.currentModel.getRootGroup()
                                 .getSubgroupForContainerNumber(ilios.gm.mm.managedContainerNumber);
    }

    rhett = ilios.gm.mm.recursivelyMatchForDataSource(queryString, topLevelModel);

    rhett = ilios.gm.mm.appendPostCreationUserAdditions(rhett, topLevelModel);

    ilios.gm.mm.resetUserGroupTree(null);

    return rhett;
};

ilios.gm.mm.ugtDataSource
                  = new YAHOO.util.FunctionDataSource(ilios.gm.mm.buildUGTDataSourceReturnForQuery);

/*
 * If users were added to this cohort after a top level group was created, that top level group
 *  will not have those users added to it. We append them here in order to make them available
 *  for selection.
 *
 * @private
 */
ilios.gm.mm.appendPostCreationUserAdditions = function (originalArray, topLevelModel) {
    var users = ilios.gm.currentModel.getRootGroup().getUsers();
    var model = null;

    for (var key in users) {
        model = users[key];

        if (! topLevelModel.treeContainsUser(model)) {
            originalArray.push(model.getAsHodgePodgeObject());
        }
    }

    return originalArray;
};

// @private
ilios.gm.mm.recursivelyMatchForDataSource = function (queryString, groupModel) {
    var rhett = new Array();
    var users = groupModel.getUsers();
    var subgroups = groupModel.getSubgroups();
    var model = null;
    var matchObject = null;
    var matchedSubgroupUsers = null;
    var key = null;

    for (key in users) {
        model = users[key];

        if ((model.getLastName().indexOf(queryString) != -1)
                || (model.getFirstName().indexOf(queryString) != -1)
                || (model.getMiddleName().indexOf(queryString) != -1)) {
            rhett.push(model.getAsHodgePodgeObject());
        }
    }

    for (key in subgroups) {
        model = subgroups[key];
        matchedSubgroupUsers = ilios.gm.mm.recursivelyMatchForDataSource(queryString, model);

        if (matchedSubgroupUsers.length > 0) {
            matchObject = new Object();

            matchObject.title = model.getTitle();
            matchObject.group_id = model.getDBId();
            matchObject.matched = matchedSubgroupUsers;

            rhett.push(matchObject);
        }
    }

    return rhett;
};

ilios.gm.mm.userGroupTreeDOMContentGenerator = function (parentDivId) {
    if (ilios.gm.mm.ugtDialogTreeView != null) {
        ilios.gm.mm.ugtDialogTreeView.destroy();

        ilios.gm.mm.ugtDialogTreeView = null;

        ilios.gm.mm.subscribedToUGTLabelClick = false;
    }

    if (ilios.gm.mm.ugtDisplayModel != null) {
        ilios.gm.mm.ugtDisplayModel.destroy();
        ilios.gm.mm.ugtSelectedModel.destroy();

        ilios.gm.mm.ugtDisplayModel = null;
        ilios.gm.mm.ugtSelectedModel = null;
    }

    ilios.gm.mm.ugtDialogTreeView = new YAHOO.widget.TreeView(parentDivId);

    ilios.gm.mm.ugtDisplayModel = new GroupModel();
    ilios.gm.mm.ugtSelectedModel = new GroupModel();
};

// @private
ilios.gm.mm.augmentedLIElementsContainMatchingModel = function(selectedList,
                                                               candidateStudentModel) {
    var liElements = selectedList.childNodes;
    var len = (liElements != null) ? liElements.length : -1;
    var i = 0;
    var liModel = null;

    for (; i < len; i++) {
        liModel = liElements[i].iliosModel;

        if (liModel != null) {
            if (liModel instanceof GroupModel) {
                if (liModel.containsUser(candidateStudentModel)) {
                    return true;
                }
            }
            else if (liModel.compareTo(candidateStudentModel) == 0) {
                return true;
            }
        }
    }

    return false;
};

// @private
ilios.gm.mm.userGroupTreeFilterResults = function (queryString, fullResponse, parsedResponse,
                                                   callback, autoCompleter, idUniquer) {
    var managedModel
           = ilios.gm.currentRootViewGroup.getSubgroupForContainerNumber(
                                                            ilios.gm.mm.managedContainerNumber);
    var len = parsedResponse.results.length;
    var selectedList = document.getElementById(autoCompleter.target);
    var filteredResults = new Array();
    var i = 0;
    var studentModel = null;

    for (; i < len; i++) {
        if (parsedResponse.results[i].user_id != null) {
            studentModel = new StudentModel(parsedResponse.results[i]);

            if (! ilios.gm.mm.augmentedLIElementsContainMatchingModel(selectedList, studentModel)) {
                filteredResults.push(parsedResponse.results[i]);
            }
        }
        else if (parsedResponse.results[i].group_id != managedModel.getDBId()) {
            filteredResults.push(parsedResponse.results[i]);
        }
    }


    filteredResults.sort(ilios.gm.mm.sortFilteredResults);
    parsedResponse.results = filteredResults;

    return parsedResponse;
};

// We reverse the order of these because the yahoo autocomplete results appear to be
//      delivered in reverse array order.. :- /
// @private
ilios.gm.mm.sortFilteredResults = function (obj1, obj2) {
    var firstIsStudentModel = ((obj1 instanceof StudentModel) || (obj1.email != null));

    if (firstIsStudentModel != ((obj2 instanceof StudentModel) || (obj2.email != null))) {
        return firstIsStudentModel ? 1 : -1;
    }
    else if (firstIsStudentModel) {
        var studentModel1 = (obj1 instanceof StudentModel) ? obj1 : new StudentModel(obj1);
        var studentModel2 = (obj2 instanceof StudentModel) ? obj2 : new StudentModel(obj2);

        return studentModel2.compareTo(studentModel1);
    }
    else {
        if (! obj1.hasBeenSorted) {
            obj1.matched.sort(ilios.gm.mm.sortFilteredResults);

            obj1.hasBeenSorted = true;
        }

        if (! obj2.hasBeenSorted) {
            obj2.matched.sort(ilios.gm.mm.sortFilteredResults);

            obj2.hasBeenSorted = true;
        }

        return obj2.title.localeCompare(obj1.title);
    }
};

ilios.gm.mm.userGroupTreeHandleAutoCompleteResults = function (resultDataObject, queryString,
                                                               resultMatch, autoCompleter,
                                                               idUniquer) {
    var canAddObject = true;

    if ((! ilios.gm.subgroup.isPresentlyCollapsed) && (resultDataObject.group_id != null)) {
        var model
            = ilios.gm.currentModel.getRootGroup()
                                .getSubgroupForContainerNumber(ilios.gm.mm.managedContainerNumber);

        canAddObject = (model.getDBId() == resultDataObject.group_id);
    }

    if (canAddObject) {
        ilios.gm.mm.addDataSourceObjectToNode(resultDataObject,
                                              ilios.gm.mm.ugtDialogTreeView.getRoot(),
                                              ilios.gm.mm.ugtDisplayModel);
    }
};

// @private
ilios.gm.mm.addDataSourceObjectToNode = function (dataSourceObject, parentNode, parentModel) {
    var TextNode = YAHOO.widget.TextNode;
    var groupId = dataSourceObject.group_id;
    var node = null;
    var model = null;

    if (groupId != null) {
        var matchedChildren = dataSourceObject.matched;

        node = new TextNode(dataSourceObject.title, parentNode, false);

        model = new GroupModel();
        model.setTitle(dataSourceObject.title);
        model.setDBId(groupId);

        parentModel.addSubgroup(model);

        node.iliosModel = model;
        node.isGroup = true;

        for (var key in matchedChildren) {
            ilios.gm.mm.addDataSourceObjectToNode(matchedChildren[key], node, model);
        }
    } else {
        var nodeAttributes = null;

        model = new StudentModel(dataSourceObject);

        nodeAttributes = {
            label: model.getFormattedName(ilios.utilities.USER_NAME_FORMAT_LAST_FIRST),
            title: model.getEmailAddress()
        };

        parentModel.addUser(model);

        node = new TextNode(nodeAttributes, parentNode, false);

        // style inactive users differently
        if (! model.isEnabled()) {
            node.labelStyle = node.labelStyle + ' group_selection_dialog_inactive_user';
        }
        node.iliosModel = model;
    }
};

ilios.gm.mm.userGroupTreeFinishedPopulation = function (type, args) {
    if (ilios.gm.mm.ugtDialogTreeView != null) {
        if (! ilios.gm.mm.subscribedToUGTLabelClick) {
            ilios.gm.mm.ugtDialogTreeView.subscribe("clickEvent",
                                                    ilios.gm.mm.handleUserGroupTreeSelection);

            ilios.gm.mm.subscribedToUGTLabelClick = true;
        }

        ilios.gm.mm.redrawTreeAndUpdateCSS();
    }
};

// we can't affect the DOM portion of the nodes until after they're rendered
// @private
ilios.gm.mm.redrawTreeAndUpdateCSS = function () {
    if (ilios.gm.mm.ugtDialogTreeView != null) {
        var groupNodes = null;

        ilios.gm.mm.ugtDialogTreeView.draw();

        groupNodes = ilios.gm.mm.ugtDialogTreeView.getNodesByProperty('isGroup', true);
        if (groupNodes != null) {
            var len = groupNodes.length;
            var content = null;
            var yElement = null;

            for (var i = 0; i < len; i++) {
                content = groupNodes[i].getContentEl();

                // null in the case that the node has been collapsed away out of view
                if (content != null) {
                    yElement = new YAHOO.util.Element(content);
                    yElement.addClass('tree_group_name');
                    yElement.setStyle('cursor', 'default');
                    yElement.setStyle('list-style', 'none inside url("")');
                    yElement.setStyle('margin-bottom', '3px');
                }
            }
        }
    }
};

// @private
ilios.gm.mm.handleUserGroupTreeSelection = function (clickObject) {
    var node = clickObject.node;
    var newLI = null;
    var textNode = null;
    var model = node.iliosModel;

    // per Sascha discussions on 24.mar, groups are no longer selectable items -- if
    //      this decision stands long term, this method can get cleaned up below as to
    //      how it is handling the group selection; for the moment we just exit the
    //      method early if a group has been selected.
    if (node.isGroup) {
        return false;
    }

    newLI = document.createElement('li');

    newLI.iliosModel = model;
    newLI.oldTreeNode = node;

    // TODO this is a big problem when the sibling or whatever has subsequently been
    //          removed from the tree - so for the time being we're just sticking
    //          it back in the parent where-ever (where 'where-ever' is at the end)
//  if (node.nextSibling != null) {
//      newLI.previousNextSibling = node.nextSibling;
//  }
//  else if (node.previousSibling != null) {
//      newLI.previousPreviousSibling = node.previousSibling;
//  }
//  else {
        newLI.previousParentNode = node.parent;
//  }

    if (node.isGroup) {
        var yElement = new YAHOO.util.Element(liElement);

        yElement.addClass('tree_group_name');
        yElement.setStyle('cursor', 'default');
        yElement.setStyle('list-style', 'none inside url("")');
        yElement.setStyle('margin-bottom', '3px');

        newLI.isGroup = true;

        textNode = document.createTextNode(model.getTitle());

        ilios.gm.mm.ugtSelectedModel.addSubgroup(model);
    }
    else {
        newLI.setAttribute('title', model.getEmailAddress());

        textNode = document.createTextNode(model.getFormattedName(ilios.utilities.USER_NAME_FORMAT_LAST_FIRST));

        ilios.gm.mm.ugtSelectedModel.addUser(model);
    }

    newLI.appendChild(textNode);
    ilios.gm.mm.selectedItemListElement.appendChild(newLI);

    ilios.gm.mm.ugtDialogTreeView.popNode(node);
    ilios.gm.mm.redrawTreeAndUpdateCSS();

    // returning false prevents the default action of a click (setting focus, etc)
    //              which would cause an error since we just got rid of the node
    //              which was clicked upon
    return false;
};

ilios.gm.mm.deselectionHandler = function (event) {
    var target = ilios.utilities.getEventTarget(event);

    if (target.tagName.toLowerCase() === 'li') {
        var model = target.iliosModel;

        // disallow node removal from the selected list if it represents a group
        if (target.isGroup) {
            return false;
        }

        // TODO this is a big problem when the sibling or whatever has subsequently been
        //          removed from the tree - so for the time being we're just sticking
        //          it back in the parent where-ever (where 'where-ever' is at the end)
        if (target.oldTreeNode != null) {   // got on list by user click in the tree
//          if (target.previousNextSibling != null) {
//              target.oldTreeNode.insertBefore(target.previousNextSibling);
//          }
//          else if (target.previousPreviousSibling != null) {
//              target.oldTreeNode.insertAfter(target.previousPreviousSibling);
//          }
//          else {
                target.oldTreeNode.appendTo(target.previousParentNode);
//          }
        } else {
            var rootNode = ilios.gm.mm.ugtDialogTreeView.getRoot();
            var newNode = new YAHOO.widget.TextNode(
                model.getFormattedName(ilios.utilities.USER_NAME_FORMAT_LAST_FIRST), rootNode, false);

            newNode.iliosModel = model;
        }

        ilios.gm.mm.redrawTreeAndUpdateCSS();

        if (target.isGroup) {
            ilios.gm.mm.ugtSelectedModel.removeSubgroupForStub(model);
        }
        else {
            ilios.gm.mm.ugtSelectedModel.removeUser(model);
        }
    }

    return true;
};

/*
 * This method returns the top level group which is, or parents, the current root view group.
 */
// @private
ilios.gm.mm.getTopLevelGroupForCurrentRootView = function () {
    var rhett = ilios.gm.currentRootViewGroup;
    var parent = rhett.getParentGroup();

    while ((parent != null) && (parent != ilios.gm.currentModel.getRootGroup())) {
        rhett = parent;
        parent = rhett.getParentGroup();
    }

    return rhett;
};

/*
 * This will get messaged when the user clicks the submit button on the dialog (this
 *      button is currently display-text'd as "Done")
 *
 * Given model states A and A' (before and after), delta(A, A') may produce one or more of the
 *      following:
 *              + users:        these users came from where? some other group (root or otherwise)
 *                                      the problem here is that we've lost the parentage when they
 *                                      were added to the dialog selection group model.
 *              - users:        these users must be obtained from A and added to the root group
 *                                      as part of the commit process.
 *
 * @see ilios.dom.generateGenericDialogMarkupAndWireContent
 */
ilios.gm.mm.ugtSubmitMethod = function () {
    var containerNumber = this.containerNumber; // 'this' should be the Dialog instance
    var rootGroup = ilios.gm.currentRootViewGroup;
    var groupModel = rootGroup.getSubgroupForContainerNumber(containerNumber);

    if (groupModel.compareTo(ilios.gm.mm.ugtSelectedModel) != 0) {
        var usersDeleted = groupModel.getUserDiffFromModel(ilios.gm.mm.ugtSelectedModel);
        var usersAdded = ilios.gm.mm.ugtSelectedModel.getUserDiffFromModel(groupModel);
        var userModel = null;
        var topLevelGroup = ilios.gm.mm.getTopLevelGroupForCurrentRootView();
        var key = null;

        // adjust other parentage
        for (key in usersAdded) {
            userModel = usersAdded[key];

            topLevelGroup.removeUserFromTree(userModel);
        }

        // deleted users get added to the root group
        for (key in usersDeleted) {
            rootGroup.addUser(usersDeleted[key]);
        }

        groupModel.replaceContentWithModel(ilios.gm.mm.ugtSelectedModel, true);
        ilios.gm.mm.ugtSelectedModel = null;
    }
};
