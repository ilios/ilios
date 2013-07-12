/**
 * This file contains the model representation of a user-made Reminder in javascript land.
 *
 * DEPENDENCY:
 *         scripts/models/abstract_js_model_form.js
 */
function ReminderModel (dbObject) {

    AbstractJavaScriptModelForm.apply(this, arguments);

    if (typeof dbObject == 'undefined') {
        this.title = '';
        this.creationDate = new Date();
        this.dueDate = null;
        this.closed = false;
    }
    else {
        this.dbId = dbObject.user_made_reminder_id;
        this.title = dbObject.note;
        this.creationDate = ilios.utilities.mySQLDateToDateObject(dbObject.creation_date, true);
        this.dueDate = ilios.utilities.mySQLDateToDateObject(dbObject.due_date, true);
        this.closed = (dbObject.closed == '1');
    }

    this.dirtyStateListener = {

            modelChanged: function (model, mockedThis) {
                // presently there is no functionality associated to this
            }

    };

};

for (var aMethod in AbstractJavaScriptModelForm.prototype) {
    ReminderModel.prototype[aMethod] = AbstractJavaScriptModelForm.prototype[aMethod];
};

ReminderModel.prototype.getNote = function () {
    return this.getTitle();
};

ReminderModel.prototype.setNote = function (noteText) {
    this.setTitle(noteText);
};

ReminderModel.prototype.isOverdue = function () {
    return this.dueDate.getTime() < (new Date()).getTime();
};

ReminderModel.prototype.getDueDate = function () {
    return this.dueDate;
};

ReminderModel.prototype.setDueDate = function (date) {
    if (this.dueDate != date) {
        this.dueDate = date;

        this.setDirtyAndNotify();
    }
};

ReminderModel.prototype.getCreationDate = function () {
    return this.creationDate;
};

ReminderModel.prototype.isClosed = function () {
    return this.closed;
};

ReminderModel.prototype.setClosed = function (flag) {
    if (this.closed != flag) {
        this.closed = flag;

        this.setDirtyAndNotify();
    }
};

ReminderModel.prototype.toggleCloseState = function () {
    this.setClosed(! this.closed);
};

/**
 * Returns a standard compare, the value of which would allow natural ordering. This compares in the order of:
 *     note
 *     due date
 *     closed
 *     dbId
 */
ReminderModel.prototype.compareTo = function (otherModel) {
    var temp = null;

    if (this.title != null) {
        temp = this.title.localeCompare(otherModel.title);

        if (temp != 0) {
            return temp;
        }
    }
    else if (otherModel.title != null) {
        return 1;            // arbitrary but consistent
    }

    if (this.dueDate != otherModel.dueDate) {
        return this.dueDate.getTime() - otherModel.dueDate.getTime();
    }

    if (this.closed != otherModel.closed) {
        return 1;            // arbitrary but consistent
    }

    return (this.dbId - otherModel.dbId);
};

ReminderModel.prototype.clone = function () {
    var rhett = new ReminderModel();

    rhett.dbId = this.dbId;
    rhett.title = this.title;
    rhett.creationDate = this.creationDate;
    rhett.dueDate = this.dueDate;
    rhett.closed = this.closed;

    return rhett;
};

/**
 * If the newModel differs from this model, then it is replaced by the newModel and this is set
 * dirty. Comparisons are done by values and not identical instance.
 */
ReminderModel.prototype.replaceContentWithModel = function (newModel, forceReplacement) {
    if (forceReplacement || (this.compareTo(newModel) != 0)) {
        this.title = newModel.title;
        this.dbId = newModel.dbId;

        this.creationDate = newModel.creationDate;
        this.dueDate = newModel.dueDate;

        this.closed = newModel.closed;

        this.setDirtyAndNotify();
    }
};

