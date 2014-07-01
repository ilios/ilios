/**
 * Curriculum inventory management (cim) model components.
 *
 * Defines the following namespaces:
 *     ilios.cim.model
 *
 *  Dependencies:
 *     application/views/scripts/ilios_base.js
 *     YUI Dom/Event/Element libs
 */
(function () {

    /**
     * The "model" module of the curriculum inventory manager (cim) application.
     * @module module
     */

    ilios.namespace('cim.model');

    var Lang = YAHOO.lang;

    /*
     * The model environment.
     * @var Env
     * @type {Object}
     */
    var Env = {};

    /*
     * Model instance counter. Gets incremented every time a new model-object is instantiated.
     * @property instanceCounter
     * @type {Number}
     * @default counter
     */
    Env.instanceCounter = 0;

    /**
     * A model implementation, utilizing <code>YAHOO.util.AttributeProvider</code>.
     * @uses YAHOO.util.AttributeProvider
     * @namespace cim.model
     * @class BaseModel
     * @param {Object} oData A key/value map of initial model data.
     * @constructor
     */
    var BaseModel = function (oData) {
        this.init.apply(this, arguments);
    };

    BaseModel.prototype = {

        /**
         * Generates a unique 'client-side' identifier for a model instance.
         * @return {String} The client id.
         * @static
         */
        generateClientId: function () {
            return this.getName() + '_' + (++Env.instanceCounter);
        },

        /**
         * Initializes the model with given data.
         * @param {Object} oData A key/value map of initial model data.
         */
        init: function (oData) {
            var data = oData || {};

            var id = data.hasOwnProperty(this.ID_ATTRIBUTE_NAME) ? data[this.ID_ATTRIBUTE_NAME] : null;

            /**
             * The model's server-side id (e.g., the corresponding db record's primary key value).
             * @attribute id
             * @type {String|Number}
             * @readOnly
             */
            this.setAttributeConfig('id', {
                value: id,
                readOnly: true
            });

            var clientId = this.generateClientId();

            /**
             * The model's client-side id. Gets generated on instantiation.
             * @attribute clientId
             * @type {String}
             * @readOnly
             */
            this.setAttributeConfig('clientId', {
                value: clientId,
                readOnly: true
            });
        },

        /**
         * The model's machine name.
         *
         * @property NAME
         * @type {String}
         * @default "baseModel"
         */
        NAME: 'baseModel',

        /**
         * The default name of the model's ID attribute.
         *
         * @property ID_ATTRIBUTE_NAME
         * @type {String}
         * @default "dbId"
         */
        ID_ATTRIBUTE_NAME: 'dbId',

        /**
         * Returns the name attribute.
         *
         * @method getName
         * @returns {String}
         */
        getName: function () {
            return this.NAME;
        },

        /**
         * Returns the model's server-side id.
         * @method getId
         * @return {String|Number}
         */
        getId: function () {
            return this.get('id');
        },

        /**
         * Returns the model's client-side id.
         * @return {String}
         */
        getClientId: function () {
            return this.get('clientId');
        }
    };

    Lang.augment(BaseModel, YAHOO.util.AttributeProvider);


    /**
     * The curriculum inventory report model.
     *
     * @extends ilios.cim.model.BaseModel
     * @namespace cim.model
     * @class ReportModel
     * @param {Object} oData A key/value map of initial model data.
     * @param {String} oData.name
     * @param {String} oData.description
     * @param {String} oData.year
     * @param {Object} oData.program
     * @param {String} oData.start_date
     * @param {String} oData.end_date
     * @param {Boolean} oData.is_finalized
     * @constructor
     */
    var ReportModel = function (oData) {
        ReportModel.superclass.constructor.call(this, oData);
    };

    Lang.extend(ReportModel, BaseModel, {

        /*
         * @override
         * @see ilios.cim.model.BaseMode.init
         */
        init : function (oData) {
            ReportModel.superclass.init.call(this, oData);
            var name = oData.name;
            var description = oData.description;
            var year = parseInt(oData.year, 10);
            var program = oData.program;
            var startDate = oData.start_date;
            var endDate = oData.end_date;
            var isFinalized = oData.is_finalized;

            /**
             * The report name.
             *
             * @attribute name
             * @type {String}
             */
            this.setAttributeConfig('name', {
                value: name,
                validator: Lang.isString
            });

            /**
             * The report description.
             *
             * @attribute description
             * @type {String}
             */
            this.setAttributeConfig('description', {
                value: description,
                validator: Lang.isString
            });

            /**
             * The academic year that this report covers.
             *
             * @attribute academicYear
             * @type {Number}
             */
            this.setAttributeConfig('academicYear', {
                writeOnce: true,
                value: year,
                validator: Lang.isNumber
            });

            /**
             * The program associated with the given report.
             *
             * @attribute program
             * @type {Object}
             */
            this.setAttributeConfig('program', {
                writeOnce: true,
                value: program,
                validator: Lang.isObject

            });

            /**
             * The report start date.
             *
             * @attribute startDate
             * @type {String}
             */
            this.setAttributeConfig('startDate', {
                value: startDate,
                validator: Lang.isString
            });

            /**
             * The report end date.
             *
             * @attribute endDate
             * @type {String}
             */
            this.setAttributeConfig('endDate', {
                value: endDate,
                validator: Lang.isString
            });

            /**
             * Flag that indicates whether the report is "in draft" or has been "finalized".
             *
             * @attribute isFinalized
             * @type {Boolean}
             */
            this.setAttributeConfig('isFinalized', {
                value: isFinalized,
                validator: Lang.isBoolean
            });

            // create custom event
            this.createEvent(this.EVT_UPDATED);
        },
        /**
         * Updates the model's attributes with given data.
         * Fires the "updated" event after updating the attributes.
         * @method update
         * @param {Object} oData a key/value map of data to update the model with.
         */
        update: function (oData) {
            this.set('name', oData.name);
            this.set('description', oData.description);
            this.set('endDate', oData.end_date);
            this.set('startDate', oData.start_date);
            this.fireEvent(this.EVT_UPDATED);
        },

        /*
         * @override
         * @see ilios.cim.model.BaseMode.NAME
         */
        NAME: 'curriculumInventoryReportModel',

        /*
         * @override
         * @see ilios.cim.model.BaseMode.ID_ATTRIBUTE_NAME
         */
        ID_ATTRIBUTE_NAME: 'report_id',

        /**
         * Fired when the model's <code>update()</code> method is invoked.
         * Please note that this does not necessarily imply that any model attributes have changed during the update.
         * For changes to the model attributes themselves, listen the various "change" events that
         * the individual model attributes are emitting.
         *
         * @event update
         * @final
         */
        EVT_UPDATED: 'updated'
    });

    /**
     * The course model.
     * Course are considered to be read-only in the context of curriculum inventory management, so this has been implemented
     * as immutable object.
     *
     * @extends ilios.cim.model.BaseModel
     * @namespace cim.model
     * @class CourseModel
     * @param {Object} oData A key/value map of initial model data. This following properties are expected:
     *     @param {String} oData.archived
     *     @param {String|null} oData.clerkship_type_id
     *     @param {String|null} oData.clerkship_type_title
     *     @param {String} oData.course_id
     *     @param {String} oData.course_level
     *     @param {Boolean} oData.deleted
     *     @param {String} oData.end_date
     *     @param {String} oData.external_id
     *     @param {String} oData.locked
     *     @param {String} oData.owning_school_id
     *     @param {String|null} oData.publish_event_id
     *     @param {String} oData.published_as_tbd
     *     @param {String} oData.start_date
     *     @param {String} oData.title
     *     @param {String} oData.year
     *     @param {Array} oData.sessions
     * @constructor
     */
    var CourseModel = function (oData) {
        CourseModel.superclass.constructor.call(this, oData);
    };

    Lang.extend(CourseModel, BaseModel, {
        /*
         * @override
         * @see ilios.cim.model.BaseModel.init
         */
        init : function (oData) {
            CourseModel.superclass.init.call(this, oData);
            var archived = !! parseInt(oData.archived, 10);
            var locked = !! parseInt(oData.locked, 10);
            var level = parseInt(oData.course_level, 10);
            var deleted = !! parseInt(oData.deleted, 10);
            var endDate = oData.end_date;
            var startDate = oData.start_date;
            var title = oData.title;
            var year = parseInt(oData.year, 10);
            var isPublished = ! Lang.isNull(oData.publish_event_id);
            var isPublishedAsTbd = !! parseInt(oData.published_as_tbd, 10);
            var externalId = oData.external_id;
            var clerkshipTypeId = Lang.isValue(oData.clerkship_type_id) ? parseInt(oData.clerkship_type_id, 10) : 0;
            var clerkshipTypeTitle = oData.clerkship_type_title;
            var owningSchoolId = parseInt(oData.owning_school_id, 10);
            var sessions = [];
            for (var key in oData.sessions) {
                var session = new SessionModel(oData.sessions[key]);
                sessions.push(session);
            }



            /**
             * A flag indicating whether the course has been archived or not.
             *
             * @attribute archived
             * @type {Boolean}
             * @readOnly
             */
            this.setAttributeConfig('archived', {
                value: archived,
                readOnly: true
            });

            /**
             * A flag indicating whether the course has been locked or not.
             *
             * @attribute locked
             * @type {Boolean}
             * @readOnly
             */
            this.setAttributeConfig('locked', {
                value: locked,
                readOnly: true
            });

            /**
             * A flag indicating whether the course has been "deleted" or not.
             *
             * @attribute deleted
             * @type {Boolean}
             * @readOnly
             */
            this.setAttributeConfig('deleted', {
                value: deleted,
                readOnly: true
            });

            /**
             * The course level.
             *
             * @attribute level
             * @type {Number}
             * @readOnly
             */
            this.setAttributeConfig('level', {
                value: level,
                readOnly: true
            });

            /**
             * The end-date of the course.
             *
             * @attribute endDate
             * @type {String}
             * @readOnly
             */
            this.setAttributeConfig('endDate', {
                value: endDate,
                readOnly: true
            });

            /**
             * The start-date of the course.
             *
             * @attribute startDate
             * @type {String}
             * @readOnly
             */
            this.setAttributeConfig('startDate', {
                value: startDate,
                readOnly: true
            });

            /**
             * The course title.
             *
             * @attribute title
             * @type {String}
             * @readOnly
             */
            this.setAttributeConfig('title', {
                value: title,
                readOnly: true
            });

            /**
             * The course year.
             *
             * @attribute year
             * @type {Number}
             * @readOnly
             */
            this.setAttributeConfig('year', {
                value: year,
                readOnly: true
            });

            /**
             * A flag indicating whether the course has been published or not.
             *
             * @attribute isPublished
             * @type {Boolean}
             * @readOnly
             */
            this.setAttributeConfig('isPublished', {
                value: isPublished,
                readOnly: true
            });

            /**
             * A flag indicating whether the course has been published in "TBD" mode or not.
             *
             * @attribute isPublishedAsTbd
             * @type {Boolean}
             * @readOnly
             */
            this.setAttributeConfig('isPublishedAsTbd', {
                value: isPublishedAsTbd,
                readOnly: true
            });

            /**
             * An alternative identifier ("machine name") of the course.
             *
             * @attribute externalId
             * @type {String}
             * @readOnly
             */
            this.setAttributeConfig('externalId', {
                value: externalId,
                readOnly: true
            });

            /**
             * The id of the clerkship type associated with this course.
             * This value is 0 if the course is not a clerkship.
             *
             * @attribute clerkshipTypeId
             * @type {Number}
             * @readOnly
             */
            this.setAttributeConfig('clerkshipTypeId', {
                value: clerkshipTypeId,
                readOnly: true
            });

            /**
             * The title of the clerkship type associated with this course.
             * This value is NULL if the course is not a clerkship
             *
             * @attribute clerkshipTypeTitle
             * @type {String|null}
             * @readOnly
             */
            this.setAttributeConfig('clerkshipTypeTitle', {
                value: clerkshipTypeTitle,
                readOnly: true
            });

            /**
             * The id of the school that this course belongs to.
             *
             * @attribute owningSchoolId
             * @type {Number}
             * @readOnly
             */
            this.setAttributeConfig('owningSchoolId', {
                value: owningSchoolId,
                readOnly: true
            });

            /**
             * Array of all the sessions
             *
             * @attribute sessions
             * @type {Array}
             * @readOnly
             */
            this.setAttributeConfig('sessions', {
                value: sessions,
                readOnly: true
            });

        },


        /**
         * Gets a session from a course by its id
         *
         * @method getSessionById
         * @param {Number} sessionId
         * @return {SessionModel|null}
         */
        getSessionById: function (sessionId) {
            var sessions = this.get('sessions');
            for (var i = 0, n = sessions.length; i < n; i++) {
                if(sessions[i].get('id') == sessionId){
                    return sessions[i];
                }
            }
            return null;
        },


        /**
         * Gets a datasource of all the sessions for use in a datatable
         *
         * @method getSessionDataSource
         * @return {YAHOO.util.DataSource}
         */
        getSessionDataSource: function () {
            var s = this.get('sessions');
            var arr = [];
            for(var i = 0; i<s.length;i++){
                arr.push(s[i].getPropertiesObject());
            }
            var ds = new YAHOO.util.DataSource(arr);
            //allow the user to filter data
            ds.doBeforeCallback = function (req,raw,res,cb) {
                var data = res.results || [];
                var filtered = [];
                if (req) {
                    req = req.toLowerCase();
                    for (var i = 0; i < data.length; ++i) {
                        if (data[i].fullText.toLowerCase().indexOf(req) != -1) {
                            filtered.push(data[i]);
                        }
                    }
                    res.results = filtered;
                }

                return res;
            }
            return ds;
        },

        /*
         * @override
         * @see ilios.cim.model.BaseModel.NAME
         */
        NAME: 'courseModel',

        /*
         * @override
         * @see ilios.cim.model.BaseModel.ID_ATTRIBUTE_NAME
         */
        ID_ATTRIBUTE_NAME: 'course_id'
    });


    /**
     * The session model.
     * Sessions are mostly read only.  The only property which can be modified
     * is countOfferingsOnce
     *
     * @extends ilios.cim.model.BaseModel
     * @namespace cim.model
     * @class SessionModel
     * @param {Object} oData A key/value map of initial model data. This following properties are expected:
     *     @param {String} oData.attire_required
     *     @param {String} oData.course_id
     *     @param {String} oData.deleted
     *     @param {String} oData.equipment_required
     *     @param {String} oData.ilm_session_facet_id
     *     @param {String} oData.last_updated_on
     *     @param {String} oData.offering_count
     *     @param {String} oData.total_offering_duration
     *     @param {String} oData.max_single_offering_duration
     *     @param {String} oData.publish_event_id
     *     @param {String} oData.published_as_tbd
     *     @param {String} oData.session_id
     *     @param {String} oData.session_type_id
     *     @param {String} oData.session_type_title
     *     @param {String} oData.supplemental
     *     @param {String} oData.title
     * @constructor
     */
    var SessionModel = function (oData) {
        SessionModel.superclass.constructor.call(this, oData);
    };

    Lang.extend(SessionModel, BaseModel, {
        /*
         * @override
         * @see ilios.cim.model.BaseModel.init
         */
        init : function (oData) {
            SessionModel.superclass.init.call(this, oData);
            var attireRequired = !! parseInt(oData.attireRequired, 10);
            var courseId = parseInt(oData.course_id, 10);
            var deleted = !! parseInt(oData.deleted, 10);
            var equipmentRequired = !! parseInt(oData.equipmentRequired, 10);
            var ilmSessionFacetId = parseInt(oData.ilm_session_facet_id, 10);
            var lastUpdatedOn = oData.last_updated_on;
            var offeringCount = parseInt(oData.offering_count, 10);
            var totalOfferingDuration = parseInt(oData.total_offering_duration, 10);
            var maxSingleOfferingDuration = parseInt(oData.max_single_offering_duration, 10);
            var isPublished = ! Lang.isNull(oData.publish_event_id);
            var isPublishedAsTbd = !! parseInt(oData.published_as_tbd, 10);
            var sessionTypeId = parseInt(oData.session_type_id, 10);
            var sessionTypeTitle = oData.session_type_title;
            var supplemental = !! parseInt(oData.supplemental, 10);
            var title = oData.title;
            var countOfferingsOnce = false;

            /**
             * The a flag for whether special attire is required
             *
             * @attribute attireRequired
             * @type {Boolean}
             * @readOnly
             */
            this.setAttributeConfig('attireRequired', {
                value: attireRequired,
                readOnly: true
            });

            /**
             * The owning course id
             *
             * @attribute courseId
             * @type {Number}
             * @readOnly
             */
            this.setAttributeConfig('courseId', {
                value: courseId,
                readOnly: true
            });

            /**
             * The a flag for whether the session has been deleted
             *
             * @attribute delted
             * @type {Boolean}
             * @readOnly
             */
            this.setAttributeConfig('deleted', {
                value: deleted,
                readOnly: true
            });

            /**
             * The a flag for whether equiptment is required
             *
             * @attribute equipmentRequired
             * @type {Boolean}
             * @readOnly
             */
            this.setAttributeConfig('equipmentRequired', {
                value: equipmentRequired,
                readOnly: true
            });

            /**
             * The ILM Facet ID
             *
             * @attribute ilmSessionFacetId
             * @type {Number|null}
             * @readOnly
             */
            this.setAttributeConfig('ilmSessionFacetId', {
                value: ilmSessionFacetId,
                readOnly: true
            });

            /**
             * The last update date
             *
             * @attribute lastUpdatedOn
             * @type {String}
             * @readOnly
             */
            this.setAttributeConfig('lastUpdatedOn', {
                value: lastUpdatedOn,
                readOnly: true
            });

            /**
             * The number of offerings for this sessions
             *
             * @attribute offeringCount
             * @type {Number}
             * @readOnly
             */
            this.setAttributeConfig('offeringCount', {
                value: offeringCount,
                readOnly: true
            });

            /**
             * The total duration of all offerings in minutes
             *
             * @attribute totalOfferingDuration
             * @type {Number}
             * @readOnly
             */
            this.setAttributeConfig('totalOfferingDuration', {
                value: totalOfferingDuration,
                readOnly: true
            });

            /**
             * The maximum single duration of any offering in this session
             *
             * @attribute maxSingleOfferingDuration
             * @type {Number}
             * @readOnly
             */
            this.setAttributeConfig('maxSingleOfferingDuration', {
                value: maxSingleOfferingDuration,
                readOnly: true
            });

            /**
             * A flag indicating whether the session has been published or not.
             *
             * @attribute isPublished
             * @type {Boolean}
             * @readOnly
             */
            this.setAttributeConfig('isPublished', {
                value: isPublished,
                readOnly: true
            });

            /**
             * A flag indicating whether the session has been published in "TBD" mode or not.
             *
             * @attribute isPublishedAsTbd
             * @type {Boolean}
             * @readOnly
             */
            this.setAttributeConfig('isPublishedAsTbd', {
                value: isPublishedAsTbd,
                readOnly: true
            });

            /**
             * The session type id
             *
             * @attribute sessionTypeId
             * @type {Number}
             * @readOnly
             */
            this.setAttributeConfig('sessionTypeId', {
                value: sessionTypeId,
                readOnly: true
            });

            /**
             * The session type title
             *
             * @attribute sessionTypeTitle
             * @type {String}
             * @readOnly
             */
            this.setAttributeConfig('sessionTypeTitle', {
                value: sessionTypeTitle,
                readOnly: true
            });

            /**
             * The a flag for whether the session is supplemental
             *
             * @attribute supplemental
             * @type {Boolean}
             * @readOnly
             */
            this.setAttributeConfig('supplemental', {
                value: supplemental,
                readOnly: true
            });

            /**
             * The session title.
             *
             * @attribute title
             * @type {String}
             * @readOnly
             */
            this.setAttributeConfig('title', {
                value: title,
                readOnly: true
            });

            /**
             * Indicates whether this session Counts all offerings seperatly
             * or as a single offering
             *
             * @attribute countOfferingsOnce
             * @type {Boolean}
             * @default false
             */
            this.setAttributeConfig('countOfferingsOnce', {
                value: countOfferingsOnce,
                validator: Lang.isBoolean
            });
        },

        /**
         * Gets a plain object with the session properties
         *
         * @method getPropertiesObject
         * @return {Object}
         */
        getPropertiesObject: function () {
            var obj = {};
            var keys = this.getAttributeKeys();
            var fullText = '';
            for(var i = 0;i<keys.length; i++){
                obj[keys[i]] = this.get(keys[i]);
                fullText += this.get(keys[i]);
            }
            obj.fullText = fullText;

            return obj;
        },

        /**
         * Gets the current offering duration in hours
         * Takes countOfferingsOnce into account
         *
         * @method getOfferingHours
         * @return {Number}
         */
        getOfferingHours: function () {
            var minutes = this.get('countOfferingsOnce')?this.get('maxSingleOfferingDuration'):this.get('totalOfferingDuration');
            var hours = minutes/60;
            if(parseInt(hours, 10) != hours){
                hours = hours.toFixed(1);
            }
            return hours;
        },

        /*
         * @override
         * @see ilios.cim.model.BaseModel.NAME
         */
        NAME: 'sessionModel',

        /*
         * @override
         * @see ilios.cim.model.BaseModel.ID_ATTRIBUTE_NAME
         */
        ID_ATTRIBUTE_NAME: 'session_id'
    });

    /**
     * The academic level model.
     *
     * @extends ilios.cim.model.BaseModel
     * @namespace cim.model
     * @class AcademicLevelModel
     * @param {Object} oData A key/value map of initial model data.
     *     @param {String} oData.academic_level_id
     *     @param {String} oData.description
     *     @param {String} oData.level
     *     @param {String} oData.name
     *     @param {String} oData.report_id
     * @constructor
     */
    var AcademicLevelModel = function (oData) {
        AcademicLevelModel.superclass.constructor.call(this, oData);
    };

    Lang.extend(AcademicLevelModel, BaseModel, {
        /*
         * @override
         * @see ilios.cim.model.BaseModel.init
         */
        init : function (oData) {
            AcademicLevelModel.superclass.init.call(this, oData);

            var reportId = parseInt(oData.report_id, 10);
            var level = parseInt(oData.level, 10);
            var name = Lang.isString(oData.name) ? oData.name : '';
            var description = Lang.isString(oData.description) ? oData.description : '';

            /**
             * The academic level's report id.
             * Identifies the report that the level belongs to.
             *
             * @attribute reportId
             * @type {Number}
             * @readOnly
             */
            this.setAttributeConfig('reportId', {
                value: reportId,
                readOnly: true
            });

            /**
             * The academic level's actual "level", a numeric value between 1 and 10.
             *
             * @attribute level
             * @type {Number}
             * @readOnly
             */
            this.setAttributeConfig('level', {
                value: level,
                readOnly: true
            });

            /**
             * The academic level's name.
             * @attribute name
             * @type {String}
             */
            this.setAttributeConfig('name', {
                value: name,
                validator: Lang.isString
            });

            /**
             * The academic level's description.
             * @attribute name
             * @type {String}
             */
            this.setAttributeConfig('description', {
                value: description,
                validator: Lang.isString
            });
        },

        /*
         * @override
         * @see ilios.cim.model.BaseModel.NAME
         */
        NAME: 'curriculumInventoryAcademicLevelModel',

        /*
         * @override
         * @see ilios.cim.model.BaseModel.ID_ATTRIBUTE_NAME
         */
        ID_ATTRIBUTE_NAME: 'academic_level_id'
    });

    /**
     * The sequence block model.
     *
     * @extends ilios.cim.model.BaseModel
     * @namespace cim.model
     * @class SequenceBlockModel
     * @param {Object} oData A key/value map of initial model data.
     *    @param {String} oData.sequence_block_id
     *    @param {String} oData.report_id
     *    @param {String} oData.required
     *    @param {String} oData.child_sequence_order
     *    @param {String} oData.order_in_sequence
     *    @param {String} oData.minimum
     *    @param {String} oData.maximum
     *    @param {String} oData.track
     *    @param {String} oData.description
     *    @param {String} oData.title
     *    @param {String|null} oData.start_date
     *    @param {String|null} oData.end_date
     *    @param {String} oData.academic_level_id
     *    @param {ilios.cim.model.AcademicLevelModel} oData.academic_level_model
     *    @param {String} oData.duration
     *    @param {ilios.cim.model.CourseModel|null} oData.course_model
     *    @param {ilios.cim.model.SequenceBlockModel|null} oData.parent_model
     * @constructor
     */
    var SequenceBlockModel = function (oData) {
        SequenceBlockModel.superclass.constructor.call(this, oData);
    };

    Lang.extend(SequenceBlockModel, BaseModel, {

        /**
         * Updates the model's attributes with given values.
         *
         * @method update
         * @param {Object} oData A key/value map of updated model data.
         *    @param {String} oData.required
         *    @param {String} oData.child_sequence_order
         *    @param {String} oData.order_in_sequence
         *    @param {String} oData.minimum
         *    @param {String} oData.maximum
         *    @param {String} oData.track
         *    @param {String} oData.description
         *    @param {String} oData.title
         *    @param {String|null} oData.start_date
         *    @param {String|null} oData.end_date
         *    @param {String} oData.academic_level_id
         *    @param {ilios.cim.model.AcademicLevelModel} oData.academic_level_model
         *    @param {String} oData.duration
         *    @param {ilios.cim.model.CourseModel|null} [oData.course_model]
         *    @param {Array} oData.sessions
         */
        update: function (oData) {
            var required = parseInt(oData.required, 10);
            var childSequenceOrder = parseInt(oData.child_sequence_order, 10);
            var orderInSequence = parseInt(oData.order_in_sequence, 10);
            var minimum = parseInt(oData.minimum, 10);
            var maximum = parseInt(oData.maximum, 10);
            var track = !! parseInt(oData.track, 10);
            var description = oData.description;
            var title = oData.title;
            var startDate = oData.start_date;
            var endDate = oData.end_date;
            var academicLevelModel = oData.academic_level_model;
            var duration = parseInt(oData.duration, 10);
            var courseModel = oData.course_model;

            this.set('required', required);
            this.set('childSequenceOrder', childSequenceOrder);
            this.set('orderInSequence', orderInSequence);
            this.set('minimum', minimum);
            this.set('maximum', maximum);
            this.set('track', track);
            this.set('description', description);
            this.set('startDate', startDate);
            this.set('endDate', endDate);
            this.set('academicLevel', academicLevelModel);
            this.set('duration', duration);
            this.set('title', title);
            if (typeof courseModel !== 'undefined') {
                this.set('course', courseModel);
            }
        },
        /*
         * @override
         * @see ilios.cim.model.BaseModel.init
         */
        init : function (oData) {
            SequenceBlockModel.superclass.init.call(this, oData);
            var reportId = parseInt(oData.report_id, 10);
            var required = parseInt(oData.required, 10);
            var childSequenceOrder = parseInt(oData.child_sequence_order, 10);
            var orderInSequence = parseInt(oData.order_in_sequence, 10);
            var minimum = parseInt(oData.minimum, 10);
            var maximum = parseInt(oData.maximum, 10);
            var track = !! parseInt(oData.track, 10);
            var description = oData.description;
            var title = oData.title;
            var startDate = oData.start_date;
            var endDate = oData.end_date;
            var academicLevelModel = oData.academic_level_model;
            var duration = parseInt(oData.duration, 10);
            var courseModel = oData.course_model;
            var parentModel = oData.parent_model;
            var children = new ilios.cim.model.SequenceBlockModelMap();
            for(var i in oData.sessions){
                var session = courseModel.getSessionById(oData.sessions[i].session_id);
                if(session){
                    session.set('countOfferingsOnce', !! parseInt(oData.sessions[i].count_offerings_once));
                }
            }

            /**
             * The id of the report that this sequence block belongs to.
             *
             * @attribute reportId
             * @type {Number}
             * @writeOnce
             */
            this.setAttributeConfig('reportId', {
                value: reportId,
                validator: Lang.isNumber,
                writeOnce: true
            });

            /**
             * Indicates whether this sequence block is a required part of the curriculum.
             * Valid values are "Required" (<code>SequenceBlockModel.REQUIRED</code>), "Optional"
             * (<code<SequenceBlockModel.OPTIONAL</code>), and "Required in Track"
             * (<code>SequenceBlockModel.REQUIRED_IN_TRACK</code>).
             *
             * @attribute required
             * @type {Number}
             */
            this.setAttributeConfig('required', {
                value: required,
                validator: function (value) {
                    return Lang.isNumber(value)
                        && (this.OPTIONAL === value
                        || this.REQUIRED === value
                        || this.REQUIRED_IN_TRACK === value);

                }
            });
            /**
             * If there are nested sequence blocks within this sequence block, <em>childSequenceOrder</em> indicates
             * how the nested sequence blocks are delivered to learners. Valid values are "Ordered"
             * (<code>SequenceBlockModel.ORDERED</code>), "Unordered" (<code>SequenceBlockModel.UNORDERED</code>)
             * , and "Parallel" (<code>SequenceBlockModel.PARALLEL</code>).
             *
             * @attribute childSequenceOrder
             * @type {Number}
             */
            this.setAttributeConfig('childSequenceOrder', {
                value: childSequenceOrder,
                validator: function (value) {
                    return Lang.isNumber(value)
                        && (this.ORDERED === value
                        || this.UNORDERED === value
                        || this.PARALLEL === value);
                }
            });

            /**
             * The parent sequence block of this sequence block.
             * NULL for top-level sequence blocks.
             *
             * @attribute parent
             * @type {ilios.cim.model.SequenceBlockModel|null}
             */
            this.setAttributeConfig('parent', {
                value: parentModel,
                validator: function (value) {
                    return (Lang.isNull(value) || (value instanceof ilios.cim.model.SequenceBlockModel));
                }
            })

            /**
             * A positive integer indicating the order of this sequence block in relation to its siblings within
             * a ordered sequence.
             *
             * @attribute orderInSequence
             * @type {Number}
             */
            this.setAttributeConfig('orderInSequence', {
                value: orderInSequence,
                validator: Lang.isNumber
            });

            /**
             * If there are nested sequence blocks, <em>minimum</em> indicates the minimum number of nested
             * sequence blocks that a learner can take.
             *
             * @attribute minimum
             * @type {Number}
             */
            this.setAttributeConfig('minimum', {
                value: minimum,
                validator: Lang.isNumber
            });

            /**
             * If there are nested sequence blocks, <em>maximum</em> indicates the maximum number of nested
             * sequence blocks that a learner can take.
             *
             * @attribute maximum
             * @type {Number}
             */
            this.setAttributeConfig('maximum', {
                value: maximum,
                validator: Lang.isNumber
            });

            /**
             * Indicates whether this sequence block is a track within the curriculum. Valid values are TRUE and FALSE.
             *
             * @attribute minimum
             * @type {Number}
             * @default false
             */
            this.setAttributeConfig('track', {
                value: track,
                validator: Lang.isBoolean
            });

            /**
             * The description of the sequence block.
             *
             * @attribute description
             * @type {String}
             */
            this.setAttributeConfig('description', {
                value: description,
                validator: Lang.isString
            });

            /**
             * The title of the sequence block.
             *
             * @attribute title
             * @type {String}
             */
            this.setAttributeConfig('title', {
                value: title,
                validator: Lang.isString
            });

            /**
             * The start date of the sequence block.
             *
             * @attribute startDate
             * @type {String|null}
             */
            this.setAttributeConfig('startDate', {
                value: startDate,
                validator: function (value) {
                    return Lang.isNull(value) || Lang.isString(value);
                }
            });

            /**
             * The end date of the sequence block.
             *
             * @attribute endDate
             * @type {String|null}
             */
            this.setAttributeConfig('endDate', {
                value: endDate,
                validator: function (value) {
                    return Lang.isNull(value) || Lang.isString(value);
                }
            });

            /**
             * The duration (in minutes) of this sequence block.
             *
             * @attribute duration
             * @type {Number}
             */
            this.setAttributeConfig('duration', {
                value: duration,
                validator: Lang.isNumber
            });

            /**
             * The course associated with this sequence block, or NULL if no course is associated.
             *
             * @attribute course
             * @type {ilios.cim.model.CourseModel|null}
             */
            this.setAttributeConfig('course', {
                value: courseModel,
                validator: function (value) {
                    return (Lang.isNull(value) || (value instanceof ilios.cim.model.CourseModel));
                }
            });

            /**
             * The academic level of this sequence block.
             *
             * @attribute academicLevel
             * @type {ilios.cim.model.AcademicLevelModel}
             */
            this.setAttributeConfig('academicLevel', {
                value: academicLevelModel,
                validator: function (value) {
                    return value instanceof ilios.cim.model.AcademicLevelModel;
                }
            });

            /**
             * An object container holding child-sequence blocks.
             *
             * @attribute children
             * @type {ilios.cim.model.SequenceBlockModelMap}
             * @readOnly
             */
            this.setAttributeConfig('children', {
                value: children,
                readOnly: true
            })

            // create custom event
            this.createEvent(this.EVT_DELETE);
        },

        /**
         * Lifecycle management method.
         * Removes the sequence block from its parent, then unsubscribes all registered event listeners.
         * This method cascades down to any children of this sequence block.
         * Fires the "delete" event.
         *
         * @method delete
         */
        delete: function () {
            var parent = this.get('parent');
            // remove itself from parent sequence
            if (parent) {
                parent.get('children').remove(this.get('id'));
            }
            this.set('course', null);
            this.set('academicYear', null);
            this.fireEvent(this.EVT_DELETE);
            this.unsubscribeAll();
            // cascading delete
            this.get('children').walk(this.delete);
        },


        /**
         * Retrieves a set of model ids, included the id of this instance and all ids of its descendants.
         * This is a recursive function.
         *
         * @method getIds
         * @param {Object} [ids] A map of object ids.
         */
        getIds: function () {
            var i, n;
            var id = this.get('id');
            var children = this.get('children').list();
            var ids = {};
            ids[id] = id;
            for (i = 0, n = children.length; i < n; i++) {
                ids = Lang.merge(ids, children[i].getIds());
            }
            return ids;
        },

        /*
         * @override
         * @see ilios.cim.model.BaseModel.NAME
         */
        NAME: 'curriculumInventorySequenceBlockModel',
        /*
         * @override
         * @see ilios.cim.model.BaseModel.ID_ATTRIBUTE_NAME
         */
        ID_ATTRIBUTE_NAME: 'sequence_block_id',

        /**
         * @property REQUIRED
         * @type {Number}
         * @final
         *
         */
        REQUIRED: 1,

        /**
         * @property OPTIONAL
         * @type {Number}
         * @final
         */
        OPTIONAL: 2,

        /**
         * @property REQUIRED_IN_TRACK
         * @type {Number}
         * @final
         */
        REQUIRED_IN_TRACK: 3,

        /**
         * @property SORT_ORDER
         * @type {Number}
         * @final
         */
        ORDERED: 1,

        /**
         * @property UNORDERED
         * @property UNORDERED
         * @type {Number}
         * @final
         */
        UNORDERED: 2,

        /**
         * @property PARALLEL
         * @type {Number}
         * @final
         */
        PARALLEL: 3,

        /**
         * Fired when this sequence block instance is being deleted.
         * @event delete
         * @final
         */
        EVT_DELETE: 'delete'
    });


    /**
     * An implementation of an object map.
     *
     * @namespace cim.model
     * @class ObjectMap
     * @constructor
     * @todo This object could be useful outside the context of this app.
     *      Refactor it out when the time comes to redo the rest of Ilios JS libs. [ST 2013-08-01]
     */
    var ObjectMap = function () {
        this._map = {};
        this._counter = 0;
    };

    ObjectMap.prototype = {

        /**
         * The internal map.
         *
         * @var _map
         * @type {Object}
         * @protected
         */
        _map: null,

        /**
         * The internal object counter.
         *
         * @param _counter
         * @type {Number}
         * @protected
         */
        _counter: null,

        /**
         * Adds a given object to the map.
         *
         * @method add
         * @param {Object} o The object to be added.
         * @return {Object} The added object.
         * @throws {Error} If an object already exists in the map under the given object's id.
         */
        add: function (o) {
            var id = this._getIdFromObject(o);
            if (this.exists(id)) {
                throw new Error('add(): id already exists. id = ' + id);
            }
            this._map[id] = o;
            this._counter = this._counter + 1;
            return o;
        },

        /**
         * Removes an given object from the map.
         *
         * @method remove
         * @param {Number|String} id The object id.
         * @return {Object} The removed object.
         * @throws {Error} If no object could be found for the given id.
         */
        remove: function (id) {
            var o = this.get(id);
            delete this._map[id];
            this._counter = this._counter - 1;
            return o;
        },

        /**
         * Returns all objects in the map as an array.
         *
         * @method list
         * @property {Function} [filterFn] an optional filter function.
         * @return {Array} A list of objects in the map.
         */
        list: function (filterFn) {
            var i, rhett, item;
            filterFn = Lang.isFunction(filterFn) ? filterFn : false;
            rhett = [];
            for (i in this._map) {
                if (this._map.hasOwnProperty(i)) {
                    item = this._map[i];
                    if (! filterFn || filterFn(item)) {
                        rhett.push(item);
                    }
                }
            }
            return rhett;
        },

        /**
         * Checks whether an object exists in the map under a given id.
         *
         * @method exists
         * @param {Number|String} id The object id.
         * @return {Boolean} TRUE if an object was found for the given id, FALSE otherwise.
         */
        exists: function (id) {
            return this._map.hasOwnProperty(id);
        },

        /**
         * Retrieves an object from the map by it's id.
         * @method get
         * @param {Number|String} id The object id.
         * @return {Object} The object.
         * @throws {Error} If no object could be found for the given id.
         */
        get: function (id) {
            if (! this.exists(id)) {
                throw new Error('get(): no object found for the given id. id = ' + id);
            }
            return this._map[id];
        },

        /**
         * Applies a given function with given arguments to each object in the map.
         *
         * @method walk
         * @param {Function} fn
         * @param {Object} [args]
         */
        walk: function (fn, args) {
            var i, o;
            args = args || {};

            for (i in this._map) {
                if (this._map.hasOwnProperty(i)) {
                    o = this._map[i];
                    fn.call(o, args);
                }
            }
        },

        /**
         * Retrieves the current number of objects in the map.
         *
         * @method size
         * @return {Number}
         */
        size: function () {
            return this._counter;
        },

        /**
         * Retrieves the "id" property for a given object.
         *
         * @method _getIdFromObject
         * @param {Object} o
         * @return {Number|String}
         * @protected
         */
        _getIdFromObject: function (o) {
            return o.id;
        }
    };

    /**
     * A map of sequence block models.
     *
     * @namespace cim.model
     * @class SequenceBlockModelMap
     * @extends ilios.cim.model.ObjectMap
     * @constructor
     */
    var SequenceBlockModelMap = function () {
        SequenceBlockModelMap.superclass.constructor.call(this);
    };

    Lang.extend(SequenceBlockModelMap, ObjectMap, {
        /**
         * Adds a given sequence block model to the map.
         *
         * @param {ilios.cim.model.SequenceBlockModel} model The model to add.
         * @return {ilios.cim.model.SequenceBlockModel} The added model.
         * @throw {Error} If the data type didn't match, or if the model already exists in the map.
         * @see ilios.cim.model.ObjectMap.add
         * @override
         */
        add: function (model) {
            if (! model instanceof ilios.cim.model.SequenceBlockModel) {
                throw new Error('add(): type mismatch.');
            }
            return SequenceBlockModelMap.superclass.add.call(this, model);
        },

        /**
         * Sorts and returns the sequence blocks in the map according to a given sorting mechanism.
         *
         * @method sort
         * @param {Function} fn Am <code>Array.sort</code> compatible callback function implementing a comparison algorithm for sequence blocks.
         * @return {Array} a sorted array of sequence blocks
         */
        sort: function (fn) {
            var list = this.list();
            list.sort(fn);
            return list;
        },

        /**
         * Callback function for <code>Array.sort()</code>
         * Compares two given sequence blocks by their following attributes in the precedence listed:
         * 1. academic level ... sort on "level" attribute. numeric sort, ascending.
         * 2. start date ... numeric sort on timestamps, ascending. NULL values will be treated a unix timestamp 0.
         * 3. title ... sort alphabetically, case-insensitive, ascending
         * 4. sequence model id ... numeric sort, ascending
         *
         * @method sortByAcademicLevel
         * @param {ilios.cim.model.SequenceBlockModel} a
         * @param {ilios.cim.model.SequenceBlockModel} b
         * @return {Number}
         * @see ilios.cim.model.SequenceBlockModelMap.sort
         */
        sortByAcademicLevel: function (a, b) {
            var valA, valB, n;
            // 1. sort by academic level
            valA = a.get('academicLevel').get('level');
            valB = b.get('academicLevel').get('level');
            if (valA > valB) {
                return 1;
            } else if (valA < valB) {
                return -1;
            }
            // 2. sort by start date
            valA = a.get('startDate');
            valB = b.get('startDate');
            valA = valA ? Date.parse(valA) : 0;
            valB = valB ? Date.parse(valB) : 0;
            if (valA > valB) {
                return 1;
            } else if (valA < valB) {
                return -1;
            }
            // 3. sort by title
            valA = a.get('title');
            valB = b.get('title');

            n = valA.toLowerCase().localeCompare(valB.toLowerCase());
            if (n) {
                return n;
            }
            return (a.getId() > b.getId()) ? 1 : -1;
        },

        /**
         * Callback function for <code>Array.sort()</code>
         * Compares two given sequence blocks by their order in sequence value.
         *
         * @method sortByOrderInSequence
         * @param {ilios.cim.model.SequenceBlockModel} a
         * @param {ilios.cim.model.SequenceBlockModel} b
         * @return {Number}
         * @see ilios.cim.model.SequenceBlockModelMap.sort
         */
        sortByOrderInSequence: function (a, b) {
            var valA = a.get('orderInSequence');
            var valB = b.get('orderInSequence');
            if (valA == valB) {
                return 0;
            }
            return (valA > valB) ? 1 : -1;
        },

        /*
         * @override
         * @see ilios.cim.model.ObjectMap._getIdFromObject
         */
        _getIdFromObject: function (o) {
            return o.get('id');
        }
    });


    /**
     * Course repository.
     * Allows for state management of courses within the application.
     * A course can either be "checked in" (available for assignment to a sequence block),
     * or "checked out" (unavailable for assignment)
     * Courses will be checked-in/out from the repo by its owning application as part of sequence block lifecycle
     * management (block-creation/deletion/update).
     *
     * @namespace cim
     * @class CourseRepository
     * @constructor
     * @see ilios.cim.model.CourseModel
     */
    var CourseRepository = function () {
        this._unavailable = {};
        this._available = {};
    };

    CourseRepository.prototype = {

        /**
         * The container object for checked-out/unavailable courses.
         *
         * @property _unavailable
         * @type {Object}
         * @protected
         */
        _unavailable: null,

        /**
         * The container object for checked-in/available courses.
         *
         * @property _unavailable
         * @type {Object}
         * @protected
         */
        _available: null,


        /**
         * Checks if the repo contains a given course.
         * @param {Number} The course id.
         * @returns {Boolean} TRUE if the course exists in the repo, otherwise FALSE.
         */
        exists: function (id) {
            return this._available.hasOwnProperty(id) || this._unavailable.hasOwnProperty(id);
        },

        /**
         * Adds a course to the repo.
         * Newly checked-in courses are automatically available.
         *
         * @method add
         * @param {ilios.cim.model.CourseModel} The course object.
         * @throws {Error} If the given course already exists in the repo.
         */
        add: function (course) {
            var id = course.getId();
            if (this.exists(id)) {
                throw new Error('add(): course already exists in repo. course id = ' + id);
            }
            this._available[id] = course;
        },

        /**
         * Checks a given course out of the repo, and flags it as unavailable.
         * If the course has already been checked-out, then it is simply returned without a status change.
         *
         * @method checkOut
         * @param {Number} id The course id.
         * @return {ilios.cim.model.CourseModel} The checked-out course.
         * @throw {Error} Throws an error if the course cannot be found or has already been checked out.
         *
         */
        checkOut: function (id) {
            var course;
            if (! this.exists(id)) {
                throw new Error('checkOut(): course does not exist in repo, course id = ' + id);
            }
            if (! this._available.hasOwnProperty(id)) {
                throw new Error('checkOut(): course is already checked out, course id = ' + id);
            }
            course = this._available[id];
            delete this._available[id];
            this._unavailable[id] = course;

            return this._unavailable[id];
        },

        /**
         * Retrieves a course from the repo by its identifier.
         * Please note that this is not the same as checking out a course, it just simply returns the course
         * without changing its availability status within the repo.
         *
         * @method get
         * @param {Number} id The course id.
         * @throws {Error} Throws an error if the course cannot be found
         * @return {ilios.cim.model.CourseModel} The course object.
         */
        get: function (id) {
            if (this._available.hasOwnProperty(id)) {
                return this._available[id];
            } else if (this._unavailable.hasOwnProperty(id)) {
                return this._unavailable[id];
            } else {
                throw new Error('get(): course does not exist in repo, course id = ' + id);
            }
        },

        /**
         * Checks a given course into the repo, and flags it as available.
         * If the course has already been checked-in, then it is simply returned without a status change.
         *
         * @method checkIn
         * @param {Number} id The course id.
         * @return {ilios.cim.model.CourseModel} The checked-in course.
         * @throw {Error} Throws an error if the course cannot be found or has already been checked in.
         */
        checkIn: function (id) {
            var course;
            if (! this.exists(id)) {
                throw new Error('checkOut(): course does not exist in repo, course id = ' + id);
            }
            if (! this._unavailable.hasOwnProperty(id)) {
                throw new Error('checkOut(): course is already checked in, course id = ' + id);
            }
            course = this._unavailable[id];
            delete this._unavailable[id];
            this._available[id] = course;
            return this._available[id];
        },

        /**
         * Retrieves a sorted list of available courses that fulfill the "linkable" criteria.
         * These criteria are:
         * - the course must not be deleted
         * - the course must not be in "draft" mode
         *
         * @method listLinkable
         * @return {Array} A list of sorted courses.
         */
        listLinkable : function () {
            var i, course, rhett;
            rhett  = [];
            for (i in this._available) {
                if (this._available.hasOwnProperty(i)) {
                    course = this._available[i];
                    if (! course.get('isPublished')  || course.get('deleted')) {
                        continue;
                    }
                    rhett.push(course);
                }
            }
            rhett.sort(function (a, b) {
                return a.get('title').localeCompare(b.get('title'));
            });

            return rhett;
        }
    };

    ilios.cim.model.AcademicLevelModel = AcademicLevelModel;
    ilios.cim.model.BaseModel = BaseModel;
    ilios.cim.model.CourseModel = CourseModel;
    ilios.cim.model.ReportModel = ReportModel;
    ilios.cim.model.SequenceBlockModel = SequenceBlockModel;
    ilios.cim.model.ObjectMap = ObjectMap;
    ilios.cim.model.SequenceBlockModelMap = SequenceBlockModelMap;
    ilios.cim.model.CourseRepository = CourseRepository;
}());
