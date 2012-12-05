/**
 * This class serves as a small 'immutable' mash up of relevant permission display and state
 *  information
 */

function PermissionModel (dbObject) {

	this.underlyingObject = null;
    this.underlyingObjectDBId = -1;
    this.displayHTML = 'Unknown Object';
    this.title = 'Unknown';

	if (typeof dbObject == 'undefined') {
        this.dbId = -1;
		this.readPermission = false;
        this.writePermission = false;
	}
	else {
        this.dbId = dbObject.permission_id;
		this.readPermission = (dbObject.can_read == '1');
        this.writePermission = (dbObject.can_write == '1');

        if (dbObject.object != null) {
            this.underlyingObject = dbObject.object;

            this.displayHTML = dbObject.object.title;
            this.title = dbObject.object.title;

            if (this.underlyingObject.object_name == 'course') {
        		var dates
                    = ilios.management
                             .getStartAndEndDateObjectsFromPermissionCourseDBObject(
                                                                            this.underlyingObject);

                this.underlyingObjectDBId = this.underlyingObject.course_id;
                this.underlyingObject.startDateObject = dates[0];
                this.underlyingObject.endDateObject = dates[1];

                this.displayHTML += ' - ' + this.underlyingObject.year + '-'
                                    + (parseInt(this.underlyingObject.year) + 1) + ' ';

                this.displayHTML += '<span style="font-weight: normal; color: #000000;">('
                                    + dates[0].format('m/d/yy') + ' - ' + dates[1].format('m/d/yy')
                                    + ')</span>';
            }
            else if (this.underlyingObject.object_name == 'program') {
                this.underlyingObjectDBId = dbObject.object.program_id;
            }
            else if (this.underlyingObject.object_name == 'school') {
                this.underlyingObjectDBId = dbObject.object.school_id;
            }
        }
	}

}

PermissionModel.prototype.getDBId = function () {
	return this.dbId;
};

PermissionModel.prototype.getUnderlyingObjectDBId = function () {
	return this.underlyingObjectDBId;
};

PermissionModel.prototype.getUnderlyingObject = function () {
	return this.underlyingObject;
};

PermissionModel.prototype.getUnderlyingObjectName = function () {
	return this.underlyingObject.object_name;
};

// MVC-huh?
PermissionModel.prototype.getDisplayHTML = function () {
	return this.displayHTML;
};

PermissionModel.prototype.getTitle = function () {
	return this.title;
};

PermissionModel.prototype.canRead = function () {
    return this.readPermission;
};

PermissionModel.prototype.canWrite = function () {
    return this.writePermission;
};
