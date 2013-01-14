
/**
 * This file contains the model repesentation of a Group in javascript land; this is used to
 * 	encapsulate, and so therefore better track the dirty state of, our model.
 *
 * DEPENDENCY:
 * 		scripts/models/user_model.js
 */
function StudentModel () {

	UserModel.apply(this, arguments);

	this.parentGroup = null;

	// this is used for mid-dirty-state storage in the dialog
	this.previousParentGroup = null;

};

for (var aMethod in UserModel.prototype) {
	StudentModel.prototype[aMethod] = UserModel.prototype[aMethod];
};

StudentModel.prototype.setParentGroup = function (parent, affectDirtyState) {
	if (typeof affectDirtyState == 'undefined') {
		affectDirtyState = true;
	}

	if (this.parentGroup != parent) {
		this.parentGroup = parent;

		if (affectDirtyState) {
			this.setDirtyAndNotify();
		}
	}
};

StudentModel.prototype.getParentGroup = function () {
	return this.parentGroup;
};

/**
 * This does not affect the dirty state
 */
StudentModel.prototype.setPreviousParentGroup = function (parent) {
	this.previousParentGroup = parent;
};

StudentModel.prototype.getPreviousParentGroup = function () {
	return this.previousParentGroup;
};

StudentModel.prototype.clone = function () {
	var rhett = new StudentModel();

	rhett.setDBId(this.dbId);

	rhett.setLastName(this.lastName);
	rhett.setFirstName(this.firstName);
	rhett.setMiddleName(this.middleName);

	rhett.setPhoneNumber(this.phoneNumber);
	rhett.setEmailAddress(this.emailAddress);

	rhett.setUCUniqueId(this.ucUID);
	rhett.setOtherId(this.otherId);

	rhett.setParentGroup(this.parentGroup);

	rhett.clearDirtyState();

	return rhett;
};

/**
 * Returns a standard compare, the value of which would allow natural ordering. Comparison takes
 * 	place in the order of:
 * 		last name
 * 		first name
 * 		middle name
 * 		uc uid
 * 		galen id
 * 		other id
 * 		email address
 * 		phone number
 * 		db id
 */
StudentModel.prototype.compareTo = function (otherModel) {
	var temp = this.lastName.localeCompare(otherModel.lastName);

	if (temp != 0) {
		return temp;
	}

	temp = this.firstName.localeCompare(otherModel.firstName);
	if (temp != 0) {
		return temp;
	}

	temp = this.middleName.localeCompare(otherModel.middleName);
	if (temp != 0) {
		return temp;
	}

	temp = this.ucUID.localeCompare(otherModel.ucUID);
	if (temp != 0) {
		return temp;
	}

	temp = this.otherId.localeCompare(otherModel.otherId);
	if (temp != 0) {
		return temp;
	}

	temp = this.emailAddress.localeCompare(otherModel.emailAddress);
	if (temp != 0) {
		return temp;
	}

	temp = this.phoneNumber.localeCompare(otherModel.phoneNumber);
	if (temp != 0) {
		return temp;
	}

	if (this.dbId != otherModel.dbId) {
		return (this.dbId - otherModel.dbId);
	}

	return 0;
};
