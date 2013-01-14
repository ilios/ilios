/**
 * This file contains the model repesentation of a user-defined Report in javascript land.
 *
 * DEPENDENCY:
 * 		scripts/models/abstract_js_model_form.js
 */
function ReportModel (dbObject) {

	AbstractJavaScriptModelForm.apply(this, arguments);

	this.prepositionalObject = null;
	this.prepositionalObjectValues = null;
    this.prepositionalObjectDisplayValues = null;

	this.requiresPrepositionalObject = false;
    this.title = null;

	if (typeof dbObject == 'undefined') {
		this.subject = '';
	}
	else {
		this.dbId = dbObject.report_id;
		this.subject = dbObject.subject;

		if ((typeof dbObject.prepositional_object != 'undefined')
				&& (dbObject.prepositional_object != null)
				&& (dbObject.prepositional_object.length > 0)) {
			this.prepositionalObject = dbObject.prepositional_object;
		}

		if (this.prepositionalObject != null) {
			this.prepositionalObjectValues = new Array();
            this.prepositionalObjectDisplayValues = new Array();

			for (var key in dbObject.po_values) {
				this.prepositionalObjectValues.push(dbObject.po_values[key]);
                this.prepositionalObjectDisplayValues.push(dbObject.po_display_values[key]);

			}
		}

        if ((typeof dbObject.title != 'undefined')
            && (dbObject.title != null)
            && (dbObject.title.length > 0)) {
            this.title = dbObject.title;
        }
	}

	this.dirtyStateListener = {

			modelChanged: function (model, mockedThis) {
				// presently there is no functionality associated to this
			}

	};

};

for (var aMethod in AbstractJavaScriptModelForm.prototype) {
	ReportModel.prototype[aMethod] = AbstractJavaScriptModelForm.prototype[aMethod];
};

ReportModel.prototype.getTitle = function() {
    return this.title;
}

ReportModel.prototype.setTitle = function(title) {
    this.title = title;
}

ReportModel.prototype.getSubject = function () {
	return this.subject;
};

ReportModel.prototype.setSubject = function (subject) {
	this.subject = subject;

	this.prepositionalObject = null;
	this.prepositionalObjectValues = null;
}

ReportModel.prototype.getPrepositionalObject = function () {
	return this.prepositionalObject;
};

ReportModel.prototype.setPrepositionalObject = function (po) {
	this.prepositionalObject = po;
};

ReportModel.prototype.mustHavePrepositionalObject = function () {
	return this.requiresPrepositionalObject;
};

ReportModel.prototype.setMustHavePrepositionalObject = function (flag) {
	this.requiresPrepositionalObject = flag;
};

ReportModel.prototype.hasPrepositionalObject = function () {
	return ((this.prepositionalObject != null) && (this.prepositionalObjectValues != null)
					&& (this.prepositionalObjectValues.length > 0));
};

ReportModel.prototype.getPrepositionalObjectValues = function () {
	return this.prepositionalObjectValues;
};

/*
 * @param poValues expected to be a non-associative array of db ids
 */
ReportModel.prototype.setPrepositionalObjectValues = function (poValues) {
	this.prepositionalObjectValues = poValues.concat();
};

ReportModel.prototype.getPrepositionalObjectDisplayValues = function () {
    return this.prepositionalObjectDisplayValues;
};
