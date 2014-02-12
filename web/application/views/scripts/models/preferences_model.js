/**
 * This object serves as a small model containing the currently logged-in user's preferences state.
 *
 * @class PreferencesModel
 * @param {Object} [serverDispatchedObject]
 * @constructor
 */
function PreferencesModel () {

    this.programYearArchiving = false;
    this.courseArchiving = false;
    this.courseRollover = false;

    /**
     * @method updateWithServerDispatchedObject
     * @param {Object} serverDispatchedObject
     *   @param {Boolean} serverDispatchedObject.py_archiving
     *   @param {Boolean} serverDispatchedObject.course_archiving
     *   @param {Boolean} serverDispatchedObject.course_rollover
     */
    this.updateWithServerDispatchedObject = function (serverDispatchedObject) {
        this.programYearArchiving = serverDispatchedObject.py_archiving;
        this.courseArchiving = serverDispatchedObject.course_archiving;
        this.courseRollover = serverDispatchedObject.course_rollover;
    };
}
