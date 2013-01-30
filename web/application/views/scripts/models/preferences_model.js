/**
 * This class serves as a small model containing the currently logged in user's preferences state.
 */

function PreferencesModel (serverDispatchedObject) {

    this.langLocale = 'en_US';

    this.canArchiveProgramYears = false;
    this.canArchiveCourses = false;

    this.canRolloverCourses = false;

    if (typeof serverDispatchedObject != 'undefined') {
        this.updateWithServerDispatchedObject(serverDispatchedObject);
    }

}

PreferencesModel.prototype.getLangLocale = function () {
    return this.langLocale;
};

PreferencesModel.prototype.showCourseArchiving = function () {
    return this.canArchiveCourses;
};

PreferencesModel.prototype.showProgramYearArchiving = function () {
    return this.canArchiveProgramYears;
};

PreferencesModel.prototype.showCourseRollover = function () {
    return this.canRolloverCourses;
};

PreferencesModel.prototype.updateWithServerDispatchedObject = function (serverDispatchedObject) {
    this.langLocale = serverDispatchedObject.lang;

    this.canArchiveProgramYears = (serverDispatchedObject.py_archiving == 'true');
    this.canArchiveCourses = (serverDispatchedObject.course_archiving == 'true');

    this.canRolloverCourses = (serverDispatchedObject.course_rollover == 'true');
};
