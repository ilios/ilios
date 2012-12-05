/**
 * This file contains the model repesentation of a Recurring Event in javascript land; this is used
 *  to encapsulate, and so therefore better track the dirty state of, our model.
 *
 * DEPENDENCY:
 *      scripts/abstract_js_model_form.js
 *      scripts/ilios_utilities.js
 */
function RecurringEventModel (dbObject) {

    AbstractJavaScriptModelForm.apply(this, arguments);

    this.eventDays = new Array(0, 0, 0, 0, 0, 0, 0);

    this.cachedEventDays = null;

    this.repetitionCount = 1;

    this.startDate = null;

    this.endDateSetExplicitly = false;
    this.endDate = null;
    this.mysqldEndDate = null;

    if (typeof dbObject != 'undefined') {
        this.dbId = dbObject.recurring_event_id;

        if (dbObject.repetition_count == 0) {
            this.endDateSetExplicitly = true;

            this.endDate = ilios.utilities.mySQLDateToDateObject(dbObject.end_date, true);
            this.mysqldEndDate = ilios.utilities.dateObjectToMySQLFriendly(this.endDate, true);
        }
        else {
            this.repetitionCount = parseInt(dbObject.repetition_count);
        }

        this.eventDays[0] = parseInt(dbObject.on_sunday);
        this.eventDays[1] = parseInt(dbObject.on_monday);
        this.eventDays[2] = parseInt(dbObject.on_tuesday);
        this.eventDays[3] = parseInt(dbObject.on_wednesday);
        this.eventDays[4] = parseInt(dbObject.on_thursday);
        this.eventDays[5] = parseInt(dbObject.on_friday);
        this.eventDays[6] = parseInt(dbObject.on_saturday);
    }

    this.dirtyStateListener = {

            modelChanged: function (model, mockedThis) {
                // presently there is no functionality associated to this
            }

    };

}

for (var aMethod in AbstractJavaScriptModelForm.prototype) {
    RecurringEventModel.prototype[aMethod] = AbstractJavaScriptModelForm.prototype[aMethod];
}

RecurringEventModel.prototype.eventDaysAsArray = function () {
    if (this.cachedEventDays == null) {
        this.cachedEventDays = new Array();

        for (var i = 0; i < 7; i++) {
            if (this.eventDays[i] == 1) {
                this.cachedEventDays.push(i);
            }
        }
    }

    return this.cachedEventDays;
};

RecurringEventModel.prototype.clearEventDays = function () {
    for (var i = 0; i < 7; i++) {
        this.eventDays[i] = 0;
    }

    this.cachedEventDays = null;
};

/**
 * The returned data structure should not be modified.
 */
RecurringEventModel.prototype.getRawEventDays = function () {
    return this.eventDays;
};

/**
 * @param dayValue 0-6 usual day of the week mapping
 * @param occurring boolean; if true then there is an event occurring on this day, otherwise not
 */
RecurringEventModel.prototype.setDayToState = function (dayValue, occurring) {
    var newValue = occurring ? 1 : 0;

    if (this.eventDays[dayValue] != newValue) {
        this.cachedEventDays = null;

        this.eventDays[dayValue] = newValue;
    }
};

RecurringEventModel.prototype.getStartDate = function () {
    return this.startDate;
};

/**
 * Calling this will void the end date unless it has been set explicitly via setEndDate. It will
 *  also clear out event days and set it to the only event day being the day of week represented
 *  by the day of the argument. Nothing happens if the startDate is already equal to the argument
 *  date.
 */
RecurringEventModel.prototype.setStartDate = function (newDate) {
    if (this.startDate != newDate) {
        this.startDate = newDate;

        if (! this.endDateSetExplicitly) {
            this.endDate = null;
        }

        // If the new date isn't one of our recurring days of the week, we said destroy!
        if (this.eventDays[newDate.getDay()] == 0) {
            this.clearEventDays();

            this.setDayToState(newDate.getDay(), true);
        }

        this.setDirtyAndNotify();
    }
};

/**
 * Calling this will void the previously set end date and that date will be calculated next
 *  time it is asked for
 */
RecurringEventModel.prototype.setRepetitionCount = function (count) {
    if (this.repetitionCount != count) {
        this.repetitionCount = count;

        this.endDateSetExplicitly = false;
        this.endDate = null;

        this.setDirtyAndNotify();
    }
};

RecurringEventModel.prototype.getRepetitionCount = function () {
    return this.repetitionCount;
};

RecurringEventModel.prototype.setEndDate = function (newDate) {
    if (this.endDate != newDate) {
        this.endDate = newDate;

        this.mysqldEndDate = ilios.utilities.dateObjectToMySQLFriendly(newDate, true);

        this.endDateSetExplicitly = true;

        this.setDirtyAndNotify();
    }
};

RecurringEventModel.prototype.getEndDate = function () {
    if (this.endDate == null) {
        var date = this.startDate.getUTCDate();
        var firstDay = -1;
        var lastDay = -1;

        /*
         * LOGIC FOR END DATE:
         *      * if it runs for X weeks, it ends with the last day of the week pattern (X-1) calendar
         *              weeks from now. So MWF class has first day on a W, runs for three weeks has
         *              this as its schedule:
         *                              M   W   F
         *                              x   o   o
         *                              o   o   o
         *                              o   o   o
         */

        date += ((this.repetitionCount - 1) * 7);

        for (var i = 0; i < 7; i++) {
            if (this.eventDays[i] == 1) {
                if (firstDay == -1) {
                    firstDay = i;
                }

                lastDay = i;
            }
        }

        date += (lastDay - firstDay);

        this.endDate = new Date();
        this.endDate.setUTCFullYear(this.startDate.getUTCFullYear(), this.startDate.getUTCMonth(),
                                    this.startDate.getUTCDate());
        // i do this in case the javascript implementation of setUTCFullYear is buggy and requires
        //      the year and month previously set correctly before handling an out-of-bounds date
        //      value
        this.endDate.setUTCDate(date);
    }

    return this.endDate;
};

RecurringEventModel.prototype.generateMySQLEndDate = function () {
    this.getEndDate();

    this.mysqldEndDate = ilios.utilities.dateObjectToMySQLFriendly(this.endDate, true);
};

RecurringEventModel.prototype.endDateWasExplicitlySet = function () {
    return this.endDateSetExplicitly;
};

/**
 * Returns a standard compare, the value of which would allow natural ordering. This compares in
 *  the order of:
 *      startDate
 *      endDate
 *      eventDays values
 *      dbId
 */
RecurringEventModel.prototype.compareTo = function (otherModel) {
    if (this.startDate != otherModel.startDate) {
        return 1;
    }

    if (this.getEndDate() != otherModel.getEndDate()) {
        return 1;
    }

    for (var i = 0; i < 7; i++) {
        if (this.eventDays[i] != otherModel.eventDays[i]) {
            return 1;
        }
    }

    return (this.dbId - otherModel.dbId);
};

RecurringEventModel.prototype.clone = function () {
    var rhett = new RecurringEventModel();

    rhett.dbId = this.dbId;
    rhett.endDateSetExplicitly = this.endDateSetExplicitly;
    rhett.endDate = this.endDate;
    rhett.startDate = this.startDate;
    rhett.repetitionCount = this.repetitionCount;
    rhett.eventDays = this.eventDays;

    return rhett;
};
