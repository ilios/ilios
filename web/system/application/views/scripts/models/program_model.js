/**
 * This file contains the model repesentation of a Program in javascript land; this is used to
 * 	encapsulate, and so therefore better track the dirty state of, our model.
 *
 * DEPENDENCY:
 * 		scripts/models/abstract_js_model_form.js
 */
function ProgramModel (dbObject) {

	AbstractJavaScriptModelForm.apply(this, arguments);

	this.shortTitle = null;
	this.duration = 0;

	this.publishEventId = -1;
    this.publishedAsTBD = false;

	this.programYearArray = new Array();
	this.programYearCount = 0;

	this.lifeTimeYearModelAdditionCount = 1;

	if (typeof dbObject != 'undefined') {
        this.dbId = dbObject.program_id;
        this.title = dbObject.title;
        this.shortTitle = dbObject.short_title;

        this.duration = dbObject.duration;

        if (dbObject.publish_event_id != null) {
            this.publishEventId = parseInt(dbObject.publish_event_id);
        }
        this.publishedAsTBD = (dbObject.published_as_tbd == '1');

    	this.isDirty = false;
    }

	this.dirtyStateListener = {

			modelChanged: function (model, mockedThis) {
				// presently there is no functionality associated to this
			}

	};

}

for (var aMethod in AbstractJavaScriptModelForm.prototype) {
	ProgramModel.prototype[aMethod] = AbstractJavaScriptModelForm.prototype[aMethod];
}

ProgramModel.prototype.addProgramYear = function (programYearModel, containerNumber) {
	var key = this.generateKeyForContainerNumber(containerNumber);

	programYearModel.addStateChangeListener(this.dirtyStateListener, this);

	this.programYearArray[key] = programYearModel;
	this.programYearCount++;

	this.lifeTimeYearModelAdditionCount++;
};

ProgramModel.prototype.removeProgramYear = function (programYearModel) {
	for (var key in this.programYearArray) {
		if (this.programYearArray[key] == programYearModel) {
			delete this.programYearArray[key];

			this.programYearCount--;

			return;
		}
	}
};

/**
 * @return -1 if the model can't be found in our internal store, otherwise the 0-based index
 */
ProgramModel.prototype.getContainerNumberForProgramYear = function (programYearModel) {
	for (var key in this.programYearArray) {
		if (this.programYearArray[key] == programYearModel) {
			var pieces = key.split('_');

			return parseInt(pieces[1]);
		}
	}

	return -1;
};

ProgramModel.prototype.getProgramYearCount = function () {
	var count = 0;

	for (var key in this.programYearArray) {
		count++;
	}

	return count;
};

ProgramModel.prototype.getProgramYearArray = function () {
	return this.programYearArray;
};

ProgramModel.prototype.setProgramYearArray = function (pyArray) {
	this.programYearArray = pyArray;
};

// should be considered protected
ProgramModel.prototype.generateKeyForContainerNumber = function (containerNumber) {
	return "key_" + containerNumber;
};

ProgramModel.prototype.getProgramYearForContainerNumber = function (containerNumber) {
	var key = this.generateKeyForContainerNumber(containerNumber);

	return this.programYearArray[key];
};

ProgramModel.prototype.getNextContainerNumber = function () {
	return this.lifeTimeYearModelAdditionCount;
};

ProgramModel.prototype.setNextContainerNumber = function (nextContainerNumber) {
	this.lifeTimeYearModelAdditionCount = nextContainerNumber;
};

ProgramModel.prototype.setShortTitle = function (newTitle) {
	if (this.shortTitle != newTitle) {
		this.shortTitle = newTitle;

		this.setDirtyAndNotify();
	}
};

ProgramModel.prototype.getShortTitle = function () {
	return this.shortTitle;
};

ProgramModel.prototype.setDuration = function (newDuration) {
	if (this.duration != newDuration) {
		this.duration = newDuration;

		this.setDirtyAndNotify();
	}
};

ProgramModel.prototype.getDuration = function () {
	return this.duration;
};

ProgramModel.prototype.setPublishEventId = function (eventId) {
	if (this.publishEventId != eventId) {
		this.publishEventId = eventId;

		this.setDirtyAndNotify();
	}
};

ProgramModel.prototype.getPublishEventId = function () {
	return this.publishEventId;
};
