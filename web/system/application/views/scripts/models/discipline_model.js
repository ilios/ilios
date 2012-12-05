/*
 * Discipline (AKA "Topic") data model.
 *
 * Dependencies:
 *
 * scripts/models/abstract_js_model_form.js
 */

/**
 * "Discipline" data model object.
 * @class DisciplineModel
 * @constructor
 * @extends AbstractJavaScriptModelForm
 */
function DisciplineModel (dbObject) {
    AbstractJavaScriptModelForm.apply(this, arguments);
};

for (var aMethod in AbstractJavaScriptModelForm.prototype) {
    DisciplineModel.prototype[aMethod] = AbstractJavaScriptModelForm.prototype[aMethod];
}
