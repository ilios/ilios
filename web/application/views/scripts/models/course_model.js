/**
 * This file contains the model repesentation of a Course in javascript land; this is used to
 *  encapsulate, and so therefore better track the dirty state of, our model.
 *
 * There's a close-but-not-exact hierarchy able to be derived:
 *                      ObjectiveModel <- SessionModel <- CourseModel
 *  but there's extraneous gunk in each parent (MD5 caching & description posing in OM; session type
 *      id in SM) which are not needed by their children...
 *
 * Dependencies:
 *      scripts/models/abstract_js_model_form.js
 *      scripts/models/discipline_model.js
 *      scripts/ilios_utilities.js
 */
function CourseModel (dbObject) {

    AbstractJavaScriptModelForm.apply(this, arguments);

    this.externalId = null;

    this.uniqueId = null;

    this.year = 0;
    this.startDate = null;
    this.startDateDO = null;
    this.endDate = null;
    this.endDateDO = null;

    this.courseLevel = 1;

    this.publishEventId = -1;
    this.publishedAsTBD = false;

    this.competencies = [];

    this.disciplines = [];

    this.directors = [];

    this.meshTerms = [];

    this.sessions = [];

    this.learningMaterials = [];

    this.learners = [];

    this.objectives = [];
    this.objectiveCount = 0;

    this.cohorts = [];
    this.cachedDataTableArray = null;

    this.clerkshipTypeId = null;

    this.locked = false;

    if (typeof dbObject != 'undefined') {
        this.dbId = dbObject.course_id;
        this.title = dbObject.title;

        this.setStartDate(dbObject.start_date);
        this.setEndDate(dbObject.end_date);

        this.year = dbObject.year;

        if (dbObject.publish_event_id != null) {
            this.publishEventId = parseInt(dbObject.publish_event_id);
        }
        this.publishedAsTBD = (dbObject.published_as_tbd == '1');

        this.locked = (dbObject.locked == '1');

        this.courseLevel = dbObject.course_level;

        this.externalId = dbObject.external_id;

        this.uniqueId = (typeof dbObject.unique_id != 'undefined') ? dbObject.unique_id : '';

        if (dbObject.clerkship_type_id) {
            this.clerkshipTypeId = dbObject.clerkship_type_id;
        }

        this.isDirty = false;
    }

    this.dirtyStateListener = {

            modelChanged: function (model, mockedThis) {
                if (model.isModelDirty()) {
                    ilios.cm.currentCourseModel.setDirtyAndNotify();
                }
            }

    };

}

for (var aMethod in AbstractJavaScriptModelForm.prototype) {
    CourseModel.prototype[aMethod] = AbstractJavaScriptModelForm.prototype[aMethod];
}

CourseModel.prototype.containsDirtySessions = function () {
    for (var key in this.sessions) {
        if (this.sessions[key].isModelDirty()) {
            return true;
        }
    }

    return false;
};

/*
 * @private
 */
CourseModel.prototype.meetsMinimumPublishingRequirements = function (reviewArray) {
    var rhett = true;
    var populateReviewArray = (typeof reviewArray != 'undefined');
    var value = null;
    var reviewObject = null;

    if (populateReviewArray) {
        reviewArray.push(rhett);
    }

    if (populateReviewArray) {
        reviewObject = new Object();
        reviewObject.displayLabel = ilios_i18nVendor.getI18NString('general.phrases.start_date');
    }

    if (this.startDateDO == null) {
        rhett = false;

        if (! populateReviewArray) {
            return rhett;
        }
        else {
            reviewObject.displayValue = '?';
            reviewObject.passed = false;
        }
    }
    else if (populateReviewArray) {
        reviewObject.displayValue = this.startDate;
        reviewObject.passed = true;
    }

    if (populateReviewArray) {
        reviewArray.push(reviewObject);

        reviewObject = new Object();
        reviewObject.displayLabel = ilios_i18nVendor.getI18NString('general.phrases.end_date');
    }

    if (this.endDateDO == null) {
        rhett = false;

        if (! populateReviewArray) {
            return rhett;
        }
        else {
            reviewObject.displayValue = '?';
            reviewObject.passed = false;
        }
    }
    else if (populateReviewArray) {
        reviewObject.displayValue = this.endDate;
        reviewObject.passed = true;
    }

    if (this.startDateDO.getTime() >= this.endDateDO.getTime()) {
        rhett = false;

        if (! populateReviewArray) {
            return rhett;
        }
        else {
            reviewObject.passed = false;
        }
    }

    if (populateReviewArray) {
        reviewArray.push(reviewObject);

        reviewObject = new Object();
        reviewObject.displayLabel = ilios_i18nVendor.getI18NString('general.terms.has') + ' '
                                    + ilios_i18nVendor.getI18NString(
                                                            'general.terms.cohort_indefinite');
    }

    value = ilios.utilities.objectPropertyCount(this.cohorts);
    if (value == 0) {
        rhett = false;

        if (! populateReviewArray) {
            return rhett;
        }
        else {
            reviewObject.displayValue
                            = ilios_i18nVendor.getI18NString('general.terms.no').toUpperCase();
            reviewObject.passed = false;
        }
    }
    else if (populateReviewArray) {
        reviewObject.displayValue
                            = ilios_i18nVendor.getI18NString('general.terms.yes').toUpperCase()
                                + ' (' + value + ')';
        reviewObject.passed = true;
    }

    if (populateReviewArray) {
        reviewArray.push(reviewObject);

        reviewArray[0] = rhett;
    }

    return rhett;
};

/*
 * This is assumed to be called after meetsMinimumPublishingRequirements in cases in which a
 *  review array is being passed around; as such - calling this with a review array which has not
 *  first passed through the minimum publishing requirements will result in a structure which has
 *  had its zeroeth element overwritten.
 *
 * @private
 */
CourseModel.prototype.meetsSecondaryPublishingRequirements = function (reviewArray) {
    var rhett = true;
    var populateReviewArray = (typeof reviewArray != 'undefined');
    var value = null;
    var reviewObject = null;

    if (populateReviewArray) {
        reviewObject = new Object();
        reviewObject.displayLabel = ilios_i18nVendor.getI18NString('general.terms.has') + ' '
                                    + ilios_i18nVendor.getI18NString(
                                                            'general.terms.topic_indefinite');
    }

    value = this.disciplines.length;
    if (value == 0) {
        rhett = false;

        if (! populateReviewArray) {
            return rhett;
        }
        else {
            reviewObject.displayValue
                            = ilios_i18nVendor.getI18NString('general.terms.no').toUpperCase();
            reviewObject.passed = false;
        }
    }
    else if (populateReviewArray) {
        reviewObject.displayValue
                            = ilios_i18nVendor.getI18NString('general.terms.yes').toUpperCase()
                                + ' (' + value + ')';
        reviewObject.passed = true;
    }

    if (populateReviewArray) {
        reviewArray.push(reviewObject);

        reviewObject = new Object();
        reviewObject.displayLabel = ilios_i18nVendor.getI18NString('general.terms.has') + ' '
                                    + ilios_i18nVendor.getI18NString(
                                                            'general.terms.objective_indefinite');
    }

    value = ilios.utilities.objectPropertyCount(this.objectives);
    if (value == 0) {
        rhett = false;

        if (! populateReviewArray) {
            return rhett;
        }
        else {
            reviewObject.displayValue
                            = ilios_i18nVendor.getI18NString('general.terms.no').toUpperCase();
            reviewObject.passed = false;
        }
    }
    else if (populateReviewArray) {
        reviewObject.displayValue
                            = ilios_i18nVendor.getI18NString('general.terms.yes').toUpperCase()
                                + ' (' + value + ')';
        reviewObject.passed = true;
    }

    if (populateReviewArray) {
        reviewArray.push(reviewObject);

        reviewObject = new Object();
        reviewObject.displayLabel = ilios_i18nVendor.getI18NString('general.terms.has') + ' '
                                    + ilios_i18nVendor.getI18NString(
                                                            'general.phrases.mesh_term_indefinite');
    }

    value = ilios.utilities.objectPropertyCount(this.meshTerms);
    if (value == 0) {
        rhett = false;

        if (! populateReviewArray) {
            return rhett;
        }
        else {
            reviewObject.displayValue
                            = ilios_i18nVendor.getI18NString('general.terms.no').toUpperCase();
            reviewObject.passed = false;
        }
    }
    else if (populateReviewArray) {
        reviewObject.displayValue
                            = ilios_i18nVendor.getI18NString('general.terms.yes').toUpperCase()
                                + ' (' + value + ')';
        reviewObject.passed = true;
    }

    if (populateReviewArray) {
        reviewArray.push(reviewObject);

        reviewArray[0] = rhett;
    }

    return rhett;
};

CourseModel.prototype.getPublishability = function () {
    var rhett = this.CANNOT_BE_PUBLISHED;

    if (this.meetsMinimumPublishingRequirements()) {
        rhett = this.MEETS_MINIMAL_PUBLISHING_REQUIREMENTS;

        if (this.meetsSecondaryPublishingRequirements()) {
            rhett = this.CAN_BE_PUBLISHED;
        }
    }

    return rhett;
};

CourseModel.prototype.getTreePublishability = function () {
    var rhett = this.getPublishability();
    var sessionPublishability = -1;

    if (rhett == this.CAN_BE_PUBLISHED) {
        for (var key in this.sessions) {
            sessionPublishability = this.sessions[key].getPublishability();

            if (sessionPublishability != this.CAN_BE_PUBLISHED) {
                return sessionPublishability;
            }
        }
    }

    return rhett;
};

// Do we need to publish (is there a model in the tree that returns true for
//      ilios.utilities.modelItemNeedsPublishing)
CourseModel.prototype.treeNeedsPublishing = function () {
    if (ilios.utilities.modelItemNeedsPublishing(this)) {
        return true;
    }

    for (var key in this.sessions) {
        if (ilios.utilities.modelItemNeedsPublishing(this.sessions[key])) {
            return true;
        }
    }

    return false;
};

// Is the entire tree published completely and not dirty (publish & not TBD)
CourseModel.prototype.treeIsFullyPublished = function () {
    if (ilios.utilities.modelItemNeedsPublishing(this) || this.publishedAsTBD) {
        return false;
    }

    for (var key in this.sessions) {
        if (ilios.utilities.modelItemNeedsPublishing(this.sessions[key])
                || this.sessions[key].isPublishedAsTBD()) {
            return false;
        }
    }

    return true;
};

/*
 * @return a review array is an ordered array of elements, where the 0th is a true or false as
 *              to whether the review [for publication] passes *just*for*this*model* (not the
 *              whole tree). the remaining elements are objects with attributes
 *                  . displayLabel
 *                  . displayValue
 *                  . passed (true/false)
 */
CourseModel.prototype.getReviewArray = function () {
    var rhett = new Array();

    this.meetsMinimumPublishingRequirements(rhett);
    this.meetsSecondaryPublishingRequirements(rhett);

    return rhett;
};

/**
 * @param {Array} cohorts an array of ProgramCohortModel objects
 */
CourseModel.prototype.setCohorts = function (cohorts) {
    var isDiff = false;
    var newCohortIds = [];
    var currentCohortIds = [];
    var i, n;

    // cheap check for changes by array length comparison
    if (this.cohorts.length !== cohorts.length) {
        isDiff = true;
    } else {
        // slightly more involved comparison:
        // compare cohort ids
        for (i = 0, n = cohorts.length; i < n; i++) { // longhand it
            newCohortIds.push(cohorts[i].getCohortId());
        }
        for (i = 0, n = this.cohorts.length; i < n; i++) {
            currentCohortIds.push(this.cohorts[i].getCohortId());
        }
        // sort ids
        newCohortIds.sort();
        currentCohortIds.sort();
        // flatten the ids out to strings and compare them
        isDiff = newCohortIds.join("") !== currentCohortIds.join("");
    }

    if (isDiff) {
        // create a shallow copy
        this.cohorts = cohorts.slice(0);
        this.cachedDataTableArray = null;
        this.setDirtyAndNotify();
    }
};

CourseModel.prototype.getCohorts = function () {
    return this.cohorts;
};

CourseModel.prototype.setLocked = function (flag) {
    this.locked = flag;
};

CourseModel.prototype.isLocked = function () {
    return this.locked;
};

/**
 * Pre-existence is not checked.
 */
CourseModel.prototype.addLearningMaterial = function (learningMaterialModel) {
    this.learningMaterials.push(learningMaterialModel);
};

CourseModel.prototype.getLearningMaterials = function () {
    return this.learningMaterials;
};

CourseModel.prototype.getLearningMaterialForId = function (learningMaterialId) {
    for (var key in this.learningMaterials) {
        if (learningMaterialId == (this.learningMaterials[key]).getDBId()) {
            return this.learningMaterials[key];
        }
    }

    return null;
};

CourseModel.prototype.containsLearningMaterial = function (learningMaterialModel) {
    return (this.getLearningMaterialForId(learningMaterialModel.getDBId()) != null);
};

CourseModel.prototype.removeLearningMaterial = function (learningMaterialModel) {
    this.removeLearningMaterialWithId(learningMaterialModel.getDBId());
};

CourseModel.prototype.removeLearningMaterialWithId = function (dbId) {
    for (var key in this.learningMaterials) {
        if (this.learningMaterials[key].getDBId() == dbId) {
            ilios.utilities.removeElementWithValue(this.learningMaterials,
                                                   this.learningMaterials[key]);

            return;
        }
    }
};

CourseModel.prototype.getCohortDataTableArray = function () {
    if (this.cachedDataTableArray == null) {
        var programCohortModel = null;
        var currentYear = parseInt((new Date()).getFullYear(), 10);
        var row = null;

        this.cachedDataTableArray = new Array();

        for (var key in this.cohorts) {
            programCohortModel = this.cohorts[key];

            row = new Object();
            row.program = programCohortModel.getProgramTitle();
            row.cohort = programCohortModel.getCohortTitle();
            // level := current_year - cohort_start_year
            row.level = currentYear - parseInt(programCohortModel.getStartYear(), 10 ) + 1;
            if (0 >= row.level) {
            	// levels equal or less than zero (e.g. calculated from future cohort start dates)
            	// are displayed as "N/A"
            	row.level = ilios_i18nVendor.getI18NString('general.terms.not_applicable');
            }

            this.cachedDataTableArray.push(row);
        }
    }

    return this.cachedDataTableArray;
};

CourseModel.prototype.setExternalCourseId = function (ecid) {
    if (this.externalId != ecid) {
        this.externalId = ecid;

        this.setDirtyAndNotify();
    }
};

CourseModel.prototype.getExternalCourseId = function () {
    return (this.externalId != null) ? this.externalId : '';
};


CourseModel.prototype.setClerkshipTypeId = function (typeId) {
    if (this.clerkshipTypeId != typeId) {
        this.clerkshipTypeId = typeId;
        this.setDirtyAndNotify();
    }
};

CourseModel.prototype.getClerkshipTypeId = function () {
    return this.clerkshipTypeId;
};

CourseModel.prototype.setUniqueId = function (uniqueid) {
    this.uniqueId = uniqueid;
};

CourseModel.prototype.getUniqueId = function () {
    //return (typeof this.dbId === 'undefined') ? '' : 'ILIOS' + parseInt(this.dbId + '00000', 10).toString(36).toUpperCase();
    return (this.uniqueId != null) ? this.uniqueId : '';
};

CourseModel.prototype.setCourseLevel = function (cl) {
    if (this.courseLevel != cl) {
        this.courseLevel = cl;

        this.setDirtyAndNotify();
    }
};

CourseModel.prototype.getCourseLevel = function () {
    return this.courseLevel;
};

CourseModel.prototype.setYear = function (yearStart) {
    if (this.year != yearStart) {
        this.year = yearStart;

        this.setDirtyAndNotify();
    }
};

CourseModel.prototype.getYear = function () {
    return this.year;
};

CourseModel.prototype.setEndDate = function (ed) {
    if (this.endDate != ed) {
        this.endDate = ed;
        this.endDateDO = ilios.utilities.mySQLTimelessDateToDateObject(ed);

        this.setDirtyAndNotify();
    }
};

CourseModel.prototype.getEndDate = function () {
    return this.endDate;
};

CourseModel.prototype.getEndDateAsDateObject = function () {
    return this.endDateDO;
};

CourseModel.prototype.setStartDate = function (sd) {
    if (this.startDate != sd) {
        this.startDate = sd;
        this.startDateDO = ilios.utilities.mySQLTimelessDateToDateObject(sd);

        this.setDirtyAndNotify();
    }
};

CourseModel.prototype.getStartDate = function () {
    return this.startDate;
};

CourseModel.prototype.getStartDateAsDateObject = function () {
    return this.startDateDO;
};

CourseModel.prototype.getStartDateObjectForNewSession = function() {
    var sd = this.startDateDO.getTime();

    for (var key in this.sessions) {
	if (this.sessions[key].getLastEventEnd() > sd) {
	    sd = this.sessions[key].getLastEventEnd();
	}
    }
    return new Date(sd);
};

CourseModel.prototype.setPublishedAsTBD = function (flag) {
    this.publishedAsTBD = flag;
};

CourseModel.prototype.isPublishedAsTBD = function () {
    return this.publishedAsTBD;
};

CourseModel.prototype.setPublishEventId = function (eventId) {
    if (this.publishEventId != eventId) {
        this.publishEventId = eventId;

        this.setDirtyAndNotify();
    }
};

CourseModel.prototype.getPublishEventId = function () {
    return this.publishEventId;
};

CourseModel.prototype.isPublished = function () {
    return (this.publishEventId > 0);
};

CourseModel.prototype.getSessions = function () {
    return this.sessions;
};

CourseModel.prototype.getSessionWithId = function (sessionId) {
    for (var key in this.sessions) {
        if (this.sessions[key].getDBId() == sessionId) {
            return this.sessions[key];
        }
    }

    return null;
};

CourseModel.prototype.getSessionForContainer = function (containerNumber) {
    return this.sessions[containerNumber];
};

CourseModel.prototype.getContainerForSession = function (session) {
    for (var key in this.sessions) {
        if (this.sessions[key].compareTo(session) == 0) {
            return key;
        }
    }

    return -1;
};

CourseModel.prototype.addSessionForContainer = function (sessionModel, containerNumber) {
    this.sessions[containerNumber] = sessionModel;
};

CourseModel.prototype.removeSessionForContainer = function (containerNumber) {
    var lovelyJavascript = (containerNumber in this.sessions);

    if (lovelyJavascript) {
        delete this.sessions[containerNumber];
    }
};

CourseModel.prototype.getNextObjectiveNumber = function () {
    this.objectiveCount++;

    return this.objectiveCount;
};

CourseModel.prototype.getObjectives = function () {
    return this.objectives;
};

CourseModel.prototype.getContainerForObjectiveWithId = function (dbId) {
    for (var key in this.objectives) {
        if (this.objectives[key].getDBId() == dbId) {
            return key;
        }
    }

    return null;
};

/*
 * This is potentially expensive.
 */
CourseModel.prototype.getContainerForObjectiveWithTextMD5 = function (md5) {
    for (var key in this.objectives) {
        if (this.objectives[key].getTextMD5() == md5) {
            return key;
        }
    }

    return null;
};

CourseModel.prototype.getObjectiveForContainer = function (containerNumber) {
    return this.objectives[containerNumber];
};

CourseModel.prototype.addObjectiveForContainer = function (objectiveModel, containerNumber) {
    this.objectives[containerNumber] = objectiveModel;

    objectiveModel.addStateChangeListener(this.dirtyStateListener, null);

    this.setDirtyAndNotify();
};

CourseModel.prototype.removeObjectiveForContainer = function (containerNumber) {
    var lovelyJavascript = (containerNumber in this.objectives);

    if (lovelyJavascript) {
        delete this.objectives[containerNumber];

        this.setDirtyAndNotify();
    }

    // TODO: OMFG why isn't this just this?
    // if (this.objectives[containerNumber]) {
    //     this.objectives.splice(containerNumber, 1);
    //     this.setDirtyAndNotify();
    // }
};

/**
 * @param competencyArray an array (map) of CompetencyModel instances keyed by their title; the
 *                          object is cloned and so may be disposed of after this method returns.
 */
CourseModel.prototype.setCompetencies = function (competencyArray) {
    this.competencies = ilios.utilities.cloneAssociativeArray(competencyArray);

    this.setDirtyAndNotify();
};

/**
 * @return an array (map) of CompetencyModel instances keyed by their title
 */
CourseModel.prototype.getCompetencies = function () {
    return this.competencies;
};

/**
 * @param {Array} disciplineArray an array of DisciplineModel instances.
 */
CourseModel.prototype.setDisciplines = function (disciplineArray) {
    this.disciplines = disciplineArray.slice(0);
    this.disciplines.sort(ilios.utilities.titledNaturalComparator);
    this.setDirtyAndNotify();
};

/**
 * @return an array of DisciplineModel instances
 */
CourseModel.prototype.getDisciplines = function () {
    return this.disciplines;
};

/**
 * @param directorArray an array (map) of UserModel instances; the contents of the array are copied
 *                          and so the array may be disposed of after this method returns.
 */
CourseModel.prototype.setDirectors = function (directorArray) {
    var director = null;

    this.directors = null;

    this.directors = new Array();

    for (var key in directorArray) {
        director = directorArray[key];

        this.directors[director.getUniqueKey()] = director;
    }

    this.setDirtyAndNotify();
};

/**
 * @param director must be an instance of UserModel
 */
CourseModel.prototype.addDirector = function (director) {
    var key = director.getUniqueKey();
    var lovelyJavascript = (key in this.directors);

    if (! lovelyJavascript) {
        this.directors[key] = director;

        this.setDirtyAndNotify();
    }
};

CourseModel.prototype.removeDirector = function (director) {
    var key = director.getUniqueKey();
    var lovelyJavascript = (key in this.directors);

    if (lovelyJavascript) {
        delete this.directors[key];

        this.setDirtyAndNotify();
    }
};

/**
 * @return an array (map) of UserModel instances keyed by the UserModel's getUniqueKey return
 */
CourseModel.prototype.getDirectors = function () {
    return this.directors;
};

CourseModel.prototype.getDirectorsAsFormattedText = function () {
    var rhett = '';

    for (var key in this.directors) {
        if (rhett.length > 0) {
            rhett += '; ';
        }

        rhett += this.directors[key].getFormattedName(ilios.utilities.UserNameFormatEnum.LAST_FIRST);
    }

    return rhett;
};

CourseModel.prototype.addMeSHItem = function (meshItem) {
    this.meshTerms.push(meshItem);
};

CourseModel.prototype.removeMeSHItem = function (meshItem) {
    for (var key in this.meshTerms) {
        if (meshItem.compareTo(this.meshTerms[key]) == 0) {
            delete this.meshTerms[key];

            return;
        }
    }
};

CourseModel.prototype.containsMeSHItem = function (meshItem) {
    for (var key in this.meshTerms) {
        if (meshItem.compareTo(this.meshTerms[key]) == 0) {
            return true;
        }
    }

    return false;
};

/**
 * @return a non-associative array of MeSHItemModel instances
 */
CourseModel.prototype.getMeSHItems = function () {
    return this.meshTerms;
};

CourseModel.prototype.getMeSHItemsAsFormattedText = function () {
    return ilios.utilities.delimitedStringOfTitledObjects(this.meshTerms, ';');
};

CourseModel.prototype.setAssociatedLearners = function (learnersArray) {
    this.learners = learnersArray.concat();
};

CourseModel.prototype.getAssociatedLearnersAsFormattedText = function () {
    return ilios.utilities.delimitedStringOfTitledObjects(this.learners, '<br/>');
};

CourseModel.prototype.getAssociatedLearners = function () {
    return this.learners;
};

CourseModel.prototype.clone = function () {
    var rhett = new CourseModel();

    rhett.dbId = this.dbId;
    rhett.title = this.title;

    rhett.publishEventId = this.publishEventId;
    rhett.publishedAsTBD = this.publishedAsTBD;

    rhett.courseLevel = this.courseLevel;

    rhett.year = this.year;
    rhett.startDate = this.startDate;
    rhett.startDateDO = new Date(this.startDateDO.getTime());
    rhett.endDate = this.endDate;
    rhett.endDateDO = new Date(this.endDateDO.getTime());

    rhett.cohorts = ilios.utilities.cloneAssociativeArray(this.cohorts);

    rhett.competencies = ilios.utilities.cloneAssociativeArray(this.competencies);

    rhett.disciplines = this.disciplines.slice(0);

    rhett.directors = ilios.utilities.cloneAssociativeArray(this.directors);

    rhett.meshTerms = this.meshTerms.concat();

    rhett.clerkshipTypeId = this.clerkshipTypeId;

    rhett.sessions = new Array();
    for (var key in this.sessions) {
        rhett.sessions[key] = this.sessions[key].clone();
        rhett.sessions[key].stateChangeListeners = this.sessions[key].stateChangeListeners.concat();
        rhett.sessions[key].stateChangeListenerArguments
                                         = this.sessions[key].stateChangeListenerArguments.concat();

        rhett.sessions.length++;
    }

    rhett.objectiveCount = this.objectiveCount;
    rhett.objectives = this.objectives.slice(0);

    // (should still be clean.. but just in case future coders accidentally add code that dirties
    //      above..)
    rhett.clearDirtyState();

    return rhett;
};

/**
 * Returns a standard compare, the value of which would allow natural ordering. This compares in
 *  the order of:
 *      title
 *      year
 *      startDate
 *      endDate
 *      course level
 *      directors
 *      competencies size
 *      cohort size
 *      disciplines size
 *      mesh size
 *      sessions size
 *      objectives size
 *      dbId
 */
CourseModel.prototype.compareTo = function (otherModel) {
    var temp = null;
    var model = null;
    var flag = false;

    if (this.title != null) {
        temp = this.title.localeCompare(otherModel.title);

        if (temp != 0) {
            return temp;
        }
    }
    else if (otherModel.title != null) {
        return 1;           // arbitrary but consistent
    }

    if (this.year != otherModel.year) {
        return (this.year - otherModel.year);
    }

    temp = this.startDate.localeCompare(otherModel.startDate);
    if (temp != 0) {
        return temp;
    }

    temp = this.endDate.localeCompare(otherModel.endDate);
    if (temp != 0) {
        return temp;
    }

    if (this.courseLevel != otherModel.courseLevel) {
        return (this.courseLevel - otherModel.courseLevel);
    }

    if (ilios.utilities.objectPropertyCount(this.directors)
                                            != ilios.utilities.objectPropertyCount(otherModel.directors)) {
        return 1;           // arbitrary but consistent
    }

    for (var key in this.directors) {
        flag = (key in otherModel.directors);

        if (! flag) {
            return -1;      // arbitrary but consistent
        }

        model = this.directors[key];

        temp = model.compareTo(otherModel.directors[key]);
        if (temp != 0) {
            return temp;
        }
    }

    if (ilios.utilities.objectPropertyCount(this.competencies)
                            != ilios.utilities.objectPropertyCount(otherModel.competencies)) {
        return 1;           // arbitrary but consistent
    }

    if (ilios.utilities.objectPropertyCount(this.cohorts) != ilios.utilities.objectPropertyCount(otherModel.cohorts)) {
        return 1;           // arbitrary but consistent
    }

    if (this.disciplines.length !== otherModel.disciplines.length) {
        return 1;           // arbitrary but consistent
    }

    if (ilios.utilities.objectPropertyCount(this.meshTerms)
                            != ilios.utilities.objectPropertyCount(otherModel.meshTerms)) {
        return 1;           // arbitrary but consistent
    }

    if (ilios.utilities.objectPropertyCount(this.sessions)
                            != ilios.utilities.objectPropertyCount(otherModel.sessions)) {
        return 1;           // arbitrary but consistent
    }

    if (ilios.utilities.objectPropertyCount(this.objectives)
                            != ilios.utilities.objectPropertyCount(otherModel.objectives)) {
        return 1;           // arbitrary but consistent
    }

    return (this.dbId - otherModel.dbId);
};

/**
 * If the newModel differs from this model, then it is replaced by the newModel and this is set
 *      dirty. Comparisons are done by values and not indentical instance.
 *
 * NOTES / REMINDERS:
 *  . Any modifications to the users held by the parameter after this method is called will also
 *          change those instances as held by this instance.
 */
CourseModel.prototype.replaceContentWithModel = function (newModel, forceReplacement) {
    if (forceReplacement || (this.compareTo(newModel) != 0)) {
        var model = null;

        this.title = newModel.title;
        this.dbId = newModel.dbId;

        this.publishEventId = newModel.publishEventId;
        this.publishedAsTBD = newModel.publishedAsTBD;

        this.courseLevel = newModel.courseLevel;

        this.startDate = newModel.startDate;
        this.startDateDO = ilios.utilities.mySQLTimelessDateToDateObject(newModel.startDate);

        this.endDate = newModel.endDate;
        this.endDateDO = ilios.utilities.mySQLTimelessDateToDateObject(newModel.endDate);

        this.year = newModel.year;

        this.directors = new Array();

        for (var key in newModel.directors) {
            model = newModel.directors[key];

            this.directors[key] = model;
        }

        this.cohorts = newModel.cohorts;

        this.competencies = newModel.competencies;

        this.disciplines = newModel.disciplines;

        this.meshTerms = newModel.meshTerms;

        this.sessions = newModel.sessions;

        this.objectiveCount = newModel.objectiveCount;
        this.objectives = newModel.objectives;

        this.clerkshipTypeId = newModel.clerkshipTypeId;

        this.setDirtyAndNotify();
    }
};
