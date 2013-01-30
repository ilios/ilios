/**
 * This class serves as a small 'immutable' mash up of relevant course-session-offering db
 * 		information used in a user's dashboard.
 */

function CalendarItemModel (dbObject) {

	this.ilmEvent = false;

    this.displayAsTBD = false;

    this.recentlyUpdated = false;

    this.sessionTypeCssClass = '';

	if (typeof dbObject == 'undefined') {
		this.offeringId = -1;
		this.sessionId = -1;
		this.courseId = -1;

		this.startDate = null;
		this.endDate = null;

		this.sessionTitle = null;
		this.courseTitle = null;

		this.location = 'N/A';

	} else {
		this.offeringId = dbObject.offering_id;
		this.sessionId = dbObject.session_id;
		this.courseId = dbObject.course_id;

		this.startDate = ilios.utilities.mySQLDateToDateObject(dbObject.start_date, true);
		this.endDate = ilios.utilities.mySQLDateToDateObject(dbObject.end_date, true);

		this.sessionTitle = dbObject.session_title;
		this.courseTitle = dbObject.course_title;

		this.location = dbObject.room;

    	this.sessionTypeCssClass = dbObject.session_type_css_class || '';

		if (typeof dbObject.is_tbd != 'undefined') {
			this.displayAsTBD = (dbObject.is_tbd == '1');
		}
        else if (typeof dbObject.published_as_tbd != 'undefined') {
			this.displayAsTBD = (dbObject.published_as_tbd == '1');
		}

        if ((! this.displayAsTBD) && (typeof dbObject.course_published_as_tbd != 'undefined')) {
			this.displayAsTBD = (dbObject.course_published_as_tbd == '1');
		}

        if ('undefined' != typeof dbObject.recently_updated) {
        	this.recentlyUpdated = (dbObject.recently_updated == '1');
        }

	}

};

CalendarItemModel.prototype.getOfferingId = function () {
	return this.offeringId;
};

CalendarItemModel.prototype.getSessionId = function () {
	return this.sessionId;
};

CalendarItemModel.prototype.getCourseId = function () {
	return this.courseId;
};

CalendarItemModel.prototype.shouldDisplayAsTBD = function () {
    return this.displayAsTBD;
};

CalendarItemModel.prototype.getStartDate = function () {
	return this.startDate;
};

CalendarItemModel.prototype.getEndDate = function () {
	return this.endDate;
};

CalendarItemModel.prototype.getSessionTitle = function () {
	return this.sessionTitle;
};

CalendarItemModel.prototype.getCourseTitle = function () {
	return this.courseTitle;
};

CalendarItemModel.prototype.getLocation = function () {
	return this.location;
};

CalendarItemModel.prototype.getSessionTypeCssClass = function () {
    return this.sessionTypeCssClass;
}

CalendarItemModel.prototype.isIndependentLearningEvent = function () {
	return this.ilmEvent;
};

CalendarItemModel.prototype.hasBeenRecentlyUpdated = function () {
	return this.recentlyUpdated;
}

/*
 * This returns true if this is 'equal' to the other model for attributes that matter in the
 * 	scheduler realm (start date, end date, parent session id)
 */
CalendarItemModel.prototype.isSchedulerEqual = function (otherModel) {
	return ((this.sessionId == otherModel.sessionId)
                && (this.startDate.getTime() == otherModel.startDate.getTime())
                && (this.endDate.getTime() == otherModel.endDate.getTime()));
};
