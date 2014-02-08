/**
 * School-competency framework.
 *
 * Provides functionality revolving around querying and printing aspects of a read-only school-competency map.
 *
 * Defines the following namespace:
 * ilios.competencies
 *
 * Dependencies:
 * YUI - YAHOO global object and TreeView widget
 * scripts/ilios_base.js
 * scripts/ilios_ui.js
 * scripts/ilios_utilities.js
 * scripts/models/competency_model.js
 * scripts/models/school_competency_model.js
 */

/**
 * @module Competencies
 */
(function () {

    /**
     * The "competencies" namespace-object.
     * @class competencies
     * @namespace ilios
     */
    var competencies = ilios.namespace('competencies');

    /**
     * The internal school-competencies lookup-map.
     * @property _schoolCompetencies
     * @type Object
     * @private
     */
    var _schoolCompetencies = {};

    /**
     * The internal lookup-map of subdomains.
     * @property _subdomains
     * @type Object
     * @private
     */
    var _subdomains = {};

    /**
     * A list of competencies that belong to the "active" school.
     * @property _activeSchoolCompetenciesList
     * @type Array
     * @private
     */
    var _activeSchoolCompetenciesList = [];

    /**
     * Builds a lookup map for subdomains from a given competency lookup map.
     * @method _buildSubdomainsMapFromCompetencyMap
     * @param {Object} o the competency lookup map.
     * @return {Object} the subdomains lookup map. Each attribute is an array of sudomains,
     *     keyed off by their parent competencies id.
     */
    var _buildSubdomainsMapFromCompetencyMap = function (o) {
        var i, competency, parentCompetency, parentCompetencyId;
        var rhett = {};

        for (i in o) {
            if (o.hasOwnProperty(i)) {
                competency = o[i];
                if (competency.isSubDomain()) {
                    parentCompetencyId = competency.getParentCompetencyId();
                    if (! rhett.hasOwnProperty(parentCompetencyId)) {
                        rhett[parentCompetencyId] = [];
                    }
                    rhett[parentCompetencyId].push(competency);
                } else {
                    if (! rhett.hasOwnProperty(i)) {
                        rhett[i] = [];
                    }
                }

            }
        }
        return rhett;
    }

    var _buildActiveSchoolCompetenciesList = function (o) {
        var i, competency;
        var rhett = [];

        for (i in o) {
            if (o.hasOwnProperty(i)) {
                competency = o[i];
                if (competency.belongsToActiveSchool()) {
                    rhett.push(competency);
                }
            }
        }
        return rhett;
    }

    /**
     * Sets the internal school-competency lookup-map and subdomains lookup-map.
     * @method setSchoolCompetencies
     * @param {Object} o the competency map
     */
    var setSchoolCompetencies = function (o) {
        _schoolCompetencies = o;
        _subdomains = _buildSubdomainsMapFromCompetencyMap(_schoolCompetencies);
        _activeSchoolCompetenciesList = _buildActiveSchoolCompetenciesList(_schoolCompetencies);
    };

    /**
     * Retrieves a competency by its id.
     * @method getCompetency
     * @param {int} id the competency id
     * @return {SchoolCompetencyModel | boolean} the competency object, or FALSE if not found
     */
    var getCompetency = function (id) {
        if (_schoolCompetencies.hasOwnProperty(id)) {
            return _schoolCompetencies[id];
        }
        return false;
    };

    /**
     * Retrieves the subcompetencies for a given competency.
     * @method getSubdomains
     * @param {int} id the competency id
     * @return {Array | boolean} and array of subcompetencies, or FALSE if not found
     */
    var getSubdomains = function (id) {
        if (_subdomains.hasOwnProperty(id)) {
            return _subdomains[id];
        }
        return false;
    }

    /**
     * Returns a list of competencies belonging to the "active" school.
     * @return {Array} the list of competencies.
     */
    var getActiveSchoolCompetenciesList = function () {
        return _activeSchoolCompetenciesList;
    };


    /**
     * Populates an element with selected competencies.
     * @param {Element} el element into which to append the results
     * @param {Array} selectedCompetencies a list of selected competencies
     * @param {Array} objectivesArray
     * @param {Array} boundingCohortObjectives
     * @todo clean this mess up
     */
    var appendListForSelectedCompetencies = function (el, selectedCompetencies, objectivesArray, boundingCohortObjectivesArray) {
        var lang = YAHOO.lang;
        var competencyMap = _schoolCompetencies;
        var displayMap = {};
        var displayArray = [];
        var stubObject = null;
        var model = null;
        var i, j, k, l, n, o, p, q;
        var key = null;
        var selectionArrayToUse = selectedCompetencies;
        var subdomains;
        var parentObject;
        var objectives = lang.isArray(objectivesArray) ? objectivesArray : [];
        var boundingCohortObjectives = lang.isArray(boundingCohortObjectivesArray) ? boundingCohortObjectivesArray : [];
        var competencyList;
        var competencyListItem;
        var competencySecondaryList;
        var competencySecondaryListItem;

        if (objectives.length) {
            var parentObjectives = null;
            var competencyIds = [];
            var objectiveId = null;
            var added = false;
            var selectionArray = [];

            for (i = 0, n = objectives.length; i < n; i++) {
                model = objectives[i];
                if (! model) { // filter out empty array elements
                    continue;
                }
                parentObjectives = model.getParentObjectives();

                for (j = 0, o = parentObjectives.length; j < o; j++) {
                    objectiveId = parentObjectives[j];
                    added = false;
                    for (k = 0, p = boundingCohortObjectives.length; k < p; k++) {
                        model = boundingCohortObjectives[k];

                        for (l = 0, q = model.objectives.length; l < q; l++) {
                            if (model.objectives[l].getDBId() == objectiveId) {
                                competencyIds.push(model.objectives[l].getCompetencyId());
                                added = true;
                                break;
                            }
                        }

                        if (added) {
                            break;
                        }
                    }
                }
            }

            // de-dupe competencies
            competencyIds = ilios.utilities.makeUniqueArray(competencyIds);

            for (i = 0, n = competencyIds.length; i < n; i++) {
                model = competencyMap[competencyIds[i]];
                selectionArray.push(model);
            }
            selectionArrayToUse = selectionArray;
        }

        // go through the the list of selected competencies and
        // extract all the parent competencies.
        // store them in the comp. display tree.
        for (i = 0, n = selectionArrayToUse.length; i < n; i++) {
            model = selectionArrayToUse[i];
            if (! model) {
                continue;
            }

            if (model.isSubDomain()) { // given competency is a subdomain
                // find the parent competency
                stubObject = displayMap[model.getParentCompetencyId()];

                if (! stubObject) { // not in the display map? create a new entry for the parent comp.
                    parentObject = competencyMap[model.getParentCompetencyId()];
                    stubObject = {};
                    if (parentObject) {
                        stubObject.competencyTitle = parentObject.getCompetencyTitle();
                        stubObject.competencyId = parentObject.getDBId();
                    }
                    stubObject.subdomains = [];
                    displayMap[stubObject.competencyId] = stubObject;
                }
                // save the title of the subdomain in the parent comp. stub
                stubObject.subdomains.push(model.getCompetencyTitle());
            } else { // given competency is top-level
                stubObject = competencyMap[model.getParentCompetencyId()];

                if (! stubObject) { // make a new entry in the display tree if non-existent
                    stubObject = {};
                    stubObject.competencyTitle = model.getCompetencyTitle();
                    stubObject.competencyId = model.getDBId();
                    stubObject.subdomains = [];
                    displayMap[stubObject.competencyId] = stubObject;
                }
            }
        }
        // convert the display map into an array
        // while at it, add the titles of all sudomains to the competencies
        // that have no explicitly selected sudomains
        for (i in displayMap) {
            if (displayMap.hasOwnProperty(i)) {
                stubObject = displayMap[i];
                if (! stubObject.subdomains.length) { // no subdomains stored? show them all!
                    subdomains = getSubdomains(stubObject.competencyId);
                    if (subdomains) {
                        for (j = 0; j < subdomains.length; j++) {
                            stubObject.subdomains.push(subdomains[j].getCompetencyTitle());
                        }
                    }
                }
                stubObject.subdomains.sort();
                displayArray.push(stubObject);
            }

        }
        // sort by competency title
        displayArray.sort(function (a, b) {
            return a.competencyTitle.localeCompare(b.competencyTitle);
        });

        // finally, generate the list
        for (i = 0, n = displayArray.length; i < n; i++) {
            stubObject = displayArray[i];
            if (0 === i) {
                competencyList = document.createElement('ul');
                competencyList.setAttribute('class', 'competency-list');
            }
            competencyListItem = document.createElement('li');
            competencyListItem.appendChild(document.createTextNode(stubObject.competencyTitle));
            competencyList.appendChild(competencyListItem);
            for (j = 0, o = stubObject.subdomains.length; j < o; j++) {
                if (0 === j) {
                    competencySecondaryList = document.createElement('ul');
                }
                competencySecondaryListItem = document.createElement('li');
                competencySecondaryListItem.appendChild(document.createTextNode(stubObject.subdomains[j]));
                competencySecondaryList.appendChild(competencySecondaryListItem);
            }
            if (j > 0) {
                competencyList.appendChild(competencySecondaryList);
            }
        }

        if (i > 0) {
            el.appendChild(competencyList);
        }
    };

    /**
     * Competency-picker utility function.
     * Hides competency-nodes from a given TreeView widget
     * once all of their respective subdomains have been selected.
     * Competency nodes that have no subdomains to begin with remain as-is.
     * @method verifyChildlessUnselectedCompetencyNodes
     * @param {YAHOO.widget.TreeView} treeview
     * @todo move this functionality elsewhere when consolidating the competency picker variations.
     */
    var verifyChildlessUnselectedCompetencyNodes = function (treeView) {
        var rootNode = treeView.getRoot();
        var children = rootNode.children;
        var len = (children != null) ? children.length : 0;
        var node = null;
        var subdomains = null;
        var i;

        for (i = 0; i < len; i++) {
            node = children[i];

            if (node.children.length == 0) { // check if all sudomain nodes have been picked off
                subdomains = getSubdomains(node.iliosModel.getDBId());

                if (subdomains.length > 0) { // ignore competencies without subdomains
                    treeView.popNode(node);
                }
            }
        }
    };

    /**
     * Returns a (deduped) list of competency titles of a given list of mixed competencies and subdomains.
     * For each sudomain in the given list, the title of its parent competency is returned.
     * @method generateSummaryStringForSelectedCompetencies
     * @param {Array} a a list of competencies
     * @return {Array} a list of competency titles
     */
    var generateSummaryStringForSelectedCompetencies = function (a) {
        var titles = {};
        var parentObject = null;
        var model = null;
        var rhett = '';
        var sortedNames = [];
        var i, n;

        for (i = 0, n = a.length; i < n; i++) {
            model = a[i];

            if (! model) {
                continue;
            }

            if (model.isSubDomain()) {
                parentObject = getCompetency(model.getParentCompetencyId());
            } else {
                parentObject = getCompetency(model.getDBId());
            }
            if (parentObject) {
                titles[parentObject.getCompetencyTitle()] = 'x';
            }
        }

        for (var key in titles) {
            sortedNames.push(key);
        }

        sortedNames.sort();

        for (i = 0, n = sortedNames.length; i < n; i++) {
            if (i > 0) {
                rhett += '; ';
            }
            rhett += sortedNames[i];
        }
        return rhett;
    };

    /**
     * Flattens out a given hierarchical structure of school records containing their respective
     * competencies/subdomains into a lookup map object containing school-competencies
     * keyed off by their competency ids.
     * @method parseSchoolCompetencies
     * @param {Array} a an array of school objects containing competencies
     * @return {Object} lookup map of school-competencies, keyed off by competency ids
     * @static
     */
    var convertSchoolCompetencyHierarchiesIntoLookupMap = function (a) {
        var competency, subdomain;
        var schoolDto, competencyDto, subdomainDto;
        var i, j, k, n, o, p;
        var map = {};

        for (i = 0, n = a.length; i < n; i++) {
            schoolDto = a[i];
            for (j = 0, o = schoolDto.competencies.length; j < o; j++) {
                competencyDto = schoolDto.competencies[j];
                competency = new SchoolCompetencyModel(competencyDto.title, -1,
                    competencyDto.competency_id, schoolDto.school_id, schoolDto.is_active_school);
                map[competencyDto.competency_id] = competency;
                for (k = 0, p = competencyDto.subdomains.length; k < p; k++) {
                    subdomainDto = competencyDto.subdomains[k];
                    subdomain = new SchoolCompetencyModel(subdomainDto.title, competencyDto.competency_id,
                        subdomainDto.competency_id, schoolDto.school_id, schoolDto.is_active_school);
                    map[subdomainDto.competency_id] = subdomain;
                }
            }
        }
        return map;
    };

    // define public interface of this module/namespace.
    competencies.getActiveSchoolCompetenciesList = getActiveSchoolCompetenciesList;
    competencies.setSchoolCompetencies = setSchoolCompetencies;
    competencies.getCompetency = getCompetency;
    competencies.getSubdomains = getSubdomains;
    competencies.appendListForSelectedCompetencies = appendListForSelectedCompetencies;
    competencies.verifyChildlessUnselectedCompetencyNodes = verifyChildlessUnselectedCompetencyNodes;
    competencies.generateSummaryStringForSelectedCompetencies = generateSummaryStringForSelectedCompetencies;
    competencies.convertSchoolCompetencyHierarchiesIntoLookupMap = convertSchoolCompetencyHierarchiesIntoLookupMap;
}());
