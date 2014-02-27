/**
 * This file contains the model repesentation of a Group in javascript land; this is used to
 * 	encapsulate, and so therefore better track the dirty state of, our model.
 *
 * DEPENDENCIES:
 * 		scripts/models/abstract_js_model_form.js
 * 		scripts/third_party/json2.js
 */
function GroupModel (dbObject) {

	AbstractJavaScriptModelForm.apply(this, arguments);

	this.parentGroup = null;

	this.subgroups = new Array();
	this.subgroupsCount = 0;

	this.subgroupKeyContainerNumberMap = new Array();

	this.users = new Array();
	this.userCount = 0;

	this.instructors = new Array();

	if (typeof dbObject == 'undefined') {
		this.location = '';

		this.courses = new Array();
	}
	else {
		var instructor = null;
		var model = null;

		this.dbId = dbObject.group_id;
		this.title = dbObject.title;

		this.setInstructorsFromDBArray(dbObject.instructors);

		this.location = dbObject.location;

		this.courses = dbObject.courses;
	}

	this.dirtyStateListener = {

			modelChanged: function (model, mockedThis) {
				// these days, this is a NOOP
			}

	};

};

for (var aMethod in AbstractJavaScriptModelForm.prototype) {
	GroupModel.prototype[aMethod] = AbstractJavaScriptModelForm.prototype[aMethod];
};

/**
 * This instance will be unusable after this call.
 */
GroupModel.prototype.destroy = function () {
	for (var key in this.users) {
		delete this.users[key];
	}

	for (var key in this.subgroups) {
		this.subgroups[key].destroy();

		delete this.subgroups[key];
	}

	for (var key in this.subgroupKeyContainerNumberMap) {
		delete this.subgroupKeyContainerNumberMap[key];
	}
};

GroupModel.prototype.getSubgroupForIndexPath = function (indexPath) {
	var pathLength = indexPath.length;
	var parentGroupModel = this;
	var groupModel = null;
	var subgroupModels = this.getSubgroups();
	var pathValue = 0;
	var index = 0;

	while (index < pathLength) {
		pathValue = indexPath[index++];

		groupModel = parentGroupModel.getNthSubgroup(pathValue);
		if (groupModel == null) {
			break;
		}

		subgroupModels = groupModel.getSubgroups();
		parentGroupModel = groupModel;
	}

	return groupModel;
};

/*
 * Since the we might have a state in which there are 3 subgroups with indices 2, 3, 8 in the
 * 	subgroups array, we use this to get the subgroup. N is considered to be 1-based
 */
GroupModel.prototype.getNthSubgroup = function (n) {
	var counter = 1;

	for (var key in this.subgroups) {
		if (counter == n) {
			return this.subgroups[key];
		}

		counter++;
	}

	return null;
};

// since the workflow doesn't support groups without a db counter part (for example, adding a group
//		is a transaction the involves an immediate instantiation on the server side), the db id
//		should suffice as a uniquer
GroupModel.prototype.getUniqueKey = function () {
	return this.dbId;
};

/**
 * @return the value which would be used with a subgroup newly added to this instance
 */
GroupModel.prototype.getNextContainerNumber = function () {
	return (this.subgroupsCount + 1);
};

/**
 * @return the container number which should be associated with the subgroup
 */
GroupModel.prototype.addSubgroup = function (subgroup, leaveDirtyAlone) {
	var containerNumber = this.getNextContainerNumber();

	this.subgroups[containerNumber] = subgroup;
	this.subgroupsCount++;

	this.subgroupKeyContainerNumberMap[subgroup.getUniqueKey()] = containerNumber;

	subgroup.setParentGroup(this);
	subgroup.addStateChangeListener(this.dirtyStateListener, this);


	if (typeof leaveDirtyAlone == 'undefined') {
		leaveDirtyAlone = false;
	}

	if (! leaveDirtyAlone) {
		this.setDirtyAndNotify();
	}

	return containerNumber;
};

/**
 * Provided a GroupModel instance with just a correct title and group_id, return a populated,
 * 	child-ed, etc version of the GroupModel if this group has one as a subgroup.
 */
GroupModel.prototype.getSubgroupForStub = function (subgroupStub, searchTree) {
	var containerNumber = this.subgroupKeyContainerNumberMap[subgroupStub.getUniqueKey()];

	if (typeof searchTree == 'undefined') {
		searchTree = false;
	}

	if ((containerNumber == null) && searchTree) {
		var subgroup = null;
		var rhett = null;

		for (var key in this.subgroups) {
			subgroup = this.subgroups[key];

			rhett = subgroup.getSubgroupForStub(subgroupStub, true);

			if (rhett != null) {
				return rhett;
			}
		}
	}

	return this.subgroups[containerNumber];
};

/**
 * Provided a GroupModel instance with just a correct title and group_id, return a populated,
 * 	child-ed, etc version of the GroupModel if this group has one as a subgroup.
 */
GroupModel.prototype.removeSubgroupForStub = function (subgroupStub) {
	this.removeSubgroupForContainerNumber(subgroupStub.getUniqueKey());
};

GroupModel.prototype.getSubgroupForContainerNumber = function (containerNumber) {
	return this.subgroups[containerNumber];
};

/*
 * All users in this subgroup are removed from the subgroup and added to this group
 */
GroupModel.prototype.removeSubgroupForContainerNumber = function (containerNumber) {
	var lovelyJavascript = (containerNumber in this.subgroups);

	if (lovelyJavascript) {
		var subgroup = this.subgroups[containerNumber];
		var users = subgroup.getUsers();
		var user = null;

		for (var userKey in users) {
			user = users[userKey];

			subgroup.removeUser(user);

			this.addUser(user);
		}

		delete this.subgroupKeyContainerNumberMap[subgroup.getUniqueKey()];
		delete this.subgroups[containerNumber];

		this.subgroupsCount--;

		// this is an action which is paired which a server transaction and so
		//		does not merit a dirty state reflection
	}
};

GroupModel.prototype.getContainerNumberForSubgroup = function (subgroup) {
	var key = subgroup.getUniqueKey();
	var lovelyJavascript = (key in this.subgroupKeyContainerNumberMap);

	if (lovelyJavascript) {
		return this.subgroupKeyContainerNumberMap[key];
	}

	return -1;
};

GroupModel.prototype.getSubgroups = function () {
	return this.subgroups;
};

GroupModel.prototype.getSubgroupCount = function () {
	return this.subgroupsCount;
};

GroupModel.prototype.addUser = function (userModel, leaveDirtyAlone) {
	var key = userModel.getUniqueKey();
	var lovelyJavascript = (key in this.users);

	if (! lovelyJavascript) {
		var previousParent = userModel.getParentGroup();

		if (typeof leaveDirtyAlone == 'undefined') {
			leaveDirtyAlone = false;
		}

		this.users[key] = userModel;

		this.userCount++;

		userModel.setParentGroup(this, false);
		userModel.setPreviousParentGroup(previousParent);

		if (! leaveDirtyAlone) {
			this.setDirtyAndNotify();
		}
	}
};

/*
 * This walks the group tree looking to remove the user; first found user is removed. Method returns
 * 	true if the user was deleted from somewhere, false otherwise.
 */
GroupModel.prototype.removeUserFromTree = function (userModel) {
	var rhett = this.removeUser(userModel);

	if (! rhett) {
		for (var key in this.subgroups) {
			rhett = this.subgroups[key].removeUserFromTree(userModel);

			if (rhett) {
				break;
			}
		}
	}

	return rhett;
};

GroupModel.prototype.removeUser = function (userModel, nullOutCurrentParent) {
	var key = userModel.getUniqueKey();
	var lovelyJavascript = (key in this.users);

	if (typeof nullOutCurrentParent == 'undefined') {
		nullOutCurrentParent = true;
	}

	if (lovelyJavascript) {
		if (nullOutCurrentParent) {
			userModel.setParentGroup(null, false);
		}

		delete this.users[key];

		this.userCount--;

		this.setDirtyAndNotify();

		return true;
	}

	return false;
};

GroupModel.prototype.removeAllUsers = function (leaveDirtyAlone) {
	for (var key in this.users) {
		delete this.users[key];
	}

	this.userCount = 0;
	this.users = new Array();

	if (typeof leaveDirtyAlone == 'undefined') {
		leaveDirtyAlone = false;
	}

	if (! leaveDirtyAlone) {
		this.setDirtyAndNotify();
	}
};

GroupModel.prototype.getUserCount = function () {
	return this.userCount;
};

GroupModel.prototype.getUsers = function () {
	return this.users;
};

/*
 * This returns an array of all the users of this group, and all the users of its subgroups (and
 * 	all the users of their subgroups, and ad [hopefully not] infinitum).
 */
GroupModel.prototype.getAllUsers = function () {
	var rhett = this.users.concat();

	for (var key in this.subgroups) {
		rhett = rhett.concat(this.subgroups[key].getAllUsers());
	}

	return rhett;
};

/**
 * @return true if this group (not its subgroups) contains the user in question; user comparisons
 * 					are done using StudentModel's compareTo method
 */
GroupModel.prototype.containsUser = function (userModel) {
	var model = null;

	for (var key in this.users) {
		model = this.users[key];

		if (model.compareTo(userModel) == 0) {
			return true;
		}
	}

	return false;
};

/**
 * @return true if this group, or its subgroups, contains the user in question; user comparisons
 * 					are done using StudentModel's compareTo method
 */
GroupModel.prototype.treeContainsUser = function (userModel) {
	var model = null;

	if (this.containsUser(userModel)) {
		return true;
	}

	for (var key in this.subgroups) {
		model = this.subgroups[key];

		if (model.treeContainsUser(userModel)) {
			return true;
		}
	}

	return false;
};

GroupModel.prototype.setLocation = function (loc) {
	if (this.location != loc) {
		this.location = loc;

		this.setDirtyAndNotify();
	}
};

GroupModel.prototype.getLocation = function () {
	return this.location;
};

GroupModel.prototype.setAssociatedCourses = function (courseArray) {
	this.courses = courseArray.concat();
};

GroupModel.prototype.getAssociatedCoursesAsDisplayString = function () {
	return ilios.utilities.delimitedStringOfTitledObjects(this.courses, ';');
};

GroupModel.prototype.setInstructorsFromDBArray = function (dbArray) {
	var instructor = null;
	var model = null;

	for (var key in dbArray) {
		instructor = dbArray[key];

		if (instructor.instructor_group_id != null) {
			model = new Object();
			model.isGroup = true;
			model.dbId = instructor.instructor_group_id;
			model.title = instructor.title;
		}
		else {
			model = new UserModel(instructor);
			model.isGroup = false;
		}

		this.instructors.push(model);
	}
};

GroupModel.prototype.setInstructors = function (inst) {
	if (this.instructors != inst) {
		this.instructors = inst;

		this.setDirtyAndNotify();
	}
};

GroupModel.prototype.getInstructors = function () {
	return this.instructors;
};

GroupModel.prototype.getInstructorsAsFormattedText = function () {
	var rhett = '';
	var model = null;

	for (var key in this.instructors) {
		model = this.instructors[key];

		if (rhett.length > 0) {
			rhett += '; ';
		}

		if (model instanceof UserModel) {
			rhett += model.getFormattedName(ilios.utilities.UserNameFormatEnum.LAST_FIRST);
		}
		else {
			rhett += model.title;
		}
	}

	return rhett;
};

// TODO cache this value
GroupModel.prototype.getEnrollment = function () {
	var rhett = this.userCount;

	for (var key in this.subgroups) {
		rhett += this.subgroups[key].getEnrollment();
	}

	return rhett;
};

/**
 * This is the total enrollment minus the count of users in all subgroups.
 */
GroupModel.prototype.getOrphanCount = function () {
	return this.userCount;
};

GroupModel.prototype.setParentGroup = function (parent, affectDirtyState) {
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

GroupModel.prototype.getParentGroup = function () {
	return this.parentGroup;
};

/**
 * This does a shallow copy of its users and a deep copy of its groups
 */
GroupModel.prototype.clone = function () {
	var rhett = new GroupModel();

	rhett.setDBId(this.dbId);
	rhett.setTitle(this.title);

	rhett.userCount = this.userCount;
	for (var key in this.users) {
		rhett.users[key] = this.users[key];
	}

	rhett.subgroupsCount = this.subgroupsCount;
	for (var key in this.subgroups) {
		rhett.subgroups[key] = this.subgroups[key].clone();
	}

	for (var key in this.subgroupKeyContainerNumberMap) {
		rhett.subgroupKeyContainerNumberMap[key] = this.subgroupKeyContainerNumberMap[key];
	}

	rhett.clearDirtyState();

	return rhett;
};

/**
 * If the newGroupModel differs from this model, then it is replaced by the newGroupModel and this
 * 	is set dirty. Comparisons are done by values and not indentical instance.
 *
 * NOTES / REMINDERS:
 * 	. This does not replace the parentage of this instance.
 * 	. Any modifications to the users or subgroups held by the parameter after this method is called
 * 			will also change those instances as held by this instance.
 */
GroupModel.prototype.replaceContentWithModel = function (newGroupModel, forceReplacement) {
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
			model.setParentGroup(this, false);
		}

		this.subgroupsCount = newGroupModel.subgroupsCount;
		this.subgroups = new Array();

		for (var key in newGroupModel.subgroups) {
			model = newGroupModel.subgroups[key];

			this.subgroups[key] = model;
			model.setParentGroup(this, false);
		}

		this.subgroupKeyContainerNumberMap = new Array();

		for (var key in newGroupModel.subgroupKeyContainerNumberMap) {
			this.subgroupKeyContainerNumberMap[key]
			                                   	 = newGroupModel.subgroupKeyContainerNumberMap[key];
		}

		this.setDirtyAndNotify();
	}
};

/**
 * Returns a standard compare, the value of which would allow natural ordering. This compares in
 * 	the order of:
 * 		title
 * 		subgroups
 * 		users
 * 		db id
 */
GroupModel.prototype.compareTo = function (otherModel) {
	var temp = this.title.localeCompare(otherModel.title);
	var model = null;
	var flag = false;

	if (temp != 0) {
		return temp;
	}

	if (this.subgroupsCount != otherModel.subgroupsCount) {
		return 1;			// arbitrary but consistent
	}

	for (var key in this.subgroups) {
		flag = (key in otherModel.subgroups);

		if (! flag) {
			return -1;		// arbitrary but consistent
		}

		model = this.subgroups[key];

		temp = model.compareTo(otherModel.subgroups[key]);
		if (temp != 0) {
			return temp;
		}
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
GroupModel.prototype.getUserDiffFromModel = function (otherModel) {
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
 * @return {Object} an Object instance with group_id, title, location, instructors, parent_group_id
 * (if parent group is not null), users, and subgroups
 * @todo replace with better serialization mechanism
 */
GroupModel.prototype.getAsHodgePodgeObject = function () {
	var rhett = new Object();
	var anArray = new Array();

	rhett.group_id = this.dbId;
	rhett.title = this.title;
	rhett.location = this.location;
	rhett.instructors = this.instructors;

	if (this.parentGroup != null) {
		rhett.parent_group_id = this.parentGroup.getDBId();
	}

	for (var key in this.users) {
		anArray.push(this.users[key].getAsHodgePodgeObject());
	}
	rhett.users = anArray;

	anArray = new Array();
	for (var key in this.subgroups) {
		anArray.push(this.subgroups[key].getAsHodgePodgeObject());
	}
	rhett.subgroups = anArray;

	return rhett;
};

/**
 * @return a JSON-ified string representation of the entire tree rooted at this instance; this
 * 				method makes a dependency upon scripts/third_party/json2.js
 */
GroupModel.prototype.getAsJSONString = function () {
	return JSON.stringify(this.getAsHodgePodgeObject());
};
