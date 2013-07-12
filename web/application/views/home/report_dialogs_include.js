/**
 * This script entails the event handling and initalization routines
 * for the various report dialogs on the instructor dashboard page.
 *
 * DEPENDENCY: YUI lib
 * DEPENDENCY: scripts/ilios_ui.js
 * DEPENDENCY: scripts/ilios_utilities.js
 * DEPENDENCY: scripts/mesh_base_framework.js
 * DEPENDENCY: scripts/models/mesh_item_model.js
 * DEPENDENCY: scripts/models/report_model.js
 * DEPENDENCY: home/educator_dashboard_transaction.js
 */

ilios.namespace('home.report');
ilios.namespace('mesh');

/*
 * ============================================================================
 * "create report" dialog support
 * ===========================================================================
 */

ilios.home.report.inEditReportModel = null;

ilios.home.report.defaultNounList = {
    "course": ilios_i18nVendor.getI18NString("general.terms.course"),
    "session": ilios_i18nVendor.getI18NString("general.terms.session"),
    "session type": ilios_i18nVendor.getI18NString("general.terms.session_type"),
    "program": ilios_i18nVendor.getI18NString("general.terms.program"),
    "program year": ilios_i18nVendor.getI18NString("general.terms.program_year"),
    "instructor": ilios_i18nVendor.getI18NString("general.terms.instructor"),
    "instructor group": ilios_i18nVendor.getI18NString("general.phrases.instructor_group"),
    "learning material": ilios_i18nVendor.getI18NString("general.phrases.learning_material"),
    "competency": ilios_i18nVendor.getI18NString("general.terms.competency"),
    "topic": ilios_i18nVendor.getI18NString("general.terms.topic"),
    "mesh term": ilios_i18nVendor.getI18NString("general.phrases.mesh_term")
};

ilios.home.report.pickMeOptionValue = "remove_me_on_first_pick";

ilios.home.report.resettingPanel = false;

// @private
ilios.home.report.populatePopup = function (serverMethod) {
    var url = controllerURL + serverMethod;
    var method = "POST";
    var paramString = "";
    var ajaxCallback = {
            success: function (resultObject) {
                var parsedObject = null;
                var item = null;
                var element = null;
                var option = null;
                var selectElement = null;
                var firstItemAdded = false;

                try {
                    parsedObject = YAHOO.lang.JSON.parse(resultObject.responseText);
                }
                catch (e) {
                    ilios.global.defaultAJAXFailureHandler(null, e);

                    return;
                }

                // MAY RETURN THIS BLOCK
                if (parsedObject.error != null) {
                    var msg
                          = ilios_i18nVendor.getI18NString('general.error.details_load');

                    ilios.alert.alert(msg + ": " + parsedObject.error);

                    return;
                }

                element = document.getElementById('report_noun_2_value_div');
                ilios.utilities.removeAllChildren(element);

                selectElement = document.createElement('select');
                selectElement.setAttribute('id', 'report_noun_2_pulldown');
                selectElement.setAttribute('style', 'width: 120px;');

                for (var key in parsedObject.items) {
                    item = parsedObject.items[key];

                    option = document.createElement('option');
                    option.value = item.value;
                    option.innerHTML = item.display_title;

                    selectElement.appendChild(option);

                    if (! firstItemAdded) {
                        ilios.home.report.inEditReportModel.setPrepositionalObjectValues([item.value]);

                        firstItemAdded = true;
                    }
                }

                YAHOO.util.Event.addListener(selectElement, 'change', function (e) {
                    ilios.home.report.restrictiveNounValueChosenViaPopup(this);
                });

                element.appendChild(selectElement);

                (new YAHOO.util.Element(document.getElementById('report_indeterminate_div')))
                                                                    .setStyle('display', 'none');
            },

            failure: function (resultObject) {
                ilios.global.defaultAJAXFailureHandler(resultObject);
            }};


    (new YAHOO.util.Element(document.getElementById('report_indeterminate_div'))).setStyle('display', 'block');

    YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);
};

// @private
ilios.home.report.restrictiveNounValueChosenViaPopup = function (selectElement) {
    var value = selectElement.options[selectElement.selectedIndex].value;

    ilios.home.report.inEditReportModel.setPrepositionalObjectValues([value]);
};

ilios.home.report.registerReportDialogListeners = function () {
    var Event = YAHOO.util.Event;
    var element = document.getElementById('report_noun_1');

    Event.addListener(element, 'change', ilios.home.report.subjectNounChanged);

    element = document.getElementById('report_support_noun_2_checkbox');
    Event.addListener(element, 'change', ilios.home.report.setDisableStatusOfRestrictiveNoun);

    element = document.getElementById('report_noun_2');
    Event.addListener(element, 'change', ilios.home.report.populateRestrictiveNounValueDiv);
};

ilios.home.report.resetReportDialog = function (dialog) {
    var element = document.getElementById('report_noun_1');
    var firstOptionElement = element.firstChild;

    if (firstOptionElement.value != ilios.home.report.pickMeOptionValue) {
        var pickOneOptionElement = document.createElement('option');

        pickOneOptionElement.setAttribute('value', ilios.home.report.pickMeOptionValue);
        pickOneOptionElement.innerHTML = ilios_i18nVendor.getI18NString('general.phrases.pick_one') + "...";

        ilios.home.report.resettingPanel = true;
        pickOneOptionElement.setAttribute('selected', 'selected');
        ilios.home.report.resettingPanel = false;

        element.insertBefore(pickOneOptionElement, element.firstChild);

    } else {
        ilios.home.report.resettingPanel = true;
        firstOptionElement.setAttribute('selected', 'selected');
        ilios.home.report.resettingPanel = false;
    }

    element = document.getElementById('report_noun_2');
    ilios.utilities.removeAllChildren(element);

    element = document.getElementById('report_noun_2_value_div')
    ilios.utilities.removeAllChildren(element);

    element = document.getElementById('report_support_noun_2_checkbox');
    element.checked = false;
    ilios.home.report.setDisableStatusOfRestrictiveNoun();

    document.getElementById('title').value = ''; // clear out title field on UI

    ilios.home.report.inEditReportModel = new ReportModel();
};

// @private
ilios.home.report.checkNoun2 = function(check) {
    var element = document.getElementById('report_support_noun_2_checkbox');

    element.checked = check;

    //check = true, then elements should be enabled, thus disabled = ! check
    //check = false, then elements should be disabled, thus diabled = ! check
    element = document.getElementById('report_noun_2');
    element.disabled = ! check;

    element = document.getElementById('report_noun_2_pulldown');
    if (element != null) {
        element.disabled = ! check;
    }
};

// @private
ilios.home.report.subjectNounChanged = function () {
    var element = null;
    var noun2List = null;
    var noun1 = null;
    var optionElement = null;
    var tableName = null;
    var defaultNounList = ilios.home.report.defaultNounList;

    // MAY RETURN THIS BLOCK
    if (ilios.home.report.resettingPanel) {
        return;
    }
    ilios.home.report.checkNoun2(false);
    ilios.home.report.inEditReportModel.setMustHavePrepositionalObject(false);

    element = document.getElementById('report_noun_1');

    noun1 = element.options[element.selectedIndex].value;

    optionElement = element.firstChild;
    if (optionElement.value == ilios.home.report.pickMeOptionValue) {
        // first select-action of fresh panel display
        element.removeChild(element.firstChild);
    }

    noun2List = {};

    if (noun1 == 'competency') {
        noun2List = {
            "course": defaultNounList.course,
            "session": defaultNounList.session,
            "session type": defaultNounList["session type"]
        };
    } else if (noun1 === 'instructor') {
        noun2List = {
            "course": defaultNounList.course,
            "session": defaultNounList.session,
            "session type": defaultNounList["session type"],
            "instructor group": defaultNounList["instructor group"],
            "learning material": defaultNounList["learning material"],
            "topic": defaultNounList.topic
        };
        ilios.home.report.checkNoun2(true);
        ilios.home.report.inEditReportModel.setMustHavePrepositionalObject(true);
    } else if (noun1 === 'instructor group') {
        noun2List = {
            "course": defaultNounList.course,
            "session": defaultNounList.session,
            "session type": defaultNounList["session type"],
            "instructor": defaultNounList["instructor"],
            "learning material": defaultNounList["learning material"],
            "topic": defaultNounList.topic
        };
    } else if (noun1 === 'learning material') {
        noun2List = {
            "course": defaultNounList.course,
            "session": defaultNounList.session,
            "session type": defaultNounList["session type"],
            "instructor": defaultNounList.instructor,
            "instructor group": defaultNounList["instructor group"],
            "mesh term": defaultNounList["mesh term"]
        };

    } else if (noun1 === 'mesh term') {
        noun2List = {
            "course": defaultNounList.course,
            "session": defaultNounList.session,
            "session type": defaultNounList["session type"],
            "learning material": defaultNounList["learning material"]
        };

        ilios.home.report.checkNoun2(true);
        ilios.home.report.inEditReportModel.setMustHavePrepositionalObject(true);
    } else if ((noun1 === 'program') || (noun1 === 'program year')) {
        noun2List = {
            "course": defaultNounList.course,
            "session": defaultNounList.session,
            "topic": defaultNounList.topic
        };
    } else if (noun1 ===  'course') {
        noun2List = {
            "session": defaultNounList.session,
            "program": defaultNounList.program,
            "program year": defaultNounList["program year"],
            "instructor": defaultNounList.instructor,
            "instructor group": defaultNounList["instructor group"],
            "learning material": defaultNounList["learning material"],
            "competency": defaultNounList.competency,
            "topic": defaultNounList.topic,
            "mesh term": defaultNounList["mesh term"]
        }
    } else if (noun1 === 'session') {
        noun2List = {
            "course": defaultNounList.course,
            "session type": defaultNounList["session type"],
            "program": defaultNounList.program,
            "program year": defaultNounList["program year"],
            "instructor": defaultNounList.instructor,
            "instructor group": defaultNounList["instructor group"],
            "learning material": defaultNounList["learning material"],
            "competency": defaultNounList.competency,
            "topic": defaultNounList.topic,
            "mesh term": defaultNounList["mesh term"]
        }
    } else if (noun1 === 'topic') {
        noun2List = {
            "course": defaultNounList.course,
            "session": defaultNounList.session,
            "session type": defaultNounList["session type"],
            "program": defaultNounList.program,
            "program year": defaultNounList["program year"],
            "instructor": defaultNounList.instructor,
            "instructor group": defaultNounList["instructor group"],
            "learning material": defaultNounList["learning material"],
            "competency": defaultNounList.competency,
            "mesh term": defaultNounList["mesh term"]
        }
    }

    element = document.getElementById('report_noun_2');
    ilios.utilities.removeAllChildren(element);

    for (key in noun2List) {
        if (noun2List.hasOwnProperty(key)) {
            optionElement = document.createElement('option');
            optionElement.setAttribute('value', key);
            optionElement.innerHTML = noun2List[key];
            element.appendChild(optionElement);
        }
    }

    optionElement = document.createElement('option');
    optionElement.setAttribute('value', ilios.home.report.pickMeOptionValue);
    optionElement.innerHTML = ilios_i18nVendor.getI18NString('general.phrases.pick_one') + "...";
    element.insertBefore(optionElement, element.firstChild);

    ilios.home.report.resettingPanel = true;
    optionElement.setAttribute('selected', 'selected');
    ilios.home.report.resettingPanel = false;

    element = document.getElementById('report_noun_2_value_div')
    ilios.utilities.removeAllChildren(element);

    ilios.home.report.inEditReportModel.setSubject(noun1.toLowerCase());
};

// @private
ilios.home.report.setDisableStatusOfRestrictiveNoun = function () {
    var element = null;
    var disable = false;
    var noun1 = null;

    if (ilios.home.report.resettingPanel) {
        return;
    }

    element = document.getElementById('report_support_noun_2_checkbox');
    disable = (! element.checked);

    element = document.getElementById('report_noun_1');
    noun1 = element.options[element.selectedIndex].value;

    if (disable && (noun1 === 'mesh term' || noun1 === 'instructor')) {
        ilios.home.report.resettingPanel = true;
        document.getElementById('report_support_noun_2_checkbox').checked = true;
        ilios.home.report.resettingPanel = false;

        return;
    }

    element = document.getElementById('report_noun_2');
    element.disabled = disable;

    element = document.getElementById('report_noun_2_pulldown');
    if (element != null) {
        element.disabled = disable;
    }
};

// @private
ilios.home.report.populateRestrictiveNounValueDiv = function () {
    var element = null;
    var noun = null;
    var optionElement = null;

    // MAY RETURN THIS BLOCK
    if (ilios.home.report.resettingPanel) {
        return;
    }

    element = document.getElementById('report_noun_2');
    noun = element.options[element.selectedIndex].value;

    optionElement = element.firstChild;
    if (optionElement.value == ilios.home.report.pickMeOptionValue) {
        // first select-action of fresh panel display
        element.removeChild(element.firstChild);
    }

    if (noun === 'course') {
        ilios.home.report.populatePopup('getAllCoursesForReportSelection');
    } else if (noun === 'session') {
        ilios.home.report.populatePopup('getAllSessionsForReportSelection');
    } else if (noun === 'session type') {
        ilios.home.report.populatePopup('getAllSessionTypesForReportSelection');
    } else if (noun === 'program') {
        ilios.home.report.populatePopup('getAllProgramsForReportSelection');
    } else if (noun === 'program year') {
        ilios.home.report.populatePopup('getAllProgramYearsForReportSelection');
    } else if (noun === 'topic') {
        ilios.home.report.populatePopup('getAllDisciplinesForReportSelection');
    } else if (noun === 'instructor group') {
        ilios.home.report.populatePopup('getAllInstructorGroupsForReportSelection');
    } else if (noun === 'competency') {
        ilios.home.report.setCustomPickerAsValueDivUI(function (e) {
            IEvent.fire({
                event: 'competency_picker_show_dialog',
                action: 'gen_dialog_open'
            });
            return false;
        });
    } else if (noun === 'instructor') {
        ilios.home.report.setCustomPickerAsValueDivUI(ilios.home.report.displayInstructorDialog);
    } else if (noun === 'learning material') {
        ilios.home.report.setCustomPickerAsValueDivUI(function (e) {
            IEvent.fire({
                action: 'alm_dialog_open'
            });
            return false;
        });
    } else if (noun === 'mesh term') {
        ilios.home.report.setCustomPickerAsValueDivUI(function (e) {
            IEvent.fire({
                action: 'mesh_picker_dialog_open'
            });
            return false;
        });
    }

    ilios.home.report.inEditReportModel.setPrepositionalObject(noun);
};

// @private
ilios.home.report.setCustomPickerAsValueDivUI = function (clickFunction) {
    var element = document.getElementById('report_noun_2_value_div');
    var child = null;

    ilios.utilities.removeAllChildren(element);

    child = document.createElement('span');
    child.setAttribute('class', 'read_only_data');
    child.setAttribute('style', 'margin-right: 6px;');
    child.setAttribute('id', 'report_noun_2_readonly_span');
    element.appendChild(child);

    child = document.createElement('div');
    child.setAttribute('class', 'search_widget');
    YAHOO.util.Event.addListener(child, 'click', clickFunction);
    element.appendChild(child);

    ilios.utilities.appendClearingDivToContainer(element);
};

ilios.home.report.handleReportDialogSubmit = function () {
    var element = document.getElementById('report_support_noun_2_checkbox');

    if ((ilios.home.report.inEditReportModel.getSubject() == null)
                            || (ilios.home.report.inEditReportModel.getSubject().length == 0)) {
        ilios.alert.alert(ilios_i18nVendor.getI18NString('dashboard.report.error.missing_input'));

        return false;
    }

    if (! element.checked) {
        ilios.home.report.inEditReportModel.setPrepositionalObject(null);
    }

    if ((ilios.home.report.inEditReportModel.mustHavePrepositionalObject() || element.checked)
                            && (! ilios.home.report.inEditReportModel.hasPrepositionalObject())) {
        ilios.alert.alert(ilios_i18nVendor.getI18NString('dashboard.report.error.missing_input'));

        return false;
    }
    var title = document.getElementById('title').value;
    if (title != null) {
        ilios.home.report.inEditReportModel.setTitle(title);
    }
    ilios.home.transaction.saveReport(ilios.home.report.inEditReportModel);

    ilios.home.report.inEditReportModel = null;

    return true;
};

ilios.home.report.handleReportDialogCancel = function () {
    ilios.home.report.inEditReportModel = null;
};

/*
 * ============================================================================
 * "compentency-report picker" dialog support
 * ===========================================================================
 */

ilios.home.report.competencyDialogInView = null;

ilios.home.report.competencyDialogTreeView = null;
ilios.home.report.subscribedToCompetencyLabelClick = false;

ilios.home.report.competencyAutoCompleter = null;

ilios.home.report.placeHolderNodeTitle = "Place holder";

ilios.home.report.resetCompetencyTree = function (dialog) {
    ilios.home.report.competencyTreeClearer();

    if (ilios.home.report.competencyAutoCompleter == null) {
        ilios.home.report.competencyAutoCompleter = dialog.autoCompleter;
    }

    ilios.home.report.competencyDialogInView = dialog;
};

ilios.home.report.competencyTreeSelectedDOMContentGenerator = function (parentDivId) {
    // NO-OP by design
};

ilios.home.report.competencyTreeDOMContentGenerator = function (parentDivId) {
    if (ilios.home.report.competencyDialogTreeView != null) {
        ilios.home.report.competencyDialogTreeView.destroy();

        ilios.home.report.competencyDialogTreeView = null;

        ilios.home.report.subscribedToCompetencyLabelClick = false;
    }

    ilios.home.report.competencyDialogTreeView = new YAHOO.widget.TreeView(parentDivId);
};

ilios.home.report.competencyTreeFilterResults = function (queryString, fullResponse, parsedResponse,
                                                          callback, autoCompleter, idUniquer) {
    var len = parsedResponse.results.length;
    var filteredResults = new Array();
    var i = 0;
    var model = null;
    var regex = new RegExp(queryString, "gi");

    ilios.home.report.competencyTreeClearer();

    for (; i < len; i++) {
        model = parsedResponse.results[i];

        if (regex.exec(model.getCompetencyTitle()) != null) {
            filteredResults.push(model);
        }
    }

    parsedResponse.results = filteredResults;

    if (filteredResults.length == 0) {
        ilios.home.report.competencyDialogTreeView.render();
    }

    return parsedResponse;
};

ilios.home.report.competencyTreeHandleResults = function (resultDataObject, queryString, resultMatch,
                                            autoCompleter, idUniquer) {
    ilios.home.report.competencyInsertModelIntoTree(ilios.home.report.competencyDialogTreeView.getRoot(),
                                                    resultDataObject);
};

// @private
ilios.home.report.findCompetencyNodeForId = function (rootNode, competencyId) {
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
ilios.home.report.competencyTreeClearer = function () {
    if (ilios.home.report.competencyDialogTreeView != null) {
        var rootNode = ilios.home.report.competencyDialogTreeView.getRoot();

        ilios.home.report.competencyDialogTreeView.removeChildren(rootNode);
    }
};

ilios.home.report.competencyTreeFinishedPopulation = function (type, args) {
    if (ilios.home.report.competencyDialogTreeView != null) {
        if (! ilios.home.report.subscribedToCompetencyLabelClick) {
            ilios.home.report.competencyDialogTreeView.subscribe("clickEvent",
                                                                 ilios.home.report.handleCompetencyTreeSelection);

            ilios.home.report.subscribedToCompetencyLabelClick = true;
        }

        ilios.home.report.correctForCompetencyPlaceHolderNodes(
                                            ilios.home.report.competencyDialogTreeView.getRoot());
        ilios.competencies.verifyChildlessUnselectedCompetencyNodes(
                                            ilios.home.report.competencyDialogTreeView);

        ilios.home.report.redrawCompetencyTreesAndUpdateCSS();
    }
};

// @private
ilios.home.report.correctForCompetencyPlaceHolderNodes = function (rootNode) {
    var children = rootNode.children;
    var len = (children != null) ? children.length : 0;
    var node = null;
    var model = null;

    for (var i = 0; i < len; i++) {
        node = children[i];

        if (node.label == ilios.home.report.placeHolderNodeTitle) {
            model = ilios.competencies.getCompetency(node.competencyId);
            node.iliosModel = model;
            node.setUpLabel(model.getCompetencyTitle());
            node.isCompetency = true;
        }
    }
};

// we can't affect the DOM portion of the nodes until after they're rendered
// @private
ilios.home.report.redrawCompetencyTreesAndUpdateCSS = function () {
    if (ilios.home.report.competencyDialogTreeView != null) {
        var competencyNodes = null;

        ilios.home.report.competencyDialogTreeView.draw();

        competencyNodes
            = ilios.home.report.competencyDialogTreeView.getNodesByProperty('isCompetency', true);
        if (competencyNodes != null) {
            var len = competencyNodes.length;

            for (var i = 0; i < len; i++) {
                (new YAHOO.util.Element(competencyNodes[i].getContentEl())).addClass('tree_group_name');
            }
        }
    }
};

// @private
ilios.home.report.handleCompetencyTreeSelection = function (clickObject) {
    var node = clickObject.node;
    var modelsToSelect = new Array();
    var nodeToPop = node;
    var element = document.getElementById('report_noun_2_readonly_span');

    // If we start allowing multiple values for a prepositional object's value, uncomment this.
//  if (node.isCompetency) {
//      var children = node.children;
//
//      for (var i = 0; i < children.length; i++) {
//          modelsToSelect.push(children[i].iliosModel.getDBId());
//      }
//  }
//  else {
//      modelsToSelect.push(node.iliosModel.getDBId());
//
//      if (node.getSiblings() == null) {
//          nodeToPop = node.parent;
//      }
//  }
//
//  element.innerHTML = ilios.competencies.generateListHTMLForSelectedCompetencies(modelsToSelect);

    // get rid of these two for multiple po values
    modelsToSelect.push(node.iliosModel.getDBId());
    element.innerHTML = node.iliosModel.getCompetencyTitle();

    ilios.home.report.inEditReportModel.setPrepositionalObjectValues(modelsToSelect);

    ilios.home.report.competencyDialogInView.cancel();

    ilios.home.report.competencyDialogInView = null;

    // returning false prevents the default action of a click (setting focus, etc)
    //              which would cause an error since we just got rid of the node
    //              which was clicked upon
    return false;
};

// @private
ilios.home.report.competencyInsertModelIntoTree = function (rootNode, model) {
    var TextNode = YAHOO.widget.TextNode;
    var competencyNode = null;

    if (! model.isSubDomain()) {
        competencyNode = ilios.home.report.findCompetencyNodeForId(rootNode, model.getDBId());

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

        competencyNode = ilios.home.report.findCompetencyNodeForId(rootNode, model.getParentCompetencyId());

        if (competencyNode == null) {
            competencyNode = new TextNode(ilios.home.report.placeHolderNodeTitle, rootNode, true);
            competencyNode.competencyId = model.getParentCompetencyId();
        }

        subdomainNode = new TextNode(model.getCompetencyTitle(), competencyNode, true);
        subdomainNode.iliosModel = model;
    }
};

/*
 * This will get messaged when the user clicks the submit button on the dialog (this
 *      button is currently display-text'd as "Done")
 *
 * @see ilios.dom.generateGenericDialogMarkupAndWireContent
 */
ilios.home.report.competencySubmitMethod = function () {
    // NO-OP by design
};

/*
 * ============================================================================
 * "instructor-report picker" dialog support
 * ===========================================================================
 */

ilios.home.report.instructorAutoCompleter = null;
ilios.home.report.instructorPickerDialog = null;

ilios.home.report.buildReportInstructorDialogDOM = function () {
    var element = null;

    var panelWidth = "510px";
    var dialog = new YAHOO.widget.Dialog('report_instructor_pick_dialog', {
        width: panelWidth,
        modal: true,
        visible: false,
        constraintoviewport: false,
        buttons: null
    });

    dialog.showDialogPane = function () {
        dialog.center();
        dialog.show();
    };

    // Render the Dialog
    dialog.render();

    ilios.home.report.instructorPickerDialog = dialog;

    element = new YAHOO.util.Element(document.getElementById('report_instructor_pick_dialog'));
    element.setStyle('display', 'block');
};

ilios.home.report.displayInstructorDialog = function () {
    ilios.home.report.instructorAutoCompleter.sendQuery('');
    ilios.home.report.instructorPickerDialog.showDialogPane();
};

ilios.home.report.instructorDataSource = new YAHOO.util.XHRDataSource(controllerURL + 'getFacultyList');
ilios.home.report.instructorDataSource.responseType = YAHOO.util.XHRDataSource.TYPE_XML;
ilios.home.report.instructorDataSource.responseSchema = {
    resultNode: "Result",
    fields: ["user_id", "last_name", "first_name", "middle_name", "phone", "email","uc_uid", "other_id", "cohort_id"]
};

ilios.home.report.setupInstructorUIElements = function () {
    var element = document.getElementById('report_instructor_dialog_title');

    element.innerHTML = ilios_i18nVendor.getI18NString('offering_management.calendar.lightbox.select_instructors');

    ilios.home.report.instructorAutoCompleter = new YAHOO.widget.AutoComplete('instructor_ac_input',
        'instructor_autolist', ilios.home.report.instructorDataSource);

    ilios.home.report.instructorAutoCompleter.alwaysShowContainer = true;
    ilios.home.report.instructorAutoCompleter.allowBrowserAutocomplete = false;
    ilios.home.report.instructorAutoCompleter.minQueryLength = 0;
    ilios.home.report.instructorAutoCompleter.maxResultsDisplayed = 2500;
    ilios.home.report.instructorAutoCompleter.autoHighlight = false;
    ilios.home.report.instructorAutoCompleter.resultTypeList = false;
    ilios.home.report.instructorAutoCompleter.target = element;

    ilios.home.report.instructorAutoCompleter.formatResult = function (resultDataObject, queryString, resultMatch) {
        var firstName = resultDataObject.first_name || '';
        var lastName = resultDataObject.last_name || '';
        var middleName = resultDataObject.middle_name || '';
        return ilios.utilities.createFormattedUserName(firstName, middleName, lastName,
            ilios.utilities.USER_NAME_FORMAT_LAST_FIRST);
    };

    ilios.home.report.instructorAutoCompleter.itemSelectEvent.subscribe(function (type, args, me) {
        ilios.home.report.handleInstructorSelection(args[2]);
        args[1].parentNode.removeChild(args[1]);
        document.getElementById("instructor_ac_input").value = "";
    });
};

/*
 * This is messaged when the user selects an instructor / instructor group from the list of them
 *  in the lightbox.
 *
 * Should be considered @private
 */
ilios.home.report.handleInstructorSelection = function (selectedModel) {
    var displayString = null;
    var element = document.getElementById('report_noun_2_readonly_span');

    //displayString = selectedModel.title;
    var firstName = selectedModel.first_name || '';
    var lastName = selectedModel.last_name || '';
    var middleName = selectedModel.middle_name || '';
    displayString = ilios.utilities.createFormattedUserName(firstName, middleName, lastName,
            ilios.utilities.USER_NAME_FORMAT_LAST_FIRST);

    ilios.home.report.inEditReportModel.setPrepositionalObjectValues([selectedModel.user_id]);

    element.innerHTML = displayString;
    ilios.home.report.instructorPickerDialog.cancel();
};

/*
 * ============================================================================
 * "learning-materials-report picker" dialog support
 * ===========================================================================
 */
ilios.home.report.registerReportLearningMaterialUI = function () {
    var element = document.getElementById('rlm_search_textfield');

    YAHOO.util.Event.addListener(element, 'keyup', ilios.home.report.searchLearningMaterials);
};

// @private
ilios.home.report.searchLearningMaterials = function () {
    var url = learningMaterialsControllerURL + "getLearningMaterialDescriptorsForSearch";
    var method = "POST";
    var paramString = "search_string="
                                    + escape(document.getElementById('rlm_search_textfield').value);
    var ajaxCallback = {
            success: function (resultObject) {
                var parsedObject = null;
                var element = null;
                var learningMaterial = null;

                try {
                    parsedObject = YAHOO.lang.JSON.parse(resultObject.responseText);
                }
                catch (e) {
                    ilios.global.defaultAJAXFailureHandler(null, e);

                    return;
                }

                // MAY RETURN THIS BLOCK
                if (parsedObject.error != null) {
                    var msg = ilios_i18nVendor.getI18NString('learning_material.error.search');

                    ilios.alert.alert(msg + ": " + parsedObject.error);

                    return;
                }

                element = document.getElementById('rlm_search_results_ul');
                ilios.utilities.removeAllChildren(element);

                for (var key in parsedObject) {
                    learningMaterial = new LearningMaterialModel(parsedObject[key]);

                    element.appendChild(
                            ilios.home.report.createLIElementForLearningMaterial(learningMaterial,
                                                                                 true, null));
                }
            },

            failure: function (resultObject) {
                ilios.global.defaultAJAXFailureHandler(resultObject);
            }};

    YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);
};

ilios.home.report.resetAddLearningMaterialsDialog = function () {
    var element = document.getElementById('rlm_search_results_ul');

    ilios.utilities.removeAllChildren(element);

    element = document.getElementById('rlm_search_textfield');
    element.value = '';
};

ilios.home.report.createLIElementForLearningMaterial = function (model) {
    var Event = YAHOO.util.Event;
    var rhett = document.createElement('li');
    var titleElement = document.createElement('span');
    var fileSizeElement = null;
    var downloadURL = learningMaterialsControllerURL
                            + "getLearningMaterialWithId?learning_material_id=" + model.getDBId();
    var affectingWidget = document.createElement('span');
    var downloadWidget = document.createElement('span');
    var buttonWidgetDiv = document.createElement('div');
    var isLink = (model.getMimeType() == 'link');
    var isCitation = (model.getMimeType() == 'citation');
    var innerHTML = model.getTitle();
    var descriptionI18NStr = ilios_i18nVendor.getI18NString('general.terms.description');
    var noneI18NStr = ilios_i18nVendor.getI18NString('general.terms.none');

    rhett.setAttribute('class', ilios.utilities.convertMimeTypeToCSSClassName(model.getMimeType()));
    rhett.setAttribute('style', 'position: relative; margin-bottom: 12px;');

    innerHTML += '<br/><span style="font-size: 8pt; font-weight: bold;">' + descriptionI18NStr;
    innerHTML += ': </span> <span style="font-size: 8pt;">';
    innerHTML += (((model.getDescription() == null) || (model.getDescription().length == 0))
                            ? ('(' + noneI18NStr + ')')
                            : model.getDescription())
                        + ' </span>';

    titleElement.innerHTML = innerHTML;
    titleElement.setAttribute('class', 'title');

    rhett.appendChild(titleElement);

    buttonWidgetDiv.setAttribute('class', 'buttonset');

    downloadWidget.setAttribute('class', 'download_widget');
    affectingWidget.setAttribute('class', 'add_widget');

    if ((! isLink) && (! isCitation)) {
        buttonWidgetDiv.appendChild(downloadWidget);

        fileSizeElement = document.createElement('span');
        fileSizeElement.setAttribute('class', 'filesize');
        fileSizeElement.innerHTML = ' &nbsp;(' + model.getFileSize() + ' KB)';
        rhett.appendChild(fileSizeElement);
    }
    buttonWidgetDiv.appendChild(affectingWidget);

    rhett.appendChild(buttonWidgetDiv);

    Event.addListener(affectingWidget, 'click', function (e) {
        ilios.home.report.handleLearningMaterialClick(this, model);
    });
    Event.addListener(downloadWidget, 'click', function (e) {
        window.location.href = downloadURL;
    });

    //Event.addListener(rhett, 'mouseover',
    //                 function (e) { (new YAHOO.util.Element(this)).setStyle('background-color', '#EBE9ED'); });
    //Event.addListener(rhett, 'mouseout',
    //                 function (e) { (new YAHOO.util.Element(this)).setStyle('background-color', null); });

    return rhett;
};

// @private
ilios.home.report.handleLearningMaterialClick = function (widgetElement, learningMaterialModel) {
    var element = document.getElementById('report_noun_2_readonly_span');

    ilios.home.report.inEditReportModel.setPrepositionalObjectValues(new Array(learningMaterialModel.getDBId()));

    element.innerHTML = learningMaterialModel.getTitle();

    ilios.home.report.learningMaterialDialog.cancel();
};

/*
 * ============================================================================
 * "mesh-term-report picker" dialog support
 * ===========================================================================
 */


// Overriding mesh_base_framework handler here
ilios.mesh.meshSearchResultSelectionHandler = function (selectedDiv) {
    var meshItem = selectedDiv.iliosModel;
    var element = document.getElementById('report_noun_2_readonly_span');

    element.innerHTML = meshItem.title;
    ilios.home.report.inEditReportModel.setPrepositionalObjectValues(
        [meshItem.dbId]
    );

    ilios.mesh.meshPickerDialog.cancel();
};

ilios.home.report.populateMeSHPickerDialog = function () {
    ilios.utilities.removeAllChildren(document.getElementById('mesh_search_results'));
    document.getElementById('mesh_search_terms').value = '';
};

ilios.home.report.handleReportMeSHSearchFieldInput = function (inputField, event) {
    var charCode = event.keyCode ? event.keyCode : (event.which ? event.which : event.charCode);

    if (charCode == 13) {
        var elem = document.getElementById('mesh_search_terms');

        ilios.mesh.performMeSHSearch(elem.value, false, false);

        event.cancelBubble = true;
        event.returnValue = false;

        return false;
    }

    return true;
};

/**
 * DEPENDENCY: view/home/report_dialogs_support.js
 */

ilios.home.report.reportResultsDialog = null;

/**
 * Event handler function.
 * @method assembleReportResultsDialog
 * @param {String} type
 * @param {Array} args
 * @param {Object} me
 *     "display_handler": a function which is called just prior to the dialog being displayed
 * @todo improve code docs
 */
ilios.home.report.assembleReportResultsDialog = function (type, args, me) {
    var handleClose = function () {
        this.cancel();
    };

    var doneStr = ilios_i18nVendor.getI18NString("general.terms.done");
    var buttonArray = [
        {text: doneStr, handler: handleClose, isDefault: true}
    ];
    var panelWidth = "600px";
    var displayOnTriggerHandler = null;
    var dialog = new YAHOO.widget.Dialog('report_results_dialog', {
        width: panelWidth,
        modal: true,
        visible: false,
        constraintoviewport: false,
        buttons: buttonArray
    });

    dialog.showDialogPane = function () {
        var displayHandler = me['display_handler'];
        if (displayHandler != null) {
            displayHandler(dialog);
        }
        dialog.center();
        dialog.show();
    };

    // Render the Dialog
    dialog.render();

    // register a listener on some page element to trigger the display of this popup dialog
    displayOnTriggerHandler = function (type, handlerArgs) {
        if (handlerArgs[0].action == 'report_results_dialog_open') {
            var container = document.getElementById('report_results_content');

            ilios.utilities.removeAllChildren(container);

            ilios.home.transaction.runReport(handlerArgs[0].report);

            dialog.showDialogPane();
        }
    };

    IEvent.subscribe(displayOnTriggerHandler);

    ilios.home.report.reportResultsDialog = dialog;
}

ilios.home.report.reportDialog = null;

/**
 * Event handler function.
 * @method assembleReportDialog
 * @param {String} type
 * @param {Array} args
 * @param {Object} me
 *     "display_handler": a function which is called just prior to the dialog being displayed
 * @todo improve code docs
 */
ilios.home.report.assembleReportDialog = function (type, args, me) {
    var handleSave = function () {
        if (ilios.home.report.handleReportDialogSubmit()) {
            ilios.home.report.reportDialog.cancel();
        }
    };

    var handleCancel = function () {
        ilios.home.report.handleReportDialogCancel();
        this.cancel();
    };

    var cancelStr = ilios_i18nVendor.getI18NString("general.terms.cancel");
    var saveStr = ilios_i18nVendor.getI18NString("general.terms.done");
    var buttonArray = [
        {text: saveStr, handler: handleSave, isDefault: true},
        {text: cancelStr, handler: handleCancel}
    ];

    var panelWidth = "700px";
    var dialog = null;
    var displayOnTriggerHandler = null;

    dialog = new YAHOO.widget.Dialog('report_dialog', {
        width: panelWidth,
        modal: true,
        visible: false,
        constraintoviewport: false,
        buttons: buttonArray
    });

    dialog.showDialogPane = function () {
        var displayHandler = me['display_handler'];
        if (displayHandler != null) {
            displayHandler(dialog);
        }
        dialog.center();
        dialog.show();
    };

    // Render the Dialog
    dialog.render();

    // register a listener on some page element to trigger the display of this popup dialog
    displayOnTriggerHandler = function (type, handlerArgs) {
        if (handlerArgs[0].action == 'report_dialog_open') {
            if (handlerArgs[0].report_model != null) {
                // modify existing case
                dialog.reportModel = handlerArgs[0].report_model;
            } else {
                // Add New case
                dialog.reportModel = null;  // new ReportModel();
            }
            dialog.showDialogPane();
        }
    };

    IEvent.subscribe(displayOnTriggerHandler);

    ilios.home.report.reportDialog = dialog;
}

ilios.home.report.learningMaterialDialog = null;

/**
 * Event handler function.
 * @method assembleAddLearningMaterialsDialog
 * @param {String} type
 * @param {Array} args
 * @param {Object} me
 *     "submit_override":
 *         if this is non-null the function is subscribed to
 *         the manual submit event of the dialog and the
 *         dialog is configured to fire that event when the
 *         user clicks the 'Done' button on the dialog.
 *     "container":
 *         the container listing the present possible choices
 *         (as possibly filtered via autocompleter input)
 *     "display_handler":
 *         a function which is called just prior to the dialog
 *         being displayed
 *     "panel_width":
 *         if this is non-null, it is expected to a valid CSS
 *         measurement, else '880px' will be used.
 */
 ilios.home.report.assembleAddLearningMaterialsDialog = function (type, args, me) {
    var handleCancel = function () {
        this.cancel();
    };

    var cancelStr = ilios_i18nVendor.getI18NString("general.terms.cancel");
    var buttonArray = [
        {text: cancelStr, handler: handleCancel, isDefault: true}
    ];

    var panelWidth = (me['panel_width'] != null) ? me['panel_width'] : "840px";
    var displayOnTriggerHandler = null;
    var dialog = new YAHOO.widget.Dialog('report_learning_materials_dialog', {
        width: panelWidth,
        modal: true,
        visible: false,
        constraintoviewport: false,
        buttons: buttonArray
    });

    dialog.showDialogPane = function () {
        var displayHandler = me['display_handler'];

        if (displayHandler != null) {
            displayHandler(dialog);
        }

        dialog.center();
        dialog.show();
    };

    // Render the Dialog
    dialog.render();

    if (me['submit_override'] != null) {
        dialog.cfg.setProperty('postmethod', 'manual');
        dialog.manualSubmitEvent.subscribe(me['submit_override'], null);
    }

    // register a listener on some page element to trigger the display of this popup dialog
    displayOnTriggerHandler = function (type, handlerArgs) {
        if (handlerArgs[0].action == 'alm_dialog_open') {
            dialog.cnumber = handlerArgs[0].container_number;

            dialog.showDialogPane();
        }
    };
    IEvent.subscribe(displayOnTriggerHandler);

    ilios.home.report.learningMaterialDialog = dialog;
}

/**
 * Event handler function.
 * @method buildReportMeSHPickerDialogDOM
 * @param {String} type
 * @param {Array} args
 * @param {Object} me
 */
ilios.home.report.buildReportMeSHPickerDialogDOM = function (type, args, me) {
    var panelWidth = "450px";
    var dialog = new YAHOO.widget.Dialog('ilios_report_mesh_picker', {
        width: panelWidth,
        modal: true,
        visible: false,
        constraintoviewport: false,
        buttons: null
    });

    var displayOnTriggerHandler = null;

    dialog.showDialogPane = function () {
        ilios.home.report.populateMeSHPickerDialog();

        dialog.center();
        dialog.show();
    };

    // Render the Dialog
    dialog.render();

    displayOnTriggerHandler = function (type, handlerArgs) {
        if (handlerArgs[0].action == 'mesh_picker_dialog_open') {
            dialog.showDialogPane();
        }
    };
    IEvent.subscribe(displayOnTriggerHandler);

    ilios.mesh.meshPickerDialog = dialog;
};
