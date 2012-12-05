/**
 * This serves as a simple 'object' wrapper around the items we get back as a cohort-program-year
 *  aggregate; it actually serves three masters (CM, management (user accounts), and GM) - but it
 *  seems a bit needlessly obscene to break out a proper multi-subclass architecture for such
 *  a functionally toe-nail-clippings item, so this class has facets that will be used by not-all
 *  of the masters.
 */

/**
 * @param programName the display title of the program
 * @param cohortName the display title of the cohort
 * @param cId cohortId
 * @param pyId programYearId
 * @param startYear the start year of the underlying cohort
 * @param groupModel if this instance will be used as part of group management, the root group model
 *                          instance
 */
function ProgramCohortModel (programName, cohortName, cId, pyId, cohortStartYear, groupModel, activeSchool) {

    this.programTitle = programName;
    this.cohortTitle = cohortName;

    this.cohortId = cId;

    this.programYearId = pyId;

    this.startYear = cohortStartYear;

    this.group = groupModel;

    this.displayText = programName + ' - ' + cohortName;

    this.programShortTitle = '';
    this.programDuration = -1;
    this.currentEnrollment = -1;

    this.activeSchool = activeSchool ? true : false;

}

ProgramCohortModel.prototype.getProgramTitle = function () {
    return this.programTitle;
};

ProgramCohortModel.prototype.getCohortTitle = function () {
    return this.cohortTitle;
};

ProgramCohortModel.prototype.getCohortId = function () {
    return this.cohortId;
};

/**
 * Root group is not an actual group in the backend stored sense, just
 *  a convenience representation on the client side.
 */
ProgramCohortModel.prototype.getRootGroup = function () {
    return this.group;
};

ProgramCohortModel.prototype.getProgramYearId = function () {
    return this.programYearId;
};

ProgramCohortModel.prototype.setProgramShortTitle = function (title) {
    this.programShortTitle = title;
};

ProgramCohortModel.prototype.getProgramShortTitle = function () {
    return this.programShortTitle;
};

ProgramCohortModel.prototype.setProgramDuration = function (duration) {
    this.programDuration = parseInt(duration);
};

ProgramCohortModel.prototype.getProgramDuration = function () {
    return this.programDuration;
};

ProgramCohortModel.prototype.setCurrentEnrollment = function (enrollment) {
    this.currentEnrollment = parseInt(enrollment);
};

ProgramCohortModel.prototype.getCurrentEnrollment = function () {
    return this.currentEnrollment;
};

ProgramCohortModel.prototype.getStartYear = function () {
    return this.startYear;
};

ProgramCohortModel.prototype.getDisplayText = function () {
    return this.displayText;
};

ProgramCohortModel.prototype.compareDisplays = function (otherModel) {
    return this.displayText.localeCompare(otherModel.getDisplayText());
};

ProgramCohortModel.prototype.compareTo = function (otherModel) {
    return (this.cohortId - otherModel.cohortId);
};

ProgramCohortModel.prototype.belongsToActiveSchool = function () {
    return this.activeSchool;
}
