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
         * @returns {String} The client id.
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
             */
            this.setAttributeConfig('id', {
                value: id,
                writeOnce: true
            });

            var clientId = this.generateClientId();

            /**
             * The model's client-side id. Gets generated on instantiation.
             * @attribute clientId
             * @type {String}
             */
            this.setAttributeConfig('clientId', {
                writeOnce: true,
                validator: Lang.isString,
                value: clientId
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
            var year = oData.year;
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
                value: year
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
     *
     * @extends ilios.cim.model.BaseModel
     * @namespace cim.model
     * @class CourseModel
     * @param {Object} oData A key/value map of initial model data.
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
            // @todo implement
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
     * The academic level model.
     *
     * @extends ilios.cim.model.BaseModel
     * @namespace cim.model
     * @class AcademicLevelModel
     * @param {Object} oData A key/value map of initial model data.
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
            // @todo implement
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

    var SequenceBlockModel = function (oData) {
        SequenceBlockModel.superclass.constructor.call(this, oData);
    };

    Lang.extend(SequenceBlockModel, BaseModel, {
        /*
         * @override
         * @see ilios.cim.model.BaseModel.init
         */
        init : function (oData) {
            SequenceBlockModel.superclass.init.call(this, oData);
            var reportId = oData.report_id;
            var status = oData.status;
            var childSequenceOrder = oData.child_sequence_order;
            var orderInSequence = oData.order_in_sequence;
            var minimum = oData.minimum;
            var maximum = oData.maximum;
            var track = oData.track;
            var description = oData.description;
            var title = oData.title;
            var startDate = oData.start_date;
            var endDate = oData.end_date;
            var academicLevelId = oData.academic_level_id;
            var duration = oData.duration;
            var courseId = oData.courseId;
            var parentId = oData.parent_sequence_block_id;

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
             * Valid values are "Required" (<code>SequenceBlockModel.STATUS_REQUIRED</code>), "Optional"
             * (<code<SequenceBlockModel.STATUS_OPTIONAL</code>), and "Required in Track"
             * (<code>SequenceBlockModel.REQUIRED_IN_TRACK</code>).
             *
             * @attribute status
             * @type {Number}
             */
            this.setAttributeConfig('status', {
                value: status,
                validator: function (value) {
                    return Lang.isNumber(value)
                        && (this.STATUS_OPTIONAL === value
                        || this.STATUS_REQUIRED === value
                        || this.STATUS_REQUIRED_IN_TRACK === value);

                }
            });
            /**
             * If there are nested sequence blocks within this sequence block, <em>childSequenceOrder</em> indicates
             * how the nested sequence blocks are delivered to learners. Valid values are "Ordered"
             * (<code>SequenceBlockModel.SORT_ORDERED</code>), "Unordered" (<code>SequenceBlockModel.SORT_UNORDERED</code>)
             * , and "Parallel" (<code>SequenceBlockModel.SORT_PARALLEL</code>).
             *
             * @attribute childSequenceOrder
             * @type {Number}
             */
            this.setAttributeConfig('childSequenceOrder', {
                value: childSequenceOrder,
                validator: function (value) {
                    return Lang.isNumber(value)
                        && (this.SORT_ORDERED === value
                        || this.SORT_UNORDERED === value
                        || this.SORT_PARALLEL === value);

                }
            });

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
             * @type {String}
             */
            this.setAttributeConfig('startDate', {
                value: startDate,
                validator: Lang.isString
            });

            /**
             * The end date of the sequence block.
             *
             * @attribute endDate
             * @type {String}
             */
            this.setAttributeConfig('endDate', {
                value: endDate,
                validator: Lang.isString
            });

            /**
             * The id of the academic level that this sequence block is set to.
             *
             * @attribute academicLevel
             * @type {Number}
             */
            this.setAttributeConfig('academicLevelId', {
                value: academicLevelId,
                validator: Lang.isNumber
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
             * The id of the course that this sequence block may be associated with.
             *
             * @attribute courseId
             * @type {Number|null}
             */
            this.setAttributeConfig('courseId', {
                value: courseId,
                validator: function (value) {
                    return (Lang.isNull(value) || Lang.isNumber(value));
                }
            });

            /**
             * The id of parent sequence block to this sequence block.
             * Top-level sequence blocks have a parent id value of <code>NULL</code>.
             *
             * @attribute parentId
             * @type {Number|null}
             */
            this.setAttributeConfig('parentId', {
                value: parentId,
                validator: function (value) {
                    return (Lang.isNull(value) || Lang.isNumber(value));
                }
            });

            // create custom event
            this.createEvent('delete');
        },

        /**
         * Lifecycle management method.
         * Fires the "delete" event, then unsubscribes all registered event listeners from the model object.
         *
         * @method delete
         */
        delete: function () {
            this.fireEvent(this.EVT_DELETE);
            this.unsubscribeAll();
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
         * "Required" status indicator.
         *
         * @property STATUS_REQUIRED
         * @type {Number}
         * @final
         *
         */
        STATUS_REQUIRED: 1,

        /**
         * "Optional" status indicator.
         *
         * @property STATUS_OPTIONAL
         * @type {Number}
         * @final
         */
        STATUS_OPTIONAL: 2,

        /**
         * "Required in Track" status indicator.
         *
         * @property STATUS_REQUIRED_IN_TRACK
         * @type {Number}
         * @final
         */
        STATUS_REQUIRED_IN_TRACK: 3,

        /**
         * "Ordered" child-sequences indicator.
         *
         * @property SORT_ORDER
         * @type {Number}
         * @final
         */
        SORT_ORDERED: 1,

        /**
         * "Unordered" child-sequences indicator.
         *
         * @property SORT_UNORDERED
         * @type {Number}
         * @final
         */
        SORT_UNORDERED: 2,

        /**
         * "Parallel" child-sequences indicator.
         *
         * @property SORT_PARALLEL
         * @type {Number}
         * @final
         */
        SORT_PARALLEL: 3,

        /**
         * Fired when this sequence block instance is being deleted.
         * @event delete
         * @final
         */
        EVT_DELETE: 'delete'
    });

    ilios.cim.model.AcademicLevelModel= AcademicLevelModel;
    ilios.cim.model.BaseModel = BaseModel;
    ilios.cim.model.CourseModel = CourseModel;
    ilios.cim.model.ReportModel = ReportModel;
    ilios.cim.model.SequenceBlockModel = SequenceBlockModel;
}());
