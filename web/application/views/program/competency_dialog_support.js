/**
 * DEPENDENCIES:
 *    YUI lib (at least YAHOO.widget and its dependencies)
 *    scripts/ilios_dom.js
 *    scripts/ilios_utilities.js
 *    program_manager_dom.js
 */

ilios.pm.competencyDialogTreeView = null;
ilios.pm.subscribedToCompetencyLabelClick = false;

ilios.pm.competencyDialogSelectedTreeView = null;
ilios.pm.subscribedToSelectedCompetencyLabelClick = false;

ilios.pm.competencySelectedModels = null;

ilios.pm.competencyAutoCompleter = null;

ilios.pm.resetCompetencyTree = function (dialog) {
  // the case for a dialog panel display; not the case for an updated query generation
  var shouldResetSelectedModel = (dialog != null);
    var rootNode = null;

  ilios.pm.competencyTreeClearer();

  if (ilios.pm.competencyAutoCompleter == null) {
    ilios.pm.competencyAutoCompleter = dialog.autoCompleter;
  }
  else if (shouldResetSelectedModel) {
    rootNode = ilios.pm.competencyDialogSelectedTreeView.getRoot();

    ilios.pm.competencyDialogSelectedTreeView.removeChildren(rootNode);
  }

  if (shouldResetSelectedModel && (ilios.pm.competencySelectedModels != null)) {
    // the case for a dialog panel display; not the case for a query generation
    ilios.pm.competencySelectedModels = null;
  }

  if (shouldResetSelectedModel) {
    var containerNumber = dialog.containerNumber;
    var programYearModel
          = ilios.pm.currentProgramModel.getProgramYearForContainerNumber(containerNumber);
    var competencyArray = programYearModel.getCompetencyArray();
    var model = null;

    rootNode = ilios.pm.competencyDialogSelectedTreeView.getRoot();

    ilios.pm.competencySelectedModels = [];

    if (! ilios.pm.subscribedToSelectedCompetencyLabelClick) {
      ilios.pm.competencyDialogSelectedTreeView.subscribe("clickEvent", ilios.pm.handleCompetencyTreeDeselection);

      ilios.pm.subscribedToSelectedCompetencyLabelClick = true;
    }

    for (var key in competencyArray) {
      model = competencyArray[key].clone();

      ilios.pm.competencySelectedModels.push(model);

      ilios.pm.competencyInsertModelIntoTree(rootNode, model);
    }

    ilios.pm.correctForCompetencyPlaceHolderNodes(rootNode);

    ilios.pm.redrawCompetencyTreesAndUpdateCSS();
  }
};

ilios.pm.competencyTreeSelectedDOMContentGenerator = function (parentDivId) {
  if (ilios.pm.competencyDialogSelectedTreeView != null) {
    ilios.pm.competencyDialogSelectedTreeView.destroy();

    ilios.pm.competencyDialogSelectedTreeView = null;

    ilios.pm.subscribedToSelectedCompetencyLabelClick = false;
  }

  ilios.pm.competencyDialogSelectedTreeView = new YAHOO.widget.TreeView(parentDivId);
};

ilios.pm.competencyTreeDOMContentGenerator = function (parentDivId) {
  if (ilios.pm.competencyDialogTreeView != null) {
    ilios.pm.competencyDialogTreeView.destroy();

    ilios.pm.competencyDialogTreeView = null;

    ilios.pm.subscribedToCompetencyLabelClick = false;
  }

  if (ilios.pm.competencySelectedModels != null) {
    ilios.pm.competencySelectedModels = null;
  }

  ilios.pm.competencyDialogTreeView = new YAHOO.widget.TreeView(parentDivId);

  ilios.pm.competencySelectedModels = new Array();
};

// @private
ilios.pm.competencyModelIsSelected = function (candidateModel) {
  var model = null;

  for (var i = 0; i < ilios.pm.competencySelectedModels.length; i++) {
    model = ilios.pm.competencySelectedModels[i];

    if (model.compareTo(candidateModel) == 0) {
      return true;
    }
    else if (candidateModel.isSubDomain()) {
      if (model.getDBId() == candidateModel.getParentCompetencyId()) {
        return true;
      }
    }
  }

  return false;
};

ilios.pm.competencyTreeFilterResults = function (queryString, fullResponse, parsedResponse,
                         callback, autoCompleter, idUniquer) {
  var len = parsedResponse.results.length;
  var filteredResults = [];
  var i = 0;
  var model = null;
  var regex = new RegExp(queryString, "gi");

  ilios.pm.competencyTreeClearer();

  for (; i < len; i++) {
    model = parsedResponse.results[i];

    if ((regex.exec(model.getCompetencyTitle()) != null)
                        && (! ilios.pm.competencyModelIsSelected(model))) {
      filteredResults.push(model);
    }
  }

  parsedResponse.results = filteredResults;

  if (filteredResults.length == 0) {
    ilios.pm.competencyDialogTreeView.render();
  }

  return parsedResponse;
};

ilios.pm.competencyTreeHandleResults = function (resultDataObject, queryString, resultMatch,
                         autoCompleter, idUniquer) {
  ilios.pm.competencyInsertModelIntoTree(ilios.pm.competencyDialogTreeView.getRoot(),
                       resultDataObject);
};

// @private
ilios.pm.findCompetencyNodeForId = function (rootNode, competencyId) {
  var children = rootNode.children;
  var len = (children != null) ? children.length : 0;
  var i = 0;

  for (; i < len; i++) {
    if (children[i].competencyId == competencyId) {
      return children[i];
    }
  }

  return null;
};

// @private
ilios.pm.competencyTreeClearer = function () {
  if (ilios.pm.competencyDialogTreeView != null) {
    var rootNode = ilios.pm.competencyDialogTreeView.getRoot();

    ilios.pm.competencyDialogTreeView.removeChildren(rootNode);
  }
};

ilios.pm.competencyTreeFinishedPopulation = function (type, args) {
  if (ilios.pm.competencyDialogTreeView != null) {
    if (! ilios.pm.subscribedToCompetencyLabelClick) {
      ilios.pm.competencyDialogTreeView.subscribe("clickEvent",
                            ilios.pm.handleCompetencyTreeSelection);

      ilios.pm.subscribedToCompetencyLabelClick = true;
    }

    ilios.pm.correctForCompetencyPlaceHolderNodes(ilios.pm.competencyDialogTreeView.getRoot());
    ilios.competencies.verifyChildlessUnselectedCompetencyNodes(ilios.pm.competencyDialogTreeView);

    ilios.pm.redrawCompetencyTreesAndUpdateCSS();

    ilios.pm.competencyDialogTreeView.getRoot().collapseAll();
  }
};

// @private
ilios.pm.correctForCompetencyPlaceHolderNodes = function (rootNode) {
  var children = rootNode.children;
  var len = (children != null) ? children.length : 0;
  var node = null;
  var model = null;

  for (var i = 0; i < len; i++) {
    node = children[i];

    if (node.label == ilios.pm.placeHolderNodeTitle) {
      model = ilios.competencies.getCompetency(node.competencyId);
      node.iliosModel = model;
      node.setUpLabel(model.getCompetencyTitle());
      node.isCompetency = true;
    }
  }
};

// we can't affect the DOM portion of the nodes until after they're rendered
// @private
ilios.pm.redrawCompetencyTreesAndUpdateCSS = function () {
  ilios.pm.competencyRedrawTreeAndCSS(ilios.pm.competencyDialogSelectedTreeView);
  ilios.pm.competencyRedrawTreeAndCSS(ilios.pm.competencyDialogTreeView);
};

// @private
ilios.pm.competencyRedrawTreeAndCSS = function (tree) {
  if (tree != null) {
    var competencyNodes = null;

    tree.draw();

    competencyNodes = tree.getNodesByProperty('isCompetency', true);
    if (competencyNodes != null) {
      var len = competencyNodes.length;

      for (var i = 0; i < len; i++) {
        (new YAHOO.util.Element(competencyNodes[i].getContentEl())).addClass('tree_group_name');
      }
    }
  }
};

// @private
ilios.pm.handleCompetencyTreeDeselection = function (clickObject) {
  var node = clickObject.node;
  var modelsToDeselect = [];
  var nodeToPop = node;
    var i = 0;

  modelsToDeselect.push(node.iliosModel);

  if (node.isCompetency) {
    var children = node.children;

    for (i = 0; i < children.length; i++) {
      modelsToDeselect.push(children[i].iliosModel);
    }
  }
  else if (node.getSiblings() == null) {
    nodeToPop = node.parent;
  }

  for (i = 0; i < modelsToDeselect.length; i++) {
    ilios.utilities.removeElementWithValue(ilios.pm.competencySelectedModels,
                         modelsToDeselect[i]);
  }

  ilios.pm.competencyDialogSelectedTreeView.popNode(nodeToPop);

  ilios.pm.resetCompetencyTree(null);
  ilios.pm.competencyAutoCompleter.sendQuery('');

  // returning false prevents the default action of a click (setting focus, etc)
  //        which would cause an error since we just got rid of the node
  //        which was clicked upon
  return false;
};

// @private
ilios.pm.handleCompetencyTreeSelection = function (clickObject) {
  var node = clickObject.node;
  var modelsToSelect = [];
  var nodeToPop = node;
  var rootNode = ilios.pm.competencyDialogSelectedTreeView.getRoot();
    var i = 0;

  if (node.isCompetency && (node.children.length > 0)) {
    var children = node.children;

    for (i = 0; i < children.length; i++) {
      modelsToSelect.push(children[i].iliosModel);
    }
  }
  else {
    modelsToSelect.push(node.iliosModel);

    if ((node.getSiblings() == null)
                                && (node.parent != ilios.pm.competencyDialogTreeView.getRoot())) {
      nodeToPop = node.parent;
    }
  }

  for (i = 0; i < modelsToSelect.length; i++) {
    ilios.pm.competencySelectedModels.push(modelsToSelect[i]);

    ilios.pm.competencyInsertModelIntoTree(rootNode, modelsToSelect[i]);
  }
  ilios.pm.correctForCompetencyPlaceHolderNodes(rootNode);

  ilios.pm.competencyDialogTreeView.popNode(nodeToPop);
  ilios.pm.redrawCompetencyTreesAndUpdateCSS();

  // returning false prevents the default action of a click (setting focus, etc)
  //        which would cause an error since we just got rid of the node
  //        which was clicked upon
  return false;
};

// @private
ilios.pm.competencyInsertModelIntoTree = function (rootNode, model) {
  var TextNode = YAHOO.widget.TextNode;
  var competencyNode = null;

  if (! model.isSubDomain()) {
    competencyNode = ilios.pm.findCompetencyNodeForId(rootNode, model.getDBId());

    if (competencyNode == null) {
      competencyNode = new TextNode(model.getCompetencyTitle(), rootNode, true);
      competencyNode.competencyId = model.getDBId();
      competencyNode.iliosModel = model;
    } else {
      competencyNode.iliosModel = model;
      competencyNode.setUpLabel(model.getCompetencyTitle());
    }

    competencyNode.isCompetency = true;
  } else {
    var subdomainNode = null;

    competencyNode = ilios.pm.findCompetencyNodeForId(rootNode, model.getParentCompetencyId());

    if (competencyNode == null) {
      competencyNode = new TextNode(ilios.pm.placeHolderNodeTitle, rootNode, true);
      competencyNode.competencyId = model.getParentCompetencyId();
    }

    subdomainNode = new TextNode(model.getCompetencyTitle(), competencyNode, true);
    subdomainNode.iliosModel = model;
  }
};

/*
 * This will get messaged when the user clicks the submit button on the dialog (this
 * button is currently display-text'd as "Done")
 *
 * @see ilios.dom.generateGenericDialogMarkupAndWireContent
 */
ilios.pm.competencySubmitMethod = function () {
    var containerNumber = this.containerNumber; // 'this' should be the Dialog instance
    var programYearModel = ilios.pm.currentProgramModel.getProgramYearForContainerNumber(containerNumber);
    var textField = document.getElementById(ilios.pm.generateIdStringForCompetencyContent(containerNumber));
    var collapseTrio;
    var str;

    programYearModel.setCompetencyArray(ilios.pm.competencySelectedModels);

    collapseTrio = ilios.pm.getCollapseTrioForFirstChildLevelDiv(textField);
    str = ilios.competencies.generateSummaryStringForSelectedCompetencies(ilios.pm.competencySelectedModels);
    collapseTrio[0].innerHTML = ilios.lang.ellipsisedOfLength(str, 75);
    str = ilios.competencies.generateListHTMLForSelectedCompetencies(ilios.pm.competencySelectedModels);
    collapseTrio[1].innerHTML = str;
};
