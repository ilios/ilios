/**
 * Preferences settings.
 * @todo Rename privileges or something more accurate than preferences
 * Defines the following namespace
 *
 * ilios.preferences
 *
 * Dependencies:
 *
 * YUI utilities
 * scripts/ilios_base.js
 */

ilios.namespace('preferences');

/**
 * The page's preferences model.
 * Should be instantiated by calling <code>ilios.preferences.installPreferencesModel()</code>.
 * @property {Object}
 */
ilios.preferences.preferencesModel = null;

/**
 * Instantiates <code>ilios.preferences.preferencesModel</code>.
 * @todo rename with init instead of install
 * @method installPreferencesModel
 */
ilios.preferences.installPreferencesModel = function () {
    ilios.preferences.preferencesModel = {
        programYearArchiving: false,
        courseArchiving: false,
        courseRollover: false,

        /**
         * @method updateWithServerDispatchedObject
         * @param {Object} serverDispatchedObject
         *   @param {Boolean} serverDispatchedObject.py_archiving
         *   @param {Boolean} serverDispatchedObject.course_archiving
         *   @param {Boolean} serverDispatchedObject.course_rollover
         */
        updateWithServerDispatchedObject: function (serverDispatchedObject) {
            this.programYearArchiving = serverDispatchedObject.py_archiving;
            this.courseArchiving = serverDispatchedObject.course_archiving;
            this.courseRollover = serverDispatchedObject.course_rollover;
        }
    };

    var domData = document.getElementById("iliosPreferencesModel");
    if (domData) {
        try {
            ilios.preferences.preferencesModel.updateWithServerDispatchedObject(JSON.parse(domData.innerHTML));
        } catch (e) {
            // SOL
            ilios.global.defaultAJAXFailureHandler(null, e);
        }
    }
};
