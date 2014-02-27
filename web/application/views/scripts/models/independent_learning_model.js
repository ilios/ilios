/**
 * This file contains the model repesentation of the optional independent learning facet of a
 *  session, in javascript land; this is used to encapsulate, and so therefore better track the
 *  dirty state of, our model.
 *
 * DEPENDENCY:
 *      scripts/third_party/md5-min.js
 *      scripts/abstract_js_model_form.js
 *      scripts/ilios_utilities.js
 */
function IndependentLearningModel (dbObject) {

    AbstractJavaScriptModelForm.apply(this, arguments);

    this.title = '';

    this.learnerGroups = new Array();
    this.instructors = new Array();

    this.hours = -1;

    this.dueDate = null;

    if (typeof dbObject != 'undefined') {
        var len = 0;
        var i = 0;
        var dbModel = null;
        var model = null;

        this.dbId = dbObject.ilm_session_facet_id;

        this.hours = dbObject.hours;

        this.dueDate = ilios.utilities.mySQLTimelessDateToDateObject(dbObject.due_date);

        i = 0;
        len = dbObject.instructors.length;
        for (; i < len; i++) {
            dbModel = dbObject.instructors[i];

            if (dbModel.instructor_group_id != null) {
                model = new Object();
                model.isGroup = true;
                model.dbId = dbModel.instructor_group_id;
                model.title = dbModel.title;
            }
            else {
                model = new UserModel(dbModel);
                model.isGroup = false;
            }

            this.instructors.push(model);
        }

        i = 0;
        len = dbObject.learners.length;
        for (; i < len; i++) {
            dbModel = dbObject.learners[i];

	    if (null === dbModel) continue;

            model = new SimplifiedGroupModel(dbModel.title,
                                             ((dbModel.parent_group_id == null)
                                                     ? -1
                                                     : dbModel.parent_group_id),
                                             dbModel.group_id);

            this.learnerGroups.push(model);
        }
    }

    this.dirtyStateListener = {

            modelChanged: function (model, mockedThis) {
                // presently there is no functionality associated to this
            }

    };

}

for (var aMethod in AbstractJavaScriptModelForm.prototype) {
    IndependentLearningModel.prototype[aMethod] = AbstractJavaScriptModelForm.prototype[aMethod];
}

IndependentLearningModel.prototype.setInstructors = function (instructorArray) {
    if (instructorArray == null) {
        this.instructors = new Array();
    }
    else {
        this.instructors = instructorArray.concat();
    }
};

IndependentLearningModel.prototype.addInstructor = function (anInstructor) {
    if (! this.containsInstructor(anInstructor)) {
        this.instructors.push(anInstructor);

        this.setDirtyAndNotify();
    }
};

IndependentLearningModel.prototype.removeInstructor = function (anInstructor) {
    for (var key in this.instructors) {
        if (this.instructors[key] == anInstructor) {
            delete this.instructors[key];

            return;
        }
    }
};

/**
 * Inefficient storage meme.. TODO
 */
IndependentLearningModel.prototype.containsInstructor = function (anInstructor) {
    for (var key in this.instructors) {
        if (this.instructors[key] == anInstructor) {
            return true;
        }
    }

    return false;
};

IndependentLearningModel.prototype.getInstructors = function () {
    return this.instructors;
};

IndependentLearningModel.prototype.getInstructorsAsFormattedText = function () {
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

IndependentLearningModel.prototype.setLearnerGroups = function (groups) {
    if (groups == null) {
        this.learnerGroups = new Array();
    }
    else {
        this.learnerGroups = groups.concat();
    }
};

IndependentLearningModel.prototype.getLearnerGroups = function () {
    return this.learnerGroups;
};

IndependentLearningModel.prototype.setHours = function (totalHours) {
    if (this.hours != totalHours) {
        this.hours = totalHours;

        this.setDirtyAndNotify();
    }
};

IndependentLearningModel.prototype.getHours = function () {
    return this.hours;
};

IndependentLearningModel.prototype.setDueDate = function (date) {
    if (this.dueDate != date) {
        if (date == null) {
            this.dueDate = null;
        }
        else {
            this.dueDate = new Date(date.getTime());
        }

        this.setDirtyAndNotify();
    }
};

IndependentLearningModel.prototype.getDueDate = function () {
    return this.dueDate;
};

IndependentLearningModel.prototype.clone = function () {
    var rhett = new IndependentLearningModel();

    rhett.dbId = this.dbId;

    rhett.hours = this.hours;

    rhett.dueDate = (this.dueDate != null) ? new Date(this.dueDate.getTime()) : null;

    rhett.instructors = ilios.utilities.cloneAssociativeArray(this.instructors);

    rhett.learnerGroups = ilios.utilities.cloneAssociativeArray(this.learnerGroups);

    // (should still be clean.. but just in case future coders accidentally add code that dirties
    //      above..)
    rhett.clearDirtyState();

    return rhett;
};
