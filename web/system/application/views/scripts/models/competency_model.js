/**
 * This serves as a simple 'object' representation of a competency.
 */

/**
 * @param competencyName the display title of the competency
 * @param parentCompetencyId id of the parent competency, it's OK to pass in null
 * @param id the database row id associated to this object
 */
function CompetencyModel (competencyName, parentCompetencyId, id) {

    this.competencyTitle = competencyName;

    this.parentId = (parentCompetencyId != null) ? parentCompetencyId : -1;

    this.dbId = id;

}

CompetencyModel.prototype.getCompetencyTitle = function () {
    return this.competencyTitle;
};

CompetencyModel.prototype.getParentCompetencyId = function () {
    return this.parentId;
};

CompetencyModel.prototype.getDBId = function () {
    return this.dbId;
};

CompetencyModel.prototype.isSubDomain = function () {
    return (this.parentId != -1);
};

/**
 * Returns a standard compare, the value of which would allow natural ordering. This compares in
 *  the order of:
 *      competencyTitle
 *      parentId
 *      db id
 */
CompetencyModel.prototype.compareTo = function (otherModel) {
    var temp = this.competencyTitle.localeCompare(otherModel.competencyTitle);

    if (temp != 0) {
        return temp;
    }

    if (this.parentId != otherModel.parentId) {
        return this.parentId - otherModel.parentId;
    }

    return this.dbId - otherModel.dbId;
};

CompetencyModel.prototype.clone = function () {
    return new CompetencyModel(this.competencyTitle, this.parentId, this.dbId);
};
