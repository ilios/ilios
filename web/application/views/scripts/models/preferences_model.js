/**
 * This object serves as a small model containing the currently logged-in user's preferences state.
 *
 * @class PreferencesModel
 * @param {Object} [serverDispatchedObject]
 * @constructor
 */
function PreferencesModel () {

    var prefs = {
        py_archiving: false,
        course_archiving: false,
        course_rollover: false
    };

    /**
     * @method showCourseArchiving
     * @return {Boolean}
     */
    this.showCourseArchiving = function () {
        return prefs.course_archiving;
    };

    /**
     * @method showProgramYearArchiving
     * @return {Boolean}
     */
    this.showProgramYearArchiving = function () {
        return prefs.py_archiving;
    };

    /**
     * @method showCourseRollover
     * @return {Boolean}
     */
    this.showCourseRollover = function () {
        return prefs.course_rollover;
    };

    /**
     * @method updateWithServerDispatchedObject
     * @param {Object} serverDispatchedObject
     *   @param {Boolean} serverDispatchedObject.py_archiving
     *   @param {Boolean} serverDispatchedObject.course_archiving
     *   @param {Boolean} serverDispatchedObject.course_rollover
     */
    this.updateWithServerDispatchedObject = function (serverDispatchedObject) {
        prefs.py_archiving = serverDispatchedObject.py_archiving;
        prefs.course_archiving = serverDispatchedObject.course_archiving;
        prefs.course_rollover = serverDispatchedObject.course_rollover;
    };
}
