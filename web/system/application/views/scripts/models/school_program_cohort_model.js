function SchoolProgramCohortModel (programName, cohortName, cId, pyId, cohortStartYear, owningSchoolId) {
    this.programTitle = programName;
    this.cohortTitle = cohortName;
    this.cohortId = cId;
    this.programYearId = pyId;
    this.startYear = cohortStartYear;
    this.displayText = programName + ' - ' + cohortName;
    this.owningSchoolId = owningSchoolId;
}

SchoolProgramCohortModel.prototype.getProgramTitle = function () {
    return this.programTitle;
};

SchoolProgramCohortModel.prototype.getCohortTitle = function () {
    return this.cohortTitle;
};

SchoolProgramCohortModel.prototype.getCohortId = function () {
    return this.cohortId;
};

SchoolProgramCohortModel.prototype.getProgramYearId = function () {
    return this.programYearId;
};

SchoolProgramCohortModel.prototype.getStartYear = function () {
    return this.startYear;
};

SchoolProgramCohortModel.prototype.getDisplayText = function () {
    return this.displayText;
};

SchoolProgramCohortModel.prototype.getOwningSchoolId = function () {
    return this.owningSchoolId;
};

SchoolProgramCohortModel.prototype.compareDisplays = function (otherModel) {
    return this.displayText.localeCompare(otherModel.getDisplayText());
};

SchoolProgramCohortModel.prototype.compareTo = function (otherModel) {
    return (this.cohortId - otherModel.cohortId);
};
