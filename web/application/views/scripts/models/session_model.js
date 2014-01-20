/**
 * This file contains the model repesentation of a Session in javascript land; this is used to
 *  encapsulate, and so therefore better track the dirty state of, our model.
 *
 * There's a close-but-not-exact hierarchy able to be derived:
 *                      ObjectiveModel <- SessionModel <- (course/)CourseModel
 *  but there's extraneous gunk in each parent (MD5 caching & description posing in OM; session type
 *      id in SM) which are not needed by their children...
 *
 * Dependencies:
 *
 * scripts/abstract_js_model_form.js
 * scripts/discipline_model.js
 * scripts/ilios_utilities.js
 */
function SessionModel (dbObject) {

    AbstractJavaScriptModelForm.apply(this, arguments);

    this.learningMaterials = new Array();

    this.description = '';

    this.independentLearningModel = null;

    this.sessionTypeId = null;

    this.is_learner = false;

    if (typeof dbObject == 'undefined') {

        this.publishEventId = -1;

        this.courseId = -1;

        this.attireRequired = false;
        this.equipmentRequired = false;
        this.supplemental = false;

        this.publishedAsTBD = false;
    }
    else {
        this.dbId = dbObject.session_id;
        this.title = dbObject.title;

        if (typeof dbObject.ilm_facet != 'undefined') {
            this.independentLearningModel = new IndependentLearningModel(dbObject.ilm_facet);
            this.sessionTypeName = 'Independent Learning';
        }

        if ((typeof dbObject.description != 'undefined')
                && (dbObject.description != null)
                && (dbObject.description.length > 0)) {
            this.description = dbObject.description;
        }

        if (typeof dbObject.session_type_title != 'undefined') {
            this.sessionTypeName = dbObject.session_type_title;
        }

        this.sessionTypeId = dbObject.session_type_id;
        this.courseId = dbObject.course_id;

        this.publishEventId
            = ((dbObject.publish_event_id < 1) || (dbObject.publish_event_id == null))
                        ? -1
                        : dbObject.publish_event_id;

        this.attireRequired = (dbObject.attire_required == '1');
        this.equipmentRequired = (dbObject.equipment_required == '1');
        this.supplemental = (dbObject.supplemental == '1');

        this.publishedAsTBD = (dbObject.published_as_tbd == '1');

        if (typeof dbObject.learning_materials != 'undefined') {
            for (var key in dbObject.learning_materials) {
                this.learningMaterials
                                .push(new LearningMaterialModel(dbObject.learning_materials[key]));
            }
        }
        if ('undefined' != typeof dbObject.is_learner) {
        	this.is_learner = dbObject.is_learner;
        }
    }

    this.disciplines = [];

    this.meshTerms = [];

    this.objectives = [];
    this.objectiveCount = 0;

    this.offerings = new Array();
    this.offeringCount = -1;

    this.dirtyStateListener = {

            modelChanged: function (model, mockedThis) {
                if (model.isModelDirty && (! mockedThis.isModelDirty())) {
                    mockedThis.setDirtyAndNotify();
                }
            }

    };

    if (this.independentLearningModel != null) {
        this.independentLearningModel.addStateChangeListener(this.dirtyStateListener, this);
    }

}

for (var aMethod in AbstractJavaScriptModelForm.prototype) {
    SessionModel.prototype[aMethod] = AbstractJavaScriptModelForm.prototype[aMethod];
}

/*
 * @private
 */
SessionModel.prototype.meetsMinimumPublishingRequirements = function (reviewArray) {
    var rhett = true;
    var populateReviewArray = (typeof reviewArray != 'undefined');
    var value = null;
    var reviewObject = null;

    if (populateReviewArray) {
        reviewArray.push(rhett);
    }

    if (populateReviewArray) {
        reviewObject = new Object();

        reviewObject.displayLabel = ilios_i18nVendor.getI18NString('general.terms.title');
    }

    if ((this.title == null) || (ilios.lang.trim(this.title) == '')) {
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
        reviewObject.displayValue = this.title
                                     + ' <span style="font-size: 8pt; color: #666666;">('
                                     + (this.isPublished()
                                         ? (ilios_i18nVendor.getI18NString('general.terms.published')
                                            + (this.isPublishedAsTBD()
                                                    ? ' - ' + ilios_i18nVendor.getI18NString('general.terms.scheduled')
                                                    : ''))
                                         : ilios_i18nVendor.getI18NString('general.terms.draft'))
                                     + ')</span>';
        reviewObject.passed = true;
    }

    if (populateReviewArray) {
        reviewArray.push(reviewObject);

        reviewObject = new Object();
        reviewObject.displayLabel = ilios_i18nVendor.getI18NString('general.terms.has') + ' '
                                    + ilios_i18nVendor.getI18NString(
                                                               'general.terms.offering_indefinite')
                                    + ' ' + ilios_i18nVendor.getI18NString('general.terms.or') + ' '
                                    + ilios_i18nVendor.getI18NString('general.phrases.due_date');
    }

    value = ilios.utilities.objectPropertyCount(this.offerings);
    if ((value == 0) && (this.independentLearningModel == null)) {
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
                            = ilios_i18nVendor.getI18NString('general.terms.yes').toUpperCase();
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
SessionModel.prototype.meetsSecondaryPublishingRequirements = function (reviewArray) {
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

SessionModel.prototype.getPublishability = function () {
    var rhett = this.CANNOT_BE_PUBLISHED;

    if (this.meetsMinimumPublishingRequirements()) {
        rhett = this.MEETS_MINIMAL_PUBLISHING_REQUIREMENTS;

        if (this.meetsSecondaryPublishingRequirements()) {
            rhett = this.CAN_BE_PUBLISHED;
        }
    }

    return rhett;
};

/*
 * @return {Array} a review array is an ordered array of elements,
 * where the 0th is a TRUE or FALSE as to whether the review [for publication]
 * passes *just*for*this*model* (not the whole tree).
 * The remaining elements are objects with attributes
 *     displayLabel
 *     displayValue
 *     passed (true/false)
 */
SessionModel.prototype.getReviewArray = function () {
    var rhett = [];
    var meetsMinimumRequirements = this.meetsMinimumPublishingRequirements(rhett);
    var meetsSecondaryRequirements = this.meetsSecondaryPublishingRequirements(rhett);
    rhett[0] = (meetsMinimumRequirements && meetsSecondaryRequirements);
    return rhett;
};

SessionModel.prototype.saveAttemptWarningMessage = function () {
    var str = null;

    if ((this.title == null) || (ilios.lang.trim(this.title) == '')) {
        str = ilios_i18nVendor.getI18NString('course_management.error.session_save.no_title');

        return ilios_i18nVendor.getI18NString('course_management.error.session_save.no_title');
    }

    for (var key in this.objectives) {
        str = this.objectives[key].getTitle();

        if ((str == null) || (ilios.lang.trim(str) == '')) {
            return ilios_i18nVendor.getI18NString(
                                        'course_management.error.session_save.blank_objective');
        }
    }

    return null;
};

SessionModel.prototype.setAttireRequired = function (flag) {
    if (this.attireRequired != flag) {
        this.attireRequired = flag;

        this.setDirtyAndNotify();
    }
};

SessionModel.prototype.isAttireRequired = function () {
    return this.attireRequired;
};


SessionModel.prototype.setEquipmentRequired = function (flag) {
    if (this.equipmentRequired != flag) {
        this.equipmentRequired = flag;

        this.setDirtyAndNotify();
    }
};

SessionModel.prototype.isEquipmentRequired = function () {
    return this.equipmentRequired;
};

SessionModel.prototype.isLearner = function () {
	return this.is_learner;
}


SessionModel.prototype.setSupplemental = function (flag) {
    if (this.supplemental != flag) {
        this.supplemental = flag;

        this.setDirtyAndNotify();
    }
};

SessionModel.prototype.isSupplemental = function () {
    return this.supplemental;
};

/**
 * @return this will return -1 unless this instance was constructed with a db object
 */
SessionModel.prototype.getCourseId = function () {
    return this.courseId;
};

SessionModel.prototype.setDescription = function (desc) {
    if (this.description != desc) {
        this.description = desc;

        this.setDirtyAndNotify();
    }
};

SessionModel.prototype.getDescription = function () {
    return this.description;
};

SessionModel.prototype.setSessionTypeId = function (stid) {
    if (this.sessionTypeId != stid) {
        this.sessionTypeId = stid;

        this.setDirtyAndNotify();
    }
};

SessionModel.prototype.getSessionTypeId = function () {
    return this.sessionTypeId;
};

SessionModel.prototype.getSessionTypeTitle = function () {
    return this.sessionTypeName;
};

SessionModel.prototype.setPublishedAsTBD = function (flag) {
    this.publishedAsTBD = flag;
};

SessionModel.prototype.isPublishedAsTBD = function () {
    return this.publishedAsTBD;
};

SessionModel.prototype.setPublishEventId = function (eventId) {
    if (this.publishEventId != eventId) {
        this.publishEventId = eventId;

        this.setDirtyAndNotify();
    }
};

SessionModel.prototype.getPublishEventId = function () {
    return this.publishEventId;
};

SessionModel.prototype.isPublished = function () {
    return (this.publishEventId > 0);
};

/**
 * Pre-existence is not checked.
 */
SessionModel.prototype.addLearningMaterial = function (learningMaterialModel) {
    this.learningMaterials.push(learningMaterialModel);
};

SessionModel.prototype.removeLearningMaterial = function (learningMaterialModel) {
    this.removeLearningMaterialWithId(learningMaterialModel.getDBId());
};

SessionModel.prototype.removeLearningMaterialWithId = function (dbId) {
    for (var key in this.learningMaterials) {
        if (this.learningMaterials[key].getDBId() == dbId) {
            ilios.utilities.removeElementWithValue(this.learningMaterials,
                                                   this.learningMaterials[key]);

            return;
        }
    }
};

/**
 * Pre-existence is checked based on offering model dbId.
 */
SessionModel.prototype.addOffering = function (offeringModel) {
    // There's all sorts of silliness going on here.. maintaining our own count of an array into
    //      which we push(); traversing by key when it's a push() array with length and standard
    //      index; ...
    for (var key in this.offerings) {
        if (offeringModel.getDBId() == this.offerings[key].getDBId()) {
            return;
        }
    }

    this.offerings.push(offeringModel);

    if (this.offeringCount == -1) {
        this.offeringCount = 1;
    }
    else {
        this.offeringCount++;
    }
};

SessionModel.prototype.removeOffering = function (offeringModel) {
    for (var key in this.offerings) {
        if (this.offerings[key] == offeringModel) {
            delete this.offerings[key];

            this.offeringCount--;

            return;
        }
    }
};

SessionModel.prototype.removeAllOfferings = function () {
    this.offerings = new Array();
    this.offeringCount = -1;
};

SessionModel.prototype.sortOfferings = function () {
    this.offerings.sort(this.offeringComparator);
};

SessionModel.prototype.offeringComparator = function (o1, o2) {
    return (o1.getStartDate().getTime() - o2.getStartDate().getTime());
};

SessionModel.prototype.getOfferings = function () {
    return this.offerings;
};

SessionModel.prototype.getOfferingsEnumerationFormattedHTML = function () {
    var rhett = '';
    var offering = null;
    var start = null;
    var end = null;

    for (var key in this.offerings) {
        offering = this.offerings[key];

        if (rhett.length > 0) {
            rhett += '<br/>';
        }

        start = offering.getStartDate();
        end = offering.getEndDate();

        rhett += '&middot; ' + start.format('ddd mmm dd yyyy HH:MM TT') + ' - ';
        if ((start.getDate() != end.getDate()) || (start.getMonth() != end.getMonth())
                || (start.getFullYear() != end.getFullYear())) {
            rhett += end.format('ddd mmm dd yyyy HH:MM TT');
        }
        else {
            rhett += end.format('HH:MM TT');
        }
    }

    return rhett;
};

SessionModel.prototype.setOfferingCount = function (count) {
    this.offeringCount = count;
};

SessionModel.prototype.getOfferingCount = function () {
    return ((this.offeringCount == -1) ? ilios.utilities.objectPropertyCount(this.offerings)
                                       : this.offeringCount);
};

/**
 * As the SessionModel is used for differing use cases, sometimes we will use the model to just
 *  track the number of offerings (but not actually carry the weight of having those representations
 *  -- like as is done in CM) and other time we'll actually want to carry the offering models (like
 *  as is done in OM). This returns true if we have the offering model instances actually stored.
 */
SessionModel.prototype.offeringStorageIsShallow = function () {
    return (this.offeringCount != ilios.utilities.objectPropertyCount(this.offerings));
};

SessionModel.prototype.setILMAttributes = function (hours, dueDate, learnerGroups, instructors) {
    if (hours > 0) {
        if (this.independentLearningModel == null) {
            this.independentLearningModel = new IndependentLearningModel();

            this.independentLearningModel.addStateChangeListener(this.dirtyStateListener, this);
        }

        this.independentLearningModel.setHours(hours);
        this.independentLearningModel.setDueDate(dueDate);
        this.independentLearningModel.setLearnerGroups(learnerGroups);
        this.independentLearningModel.setInstructors(instructors);

        this.setDirtyAndNotify();
    }
    else {
        if (this.independentLearningModel != null) {
            this.independentLearningModel = null;

            this.setDirtyAndNotify();
        }
    }
};

SessionModel.prototype.setIndependentLearningModel = function (ilm) {
    this.independentLearningModel = ilm;

    this.setDirtyAndNotify();
};

SessionModel.prototype.getIndependentLearningModel = function () {
    return this.independentLearningModel;
};

SessionModel.prototype.addMeSHItem = function (meshItem) {
    this.meshTerms.push(meshItem);
};

SessionModel.prototype.removeMeSHItem = function (meshItem) {
    for (var key in this.meshTerms) {
        if (meshItem.compareTo(this.meshTerms[key]) == 0) {
            delete this.meshTerms[key];

            return;
        }
    }
};

SessionModel.prototype.containsMeSHItem = function (meshItem) {
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
SessionModel.prototype.getMeSHItems = function () {
    return this.meshTerms;
};

SessionModel.prototype.getMeSHItemsAsFormattedText = function () {
    return ilios.utilities.delimitedStringOfTitledObjects(this.meshTerms, ';');
};

SessionModel.prototype.getLearningMaterials = function () {
    return this.learningMaterials;
};

SessionModel.prototype.getLearningMaterialForId = function (learningMaterialId) {
    for (var key in this.learningMaterials) {
        if (learningMaterialId == (this.learningMaterials[key]).getDBId()) {
            return this.learningMaterials[key];
        }
    }

    return null;
};

SessionModel.prototype.containsLearningMaterial = function (learningMaterialModel) {
    return (this.getLearningMaterialForId(learningMaterialModel.getDBId()) != null);
};

SessionModel.prototype.getNextObjectiveNumber = function () {
    this.objectiveCount++;

    return this.objectiveCount;
};

SessionModel.prototype.getObjectives = function () {
    return this.objectives;
};

SessionModel.prototype.getContainerForObjectiveWithId = function (dbId) {
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
SessionModel.prototype.getContainerForObjectiveWithTextMD5 = function (md5) {
    for (var key in this.objectives) {
        if (this.objectives[key].getTextMD5() == md5) {
            return key;
        }
    }

    return null;
};

SessionModel.prototype.getObjectiveForContainer = function (containerNumber) {
    return this.objectives[containerNumber];
};

SessionModel.prototype.addObjectiveForContainer = function (objectiveModel, containerNumber) {
    this.objectives[containerNumber] = objectiveModel;

    objectiveModel.addStateChangeListener(this.dirtyStateListener, this);

    this.setDirtyAndNotify();
};

SessionModel.prototype.removeObjectiveForContainer = function (containerNumber) {
    var lovelyJavascript = (containerNumber in this.objectives);

    if (lovelyJavascript) {
        delete this.objectives[containerNumber];

        this.setDirtyAndNotify();
    }
};

/**
 * @param {Array} disciplineArray an array of DisciplineModel instances.
 */
SessionModel.prototype.setDisciplines = function (disciplineArray) {
    this.disciplines = disciplineArray.slice(0);
    this.disciplines.sort(ilios.utilities.titledNaturalComparator);
    this.setDirtyAndNotify();
};

/**
 * @return {Array} an array of DisciplineModel instances.
 */
SessionModel.prototype.getDisciplines = function () {
    return this.disciplines;
};

SessionModel.prototype.getFirstEventStart = function () {
    // TODO cache cache cache
    var rhett = Number.MAX_VALUE;
    var offering = null;

    for (var key in this.offerings) {
        offering = this.offerings[key];

        if (offering.getStartDate().getTime() < rhett) {
            rhett = offering.getStartDate().getTime();
        }
    }

    return rhett;
};

SessionModel.prototype.getLastEventEnd = function () {
    // TODO cache cache cache
    var rhett = Number.MIN_VALUE;
    var offering = null;

    for (var key in this.offerings) {
        offering = this.offerings[key];

        if (offering.getEndDate().getTime() > rhett) {
            rhett = offering.getEndDate().getTime();
        }
    }

    return rhett;
};

SessionModel.prototype.clone = function () {
    var rhett = new SessionModel();

    rhett.dbId = this.dbId;
    rhett.title = this.title;

    rhett.publishEventId = this.publishEventId;
    rhett.publishedAsTBD = this.publishedAsTBD;

    rhett.sessionTypeId = this.sessionTypeId;

    rhett.disciplines = this.disciplines.slice(0);

    rhett.meshTerms = this.meshTerms.concat();

    rhett.objectiveCount = this.objectiveCount;
    rhett.objectives = ilios.utilities.deepCloneAssociativeArray(this.objectives);

    rhett.offerings = ilios.utilities.cloneAssociativeArray(this.offerings);

    // (should still be clean.. but just in case future coders accidentally add code that dirties
    //      above..)
    rhett.clearDirtyState();

    return rhett;
};

/**
 * Returns a standard compare, the value of which would allow natural ordering. This compares in
 *  the order of:
 *      title
 *      session type
 *      disciplines size
 *      mesh size
 *      objectives size
 *      offerings size
 *      dbId
 */
SessionModel.prototype.compareTo = function (otherModel) {
    var temp = null;

    if (this.title != null) {
        temp = this.title.localeCompare(otherModel.title);

        if (temp != 0) {
            return temp;
        }
    }
    else if (otherModel.title != null) {
        return 1;           // arbitrary but consistent
    }

    if (this.sessionTypeId != otherModel.sessionTypeId) {
        return (this.sessionTypeId - otherModel.sessionTypeId);
    }

    if (this.disciplines.length !== otherModel.disciplines.length) {
        return 1;           // arbitrary but consistent
    }

    if (ilios.utilities.objectPropertyCount(this.meshTerms)
                            != ilios.utilities.objectPropertyCount(otherModel.meshTerms)) {
        return 1;           // arbitrary but consistent
    }

    if (ilios.utilities.objectPropertyCount(this.objectives)
                            != ilios.utilities.objectPropertyCount(otherModel.objectives)) {
        return 1;           // arbitrary but consistent
    }

    if (ilios.utilities.objectPropertyCount(this.offerings)
                            != ilios.utilities.objectPropertyCount(otherModel.offerings)) {
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
SessionModel.prototype.replaceContentWithModel = function (newModel, forceReplacement) {
    if (forceReplacement || (this.compareTo(newModel) != 0)) {
        this.title = newModel.title;
        this.dbId = newModel.dbId;

        this.publishEventId = newModel.publishEventId;
        this.publishedAsTBD = newModel.publishedAsTBD;

        this.sessionTypeId = newModel.sessionTypeId;

        this.disciplines = newModel.disciplines;

        this.meshTerms = newModel.meshTerms;

        this.objectiveCount = newModel.objectiveCount;
        this.objectives = newModel.objectives;

        this.offerings = newModel.offerings;

        this.setDirtyAndNotify();
    }
};
