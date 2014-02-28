/**
 * This file contains the model repesentation of a Group in javascript land; this is used to
 *  encapsulate, and so therefore better track the dirty state of, our model.
 *
 * DEPENDENCY:
 *      abstract_js_model_form.js
 */
function UserModel (dbObject) {

    AbstractJavaScriptModelForm.apply(this, arguments);

    this.enabled = true;
    this.syncIgnored = false;

    this.iliosAuthenticationUsername = '';

    if (typeof dbObject == 'undefined') {
        this.lastName = null;
        this.firstName = null;
        this.middleName = '';

        this.phoneNumber = '';
        this.emailAddress = null;

        this.schoolId = -1;

        this.ucUID = '';
        this.otherId = '';
    } else {
        if (dbObject.user_id) {
            this.dbId = dbObject.user_id;
        }

        if (dbObject.hasOwnProperty('enabled')) {
            this.enabled = (dbObject.enabled == '1');
        }

        this.lastName = dbObject.last_name;
        this.firstName = dbObject.first_name;
        this.middleName = dbObject.middle_name;

        this.phoneNumber = (dbObject.phone != null) ? dbObject.phone : '';
        this.emailAddress = dbObject.email;

        this.schoolId = dbObject.primary_school_id;

        this.ucUID = (dbObject.uc_uid != null) ? dbObject.uc_uid : '';
        this.otherId = (dbObject.other_id != null) ? dbObject.other_id : '';
        if (dbObject.user_sync_ignore) {
            this.syncIgnored = (dbObject.user_sync_ignore == '1');
        }
    }
}

for (var aMethod in AbstractJavaScriptModelForm.prototype) {
    UserModel.prototype[aMethod] = AbstractJavaScriptModelForm.prototype[aMethod];
}

UserModel.prototype.setLastName = function (name) {
    if (this.lastName != name) {
        this.lastName = name;

        this.setDirtyAndNotify();
    }
};

UserModel.prototype.getLastName = function () {
    return this.lastName;
};

UserModel.prototype.setFirstName = function (name) {
    if (this.firstName != name) {
        this.firstName = name;

        this.setDirtyAndNotify();
    }
};

UserModel.prototype.getFirstName = function () {
    return this.firstName;
};

UserModel.prototype.setMiddleName = function (name) {
    if ((this.middleName != name) && (name != null)) {
        this.middleName = name;

        this.setDirtyAndNotify();
    }
};

UserModel.prototype.getMiddleName = function () {
    return this.middleName;
};

UserModel.prototype.setPhoneNumber = function (phone) {
    if ((this.phoneNumber != phone) && (phone != null)) {
        this.phoneNumber = phone;

        this.setDirtyAndNotify();
    }
};

UserModel.prototype.getPhoneNumber = function () {
    return this.phoneNumber;
};

UserModel.prototype.setEmailAddress = function (email) {
    if (this.emailAddress != email) {
        this.emailAddress = email;

        this.setDirtyAndNotify();
    }
};

UserModel.prototype.getEmailAddress = function () {
    return this.emailAddress;
};

UserModel.prototype.setEnabled = function (flag) {
    if (flag != this.enabled) {
        this.enabled = flag;

        this.setDirtyAndNotify();
    }
};

UserModel.prototype.isEnabled = function () {
    return this.enabled;
};

UserModel.prototype.getSchoolId = function () {
    return this.schoolId;
};

UserModel.prototype.setUCUniqueId = function (ucid) {
    if ((this.ucUID != ucid) && (ucid != null)) {
        this.ucUID = ucid;

        this.setDirtyAndNotify();
    }
};

UserModel.prototype.getUCUniqueId = function () {
    return this.ucUID;
};

UserModel.prototype.getIliosAuthenticationUsername = function () {
    return this.iliosAuthenticationUsername;
};

UserModel.prototype.setOtherId = function (oid) {
    if ((this.otherId != oid) && (oid != null)) {
        this.otherId = oid;

        this.setDirtyAndNotify();
    }
};

UserModel.prototype.getOtherId = function () {
    return this.otherId;
};

UserModel.prototype.setIgnoredFromSync = function (flag) {
    if (flag != this.syncIgnored) {
        this.syncIgnored = flag;

        this.setDirtyAndNotify();
    }
};

UserModel.prototype.isIgnoredFromSync = function () {
    return this.syncIgnored;
};

/**
 * Returns a unique value for this user, based on the user name, ucid, and dbid
 */
UserModel.prototype.getUniqueKey = function () {
    return this.lastName + '.' + this.firstName  + '.' + this.middleName + '.' + this.ucUID
                + '.' + this.dbId;
};

/*
 * @param userNameFormatType one of the following ~constants:
 *          ilios.utilities.UserNameFormatEnum.LAST_FIRST
 *          ilios.utilities.UserNameFormatEnum.FIRST_FIRST
 *          ilios.utilities.UserNameFormatEnum.FIRST_INITIAL_FIRST
 */
UserModel.prototype.getFormattedName = function (userNameFormatType) {
    return ilios.utilities.createFormattedUserName(this.firstName, this.middleName, this.lastName,
                                                   userNameFormatType);
};

UserModel.prototype.clone = function () {
    var rhett = new UserModel();

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
 *  place in the order of:
 *      last name
 *      first name
 *      middle name
 *      uc uid
 *      galen id
 *      other id
 *      email address
 *      phone number
 *      db id
 */
UserModel.prototype.compareTo = function (otherModel) {
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

/**
 * @return {Object} a representation of this instance as a hodge podge object whose properties are the User
 *     column names for the Ilios schema.
 * @todo replace with better serialization mechanism
 */
UserModel.prototype.getAsHodgePodgeObject = function () {
    var rhett = new Object();

    rhett.user_id = this.dbId;
    rhett.last_name = this.lastName;
    rhett.first_name = this.firstName;
    rhett.middle_name = this.middleName;
    rhett.phone = this.phoneNumber;
    rhett.email = this.emailAddress;
    rhett.uc_uid = this.ucUID;
    rhett.other_id = this.otherId;
    rhett.enabled = this.enabled;
    return rhett;
};
