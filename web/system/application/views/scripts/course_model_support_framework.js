/**
 * DEPENDENCY: YUI lib
 * DEPENDENCY: scripts/ilios_ui.js
 * DEPENDENCY: scripts/ilios_utilities.js
 * DEPENDENCY: scripts/models/competency_model.js
  * DEPENDENCY: scripts/models/school_competency_model.js
 * DEPENDENCY: scripts/models/course_model.js
 * DEPENDENCY: scripts/models/learning_material_model.js
 * DEPENDENCY: scripts/models/mesh_item_model.js
 * DEPENDENCY: scripts/models/objective_model.js
 * DEPENDENCY: scripts/models/offering_model.js
 * DEPENDENCY: scripts/models/program_cohort_model.js
 * DEPENDENCY: scripts/models/session_model.js
 * DEPENDENCY: scripts/models/user_model.js
 * DEPENDENCY: controllerURL defined (can be any controller which 'subclasses' the abstract ilios
 *                                      controller)
 * DEPENDENCY: common/displine_picker_include.php (generates DisciplineModel class javascript)
 */



ilios.namespace('course_model');

// @private
ilios.course_model.inLoadCourseModel = null;
ilios.course_model.loadingSessionCount = 0;


ilios.course_model.courseTreeCurrentlyLoading = function () {
    return (ilios.course_model.loadingSessionCount > 0);
};

/*
 Calling this when ilios.course_model.courseTreeCurrentlyLoading() is true will produce an
    inauthentic course model representation and possible exceptions.

 @param returnCourseModel - if true, a populated CourseModel object will be build and returned; if
              false an Object with the following attributes defined will be returned:
                    cohorts             -- instances of ProgramCohortModel
                    competencies        -- instances of CompetencyModel
                    directors           -- instances of UserModel
                    disciplines         -- instances of DisciplineModel
                    learners            -- a structure to be passed to CourseModel.setAssociatedLearners
                    learningMaterials   -- an array of Objects each to be passed to CourseModel.addLearningMaterial
                    meshTerms           -- instances of MeSHItemModel
                    objectives          -- instances of ObjectiveModel
                    sessions            -- fully populated instances of SessionModel
                    clerkship_type_id   -- course clerkship course id, or NULL if the course is not a clerkship
 */
ilios.course_model.loadCourseTreeSynchronously = function (courseId, returnCourseModel, offeringsLoadsFinishedListener, action) {
    var rhett = null;
    var cohorts = null;
    var competencies = null;
    var directors = null;
    var disciplines = null;
    var learningMaterials = null;
    var meshItems = null;
    var objectives = null;
    var sessions = null;
    var xmlHTTPRequest = ilios.utilities.getXMLHttpRequest();
    var url = null;
    var method = null;
    var paramString = null;
    var parsedObject = null;
    var parsedModel = null;
    var model = null;
    var msg = null;
    var key = null;
    var i, n;
    var parentCompetencyId;

    if (xmlHTTPRequest == null) {
        msg = ilios_i18nVendor.getI18NString('course_management.error.failed_connection');

        // If we throw up our custom YUI alert dialog now, the return which triggers the
        //      collapse of the autocomplete div also has some behind the scenes YUI stupidity
        //      to clear out the dialog.
        setTimeout("ilios.alert.alert('" + msg + "')", 500);

        return null;
    }

    url = controllerURL + action;

    method = 'GET';
    paramString = 'course_id=' + courseId + '&rnd=' + new Date().getTime();

    xmlHTTPRequest.open(method, url + '?' + paramString, false);
    if ('undefined' !== typeof xmlHTTPRequest.overrideMimeType) {
        xmlHTTPRequest.overrideMimeType("text/plain");
    }
    xmlHTTPRequest.send(null);

    try {
        parsedObject = YAHOO.lang.JSON.parse(xmlHTTPRequest.responseText);
    }
    catch (e) {
        ilios.global.defaultAJAXFailureHandler(null, e);

        return null;
    }

    if (parsedObject.error != null) {
        msg = ilios_i18nVendor.getI18NString('course_management.error.course_load');

        // If we throw up our custom YUI alert dialog now, the return which triggers the
        //      collapse of the autocomplete div also has some behind the scenes YUI stupidity
        //      to clear out the dialog.
        setTimeout("ilios.alert.alert('" + msg + ': ' + parsedObject.error + "')", 500);

        return null;
    }

    if (parsedObject.cohorts.length > 0) {
        cohorts = [];

        for (i = 0, n = parsedObject.cohorts.length; i < n; i++) {
            parsedModel = parsedObject.cohorts[i];

            model = new ProgramCohortModel(parsedModel.program_title, parsedModel.cohort_title,
                                           parsedModel.cohort_id, -1, parsedModel.start_year, null, parsedModel.is_active_school);
            cohorts.push(model);
        }
    }

    if (parsedObject.competencies.length > 0) {
        competencies = [];
        for (i = 0, n = parsedObject.competencies.length; i < n; i++) {
            parsedModel = parsedObject.competencies[i];
            parentCompetencyId = parsedModel.parent_competency_id;
            if ((parsedModel.parent_competency_id == null) || (parsedModel.parent_competency_id < 1)) {
                parentCompetencyId = -1;
            }
            model = new CompetencyModel(parsedModel.title, parentCompetencyId, parsedModel.competency_id);
            competencies.push(model);
        }
    }

    disciplines = ilios.course_model.constructDisciplineModelArrayFromParsedObjectArray(parsedObject.disciplines);

    if (parsedObject.directors.length > 0) {
        directors = [];

        for (key in parsedObject.directors) {
            parsedModel = parsedObject.directors[key];

            model = new UserModel(parsedModel);

            directors.push(model);
        }
    }

    objectives = ilios.course_model.constructObjectiveModelArrayFromParsedObjectArray(
                                                                           parsedObject.objectives);

    learningMaterials
       = ilios.course_model.constructLearningMaterialsModelArrayFromParsedObjectArray(
                                                                   parsedObject.learning_materials);

    if (parsedObject.sessions.length > 0) {
        var objArray = null;
        var objectiveNumber = 0;

        sessions = [];

        for (key in parsedObject.sessions) {
            parsedModel = parsedObject.sessions[key];

            model = new SessionModel(parsedModel);
            model.setOfferingCount(parseInt(parsedModel.offering_count));

            objArray = ilios.course_model.constructDisciplineModelArrayFromParsedObjectArray(
                                                                           parsedModel.disciplines);
            model.setDisciplines((objArray != null) ? objArray : new Array());

            objArray = ilios.course_model.constructObjectiveModelArrayFromParsedObjectArray(
                                                                            parsedModel.objectives);
            for (key in objArray) {
                objectiveNumber = model.getNextObjectiveNumber();

                model.addObjectiveForContainer(objArray[key], objectiveNumber);
            }

            for (key in parsedModel.mesh_terms) {
                model.addMeSHItem(new MeSHItemModel(parsedModel.mesh_terms[key]));
            }

            model.clearDirtyState();

            sessions.push(model);
        }
    }

    if (parsedObject.mesh_terms.length > 0) {
        meshItems = new Array();
        for (key in parsedObject.mesh_terms) {
            meshItems.push(new MeSHItemModel(parsedObject.mesh_terms[key]));
        }
    }

    ilios.course_model.loadingSessionCount = 0;
    if (! returnCourseModel) {
        rhett = new Object();

        rhett.cohorts = cohorts;
        rhett.competencies = competencies;
        rhett.directors = directors;
        rhett.disciplines = disciplines;
        rhett.learners = parsedObject.learners;
        rhett.learningMaterials = learningMaterials;
        rhett.meshTerms = meshItems;
        rhett.objectives = objectives;
        rhett.sessions = sessions;
        ilios.course_model.inLoadCourseModel = null;
        rhett.clerkship_type_id = null;
        if (parsedObject.clerkship_type_id) {
            rhett.clerkship_type_id = parsedObject.clerkship_type_id;
        }
    } else {
        rhett = new CourseModel();

        rhett.setDBId(courseId);
        rhett.setTitle(parsedObject.title);
        rhett.setExternalCourseId(parsedObject.external_id);
        rhett.setUniqueId(parsedObject.unique_id);
        rhett.setStartDate(parsedObject.start_date);
        rhett.setEndDate(parsedObject.end_date);
        rhett.setYear(parsedObject.year);
        rhett.setCourseLevel(parsedObject.course_level);
        rhett.setLocked(parseInt(parsedObject.locked) == 1);

        if (parsedObject.clerkship_type_id) {
            rhett.setClerkshipTypeId(parsedObject.clerkship_type_id);
        }

        if (parsedObject.publish_event_id != null) {
            rhett.setPublishEventId(parsedObject.publish_event_id);
            rhett.setPublishedAsTBD(parseInt(parsedObject.published_as_tbd) == 1);
        }

        if (cohorts != null) {
            rhett.setCohorts(cohorts);
        }

        if (competencies != null) {
            rhett.setCompetencies(competencies);
        }

        if (disciplines != null) {
            rhett.setDisciplines(disciplines);
        }

        if (directors != null) {
            rhett.setDirectors(directors);
        }

        if (meshItems != null) {
            for (key in meshItems) {
                rhett.addMeSHItem(meshItems[key]);
            }
        }

        if (objectives != null) {
            var objectiveModel = null;

            objectiveNumber = 0;

            for (key in objectives) {
                objectiveModel = objectives[key];

                objectiveNumber = rhett.getNextObjectiveNumber();
                rhett.addObjectiveForContainer(objectiveModel, objectiveNumber);
            }
        }

        if (learningMaterials != null) {
            for (key in learningMaterials) {
                rhett.addLearningMaterial(learningMaterials[key]);
            }
        }

        ilios.course_model.inLoadCourseModel = rhett;

        if (sessions != null) {
            var sessionModel = null;
            var containerNumber = 0;

            for (key in sessions) {
                sessionModel = sessions[key];

                rhett.addSessionForContainer(sessionModel, containerNumber++);

                ilios.course_model.loadOfferingsForSession(sessionModel.getDBId(),
                                                           offeringsLoadsFinishedListener);
            }
        }

        if (parsedObject.learners != null) {
            rhett.setAssociatedLearners(parsedObject.learners);
        }
    }

    if (offeringsLoadsFinishedListener != null) {
       setTimeout(offeringsLoadsFinishedListener, 600);
    }

    return rhett;
};

/**
 * @param {Array} disciplineArray
 * @return {Array}
 */
ilios.course_model.constructDisciplineModelArrayFromParsedObjectArray = function (disciplineArray) {
    var rhett = null;
    var disciplines = YAHOO.lang.isArray(disciplineArray) ? disciplineArray : [];
    var i, n;

    if (disciplines.length > 0) {
        var parsedModel = null;
        var model = null;

        rhett = [];

        for (i = 0, n = disciplines.length; i < n; i++) {
            parsedModel = disciplineArray[i];
            model = new DisciplineModel();
            model.setTitle(parsedModel.title);
            model.setDBId(parsedModel.discipline_id);
            rhett.push(model);
        }
    }

    return rhett;
};

ilios.course_model.constructObjectiveModelArrayFromParsedObjectArray = function (objectiveArray) {
    var rhett = null;

    if ((objectiveArray != null) && (objectiveArray.length > 0)) {
        rhett = new Array();

        for (var key in objectiveArray) {
            rhett.push(new ObjectiveModel(objectiveArray[key]));
        }
    }

    return rhett;
};

ilios.course_model.constructLearningMaterialsModelArrayFromParsedObjectArray
                                                                = function (learningMaterialArray) {
    var rhett = null;

    if ((learningMaterialArray != null) && (learningMaterialArray.length > 0)) {
        var parsedModel = null;
        var model = null;

        rhett = new Array();

        for (var key in learningMaterialArray) {
            parsedModel = learningMaterialArray[key];

            model = new LearningMaterialModel(parsedModel);

            rhett.push(model);
        }
    }

    return rhett;
};

ilios.course_model.loadOfferingsForSession = function (sessionId, offeringsLoadsFinishedListener) {
    var url = controllerURL + "getOfferingsForSession";
    var method = "POST";
    var paramString = "session_id=" + sessionId;
    var ajaxCallback = {
            success: function (resultObject) {
                var parsedObject = null;
                var sessionModel = null;
                var offeringModel = null;

                ilios.course_model.loadingSessionCount--;

                try {
                    parsedObject = YAHOO.lang.JSON.parse(resultObject.responseText);
                }
                catch (e) {
                    //  closure
                    if ((offeringsLoadsFinishedListener != null)
                                        && (ilios.course_model.loadingSessionCount == 0)) {
                        offeringsLoadsFinishedListener();
                    }

                    ilios.global.defaultAJAXFailureHandler(null, e);

                    return;
                }

                for (var key in parsedObject) {
                    offeringModel = new OfferingModel(parsedObject[key]);

                    if (sessionModel == null) {
                        sessionModel
                            = ilios.course_model.inLoadCourseModel.getSessionWithId(offeringModel.getSessionId());

                        sessionModel.setOfferingCount(-1);
                    }

                    sessionModel.addOffering(offeringModel);
                }

                //  closure
                if ((offeringsLoadsFinishedListener != null)
                                                && (ilios.course_model.loadingSessionCount == 0)) {
                    offeringsLoadsFinishedListener();
                }
            },

            failure: function (resultObject) {
                ilios.course_model.loadingSessionCount--;
                ilios.global.defaultAJAXFailureHandler(resultObject);
            }};

    ilios.course_model.loadingSessionCount++;
    YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);
};
