/**
 * This file contains the model repesentation of a Objective in javascript land; this is used to
 *  encapsulate, and so therefore better track the dirty state of, our model.
 *
 * There's a close-but-not-exact hierarchy able to be derived:
 *                      ObjectiveModel <- SessionModel <- (course/)CourseModel
 *  but there's extraneous gunk in each parent (MD5 caching & description posing in OM; session type
 *      id in SM) which are not needed by their children...
 *
 * DEPENDENCY:
 *      scripts/third_party/md5-min.js
 *      scripts/abstract_js_model_form.js
 *      scripts/ilios_utilities.js
 */
function ObjectiveModel (dbObject) {

    AbstractJavaScriptModelForm.apply(this, arguments);

    this.meshTerms = [];
    this.cachedMD5 = null;

    this.parentObjectives = [];

    this.competencyId = -1;

    if (typeof dbObject != 'undefined') {
        var key = null;

        this.dbId = dbObject.objective_id;
        this.title = dbObject.title;
        this.cachedMD5 = this.getTextMD5();

        for (key in dbObject.mesh_terms) {
            this.meshTerms.push(new MeSHItemModel(dbObject.mesh_terms[key]));
        }

        if (dbObject.parent_objectives != null) {
            for (key in dbObject.parent_objectives) {
                this.parentObjectives.push(dbObject.parent_objectives[key]);
            }
        }

        if (dbObject.competency_id != null) {
            this.competencyId = dbObject.competency_id;
        }
    }
    else {
        this.title = '';
    }

    this.dirtyStateListener = {

            modelChanged: function (model, mockedThis) {
                // presently there is no functionality associated to this
            }

    };

}

for (var aMethod in AbstractJavaScriptModelForm.prototype) {
    ObjectiveModel.prototype[aMethod] = AbstractJavaScriptModelForm.prototype[aMethod];
}

ObjectiveModel.prototype.addMeSHItem = function (meshItem) {
    this.meshTerms.push(meshItem);
};

ObjectiveModel.prototype.removeMeSHItem = function (meshItem) {
    var i = 0,
        n = this.meshTerms.length;

    for (; i < n; i++) {
        if (0 == meshItem.compareTo(this.meshTerms[i])) {
            this.meshTerms.splice(i, 1);
            return;
        }
    }
};

ObjectiveModel.prototype.containsMeSHItem = function (meshItem) {
    for (var key in this.meshTerms) {
        if (meshItem.compareTo(this.meshTerms[key]) == 0) {
            return true;
        }
    }

    return false;
};

/**
 * @return a non-associative array of MeSHItemModel instances
 */
ObjectiveModel.prototype.getMeSHItems = function () {
    return this.meshTerms;
};

ObjectiveModel.prototype.getMeSHItemsAsFormattedText = function () {
    return ilios.utilities.delimitedStringOfTitledObjects(this.meshTerms, ';');
};

ObjectiveModel.prototype.getTextMD5 = function () {
    if (this.cachedMD5 == null) {
        this.cachedMD5 = hex_md5(this.title);
    }

    return this.cachedMD5;
};

ObjectiveModel.prototype.setCompetencyId = function (anId) {
    if (this.competencyId != anId) {
        this.competencyId = anId;

        this.setDirtyAndNotify();
    }
};

ObjectiveModel.prototype.getCompetencyId = function () {
    return this.competencyId;
};

ObjectiveModel.prototype.setDescription = function (description) {
    this.setTitle(description);

    this.cachedMD5 = this.getTextMD5();
};

ObjectiveModel.prototype.getDescription = function () {
    return this.getTitle();
};

ObjectiveModel.prototype.setParentObjectives = function (objectives) {
    if (! ilios.utilities.simplyArrayEquality(objectives, this.parentObjectives)) {
        this.parentObjectives = objectives.concat();

        this.setDirtyAndNotify();
    }
};

ObjectiveModel.prototype.getParentObjectives = function () {
    return this.parentObjectives;
};

ObjectiveModel.prototype.clone = function () {
    var rhett = new ObjectiveModel();

    rhett.replaceContentWithModel(this, true);

    rhett.clearDirtyState();

    return rhett;
};

/**
 * Returns a standard compare, the value of which would allow natural ordering. This compares in
 *  the order of:
 *      title
 *      competency id
 *      mesh size
 *      dbId
 */
ObjectiveModel.prototype.compareTo = function (otherModel) {
    var temp = null;

    if (this.title != null) {
        temp = this.title.localeCompare(otherModel.title);

        if (temp != 0) {
            return temp;
        }
    }
    else if (otherModel.title != null) {
        return 1;           // arbitrary but consistent
    }

    if (this.competencyId != otherModel.competencyId) {
        return 1;           // arbitrary but consistent
    }

    // TODO better comparison needed
    if (ilios.utilities.arraySize(this.meshTerms)
                            != ilios.utilities.arraySize(otherModel.meshTerms)) {
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
ObjectiveModel.prototype.replaceContentWithModel = function (newModel, forceReplacement) {
    if (forceReplacement || (this.compareTo(newModel) != 0)) {
        this.title = newModel.title;
        this.dbId = newModel.dbId;

        this.cachedMD5 = this.getTextMD5();

        this.meshTerms = newModel.meshTerms.concat();

        this.competencyId = newModel.competencyId;

        this.setDirtyAndNotify();
    }
};
