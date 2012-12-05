/**
 * This file contains the model repesentation of an Offering in javascript land; this is used to
 *  encapsulate, and so therefore better track the dirty state of, our model.
 *
 * DEPENDENCY:
 *      scripts/models/abstract_js_model_form.js
 *      scripts/models/recurring_event_model.js
 *      scripts/ilios_utilities.js
 */
function OfferingModel (dbObject) {

    AbstractJavaScriptModelForm.apply(this, arguments);

    this.readOnly = false;

    this.ilmEvent = false;

    if (typeof dbObject == 'undefined') {
        this.room = '';

        this.publishEventId = -1;

        this.sessionId = -1;
        this.sesssionTypeId = -1;

        this.startDate = null;
        this.endDate = null;

        this.displayAsTBD = false;
    }
    else {
        this.dbId = dbObject.offering_id;

        if (dbObject.is_silm == 'true') {
            var dateStr = null;

            this.ilmEvent = true;

            this.room = '';

            this.sessionTypeId = -1;

            dateStr = dbObject.due_date + ' 17:00:00';
            this.startDate = ilios.utilities.mySQLDateToDateObject(dateStr, false);

            dateStr = dbObject.due_date + ' 17:30:00';
            this.endDate = ilios.utilities.mySQLDateToDateObject(dateStr, false);

            this.readOnly = true;
        }
        else {
            this.room = dbObject.room;

            // this is clashing with the architecture, but we need this state information for
            // rendering in the calendar view :-/
            // @todo separate models for SILM from Session Offerings.
            this.sessionTypeId = dbObject.session_type_id;

            this.startDate = ilios.utilities.mySQLDateToDateObject(dbObject.start_date, true);
            this.endDate = ilios.utilities.mySQLDateToDateObject(dbObject.end_date, true);
        }

        this.publishEventId
            = ((dbObject.publish_event_id < 1) || (dbObject.publish_event_id == null))
                        ? -1
                        : dbObject.publish_event_id;

        this.sessionId = dbObject.session_id;

        this.displayAsTBD = dbObject.is_tbd;
    }

    this.offerIsRecurring = false;
    this.recurringEvent = null;

    this.owningSessionTitle = null;

    this.instructors = new Array();
    this.studentGroups = new Array();

}

for (var aMethod in AbstractJavaScriptModelForm.prototype) {
    OfferingModel.prototype[aMethod] = AbstractJavaScriptModelForm.prototype[aMethod];
}

OfferingModel.prototype.canPublish = function () {
    if ((this.room == null) || (this.title.room() == '')) {
        return false;
    }

    if (ilios.utilities.arraySize(this.instructors) == 0) {
        return false;
    }

    if (ilios.utilities.arraySize(this.studentGroups) == 0) {
        return false;
    }

    return true;
};

OfferingModel.prototype.setRecurring = function (recurringFlag) {
    if (this.offerIsRecurring != recurringFlag) {
        this.offerIsRecurring = recurringFlag;

        if (this.recurringEvent == null) {
            this.recurringEvent = new RecurringEventModel();
            this.recurringEvent.setStartDate(this.startDate);
        }

        this.setDirtyAndNotify();
    }
};

// todo - ponder whether this is obsolete given the new model of "it recurs and is saved, it is
//          forever recurring"
OfferingModel.prototype.isRecurring = function () {
    return this.offerIsRecurring;
};

OfferingModel.prototype.recurringEventIsImmutable = function () {
    if (this.recurringEvent != null) {
        return (this.recurringEvent.getDBId() != -1);
    }

    return false;
};

OfferingModel.prototype.setRecurringEvent = function (model) {
    this.recurringEvent = model;

    this.setDirtyAndNotify();
};

OfferingModel.prototype.getRecurringEvent = function () {
    return this.recurringEvent;
};

OfferingModel.prototype.shouldDisplayAsTBD = function () {
    return this.displayAsTBD;
};

/**
 * @param date must be a javascript date object
 */
OfferingModel.prototype.setStartDate = function (date) {
    if (this.startDate != date) {
        this.startDate = new Date(date.getTime());

        if (this.recurringEvent != null) {  // and this offering is recurring
            this.recurringEvent.setStartDate(this.startDate);
        }

        this.setDirtyAndNotify();
    }
};

/**
 * @return a javascript date object
 */
OfferingModel.prototype.getStartDate = function () {
    return this.startDate;
};

/**
 * @param date must be a javascript date object
 */
OfferingModel.prototype.setEndDate = function (date) {
    if (this.endDate != date) {
        this.endDate = new Date(date.getTime());

        this.setDirtyAndNotify();
    }
};

/**
 * @return a javascript date object
 */
OfferingModel.prototype.getEndDate = function () {
    return this.endDate;
};

OfferingModel.prototype.setIndependentLearningEvent = function (flag) {
    this.ilmEvent = flag;
};

OfferingModel.prototype.isIndependentLearningEvent = function () {
    return this.ilmEvent;
};

OfferingModel.prototype.setReadOnly = function (flag) {
    this.readOnly = flag;
};

OfferingModel.prototype.isReadOnly = function () {
    return this.readOnly;
};

OfferingModel.prototype.setOwningSessionTitle = function (title) {
    this.owningSessionTitle = title;
};

/**
 * If this returns non-null, this should be used as the session display title as opposed to the
 *  presently held SessionModel instance
 */
OfferingModel.prototype.getOwningSessionTitle = function () {
    return this.owningSessionTitle;
};

OfferingModel.prototype.addInstructor = function (instructor) {
    if (! this.containsInstructor(instructor)) {
        this.instructors.push(instructor);

        this.setDirtyAndNotify();
    }
};

OfferingModel.prototype.removeInstructor = function (instructor) {
    for (var key in this.instructors) {
        if (this.instructors[key] == instructor) {
            delete this.instructors[key];

            return;
        }
    }
};

/**
 * Inefficient storage meme.. TODO
 */
OfferingModel.prototype.containsInstructor = function (instructor) {
    for (var key in this.instructors) {
        if (this.instructors[key] == instructor) {
            return true;
        }
    }

    return false;
};

OfferingModel.prototype.getInstructors = function () {
    return this.instructors;
};

OfferingModel.prototype.getInstructorsAsFormattedText = function () {
    var rhett = '';
    var model = null;

    for (var key in this.instructors) {
        model = this.instructors[key];

        if (rhett.length > 0) {
            rhett += '; ';
        }

        if (model instanceof UserModel) {
            rhett += model.getFormattedName(ilios.utilities.USER_NAME_FORMAT_LAST_FIRST);
        }
        else {
            rhett += model.title;
        }
    }

    return rhett;
};

/**
 * @param studentGroup assumed to be an instance of SimplifiedGroupModel
 */
OfferingModel.prototype.addStudentGroup = function (studentGroup) {
    if (! this.containsStudentGroup(studentGroup)) {
        this.studentGroups.push(studentGroup);

        this.setDirtyAndNotify();
    }
};

OfferingModel.prototype.removeStudentGroup = function (studentGroup) {
    for (var key in this.studentGroups) {
        if (this.studentGroups[key] == studentGroup) {
            delete this.studentGroups[key];

            return;
        }
    }
};

OfferingModel.prototype.removeAllStudentGroups = function () {
    if (ilios.utilities.arraySize(this.studentGroups) > 0) {
        this.studentGroups = new Array();

        this.setDirtyAndNotify();
    }
};

/**
 * Inefficient storage meme.. TODO
 */
OfferingModel.prototype.containsStudentGroup = function (studentGroup) {
    for (var key in this.studentGroups) {
        if (this.studentGroups[key] == studentGroup) {
            return true;
        }
    }

    return false;
};

OfferingModel.prototype.getStudentGroups = function () {
    return this.studentGroups;
};

OfferingModel.prototype.getStudentGroupsAsFormattedText = function () {
    var rhett = '';

    for (var key in this.studentGroups) {
        if (rhett.length > 0) {
            rhett += '; ';
        }

        rhett += this.studentGroups[key].getGroupTitle();
    }

    return rhett;
};

OfferingModel.prototype.setRoom = function (location) {
    if (this.room != location) {
        this.room = location;

        this.setDirtyAndNotify();
    }
};

OfferingModel.prototype.getRoom = function () {
    return this.room;
};

OfferingModel.prototype.setSessionId = function (sid) {
    if (this.sessionId != sid) {
        this.sessionId = sid;

        this.setDirtyAndNotify();
    }
};

OfferingModel.prototype.getSessionId = function () {
    return this.sessionId;
};

OfferingModel.prototype.setSessionTypeId = function (stid) {
    if (this.sessionTypeId != stid) {
        this.sessionTypeId = stid;

        this.setDirtyAndNotify();
    }
};

OfferingModel.prototype.getSessionTypeId = function () {
    return this.sessionTypeId;
};

OfferingModel.prototype.setPublishEventId = function (eventId) {
    if (this.publishEventId != eventId) {
        this.publishEventId = eventId;

        this.setDirtyAndNotify();
    }
};

OfferingModel.prototype.getPublishEventId = function () {
    return this.publishEventId;
};

/*
 * This returns true if this is 'equal' to the other model for attributes that matter in the
 *  scheduler realm (start date, end date, parent session id)
 */
OfferingModel.prototype.isSchedulerEqual = function (otherModel) {
    return ((this.sessionId == otherModel.sessionId)
                && (this.startDate.getTime() == otherModel.startDate.getTime())
                && (this.endDate.getTime() == otherModel.endDate.getTime()));
};

OfferingModel.prototype.clone = function () {
    var rhett = new OfferingModel();

    rhett.dbId = this.dbId;

    rhett.room = this.room;

    rhett.publishEventId = this.publishEventId;

    rhett.sessionId = this.sessionId;
    rhett.sessionTypeId = this.sessionTypeId;

    rhett.startDate = this.startDate;
    rhett.endDate = this.endDate;

    rhett.instructors = ilios.utilities.cloneAssociativeArray(this.instructors);

    rhett.studentGroups = ilios.utilities.cloneAssociativeArray(this.studentGroups);

    rhett.offerIsRecurring = this.offerIsRecurring;
    if (this.recurringEvent != null) {
        rhett.recurringEvent = this.recurringEvent.clone();
    }

    // (should still be clean.. but just in case future coders accidentally add code that dirties
    //      above..)
    rhett.clearDirtyState();

    return rhett;
};

/**
 * Returns a standard compare, the value of which would allow natural ordering. This compares in
 *  the order of:
 *      room
 *      is recurring?
 *      if recurring - compare recurrence events
 *      session id
 *      start date
 *      end date
 *      instructors size
 *      student groups size
 *      dbId
 */
OfferingModel.prototype.compareTo = function (otherModel) {
    var temp = null;

    if (this.room != null) {
        temp = this.room.localeCompare(otherModel.room);

        if (temp != 0) {
            return temp;
        }
    }
    else if (otherModel.room != null) {
        return 1;           // arbitrary but consistent
    }

    if (this.offerIsRecurring != otherModel.offerIsRecurring) {
        return 1;           // arbitrary but consistent
    }

    if (this.offerIsRecurring) {
        temp = this.recurringEvent.compareTo(otherModel.recurringEvent);

        if (temp != 0) {
            return temp;
        }
    }

    if (this.sessionId != otherModel.sessionId) {
        return (this.sessionId - otherModel.sessionId);
    }

    if (this.startDate != otherModel.startDate) {
        return 1;           // arbitrary but consistent
    }

    if (this.endDate != otherModel.endDate) {
        return 1;           // arbitrary but consistent
    }

    if (ilios.utilities.arraySize(this.instructors)
                            != ilios.utilities.arraySize(otherModel.instructors)) {
        return 1;           // arbitrary but consistent
    }

    if (ilios.utilities.arraySize(this.studentGroups)
                            != ilios.utilities.arraySize(otherModel.studentGroups)) {
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
OfferingModel.prototype.replaceContentWithModel = function (newModel, forceReplacement) {
    if (forceReplacement || (this.compareTo(newModel) != 0)) {
        this.room = newModel.room;
        this.dbId = newModel.dbId;

        this.publishEventId = newModel.publishEventId;

        this.sessionId = newModel.sessionId;

        this.startDate = newModel.startDate;
        this.endDate = newModel.endDate;

        this.instructors = newModel.instructors;

        this.studentGroups = newModel.studentGroups;

        this.offerIsRecurring = newModel.offerIsRecurring;
        this.recurringEvent = newModel.recurringEvent;

        this.setDirtyAndNotify();
    }
};
