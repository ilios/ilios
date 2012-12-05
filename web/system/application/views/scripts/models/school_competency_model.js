/**
 * This serves as a simple 'object' representation of a school competency.
 * Extends <code>CompetencyModel</code>.
 *
 * Depends on:
 *    YAHOO core object.
 *    scripts/models/competency_model.js
 */

/**
 * Constructor
 * @param {String} competencyName the display title of the competency
 * @param {int} parentCompetencyId id of the parent competency, it's OK to pass in null
 * @param {int} id the database row id associated to this object
 * @param {int} schoolId the id of the school that this competency belongs to
 * @param {boolean} belongsToActiveSchool TRUE if the competency belongs to the currently "active" school
 */
function SchoolCompetencyModel (competencyName, parentCompetencyId, id, schoolId, belongsToActiveSchool) {
    SchoolCompetencyModel.superclass.constructor.call(this, competencyName, parentCompetencyId, id);
    this.activeSchool = belongsToActiveSchool;
    this.schoolId = schoolId;
};

YAHOO.lang.extend(SchoolCompetencyModel, CompetencyModel);

/**
 * Returns the competency's school id.
 * @method getSchoolId
 * @return {int} the school id
 */
SchoolCompetencyModel.prototype.getSchoolId = function () {
    return this.schoolId;
};

/**
 * Returns whether the competency belongs to the currently "active" school or not.
 * @method belongsToActiveSchool
 * @return {boolean} TRUE if the competency belongs to the currently "active" school
 */
SchoolCompetencyModel.prototype.belongsToActiveSchool = function () {
    return this.activeSchool;
};

/**
 * Clones this object.
 * @method clone
 * @return {SchoolCompetencyId} the clones object
 */
SchoolCompetencyModel.prototype.clone = function () {
    return new SchoolCompetencyModel(this.competencyTitle, this.parentId, this.dbId, this.schoolId, this.activeSchool);
};
