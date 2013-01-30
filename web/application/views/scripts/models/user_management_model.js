/**
 * This object represents a representation of a user record suitable
 * for user-account management tasks, such as cohort assignment.
 * Extends <code>UserModel</code>.
 *
 * Depends on:
 *    YAHOO core object.
 *    scripts/models/user_model.js
 *    scripts/models/school_program_cohort_model.js
 *
 * @todo add user-role properties
 */

/**
 * User-account management model.
 * @class UserManagementModel
 * @extends UserModel
 * @constuctor
 */

/**
 * The user's primary cohort.
 * @property primaryCohort
 * @type SchoolProgramCohortModel
 */

/**
 * A collection of secondary user cohorts, implemented as array of {SchoolProgramCohortModel} objects
 * @property secondaryCohort
 * @type Array
 */

/**
 * The login username.
 * @property loginName
 * @type String | Boolean
 * @default false
 */

/**
 * Constructor
 * @param {Object} dbObject a data transfer object containing the standard user properties.
 */
function UserManagementModel (dbObject) {
    UserManagementModel.superclass.constructor.call(this, dbObject);
    this.primaryCohort = false;
    this.secondaryCohorts = [];
    this.loginName = false;
};

YAHOO.lang.extend(UserManagementModel, UserModel);

/**
 * Sets the primary cohort.
 * @method setPrimaryCohort
 * @param {SchoolProgramCohortModel} cohort The primary cohort
 */
UserManagementModel.prototype.setPrimaryCohort = function (cohort) {
    this.primaryCohort = cohort;
};

/**
 * Gets the primary cohort.
 * @method getPrimaryCohort
 * @return {SchoolProgramCohortModel | boolean} the primary cohort object, or FALSE if non-existent
 */
UserManagementModel.prototype.getPrimaryCohort = function () {
    return this.primaryCohort;
};

/**
 * Sets the login username.
 * @param {String | boolean} loginName
 */
UserManagementModel.prototype.setLoginName = function (loginName) {
    return this.loginName= loginName;
};

/**
 * Gets the login username.
 * @method getPrimaryCohort
 * @return {String | boolean} the login name, or FALSE if non-existent.
 */
UserManagementModel.prototype.getLoginName = function () {
    return this.loginName;
};

/**
 * Sets the secondary cohorts.
 * @method setSecondaryCohorts
 * @param {Array} cohorts A list of secondary cohorts
 */
UserManagementModel.prototype.setSecondaryCohorts = function (cohorts) {
    this.secondaryCohorts = cohorts;
};

/**
 * Gets the secondary cohorts.
 * @method getSecondaryCohorts
 * @return {Array} A list of secondary cohorts
 */
UserManagementModel.prototype.getSecondaryCohorts = function () {
    return this.secondaryCohorts;
};
