/**
 * DEPENDENCIES:
 * 		YUI lib (at least YAHOO.widget and its dependencies)
 * 		scripts/ilios_dom.js
 * 		scripts/ilios_utilities.js
 *		program_manager_dom.js
 *		steward_model.js
 */

// This is set after the declaration of the data source function within namespace.
ilios.pm.stewardDataSource = null;

ilios.pm.stewardDialogTreeView = null;
ilios.pm.subscribedToStewardLabelClick = false;

ilios.pm.stewardDialogSelectedTreeView = null;
ilios.pm.subscribedToSelectedStewardLabelClick = false;

ilios.pm.stewardSelectedModels = null;

ilios.pm.stewardAutoCompleter = null;

/*
 * Invoked on both user-group member dialog show, and query forming for that dialog
 */
ilios.pm.resetStewardTree = function (dialog) {
	// the case for a dialog panel display; not the case for an updated query generation
	var shouldResetSelectedModel = (dialog != null);
    var rootNode = null;

	if (ilios.pm.stewardDialogTreeView != null) {
		rootNode = ilios.pm.stewardDialogTreeView.getRoot();

		ilios.pm.stewardDialogTreeView.removeChildren(rootNode);
	}


	if (ilios.pm.stewardAutoCompleter == null) {
		ilios.pm.stewardAutoCompleter = dialog.autoCompleter;
	}
	else if (shouldResetSelectedModel) {
		rootNode = ilios.pm.stewardDialogSelectedTreeView.getRoot();

		ilios.pm.stewardDialogSelectedTreeView.removeChildren(rootNode);
	}

	if (shouldResetSelectedModel && (ilios.pm.stewardSelectedModels != null)) {
		// the case for a dialog panel display; not the case for a query generation
		ilios.pm.stewardSelectedModels = null;
	}

	if (shouldResetSelectedModel) {
		var containerNumber = dialog.containerNumber;
		var programYearModel
				= ilios.pm.currentProgramModel.getProgramYearForContainerNumber(containerNumber);
		var stewardArray = programYearModel.getStewardArray();
		var model = null;
		rootNode = ilios.pm.stewardDialogSelectedTreeView.getRoot();

		ilios.pm.stewardSelectedModels = new Array();

		if (! ilios.pm.subscribedToSelectedStewardLabelClick) {
			ilios.pm.stewardDialogSelectedTreeView.subscribe("clickEvent",
															 ilios.pm.handleStewardTreeDeselection);

			ilios.pm.subscribedToSelectedStewardLabelClick = true;
		}

		for (var key in stewardArray) {
			model = stewardArray[key].clone();

			ilios.pm.stewardSelectedModels.push(model);

			ilios.pm.stewardInsertModelIntoTree(rootNode, model);
		}

		ilios.pm.correctForStewardPlaceHolderNodes(rootNode);

		ilios.pm.redrawStewardTreesAndUpdateCSS();
	}
};

/*
 * This is used with the YUI FunctionDataSource which is handed to the autocompleter in the
 * 	dialog panel; presently there is no autocompletion functionality exposed to the user so the
 * 	only query is the initial blank query done on panel open which should return the entire
 * 	school-department tree.
 */
ilios.pm.buildStewardDataSourceReturnForQuery = function (queryString) {
	return (ilios.pm.schoolDepartmentTreeModel != null) ? ilios.pm.schoolDepartmentTreeModel
														: new Array();
};

ilios.pm.stewardDataSource
				= new YAHOO.util.FunctionDataSource(ilios.pm.buildStewardDataSourceReturnForQuery);

ilios.pm.stewardTreeSelectedDOMContentGenerator = function (parentDivId) {
	if (ilios.pm.stewardDialogSelectedTreeView != null) {
		ilios.pm.stewardDialogSelectedTreeView.destroy();

		ilios.pm.stewardDialogSelectedTreeView = null;

		ilios.pm.subscribedToSelectedStewardLabelClick = false;
	}

	ilios.pm.stewardDialogSelectedTreeView = new YAHOO.widget.TreeView(parentDivId);
};

ilios.pm.stewardTreeDOMContentGenerator = function (parentDivId) {
	if (ilios.pm.stewardDialogTreeView != null) {
		ilios.pm.stewardDialogTreeView.destroy();

		ilios.pm.stewardDialogTreeView = null;

		ilios.pm.subscribedToStewardLabelClick = false;
	}

	if (ilios.pm.stewardSelectedModels != null) {
		ilios.pm.stewardSelectedModels = null;
	}

	ilios.pm.stewardDialogTreeView = new YAHOO.widget.TreeView(parentDivId);

	ilios.pm.stewardSelectedModels = new Array();
};

// @private
ilios.pm.stewardModelIsSelected = function (candidateModel) {
	var model = null;

	for (var i = 0; i < ilios.pm.stewardSelectedModels.length; i++) {
		model = ilios.pm.stewardSelectedModels[i];

		if (model.compareTo(candidateModel) == 0) {
			return true;
		}
		else if (candidateModel.isDepartment() && (! model.isDepartment())) {
			if (model.getDBId() == candidateModel.getParentSchoolId()) {
				return true;
			}
		}
	}

	return false;
};

ilios.pm.stewardTreeFilterResults = function (queryString, fullResponse, parsedResponse, callback,
											  autoCompleter, idUniquer) {
	var len = parsedResponse.results.length;
	var filteredResults = new Array();
	var i = 0;
	var regex = new RegExp(queryString, "gi");
	var model = null;

	ilios.pm.stewardTreeClearer();

	for (; i < len; i++) {
		model = parsedResponse.results[i];

		if ((regex.exec(model.getStewardTitle()) != null)
						&& (! ilios.pm.stewardModelIsSelected(model))) {
			filteredResults.push(model);
		}
	}

	parsedResponse.results = filteredResults;

	if (filteredResults.length == 0) {
		ilios.pm.stewardDialogTreeView.render();
	}

	return parsedResponse;
};

ilios.pm.stewardTreeHandleResults = function (resultDataObject, queryString, resultMatch,
											  autoCompleter, idUniquer) {
	ilios.pm.stewardInsertModelIntoTree(ilios.pm.stewardDialogTreeView.getRoot(), resultDataObject);
};

// @private
ilios.pm.findSchoolNodeForId = function (rootNode, schoolId) {
	var children = rootNode.children;
	var len = (children != null) ? children.length : 0;
	var i = 0;

	for (; i < len; i++) {
		if (children[i].schoolId == schoolId) {
			return children[i];
		}
	}

	return null;
};

// @private
ilios.pm.stewardTreeClearer = function () {
	if (ilios.pm.stewardDialogTreeView != null) {
		var rootNode = ilios.pm.stewardDialogTreeView.getRoot();

		ilios.pm.stewardDialogTreeView.removeChildren(rootNode);
	}
};

ilios.pm.stewardTreeFinishedPopulation = function (type, args) {
	if (ilios.pm.stewardDialogTreeView != null) {
		if (! ilios.pm.subscribedToStewardLabelClick) {
			ilios.pm.stewardDialogTreeView.subscribe("clickEvent",
													 ilios.pm.handleStewardTreeSelection);

			ilios.pm.subscribedToStewardLabelClick = true;
		}

		ilios.pm.correctForStewardPlaceHolderNodes(ilios.pm.stewardDialogTreeView.getRoot());
		ilios.pm.verifyChildlessUnselectedStewardNodes();

		ilios.pm.redrawStewardTreesAndUpdateCSS();

		ilios.pm.stewardDialogTreeView.getRoot().collapseAll();
	}
};

// @private
ilios.pm.correctForStewardPlaceHolderNodes = function (rootNode) {
	var children = rootNode.children;
	var len = (children != null) ? children.length : 0;
	var node = null;
	var model = null;

	for (var i = 0; i < len; i++) {
		node = children[i];

		if (node.label == ilios.pm.placeHolderNodeTitle) {
			model = ilios.pm.getStewardModelForId(node.schoolId);

			node.iliosModel = model;
			node.setUpLabel(model.getStewardTitle());
			node.isSchool = true;
		}
	}
};

// @private
ilios.pm.verifyChildlessUnselectedStewardNodes = function () {
	var rootNode = ilios.pm.stewardDialogTreeView.getRoot();
	var children = rootNode.children;
	var len = (children != null) ? children.length : 0;
	var node = null;
	var departments = null;

	for (var i = 0; i < len; i++) {
		node = children[i];

		if (node.children.length == 0) {
			departments = ilios.pm.getDepartmentsForParentId(node.iliosModel.getDBId());

			if (departments.length > 0) {
				ilios.pm.stewardDialogTreeView.popNode(node);
			}
		}
	}
};

// @private
//we can't affect the DOM portion of the nodes until after they're rendered
ilios.pm.redrawStewardTreesAndUpdateCSS = function () {
	ilios.pm.stewardRedrawTreeAndCSS(ilios.pm.stewardDialogSelectedTreeView);
	ilios.pm.stewardRedrawTreeAndCSS(ilios.pm.stewardDialogTreeView);
};

// @private
ilios.pm.stewardRedrawTreeAndCSS = function (tree) {
	if (tree != null) {
		var schoolNodes = null;

		tree.draw();

		schoolNodes = tree.getNodesByProperty('isSchool', true);
		if (schoolNodes != null) {
			var len = schoolNodes.length;

			for (var i = 0; i < len; i++) {
				(new YAHOO.util.Element(schoolNodes[i].getContentEl())).addClass('tree_group_name');
			}
		}
	}
};

// @private
ilios.pm.handleStewardTreeDeselection = function (clickObject) {
	var node = clickObject.node;
	var modelsToDeselect = new Array();
	var nodeToPop = node;
    var i = 0;

	modelsToDeselect.push(node.iliosModel);

	if (node.isSchool) {
		var children = node.children;

		for (i = 0; i < children.length; i++) {
			modelsToDeselect.push(children[i].iliosModel);
		}
	}
	else if (node.getSiblings() == null) {
		nodeToPop = node.parent;
	}

	for (i = 0; i < modelsToDeselect.length; i++) {
		ilios.utilities.removeElementWithValue(ilios.pm.stewardSelectedModels, modelsToDeselect[i]);
	}

	ilios.pm.stewardDialogSelectedTreeView.popNode(nodeToPop);

	ilios.pm.resetStewardTree(null);
	ilios.pm.stewardAutoCompleter.sendQuery('');

	// returning false prevents the default action of a click (setting focus, etc)
	//				which would cause an error since we just got rid of the node
	//				which was clicked upon
	return false;
};

// @private
ilios.pm.handleStewardTreeSelection = function (clickObject) {
	var node = clickObject.node;
	var modelsToSelect = new Array();
	var nodeToPop = node;
	var rootNode = ilios.pm.stewardDialogSelectedTreeView.getRoot();
    var i = 0;

	if (node.isSchool && (node.children.length > 0)) {
		var children = node.children;

		for (i = 0; i < children.length; i++) {
			modelsToSelect.push(children[i].iliosModel);
		}
	}
	else {
		modelsToSelect.push(node.iliosModel);

		if ((node.getSiblings() == null)
                                && (node.parent != ilios.pm.stewardDialogTreeView.getRoot())) {
			nodeToPop = node.parent;
		}
	}

	for (i = 0; i < modelsToSelect.length; i++) {
		ilios.pm.stewardSelectedModels.push(modelsToSelect[i]);

		ilios.pm.stewardInsertModelIntoTree(rootNode, modelsToSelect[i]);
	}
	ilios.pm.correctForStewardPlaceHolderNodes(rootNode);

	ilios.pm.stewardDialogTreeView.popNode(nodeToPop);
	ilios.pm.redrawStewardTreesAndUpdateCSS();

	// returning false prevents the default action of a click (setting focus, etc)
	//				which would cause an error since we just got rid of the node
	//				which was clicked upon
	return false;
};

// @private
ilios.pm.stewardInsertModelIntoTree = function (rootNode, model) {
	var TextNode = YAHOO.widget.TextNode;
	var schoolNode = null;

	if (! model.isDepartment()) {
		schoolNode = ilios.pm.findSchoolNodeForId(rootNode, model.getDBId());

		if (schoolNode == null) {
			schoolNode = new TextNode(model.getStewardTitle(), rootNode, true);
			schoolNode.schoolId = model.getDBId();
			schoolNode.iliosModel = model;
		} else {
			schoolNode.iliosModel = model;
			schoolNode.setUpLabel(model.getStewardTitle());
		}

		schoolNode.isSchool = true;
	} else {
		var departmentNode = null;

		schoolNode = ilios.pm.findSchoolNodeForId(rootNode, model.getParentSchoolId());

		if (schoolNode == null) {
			schoolNode = new TextNode(ilios.pm.placeHolderNodeTitle, rootNode, true);
			schoolNode.schoolId = model.getParentSchoolId();
		}

		departmentNode = new TextNode(model.getStewardTitle(), schoolNode, true);
		departmentNode.iliosModel = model;
	}
};

/*
 * This will get messaged when the user clicks the submit button on the dialog (this
 *		button is currently display-text'd as "Done")
 *
 * @see ilios.dom.generateGenericDialogMarkupAndWireContent
 */
ilios.pm.stewardSubmitMethod = function () {
	var containerNumber = this.containerNumber; // 'this' should be the Dialog instance
	var programYearModel
				= ilios.pm.currentProgramModel.getProgramYearForContainerNumber(containerNumber);
	var textField
			= document.getElementById(ilios.pm.generateIdStringForStewardContent(containerNumber));
	var textFieldString = '';
	var collapseTrio = null;

	programYearModel.setStewardArray(ilios.pm.stewardSelectedModels);

	for (var i = 0; i < ilios.pm.stewardSelectedModels.length; i++) {
		if (i > 0) {
			textFieldString += '; ';
		}

		textFieldString += ilios.pm.stewardSelectedModels[i].getStewardTitle();
	}

	collapseTrio = ilios.pm.getCollapseTrioForFirstChildLevelDiv(textField);
	collapseTrio[0].innerHTML = ilios.lang.ellipsisedOfLength(textFieldString, 75);
	collapseTrio[1].innerHTML = textFieldString;
};

// @private
//todo optimize spot - a different data model would make all the difference
ilios.pm.getStewardModelForId = function (stewardId) {
	var model = null;

	for (var key in ilios.pm.schoolDepartmentTreeModel) {
		model = ilios.pm.schoolDepartmentTreeModel[key];
		if ((! model.isDepartment()) && (model.getDBId() == stewardId)) {
			return model;
		}
	}

	return null;
};

// @private
//todo optimize spot - a different data model would make all the difference
ilios.pm.getDepartmentsForParentId = function (stewardId) {
	var model = null;
	var rhett = new Array();

	for (var key in ilios.pm.schoolDepartmentTreeModel) {
		model = ilios.pm.schoolDepartmentTreeModel[key];
		if (model.getParentSchoolId() == stewardId) {
			rhett.push(model);
		}
	}

	return rhett;
};
