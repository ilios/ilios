/**
 * This file contains the model repesentation of a Program Year in javascript land; this is used to
 * 	encapsulate, and so therefore better track the dirty state of, our model.
 *
 * DEPENDENCY:
 * 		scripts/models/abstract_js_model_form.js
 * 		scripts/ilios_utilities.js
 */

function ProgramYearModel () {

	AbstractJavaScriptModelForm.apply(this, arguments);

	this.startYear = 0;
	this.publishEventId = -1;		// todo ideally this is a reference to a model representation

    this.locked = false;

	this.competencyArray = [];

	this.objectiveArray = [];
	this.objectiveCount = 0;

	this.disciplines = [];

	this.directors = [];
	this.directorCount = 0;

	this.stewardArray = new Array();

	this.dirtyStateListener = {

			modelChanged: function (model, mockedThis) {
				if (model.isModelDirty() && (! mockedThis.isModelDirty())) {
					mockedThis.setDirtyAndNotify();
				}
			}

	};

};

for (var aMethod in AbstractJavaScriptModelForm.prototype) {
	ProgramYearModel.prototype[aMethod] = AbstractJavaScriptModelForm.prototype[aMethod];
};

ProgramYearModel.prototype.setStartYear = function (newYear) {
	var year = parseInt(newYear);

	if (this.startYear != year) {
		this.startYear = year;

		this.setDirtyAndNotify();
	}
};

ProgramYearModel.prototype.getStartYear = function () {
	return this.startYear;
};

ProgramYearModel.prototype.setPublishEventId = function (eventId) {
	if (this.publishEventId != eventId) {
		this.publishEventId = eventId;

		this.setDirtyAndNotify();
	}
};

ProgramYearModel.prototype.getPublishEventId = function () {
	return this.publishEventId;
};

ProgramYearModel.prototype.setLocked = function (flag) {
    this.locked = flag;
};

ProgramYearModel.prototype.isLocked = function () {
	return this.locked;
};

ProgramYearModel.prototype.isPublished = function () {
	return ((this.publishEventId != -1) && (this.publishEventId != null));
};

/**
 * @param competencies an array (map) of CompetencyModel instances keyed by their title; the object
 * 							is cloned and so may be disposed of after this method returns.
 */
ProgramYearModel.prototype.setCompetencyArray = function (competencies) {
    var shouldReplace = false;

    if (this.competencyArray.length != competencies.length) {
    shouldReplace = true;
    } else {
        var model = null;

        for (var i = 0; ((i < this.competencyArray.length) && (! shouldReplace)); i++) {
            model = this.competencyArray[i];

            for (var j = 0; ((j < competencies.length) && (! shouldReplace)); j++) {
                if (model.compareTo(competencies[j]) != 0) {
                    shouldReplace = true;
                }
            }
        }
    }

    if (shouldReplace) {
        this.competencyArray = competencies.slice(0);
        this.setDirtyAndNotify();
    }
};

/**
 * @return an array (map) of CompetencyModel instances keyed by their title
 */
ProgramYearModel.prototype.getCompetencyArray = function () {
	return this.competencyArray;
};

ProgramYearModel.prototype.getNextObjectiveNumber = function () {
	this.objectiveCount++;

	return this.objectiveCount;
};

ProgramYearModel.prototype.getObjectiveCount = function () {
	return this.objectiveCount;
};

ProgramYearModel.prototype.getObjectives = function () {
	return this.objectiveArray;
};

ProgramYearModel.prototype.getContainerForObjectiveWithId = function (dbId) {
	for (var key in this.objectiveArray) {
		if (this.objectiveArray[key].getDBId() == dbId) {
			return key;
		}
	}

	return null;
};

/*
 * This is potentially expensive.
 */
ProgramYearModel.prototype.getContainerForObjectiveWithTextMD5 = function (md5) {
	for (var key in this.objectiveArray) {
		if (this.objectiveArray[key].getTextMD5() == md5) {
			return key;
		}
	}

	return null;
};

ProgramYearModel.prototype.getObjectiveForContainer = function (containerNumber) {
	return this.objectiveArray[containerNumber];
};

ProgramYearModel.prototype.addObjectiveForContainer = function (objectiveModel, containerNumber) {
	this.objectiveArray[containerNumber] = objectiveModel;

	objectiveModel.addStateChangeListener(this.dirtyStateListener, this);

	this.setDirtyAndNotify();
};

ProgramYearModel.prototype.removeObjectiveForContainer = function (containerNumber) {
	var lovelyJavascript = (containerNumber in this.objectiveArray);

	if (lovelyJavascript) {
		delete this.objectiveArray[containerNumber];

		this.setDirtyAndNotify();
	}
};

/**
 * @param {Array} disciplines an array of DisciplineModel instances.
 */
ProgramYearModel.prototype.setDisciplineArray = function (disciplines) {
    var i, n;
    this.disciplines = disciplines.slice(0);
    this.disciplines.sort(ilios.utilities.titledNaturalComparator);
    for (i =0, n = this.disciplines.length; i < n; i++) {
        this.disciplines[i].addStateChangeListener(this.dirtyStateListener, this);
    }
    this.setDirtyAndNotify();
};

/**
 * @return {Array} an array of DisciplineModel instances.
 */
ProgramYearModel.prototype.getDisciplineArray = function () {
    return this.disciplines;
};

/**
 * @param directorArray an array (map) of UserModel instances; the contents of the array are copied
 * 							and so the array may be disposed of after this method returns.
 */
ProgramYearModel.prototype.setDirectors = function (directorArray) {
	var director = null;

	this.directors = null;

	this.directors = new Array();
	this.directorCount = 0;

	for (var key in directorArray) {
		director = directorArray[key];

		this.directors[director.getUniqueKey()] = director;
		this.directorCount++;
	}

	this.registerSelfWithModels(this.directors);

	this.setDirtyAndNotify();
};

/**
 * @param director must be an instance of UserModel
 */
ProgramYearModel.prototype.addDirector = function (director) {
	var key = director.getUniqueKey();
	var lovelyJavascript = (key in this.directors);

	if (! lovelyJavascript) {
		this.directors[key] = director;

		this.directorCount++;

		this.setDirtyAndNotify();
	}
};

ProgramYearModel.prototype.removeDirector = function (director) {
	var key = director.getUniqueKey();
	var lovelyJavascript = (key in this.directors);

	if (lovelyJavascript) {
		delete this.directors[key];

		this.directorCount--;

		this.setDirtyAndNotify();
	}
};

/**
 * @return an array (map) of UserModel instances keyed by the UserModel's getUniqueKey return
 */
ProgramYearModel.prototype.getDirectors = function () {
	return this.directors;
};

ProgramYearModel.prototype.getDirectorsAsFormattedText = function () {
	var rhett = '';

	for (var key in this.directors) {
		if (rhett.length > 0) {
			rhett += '; ';
		}

		rhett += this.directors[key].getFormattedName(ilios.utilities.USER_NAME_FORMAT_LAST_FIRST);
	}

	return rhett;
};

ProgramYearModel.prototype.setStewardArray = function (stewards) {
	var shouldReplace = false;

	if (this.stewardArray.length != stewards.length) {
		shouldReplace = true;
	}
	else {
		var model = null;

		for (var i = 0; ((i < this.stewardArray.length) && (! shouldReplace)); i++) {
			model = this.stewardArray[i];

			for (var j = 0; ((j < stewards.length) && (! shouldReplace)); j++) {
				if (model.compareTo(stewards[j]) != 0) {
					shouldReplace = true;
				}
			}
		}
	}

	if (shouldReplace) {
		this.stewardArray = ilios.utilities.cloneAssociativeArray(stewards);

		this.setDirtyAndNotify();
	}
};

ProgramYearModel.prototype.getStewardArray = function () {
	return this.stewardArray;
};

//should be considered protected
ProgramYearModel.prototype.registerSelfWithModels = function (modelArray) {
	for (var key in modelArray) {
		modelArray[key].addStateChangeListener(this.dirtyStateListener, this);
	}
};

/**
 * Overridden to deal with the various possible states of this model.
 */
ProgramYearModel.prototype.canPublish = function () {
	return (this.startYear > 1872);
};

/**
 * This does not clone:
 *		. dirty state
 *		. publishEventId
 *		. db id
 * It also does not do a deep clone on the arrays so, in other words, what models are returned are
 * 	handles to the same models held by this instance; the exception to this are the objectives
 *  which are themselves cloned for the return PYM clone.
 *
 * NOTE: we cannot presently include the db id in the clone as its absence is used as an indicator
 * 			during the save transactions.
 */
ProgramYearModel.prototype.clone = function () {
	var rhett = new ProgramYearModel();
    var objectiveModel = null;

	rhett.setTitle(this.title);
	rhett.setStartYear(this.startYear);
	rhett.setCompetencyArray(this.competencyArray);
	rhett.setDisciplineArray(this.disciplines);
	rhett.setDirectors(this.directors);
	rhett.setStewardArray(this.stewardArray);

    rhett.objectiveCount = this.objectiveCount;
    for (var i = 0; i <= this.objectiveCount; i++) {
        objectiveModel = this.objectiveArray[i];

        if (typeof objectiveModel != 'undefined') {
            rhett.addObjectiveForContainer(objectiveModel.clone(), i);
        }
    }

	return rhett;
};

/**
 * Returns a standard compare, the value of which would allow natural ordering. This compares in
 * 	the order of:
 * 		title
 * 		startYear
 * 		directors
 * 		disciplines size
 * 		competencies size
 * 		stewards size
 * 		objective size
 * We do not include dbId in the comparison since a clone does not include this attribute.
 */
ProgramYearModel.prototype.compareTo = function (otherModel) {
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
		return 1;			// arbitrary but consistent
	}

	if (this.startYear != otherModel.startYear) {
		return (this.startYear - otherModel.startYear);
	}

	if (this.directorCount != otherModel.directorCount) {
		return 1;			// arbitrary but consistent
	}

	for (var key in this.directors) {
		flag = (key in otherModel.directors);

		if (! flag) {
			return -1;		// arbitrary but consistent
		}

		model = this.directors[key];

		temp = model.compareTo(otherModel.directors[key]);
		if (temp != 0) {
			return temp;
		}
	}

	if (this.disciplines.length !== otherModel.disciplines.length) {
		return 1;			// arbitrary but consistent
	}

	if (ilios.utilities.objectPropertyCount(this.competencyArray)
							!= ilios.utilities.objectPropertyCount(otherModel.competencyArray)) {
		return 1;			// arbitrary but consistent
	}

	if (ilios.utilities.objectPropertyCount(this.stewardArray)
							!= ilios.utilities.objectPropertyCount(otherModel.stewardArray)) {
		return 1;			// arbitrary but consistent
	}

	if (this.objectiveCount != otherModel.objectiveCount) {
		return 1;			// arbitrary but consistent
	}

	return 0;
};


/**
 * If the newModel differs from this model, then it is replaced by the newModel and this is set
 * 		dirty. Comparisons are done by values and not indentical instance.
 *
 * NOTES / REMINDERS:
 * 	. Any modifications to the users held by the parameter after this method is called will also
 * 			change those instances as held by this instance.
 */
ProgramYearModel.prototype.replaceContentWithModel = function (newModel, forceReplacement) {
	if (forceReplacement || (this.compareTo(newModel) != 0)) {
		var model = null;

		this.title = newModel.title;
		this.dbId = newModel.dbId;
		this.startYear = newModel.startYear;

		this.directorCount = newModel.directorCount;
		this.directors = new Array();

		for (var key in newModel.directors) {
			model = newModel.directors[key];

			this.directors[key] = model;
		}

		this.disciplines = newModel.disciplines;
		this.competencyArray = newModel.competencyArray;
		this.stewardArray = newModel.stewardArray;

        this.objectiveArray = newModel.objectiveArray;
        this.objectiveCount = newModel.objectiveCount;

		this.setDirtyAndNotify();
	}
};
