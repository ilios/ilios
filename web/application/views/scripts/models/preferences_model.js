/**
 * This object serves as a small model containing the currently logged-in user's preferences state.
 *
 * @class PreferencesModel
 * @param {Object} [serverDispatchedObject]
 * @constructor
 */
function PreferencesModel (serverDispatchedObject) {

    /**
     * @property canArchiveProgramYears
     * @type {Boolean}
     * @default false
     */
    this.canArchiveProgramYears = false;

    /**
     * @property canArchiveCourses
     * @type {Boolean}
     * @default false
     */
    this.canArchiveCourses = false;

    /**
     * @property canRolloverCourses
     * @type {Boolean}
     * @default false
     */
    this.canRolloverCourses = false;

    if (typeof serverDispatchedObject !== 'undefined') {
        this.updateWithServerDispatchedObject(serverDispatchedObject);
    }
}
/**
 * @method showCourseArchiving
 * @return {Boolean}
 */
PreferencesModel.prototype.showCourseArchiving = function () {
    return this.canArchiveCourses;
};

/**
 * @method showProgramYearArchiving
 * @return {Boolean}
 */
PreferencesModel.prototype.showProgramYearArchiving = function () {
    return this.canArchiveProgramYears;
};

/**
 * @method showCourseRollover
 * @return {Boolean}
 */
PreferencesModel.prototype.showCourseRollover = function () {
    return this.canRolloverCourses;
};

/**
 * @method updateWithServerDispatchedObject
 * @param {Object} serverDispatchedObject
 *   @param {Boolean} serverDispatchedObject.py_archiving
 *   @param {Boolean} serverDispatchedObject.course_archiving
 *   @param {Boolean} serverDispatchedObject.course_rollover
 */
PreferencesModel.prototype.updateWithServerDispatchedObject = function (serverDispatchedObject) {
    this.canArchiveProgramYears = serverDispatchedObject.py_archiving;
    this.canArchiveCourses = serverDispatchedObject.course_archiving;
    this.canRolloverCourses = serverDispatchedObject.course_rollover;
};
