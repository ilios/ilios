/**
 * This class serves as base-object for JavaScript-land embodiments of our model.
 *
 * Note: not all models will have a title, but most will so we'll keep it at this architectural level.
 *
 * @todo namespace models under "ilios"
 */

function AbstractJavaScriptModelForm () {

    this.dbId = -1;

    this.title = null;

    this.isDirty = false;

    this.stateChangeListeners = new Array();
    this.stateChangeListenerArguments = new Array();

}

AbstractJavaScriptModelForm.prototype.CANNOT_BE_PUBLISHED = 0;
AbstractJavaScriptModelForm.prototype.MEETS_MINIMAL_PUBLISHING_REQUIREMENTS = 1;
AbstractJavaScriptModelForm.prototype.CAN_BE_PUBLISHED = 2;

AbstractJavaScriptModelForm.prototype.setDBId = function (newId) {
    if (this.dbId != newId) {
        this.dbId = newId;

        this.setDirtyAndNotify();
    }
};

AbstractJavaScriptModelForm.prototype.getDBId = function () {
    return this.dbId;
};

AbstractJavaScriptModelForm.prototype.setTitle = function (newTitle) {
    if (this.title != newTitle) {
        this.title = newTitle;

        this.setDirtyAndNotify();
    }
};

AbstractJavaScriptModelForm.prototype.toString = function () {
    return this.title + "[" + this.dbId + "]" + (this.isDirty ? " (dirty)" : "");
};

AbstractJavaScriptModelForm.prototype.getTitle = function () {
    return this.title;
};

/**
 * Subclasses should override this as they see fit; the default assumption is that if the model
 *  item exists, it can be published (since the majority of the cases of models are little schlotzky
 *  things that don't have dependencies (like DisciplineModel, CompetencyModel, SchoolModel, etal
 *  v. ProgramYearModel))
 */
AbstractJavaScriptModelForm.prototype.getPublishability = function () {
    return AbstractJavaScriptModelForm.CAN_BE_PUBLISHED;
};

AbstractJavaScriptModelForm.prototype.isModelDirty = function () {
    return this.isDirty;
};

AbstractJavaScriptModelForm.prototype.clearDirtyState = function () {
    this.isDirty = false;

    this.notifyListeners();
};

AbstractJavaScriptModelForm.prototype.setDirtyAndNotify = function () {
    this.isDirty = true;

    this.notifyListeners();
};

//really should be considered protected
AbstractJavaScriptModelForm.prototype.notifyListeners = function () {
    var len = this.stateChangeListeners.length;

    for (var i = 0; i < len; i++) {
        this.stateChangeListeners[i].modelChanged(this, this.stateChangeListenerArguments[i]);
    }
};

/**
 * @param obj an argument to be passed to the listener as desired
 * @param listener this is expected to implement modelChanged: function (model, obj)
 */
AbstractJavaScriptModelForm.prototype.addStateChangeListener = function (listener, obj) {
    var len = this.stateChangeListeners.length;

    for (var i = 0; i < len; i++) {
        if (this.stateChangeListeners[i] == listener) {
            return;
        }
    }

    this.stateChangeListeners.push(listener);
    this.stateChangeListenerArguments.push(obj);
};

AbstractJavaScriptModelForm.prototype.dropStateChangeListeners = function () {
    this.stateChangeListeners = new Array();
    this.stateChangeListenerArguments = new Array();
};
