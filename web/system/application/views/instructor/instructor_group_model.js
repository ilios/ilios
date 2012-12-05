/**
 * This file contains the model repesentation of a Group in javascript land; this is used to
 * encapsulate, and so therefore better track the dirty state of, our model.
 *
 * DEPENDENCIES:
 *     scripts/models/abstract_js_model_form.js
 */
function InstructorGroupModel () {

	AbstractJavaScriptModelForm.apply(this, arguments);

	this.users = new Array();
	this.userCount = 0;

	this.associatedCourses = new Array();

};

for (var aMethod in AbstractJavaScriptModelForm.prototype) {
	InstructorGroupModel.prototype[aMethod] = AbstractJavaScriptModelForm.prototype[aMethod];
};

/**
 * This instance will be unusable after this call.
 */
InstructorGroupModel.prototype.destroy = function () {
	for (var key in this.users) {
		delete this.users[key];
	}
};

InstructorGroupModel.prototype.addUser = function (userModel) {
	var key = userModel.getUniqueKey();
	var lovelyJavascript = (key in this.users);

	if (! lovelyJavascript) {
		this.users[key] = userModel;

		this.userCount++;

		this.setDirtyAndNotify();
	}
};

InstructorGroupModel.prototype.removeUser = function (userModel) {
	var key = userModel.getUniqueKey();
	var lovelyJavascript = (key in this.users);

	if (lovelyJavascript) {
		delete this.users[key];

		this.userCount--;

		this.setDirtyAndNotify();
	}
};

InstructorGroupModel.prototype.removeAllUsers = function () {
	for (var key in this.users) {
		delete this.users[key];
	}

	this.userCount = 0;
	this.users = new Array();

	this.setDirtyAndNotify();
};

InstructorGroupModel.prototype.getUsers = function () {
	return this.users;
};

InstructorGroupModel.prototype.getMemberCount = function () {
	return this.userCount;
};

InstructorGroupModel.prototype.getUsersAsArrayOfUserIds = function () {
	var rhett = new Array();
	var userModel = null;

	for (var key in this.users) {
		userModel = this.users[key];

		rhett.push(userModel.getDBId());
	}

	return rhett;
};

/**
 * This does a shallow copy of its users
 */
InstructorGroupModel.prototype.clone = function () {
	var rhett = new InstructorGroupModel();

	rhett.setDBId(this.dbId);
	rhett.setTitle(this.title);

	rhett.userCount = this.userCount;
	for (var key in this.users) {
		rhett.users[key] = this.users[key];
	}

	rhett.clearDirtyState();

	return rhett;
};

/**
 * If the newGroupModel differs from this model, then it is replaced by the newGroupModel and this
 * 	is set dirty. Comparisons are done by values and not indentical instance.
 *
 * NOTES / REMINDERS:
 * 	. Any modifications to the users held by the parameter after this method is called will also
 * 			change those instances as held by this instance.
 */
InstructorGroupModel.prototype.replaceContentWithModel = function (newGroupModel,
																   forceReplacement) {
	if (forceReplacement || (this.compareTo(newGroupModel) != 0)) {
		var model = null;

		this.title = newGroupModel.title;
		this.dbId = newGroupModel.dbId;

		this.destroy();

		this.userCount = newGroupModel.userCount;
		this.users = new Array();

		for (var key in newGroupModel.users) {
			model = newGroupModel.users[key];

			this.users[key] = model;
		}

		this.setDirtyAndNotify();
	}
};

/**
 * Returns a standard compare, the value of which would allow natural ordering. This compares in
 * 	the order of:
 * 		title
 * 		users
 * 		db id
 */
InstructorGroupModel.prototype.compareTo = function (otherModel) {
	var temp = this.title.localeCompare(otherModel.title);
	var model = null;
	var flag = false;

	if (temp != 0) {
		return temp;
	}

	if (this.userCount != otherModel.userCount) {
		return 1;			// arbitrary but consistent
	}

	for (var key in this.users) {
		flag = (key in otherModel.users);

		if (! flag) {
			return -1;		// arbitrary but consistent
		}

		model = this.users[key];

		temp = model.compareTo(otherModel.users[key]);
		if (temp != 0) {
			return temp;
		}
	}

	if (this.dbId != otherModel.dbId) {
		return (this.dbId - otherModel.dbId);
	}

	return 0;
};

/**
 * Given another group model return an array of users that are in this instance but not in the
 * 	other instance. This does NOT examine subgroups.
 */
InstructorGroupModel.prototype.getUserDiffFromModel = function (otherModel) {
	var rhett = new Array();
	var lovelyJavascript = false;

	for (var key in this.users) {
		lovelyJavascript = (key in otherModel.users);

		if (! lovelyJavascript) {
			rhett.push(this.users[key]);
		}
	}

	return rhett;
};

/**
 * @return {Object} an Object instance with instructor_group_id, title, and users
 * @todo replace with better serialization mechanism
 */
InstructorGroupModel.prototype.getAsHodgePodgeObject = function () {
	var rhett = new Object();
	var anArray = new Array();

	rhett.instructor_group_id = this.dbId;
	rhett.title = this.title;

	for (var key in this.users) {
		anArray.push(this.users[key].getAsHodgePodgeObject());
	}
	rhett.users = anArray;

	return rhett;
};

/**
 * @return a JSON-ified string representation of this instance; this method makes a dependency upon
 * 					scripts/third_party/json2.js
 */
InstructorGroupModel.prototype.getAsJSONString = function () {
	return JSON.stringify(this.getAsHodgePodgeObject());
};
