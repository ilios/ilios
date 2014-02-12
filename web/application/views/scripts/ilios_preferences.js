/**
 * The page's preferences model.
 * Should be instantiated by calling <code>ilios.global.installPreferencesModel()</code>.
 * @property {PreferencesModel} preferencesModel
 */
ilios.global.preferencesModel = null;

/**
 * Instantiates <code>ilios.global.preferencesModel</code>.
 * @method installPreferencesModel
 */
ilios.global.installPreferencesModel = function () {
    ilios.global.preferencesModel = {
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
            ilios.global.preferencesModel.updateWithServerDispatchedObject(JSON.parse(domData.innerHTML));
        } catch (e) {
            // SOL
            ilios.global.defaultAJAXFailureHandler(null, e);
        }
    }
};
