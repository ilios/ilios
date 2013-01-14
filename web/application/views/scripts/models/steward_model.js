/**
 * This serves as a simple 'object' representation of a steward.
 */

/**
 * @param stewardName the display title of the steward
 * @param parentSchoolId must be -1 if this is for a school and not a department
 * @param id the database row id associated to this object
 */
function StewardModel (stewardName, parentSchoolId, id) {

	this.stewardTitle = stewardName;

	this.parentId = parentSchoolId;

	this.dbId = id;

};

StewardModel.prototype.getStewardTitle = function () {
	return this.stewardTitle;
};

StewardModel.prototype.getParentSchoolId = function () {
	return this.parentId;
};

StewardModel.prototype.getDBId = function () {
	return this.dbId;
};

StewardModel.prototype.isDepartment = function () {
	return (this.parentId != -1);
};

/**
 * Returns a standard compare, the value of which would allow natural ordering. This compares in
 * 	the order of:
 * 		stewardTitle
 * 		parentId
 * 		db id
 */
StewardModel.prototype.compareTo = function (otherModel) {
	var temp = this.stewardTitle.localeCompare(otherModel.stewardTitle);

	if (temp != 0) {
		return temp;
	}

	if (this.parentId != otherModel.parentId) {
		return this.parentId - otherModel.parentId;
	}

	return this.dbId - otherModel.dbId;
};

StewardModel.prototype.clone = function () {
	return new StewardModel(this.stewardTitle, this.parentId, this.dbId);
};
