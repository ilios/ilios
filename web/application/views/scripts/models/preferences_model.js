/**
 * This object serves as a small model containing the currently logged-in user's preferences state.
 *
 * @class PreferencesModel
 * @param {Object} [serverDispatchedObject]
 * @constructor
 */
function PreferencesModel () {

    /**
     * @property canArchiveProgramYears
     * @type {Boolean}
     * @default false
     */
    var canArchiveProgramYears = false;

    /**
     * @property canArchiveCourses
     * @type {Boolean}
     * @default false
     */
    var canArchiveCourses = false;

    /**
     * @property canRolloverCourses
     * @type {Boolean}
     * @default false
     */
    var canRolloverCourses = false;

    /**
     * @method showCourseArchiving
     * @return {Boolean}
     */
    this.showCourseArchiving = function () {
        return canArchiveCourses;
    };

    /**
     * @method showProgramYearArchiving
     * @return {Boolean}
     */
    this.showProgramYearArchiving = function () {
        return canArchiveProgramYears;
    };

    /**
     * @method showCourseRollover
     * @return {Boolean}
     */
    this.showCourseRollover = function () {
        return canRolloverCourses;
    };

    /**
     * @method updateWithServerDispatchedObject
     * @param {Object} serverDispatchedObject
     *   @param {Boolean} serverDispatchedObject.py_archiving
     *   @param {Boolean} serverDispatchedObject.course_archiving
     *   @param {Boolean} serverDispatchedObject.course_rollover
     */
    this.updateWithServerDispatchedObject = function (serverDispatchedObject) {
        this.canArchiveProgramYears = serverDispatchedObject.py_archiving;
        this.canArchiveCourses = serverDispatchedObject.course_archiving;
        this.canRolloverCourses = serverDispatchedObject.course_rollover;
    };
}
