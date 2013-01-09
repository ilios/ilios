/**
 * Defines the following Namespaces
 *
 * ilios.mesh
 *
 * Dependencies:
 *
 * YUI lib
 * scripts/ilios_ui.js
 * scripts/ilios_utilities.js
 * scripts/models/mesh_item_model.js
 * variable "controllerURL" must be defined (can be any controller which 'subclasses' the abstract ilios controller)
 */

ilios.namespace('mesh');

// Consumers can modify these two to insert their own handlers
ilios.mesh.meshSearchResultSelectionHandler = null;  // defaults to ilios.mesh.handleMeSHResultSelection
ilios.mesh.meshSearchResultDeselectionHandler = null;  // defaults to ilios.mesh.handleMeSHDeselection

ilios.mesh.currentReturnPreviousSearchModels = null;

ilios.mesh.meshPickerDialog = null;
ilios.mesh.meshInEditReferenceModel = null;
ilios.mesh.meshInEditModel = null;

// these two are intended to be used to keep track of a user's current searching and
//      selecting - with the goal of informing the server of these selections once
//      the user 'save's the panel (so that the server can include these selections
//      as search results in the future)
ilios.mesh.lastMeSHSearchQuery = null;
ilios.mesh.currentEditQueryUIDPairSelections = null;


ilios.mesh.saveMeSHSearchSelections = function () {
    var url = controllerURL + "saveMeSHSearchSelection";
    var method = "POST";
    var paramString = "selection_pairs="
                            + escape(YAHOO.lang.JSON.stringify(ilios.mesh.currentEditQueryUIDPairSelections));
    var ajaxCallback = {
            success: function (resultObject) {
                var parsedObject = null;

                try {
                    parsedObject = YAHOO.lang.JSON.parse(resultObject.responseText);
                }
                catch (e) {
                    // don't really care here

                    return;
                }

                // MAY RETURN THIS BLOCK
                if (parsedObject.error != null) {
                    // do we really care to alert users that this failed?

                    return;
                }
            },

            failure: function (resultObject) {
                ilios.global.defaultAJAXFailureHandler(resultObject);
            }};

    YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);
};

ilios.mesh.currentSearchTransactionASyncObject = null;

ilios.mesh.performMeSHSearch = function (searchTerms, filterSelectedItems, storeQueryTerms) {
    var searchInfoElement = document.getElementById('mesh_search_status');

    if (ilios.lang.trim(searchTerms).length < 3) {
        searchInfoElement.innerHTML = ilios_i18nVendor.getI18NString('general.error.query_length');
    }
    else {
        var url = controllerURL + "searchMeSHUniverseForIlios";
        var method = "POST";
        var paramString = "query=" + escape(searchTerms);
        var ajaxCallback = {
            success: function (resultObject) {
                var parsedObject = null;
                var resultsContainer = document.getElementById('mesh_search_results');
                var resultsObject = null;
                var element = null;
                var model = null;
                var i18nString = ilios_i18nVendor.getI18NString('mesh.dialog.parsing_results');
                var meshItemModel = null;
                var universeSearchCount = 0;
                var key = null;

                ilios.mesh.currentSearchTransactionASyncObject = null;

                try {
                    parsedObject = YAHOO.lang.JSON.parse(resultObject.responseText);
                }
                catch (e) {
                    document.getElementById('mesh_search_status').innerHTML
                                        = ilios_i18nVendor.getI18NString('general.error.must_retry');

                    return;
                }
                resultsObject = parsedObject.results;
                universeSearchCount = ilios.utilities.arraySize(resultsObject.search_results);

                document.getElementById('mesh_search_status').innerHTML = i18nString + '&hellip;';

                ilios.utilities.removeAllChildren(resultsContainer);

                // TODO screen against items already existing in the inEdit model

                ilios.mesh.currentReturnPreviousSearchModels = new Array();

                if ((resultsObject.previous_searches.length == 0) && (universeSearchCount == 0)) {
                    document.getElementById('mesh_search_status').innerHTML
                                        = ilios_i18nVendor.getI18NString('mesh.dialog.no_matches');

                    return;
                }

                element = document.createElement('div');
                element.setAttribute('id', 'mesh_previous_searches_results_sub_div');
                element.setAttribute('style', 'font-size: 11pt; text-align: center;');
                i18nString = ilios_i18nVendor.getI18NString('mesh.dialog.previous_search_results');
                element.innerHTML = i18nString + " <span style='font-weight:bold; "
                                        + "font-size: 8pt; color: #3261af;'>("
                                        + resultsObject.previous_searches.length + ")</span>";
                for (key in resultsObject.previous_searches) {
                    model = resultsObject.previous_searches[key];

                    meshItemModel = new MeSHItemModel(model.mesh_object);
                    meshItemModel.isPreviousSearch = true;
                    meshItemModel.searchedTerm = model.searched_term;

                    ilios.mesh.currentReturnPreviousSearchModels.push(meshItemModel);

                    if ((! filterSelectedItems)
                               || (! ilios.mesh.meshInEditModel.containsMeSHItem(meshItemModel))) {
                        element.appendChild(ilios.mesh.searchResultDivForMeSHModel(meshItemModel,
                                                                                   model.searched_term));
                    }
                }
                resultsContainer.appendChild(element);

                element = document.createElement('hr');
                element.setAttribute('style', 'align: center; width: 73%;');
                resultsContainer.appendChild(element);


                element = document.createElement('div');
                element.setAttribute('id', 'mesh_universe_search_results_sub_div');
                element.setAttribute('style', 'font-size: 11pt; text-align: center;');
                i18nString = ilios_i18nVendor.getI18NString('mesh.dialog.search_matches');
                element.innerHTML = i18nString + " <span style='font-weight:bold; "
                                        + "font-size: 8pt; color: #3261af;'>("
                                        + universeSearchCount + ")</span>";
                for (key in resultsObject.search_results) {
                    model = resultsObject.search_results[key];

                    meshItemModel = new MeSHItemModel(model);

                    if ((! filterSelectedItems)
                               || (! ilios.mesh.meshInEditModel.containsMeSHItem(meshItemModel))) {
                        element.appendChild(ilios.mesh.searchResultDivForMeSHModel(meshItemModel));
                    }
                }
                resultsContainer.appendChild(element);

                document.getElementById('mesh_search_status').innerHTML = "";
            },

            failure: function (resultObject) {
                ilios.mesh.currentSearchTransactionASyncObject = null;
            }};

        if (storeQueryTerms) {
            ilios.mesh.lastMeSHSearchQuery = searchTerms;
        }

        searchInfoElement.innerHTML = ilios_i18nVendor.getI18NString('general.terms.searching')
                                        + '&hellip;';

        ilios.mesh.currentSearchTransactionASyncObject
                        = YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);
    }
};

ilios.mesh.handleMeSHPickerCancel = function (dialogPanel) {
    if (ilios.mesh.currentSearchTransactionASyncObject != null) {
        YAHOO.util.Connect.abort(ilios.mesh.currentSearchTransactionASyncObject);

        ilios.mesh.currentSearchTransactionASyncObject = null;
    }

    ilios.mesh.meshInEditModel = null;
    ilios.mesh.meshInEditReferenceModel = null;

    ilios.mesh.lastMeSHSearchQuery = null;
    ilios.mesh.currentEditQueryUIDPairSelections = null;
};

ilios.mesh.populateMeSHPickerDialog = function () {
    var meshItems = ilios.mesh.meshInEditModel.getMeSHItems();
    var searchResultsSelectionContainer = document.getElementById('mesh_descriptors_picked');

    ilios.utilities.removeAllChildren(document.getElementById('mesh_search_results'));
    document.getElementById('mesh_search_terms').value = '';

    document.getElementById('mesh_search_status').innerHTML = '';

    ilios.utilities.removeAllChildren(searchResultsSelectionContainer);
    for (var key in meshItems) {
        searchResultsSelectionContainer.appendChild(
                                            ilios.mesh.selectionLIForMeSHItemModel(meshItems[key]));
    }
};

/**
 * Generates markup for displaying a given search result details from the MeSH universe.
 * @method meshDetailDivForModel
 * @param {MeSHItemModel} meshItemModel the MeSH item
 * @param {String} [previousSearchTerm] an previously used search term
 * @return {HTMLElement} the generated markup
 * @private
 */
ilios.mesh.meshDetailDivForModel = function (meshItemModel, previousSearchTerm) {
    var rhett, elem, str;
    var scopeNotes, scopeNote, i, n;
    var treeNodes, treeNode;

    rhett = document.createElement('div');
    rhett.setAttribute('class', 'mesh_search_result_item');

    elem = document.createElement('span');
    elem.setAttribute('class', 'title');
    elem.innerHTML = meshItemModel.getTitle();
    rhett.appendChild(elem);

    previousSearchTerm = YAHOO.lang.isString(previousSearchTerm) ? previousSearchTerm : '';
    if (previousSearchTerm.length) {
        elem = document.createElement('span');
        elem.setAttribute('class', 'prev_search');
        elem.innerHTML = "(" + previousSearchTerm + ")";
        rhett.appendChild(elem);
    }

    elem = document.createElement('div');
    elem.setAttribute('class', 'identifiers');
    str = meshItemModel.getDBId();
    treeNodes = meshItemModel.getTree();
    if (treeNodes.length) {
        treeNode = treeNodes[treeNodes.length - 1]; // get the last node in the list
        str += ' - ' + treeNode.tree_number;
    }
    elem.innerHTML = str;
    rhett.appendChild(elem);

    scopeNotes = meshItemModel.getScopeNotes();
    for (i = 0, n =  scopeNotes.length; i < n; i++) {
        scopeNote = scopeNotes[i];

        if (scopeNote.length > 150) {
            scopeNote = scopeNote.substring(0, 150) + "&hellip;";
        }

        elem = document.createElement('div');
        elem.setAttribute('class', 'scope_note');
        elem.innerHTML = "<strong>&middot;</strong> " + scopeNote;
        rhett.appendChild(elem);
    }


    return rhett;
};

/**
 * Generates markup for displaying a given search result item from the MeSH universe.
 * @method searchResultDivForMeSHModel
 * @param {MeSHItemModel} meshItemModel the MeSH item
 * @param {String} [previousSearchTerm] an previously used search term
 * @return {HTMLElement} the generated markup
 * @private
 */
ilios.mesh.searchResultDivForMeSHModel = function (meshItemModel, previousSearchTerm) {
    var Event = YAHOO.util.Event;
    var rhett, elem;

    rhett = document.createElement('div');
    rhett.setAttribute('class', 'mesh_search_result_item_wrapper');
    elem = ilios.mesh.meshDetailDivForModel(meshItemModel, previousSearchTerm);
    rhett.appendChild(elem);
    rhett.iliosModel = meshItemModel;

    // @todo delegate event handling to parent elem
    Event.addListener(rhett, 'mouseover', function (e) {
        (new YAHOO.util.Element(this)).setStyle('background-color','#EBE9ED');
    });
    Event.addListener(rhett, 'mouseout', function (e) {
        (new YAHOO.util.Element(this)).setStyle('background-color', '#fff');
    });
    Event.addListener(rhett, 'click', function (e) {
        ilios.mesh.meshSearchResultSelectionHandler(this);
    });

    ilios.utilities.appendClearingDivToContainer(rhett);

    return rhett;
};

// @private
ilios.mesh.selectionLIForMeSHItemModel = function (meshItemModel) {
    var rhett = document.createElement('li');

    rhett.setAttribute('style', 'cursor: pointer;');
    rhett.innerHTML = meshItemModel.getTitle();

    rhett.iliosModel = meshItemModel;

    YAHOO.util.Event.addListener(rhett, 'click', function (e) {
        ilios.mesh.meshSearchResultDeselectionHandler(this);
    });

    return rhett;
};

// @private
ilios.mesh.handleMeSHResultSelection = function (selectedDiv) {
    var meshItem = selectedDiv.iliosModel;
    var searchResultsContainer = selectedDiv.parentNode;
    var searchResultsSelectionContainer = document.getElementById('mesh_descriptors_picked');
    var pairObject = null;

    searchResultsContainer.removeChild(selectedDiv);
    searchResultsSelectionContainer.appendChild(ilios.mesh.selectionLIForMeSHItemModel(meshItem));

    ilios.mesh.meshInEditModel.addMeSHItem(meshItem);

    // MAY RETURN THIS BLOCK
    if (meshItem.isPreviousSearch) {
        return;
    }
    else {
        var model = null;
        var searchedTerm = document.getElementById('mesh_search_terms').value;

        for (var i = 0; i < ilios.mesh.currentReturnPreviousSearchModels.length; i++) {
            model = ilios.mesh.currentReturnPreviousSearchModels[i];

            if (model.searchedTerm == searchedTerm) {
                if (model.getDBId() == meshItem.getDBId()) {
                    return;
                }
            }
        }
    }

    pairObject = new Object();
    pairObject.searchTerm = ilios.mesh.lastMeSHSearchQuery;
    pairObject.uid = meshItem.getDBId();

    if (ilios.mesh.currentEditQueryUIDPairSelections == null) {
        ilios.mesh.currentEditQueryUIDPairSelections = new Array();
    }

    ilios.mesh.currentEditQueryUIDPairSelections.push(pairObject);
};

ilios.mesh.meshSearchResultSelectionHandler = ilios.mesh.handleMeSHResultSelection;

// @private
ilios.mesh.handleMeSHDeselection = function (selectedLI) {
    var meshItem = selectedLI.iliosModel;
    var searchResultsSelectionContainer = document.getElementById('mesh_descriptors_picked');

    searchResultsSelectionContainer.removeChild(selectedLI);

    ilios.mesh.meshInEditModel.removeMeSHItem(meshItem);

    if (ilios.mesh.currentEditQueryUIDPairSelections != null) {
        for (var key in ilios.mesh.currentEditQueryUIDPairSelections) {
            if (ilios.mesh.currentEditQueryUIDPairSelections[key].uid == meshItem.getDBId()) {
                delete ilios.mesh.currentEditQueryUIDPairSelections[key];

                return;
            }
        }
    }
};

ilios.mesh.meshSearchResultDeselectionHandler = ilios.mesh.handleMeSHDeselection;
