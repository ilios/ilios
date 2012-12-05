/**
 * Defines functionality for handling/exposing 'learner view' data (in the student dashboard/calendar).

 * DEPENDENCY: YUI lib
 * DEPENDENCY: scripts/ilios_ui.js
 * DEPENDENCY: scripts/ilios_utilities.js
 * DEPENDENCY: scripts/models/learning_material_model.js
 * DEPENDENCY: scripts/models/offering_model.js
 * DEPENDENCY: scripts/models/session_model.js
 * DEPENDENCY: scripts/models/user_model.js
 * DEPENDENCY: scripts/public_course_summary_base_framework.js
 * DEPENDENCY: controllerURL defined (can be any controller which 'subclasses' the abstract ilios
 *                                         controller)
 */

/**
 * Namespace object for grouping learner-view functionality.
 * @class learner_view
 * @static
 */
ilios.namespace('learner_view');

ilios.learner_view.learnerViewDialog = null;

ilios.learner_view.loadSILMDetailsIntoDivContainer = function (sessionId, divContainer,
                                                               updateProgress) {
    var url = controllerURL + "getLearnerDashboardSummaryForSILM";
    var method = "POST";
    var paramString = "session_id=" + sessionId;
    var ajaxCallback = {
            success: function (resultObject) {
                var parsedObject = null;

                try {
                    parsedObject = YAHOO.lang.JSON.parse(resultObject.responseText);
                }
                catch (e) {
                    if (updateProgress) {
                        ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                                                               'general.phrases.loading_details');
                        ilios.alert.updateServerInteractionProgress();
                    }

                    ilios.global.defaultAJAXFailureHandler(null, e);

                    return;
                }

                // MAY RETURN THIS BLOCK
                if (parsedObject.error != null) {
                    var msg
                          = ilios_i18nVendor.getI18NString('general.error.details_load');

                    if (updateProgress) {
                        ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                                                               'general.phrases.loading_details');
                        ilios.alert.updateServerInteractionProgress();
                    }

                    ilios.alert.alert(msg + ": " + parsedObject.error);

                    return;
                }

                ilios.learner_view.generateCourseAndSessionSummaryFromParsedServerReturn(parsedObject,
                                                                                         divContainer);

                if (updateProgress) {
                    ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                                                           'general.phrases.loading_details');
                    ilios.alert.updateServerInteractionProgress();
                }
            },

            failure: function (resultObject) {
                ilios.global.defaultAJAXFailureHandler(resultObject);
            }};


    if (updateProgress) {
        ilios.alert.networkActivityI18NStrings.push('general.phrases.loading_details');
        ilios.alert.updateServerInteractionProgress();
    }

    YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);
};


ilios.learner_view.loadOfferingDetailsIntoDivContainer = function (offeringId, divContainer,
                                                                   updateProgress) {
    var url = controllerURL + "getLearnerDashboardSummaryForOffering";
    var method = "POST";
    var paramString = "offering_id=" + offeringId;
    var ajaxCallback = {
            success: function (resultObject) {
                var parsedObject = null;

                try {
                    parsedObject = YAHOO.lang.JSON.parse(resultObject.responseText);
                }
                catch (e) {
                    if (updateProgress) {
                        ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                                                               'general.phrases.loading_details');
                        ilios.alert.updateServerInteractionProgress();
                    }

                    ilios.global.defaultAJAXFailureHandler(null, e);

                    return;
                }

                // MAY RETURN THIS BLOCK
                if (parsedObject.error != null) {
                    var msg
                          = ilios_i18nVendor.getI18NString('general.error.details_load');

                    if (updateProgress) {
                        ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                                                               'general.phrases.loading_details');
                        ilios.alert.updateServerInteractionProgress();
                    }

                    ilios.alert.alert(msg + ": " + parsedObject.error);

                    return;
                }

                ilios.learner_view.generateCourseAndSessionSummaryFromParsedServerReturn(parsedObject,
                                                                                         divContainer);

                if (updateProgress) {
                    ilios.utilities.removeElementWithValue(ilios.alert.networkActivityI18NStrings,
                                                           'general.phrases.loading_details');
                    ilios.alert.updateServerInteractionProgress();
                }
            },

            failure: function (resultObject) {
                ilios.global.defaultAJAXFailureHandler(resultObject);
            }};


    if (updateProgress) {
        ilios.alert.networkActivityI18NStrings.push('general.phrases.loading_details');
        ilios.alert.updateServerInteractionProgress();
    }

    YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);
};

// @private
ilios.learner_view.generateCourseAndSessionSummaryFromParsedServerReturn = function (parsedObject, inspectorContainer) {
    var Element = YAHOO.util.Element;
    var sessionModel = new SessionModel(parsedObject.session);
    var isSILM = (typeof parsedObject.offering == 'undefined');
    var offeringModel = isSILM ? null : new OfferingModel(parsedObject.offering);
    var learningModel = null;
    var container = null;
    var subContainer = null;
    var element = null;
    var str = null;
    var key = null;
    var collapsingDiv = null;
    var collapsingDivId = null;
    var enableAttachmentLinks = (typeof parsedObject.is_learner != 'undefined') && parsedObject.is_learner;
    var courseInfo, sessionInfo;

    enableAttachmentLinks = (typeof pageLoadedForStudent != 'undefined') && pageLoadedForStudent ? enableAttachmentLinks : true;

    ilios.utilities.removeAllChildren(inspectorContainer);

    container = document.createElement('h2');
    container.setAttribute('class', 'lv-title');

    if (parsedObject.is_tbd) {
        str = "<center>" + ilios_i18nVendor.getI18NString('general.terms.scheduled') + "</center>";
        container.innerHTML = str;
        inspectorContainer.appendChild(container);
        return;
    }

    str = parsedObject.course.title + " - " + sessionModel.getTitle();
    container.appendChild(document.createTextNode(str));
    inspectorContainer.appendChild(container);

    courseInfo = document.createElement('fieldset');
    subContainer = document.createElement('legend');
    str = ilios_i18nVendor.getI18NString('general.terms.course');
    subContainer.appendChild(document.createTextNode(str));
    courseInfo.appendChild(subContainer);

    container = document.createElement('div');
    container.setAttribute('class','row lv-overview');
    if (isSILM) {
	var dueDate = ilios.utilities.mySQLDateToDateObject(parsedObject.silm.due_date + " 17:00:00", false);

        str = parsedObject.silm.hours + ' ' + ilios_i18nVendor.getI18NString('general.terms.hours').toLowerCase();
        str += ' ' + ilios_i18nVendor.getI18NString('general.phrases.due_by').toLowerCase();
        str += ' ' + dueDate.format("dddd, mmmm d, yyyy");
    } else {
        str = ilios_i18nVendor.getI18NString('general.terms.on_date_like');
        str += ' ' + offeringModel.getStartDate().format("dddd, mmmm d, yyyy");
        str += " " + ilios_i18nVendor.getI18NString('general.phrases.starting_at').toLowerCase() + " ";
        str += offeringModel.getStartDate().format("h:MM TT");
        if (ilios.utilities.arraySize(parsedObject.offering_instructors) > 0) {
            var firstDone = false;
            var userModel = false;

            str += " " + ilios_i18nVendor.getI18NString('general.phrases.taught_by').toLowerCase() + " ";

            for (key in parsedObject.offering_instructors) {
                userModel = new UserModel(parsedObject.offering_instructors[key]);

                if (firstDone) {
                    str += ", ";
                }

                str += userModel.getFormattedName(ilios.utilities.USER_NAME_FORMAT_FIRST_INITIAL_FIRST);

                firstDone = true;
            }
        }
        if ((typeof parsedObject.offering.room != 'undefined')
                && (parsedObject.offering.room != null)
                && (parsedObject.offering.room.length > 0)) {
            str += " " + ilios_i18nVendor.getI18NString('general.terms.in_location').toLowerCase() + " "
                + parsedObject.offering.room;
        }
    }

    container.appendChild(document.createTextNode(str));
    courseInfo.appendChild(container);


    container = document.createElement('div');
    container.setAttribute('class', 'row lv-type');
    str = ilios_i18nVendor.getI18NString('dashboard.offering_description.offering_type') + " ";
    str += isSILM ? ilios_i18nVendor.getI18NString('general.phrases.independent_learning_item')
                  : parsedObject.session_type.title;
    if (sessionModel.isSupplemental()) {
        str += " " + ilios_i18nVendor.getI18NString('dashboard.offering_description.offering_supplemental_suffix');
    }
    container.appendChild(document.createTextNode(str));
    courseInfo.appendChild(container);


    if (sessionModel.isAttireRequired()) {
        container = document.createElement('div');
        container.setAttribute('class', 'row lv-attire');

        str = ilios_i18nVendor.getI18NString('dashboard.offering_description.special_attire') + ".";
        container.appendChild(document.createTextNode(str));

        subContainer = document.createElement('div');
        subContainer.setAttribute('class', 'attire_needed_badge');
        container.appendChild(subContainer);
        courseInfo.appendChild(container);
    }


    if (sessionModel.isEquipmentRequired()) {
        container = document.createElement('div');
        container.setAttribute('class', 'row lv-equipment');

        str = ilios_i18nVendor.getI18NString('dashboard.offering_description.special_equipment') + ".";
        container.appendChild(document.createTextNode(str));

        subContainer = document.createElement('div');
        subContainer.setAttribute('class', 'equipment_needed_badge');
        container.appendChild(subContainer);
        courseInfo.appendChild(container);
    }


    if (sessionModel.getDescription().length > 0) {
        container = document.createElement('div');
        container.setAttribute('class', 'row lv-description');
        str = ilios_i18nVendor.getI18NString('dashboard.offering_description.session_overview') + ":";
        container.appendChild(document.createTextNode(str));

        container.appendChild(document.createElement('br'));

        str = ilios.utilities.percentUnicodeToHTML(sessionModel.getDescription());
        container.appendChild(document.createTextNode(str));
        courseInfo.appendChild(container);
    }

    if (ilios.utilities.arraySize(parsedObject.course_objectives) > 0) {
        container = document.createElement('div');
        container.setAttribute('class', 'row lv-objectives');
        str = ilios_i18nVendor.getI18NString('general.terms.course') + ' ';
        str += ilios_i18nVendor.getI18NString('general.terms.objectives') + ":";
        container.appendChild(document.createTextNode(str));

        collapsingDivId = ilios.dom.childCollapsingContainerIdForContainerNumber(5000);
        collapsingDiv = new Element(document.createElement('div'), { id: collapsingDivId });
        collapsingDiv.addClass('collapsible_container');
        collapsingDiv.addClass('display_hide');

        subContainer = document.createElement('ul');
        for (key in parsedObject.course_objectives) {
            element = document.createElement('li');
            str = ilios.utilities.percentUnicodeToHTML(parsedObject.course_objectives[key].title);
            // Must be innerHTML to correctly render the HTML markup
            element.innerHTML = str;
            subContainer.appendChild(element);
        }

        collapsingDiv.appendChild(subContainer);

        container.appendChild(ilios.dom.createExpandCollapseWidgetForContainerNumber(5000, null));
        container.appendChild(collapsingDiv.get('element'));
        courseInfo.appendChild(container);
    }

    if (ilios.utilities.arraySize(parsedObject.course_learning_materials) > 0) {
        container = document.createElement('div');
        container.setAttribute('class','row lv-learning-materials');
        str = ilios_i18nVendor.getI18NString('general.terms.course') + ' ';
        str += ilios_i18nVendor.getI18NString('general.phrases.learning_materials') + ":";
        container.appendChild(document.createTextNode(str));

        collapsingDivId = ilios.dom.childCollapsingContainerIdForContainerNumber(5001);
        collapsingDiv = new Element(document.createElement('div'), { id: collapsingDivId });
        collapsingDiv.addClass('collapsible_container');
        collapsingDiv.addClass('display_hide');

        subContainer = document.createElement('ul');
        subContainer.setAttribute('class', 'learning_material_list');
        for (key in parsedObject.course_learning_materials) {
            learningModel = new LearningMaterialModel(parsedObject.course_learning_materials[key]);
            element = ilios.course_summary.createListElementForLearnerViewLearningMaterial(learningModel,enableAttachmentLinks);
            subContainer.appendChild(element);
        }

        collapsingDiv.appendChild(subContainer);

        container.appendChild(ilios.dom.createExpandCollapseWidgetForContainerNumber(5001, null));
        container.appendChild(collapsingDiv.get('element'));
        courseInfo.appendChild(container);

    }

    inspectorContainer.appendChild(courseInfo);

    //show course summary button:
    container = document.createElement('div');
    container.setAttribute('class', 'row buttons');
    element = document.createElement('a');
    element.setAttribute('class', 'tiny radius button');
    element.setAttribute('href', '');
    element.setAttribute('onclick', 'return false;');
    element.innerHTML = ilios_i18nVendor.getI18NString('general.phrases.show_course_summary');
    YAHOO.util.Event.addListener(element, 'click', function (e) { // Closure!
        ilios.course_summary.loadCourseAndShowSummary(parsedObject.course.course_id);
        if (ilios.learner_view.learnerViewDialog != null) {
            ilios.learner_view.learnerViewDialog.cancel();
        }
    });
    container.appendChild(element);
    inspectorContainer.appendChild(container);

    if ((ilios.utilities.arraySize(parsedObject.session_objectives) > 0)
        || (ilios.utilities.arraySize(parsedObject.session_learning_materials) > 0)
        || ((parsedObject.session_description != null)
            && (parsedObject.session_description.length > 0))) {
        sessionInfo = document.createElement('fieldset');
        subContainer = document.createElement('legend');
        subContainer.innerHTML = ilios_i18nVendor.getI18NString('general.terms.session');
        sessionInfo.appendChild(subContainer);
    }

    if ((parsedObject.session_description != null)
        && (parsedObject.session_description.length > 0)) {
        container = document.createElement('div');
        container.setAttribute('class','row');
        str = ilios_i18nVendor.getI18NString('general.terms.session') + ' ';
        str += ilios_i18nVendor.getI18NString('general.terms.description') + ":";
        container.appendChild(document.createTextNode(str));
        container.appendChild(document.createElement('br'));

        collapsingDivId = ilios.dom.childCollapsingContainerIdForContainerNumber(5002);
        collapsingDiv = new Element(document.createElement('div'), { id: collapsingDivId });
        collapsingDiv.addClass('collapsible_container');
        collapsingDiv.addClass('display_show');

        subContainer = document.createElement('div');
        subContainer.innerHTML = parsedObject.session_description;

        collapsingDiv.appendChild(subContainer);

        container.appendChild(ilios.dom.createExpandCollapseWidgetForContainerNumber(5002, null));
        container.appendChild(collapsingDiv.get('element'));
        sessionInfo.appendChild(container);
    }

    if (ilios.utilities.arraySize(parsedObject.session_objectives) > 0) {
        container = document.createElement('div');
        container.setAttribute('class','row');
        str = ilios_i18nVendor.getI18NString('general.terms.session') + ' ';
        str += ilios_i18nVendor.getI18NString('general.terms.objectives') + ":";
        container.appendChild(document.createTextNode(str));
        container.appendChild(document.createElement('br'));

        collapsingDivId = ilios.dom.childCollapsingContainerIdForContainerNumber(5003);
        collapsingDiv = new Element(document.createElement('div'), { id: collapsingDivId });
        collapsingDiv.addClass('collapsible_container');
        collapsingDiv.addClass('display_show');

        ilios.learner_view.buildCollatedSessionObjectives(parsedObject,
                                                          collapsingDiv.get('element'));

        container.appendChild(ilios.dom.createExpandCollapseWidgetForContainerNumber(5003, null));
        container.appendChild(collapsingDiv.get('element'));
        sessionInfo.appendChild(container);
    }

    if (ilios.utilities.arraySize(parsedObject.session_learning_materials) > 0) {
        container = document.createElement('div');
        container.setAttribute('class','row');
        str = ilios_i18nVendor.getI18NString('general.terms.session') + ' ';
        str += ilios_i18nVendor.getI18NString('general.phrases.learning_materials') + ":";
        container.appendChild(document.createTextNode(str));

        collapsingDivId = ilios.dom.childCollapsingContainerIdForContainerNumber(5004);
        collapsingDiv = new Element(document.createElement('div'), { id: collapsingDivId });
        collapsingDiv.addClass('collapsible_container');
        collapsingDiv.addClass('display_show');

        subContainer = document.createElement('ul');
        subContainer.setAttribute('class', 'learning_material_list');
        for (key in parsedObject.session_learning_materials) {
            learningModel = new LearningMaterialModel(parsedObject.session_learning_materials[key]);
            element = ilios.course_summary.createListElementForLearnerViewLearningMaterial(learningModel,enableAttachmentLinks);
            subContainer.appendChild(element);
        }

        collapsingDiv.appendChild(subContainer);

        container.appendChild(ilios.dom.createExpandCollapseWidgetForContainerNumber(5004, null));
        container.appendChild(collapsingDiv.get('element'));
        sessionInfo.appendChild(container);
    }
    if (sessionInfo) {
        inspectorContainer.appendChild(sessionInfo);
    }
};

// @private
ilios.learner_view.buildCollatedSessionObjectives = function (parsedObject, container) {
    var competencySessionObjectiveMap = {}; // competency-to-session-objectives lookup table
    var courseObjectiveCompetencyMap = {}; // course-objectives-to-competency lookup table

    var sessionObjectives = parsedObject.session_objectives; // the given session objectives
    var courseObjectives = parsedObject.course_objectives; // the given course objectives

    var courseObjective, sessionObjective = null;
    var competencyId, competencyTitle, competencyMap = null;
    var courseObjectiveId, sessionObjectiveId = null;
    var hasCompetencyAssociation = false;
    var i, j, m, n = 0;

    // create the catch-all "unassociated" competency
    competencySessionObjectiveMap['unassociated'] = {
        'title' : ilios_i18nVendor.getI18NString('general.phrases.no_associated_competencies'),
        'sessionObjectives' : [],
    };

    // create a lookup map of course-objective-ids as keys
    // and competencies as values
    for (i = 0, m = courseObjectives.length; i < m; i++) {
        courseObjective = courseObjectives[i];
        competencyMap = {}; // competency map
        // iterate over each course objective's competency and store it in the map
        for (j = 0, n = courseObjective.parent_objectives.length; j < n; j++) {
            competencyId = courseObjective.parent_objectives[j];
            competencyTitle = courseObjective.parent_competency_titles[j];
            competencyMap[competencyId] = {
                'title' : competencyTitle
            };
        }
        // add the course objective map to the lookup table
        courseObjectiveCompetencyMap[courseObjective.objective_id] = competencyMap;
    }

    // co-relate session objectives to competencies
    // and group them by competency
    for (i = 0, m = sessionObjectives.length; i < m; i++) { // iterae
        sessionObjective = sessionObjectives[i];
        hasCompetencyAssociation = false; // initially, no association must be assume
        for (j = 0, n = sessionObjective.parent_objectives.length; j < n; j++) { // iterate over the parent course objectives
            courseObjectiveId = sessionObjective.parent_objectives[j];
            // look up the course objective in the mapping estabished above
            if (courseObjectiveCompetencyMap.hasOwnProperty(courseObjectiveId)) {
                competencyMap = courseObjectiveCompetencyMap[courseObjectiveId];
                for (competencyId in competencyMap) { // iterate over the course objective's competencies
                    if (! competencySessionObjectiveMap.hasOwnProperty(competencyId)) { // check if competency already exists
                        // create an new competency in the map
                        competencySessionObjectiveMap[competencyId] = {
                            'title' : competencyMap[competencyId].title,
                            'sessionObjectives' : []
                        };
                    }
                    // assign the session objective to the parent course objective's competency group
                    competencySessionObjectiveMap[competencyId].sessionObjectives.push({
                        "title" : sessionObjective.title
                    });
                    // mark the session as having a competency association
                    hasCompetencyAssociation = true;
                }
            }
        }
        // assign orphaned session objectives
        //and session objectives without competency association
        // to the "unassociated" competency group
        if (! hasCompetencyAssociation) {
            competencySessionObjectiveMap['unassociated'].sessionObjectives.push({
                "title" : sessionObjective.title
            });
        }
    }


    // generate markup for output:
    // build nested lists of competencies and their associated sessions
    for (competencyKey in competencySessionObjectiveMap) {
        competency = competencySessionObjectiveMap[competencyKey];
        if (0 < competency.sessionObjectives.length) {
            element = document.createElement('span');
            element.setAttribute('style',
                                 'font-style: italic; font-size: 8pt; padding-left: 16px;');
            // Must be innerHTML to correctly render the HTML markup
            element.innerHTML = ilios.utilities.percentUnicodeToHTML(competency.title);

            container.appendChild(element);

            ulElement = document.createElement('ul');
            ulElement.setAttribute('style', 'margin-top: 2px;');
            // create a list item for each
            for (i = 0, n = competency.sessionObjectives.length; i < n; i++) {
                element = document.createElement('li');
                str = ilios.utilities.percentUnicodeToHTML(competency.sessionObjectives[i].title);
                // Must be innerHTML to correctly render the HTML markup
                element.innerHTML = str;
                ulElement.appendChild(element);
            }
            container.appendChild(ulElement);
        }
    }
};
