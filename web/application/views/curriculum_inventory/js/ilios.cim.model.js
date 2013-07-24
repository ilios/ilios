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

    ilios.namespace('cim.model');

    var Lang = YAHOO.lang;

    var Env = {};
    Env.instanceCounter = 0;


    // Base model object.
    var BaseModel = function (oData) {
        this.init.apply(this, arguments);
    };

    BaseModel.prototype = {
        generateClientId: function () {
            return this.getName() + '_' + (++Env.instanceCounter);
        },
        init: function (oData) {
            var data = oData || {};

            var id = data.hasOwnProperty(this.ID_ATTRIBUTE_NAME) ? data[this.ID_ATTRIBUTE_NAME] : null;

            this.setAttributeConfig('id', {
                value: id,
                writeOnce: true
            });

            var clientId = this.generateClientId();
            this.setAttributeConfig('clientId', {
                writeOnce: true,
                validator: Lang.isString,
                value: clientId
            });
        },
        NAME: 'baseModel',
        ID_ATTRIBUTE_NAME: 'dbId',
        getName: function () {
            return this.NAME;
        }
    };

    Lang.augment(BaseModel, YAHOO.util.AttributeProvider);

    var ReportModel = function (oData) {
        ReportModel.superclass.constructor.call(this, oData);
    };

    Lang.extend(ReportModel, BaseModel, {
        init : function (oData) {
            ReportModel.superclass.init.call(this, oData);
            var name = oData.name;
            var description = oData.description;
            var year = oData.year;
            var program = oData.program;
            var startDate = oData.start_date;
            var endDate = oData.end_date;
            var isFinalized = oData.is_finalized;

            this.setAttributeConfig('name', {
                value: name,
                validator: Lang.isString
            });
            this.setAttributeConfig('description', {
                value: description,
                validator: Lang.isString
            });
            this.setAttributeConfig('academicYear', {
                writeOnce: true,
                value: year
            });
            this.setAttributeConfig('program', {
                writeOnce: true,
                value: program,
                validator: Lang.isObject
            });
            this.setAttributeConfig('startDate', {
                value: startDate,
                validator: Lang.isString
            });
            this.setAttributeConfig('endDate', {
                value: endDate,
                validator: Lang.isString
            });
            this.setAttributeConfig('isFinalized', {
                value: isFinalized,
                validator: Lang.isBoolean
            });

            this.createEvent('afterUpdate');
        },
        NAME: 'curriculumInventoryReportModel',
        ID_ATTRIBUTE_NAME: 'report_id'
    });

    var CourseModel = function (oData) {
        CourseModel.superclass.constructor.call(this, oData);
    };

    Lang.extend(CourseModel, BaseModel, {
        init : function (oData) {
            CourseModel.superclass.init.call(this, oData);
            // @todo implement
        },
        NAME: 'courseModel',
        ID_ATTRIBUTE_NAME: 'course_id'
    });

    var AcademicLevelModel = function (oData) {
        AcademicLevelModel.superclass.constructor.call(this, oData);
    };

    Lang.extend(AcademicLevelModel, BaseModel, {
        init : function (oData) {
            AcademicLevelModel.superclass.init.call(this, oData);
            // @todo implement
        },
        NAME: 'curriculumInventoryAcademicLevelModel',
        ID_ATTRIBUTE_NAME: 'academic_level_id'
    });

    var SequenceBlockModel = function (oData) {
        SequenceBlockModel.superclass.constructor.call(this, oData);
    };

    Lang.extend(SequenceBlockModel, BaseModel, {
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
            var parentSequenceBlockId = oData.parent_sequence_block_id;

            this.setAttributeConfig('reportId', {
                value: reportId,
                validator: Lang.isNumber,
                writeOnce: true
            });
            this.setAttributeConfig('status', {
                value: status,
                validator: function (value) {
                    return Lang.isNumber(value)
                        && (SequenceBlockModel.STATUS_OPTIONAL === value
                        || SequenceBlockModel.STATUS_REQUIRED === value
                        || SequenceBlockModel.STATUS_REQUIRED_IN_TRACK === value);

                }
            });
            this.setAttributeConfig('childSequenceOrder', {
                value: childSequenceOrder,
                validator: function (value) {
                    return Lang.isNumber(value)
                        && (SequenceBlockModel.SORT_ORDERED === value
                        || SequenceBlockModel.SORT_UNORDERED === value
                        || SequenceBlockModel.SORT_PARALLEL === value);

                }
            });
            this.setAttributeConfig('orderInSequence', {
                value: orderInSequence,
                validator: Lang.isNumber
            });
            this.setAttributeConfig('minimum', {
                value: minimum,
                validator: Lang.isNumber
            });
            this.setAttributeConfig('maximum', {
                value: maximum,
                validator: Lang.isNumber
            });
            this.setAttributeConfig('track', {
                value: track,
                validator: Lang.isBoolean
            });
            this.setAttributeConfig('description', {
                value: description,
                validator: Lang.isString
            });
            this.setAttributeConfig('title', {
                value: title,
                validator: Lang.isString
            });
            this.setAttributeConfig('startDate', {
                value: startDate,
                validator: Lang.isString
            });
            this.setAttributeConfig('endDate', {
                value: endDate,
                validator: Lang.isString
            });
            this.setAttributeConfig('academicLevelId', {
                value: academicLevelId,
                validator: Lang.isNumber
            });
            this.setAttributeConfig('duration', {
                value: duration,
                validator: Lang.isNumber
            });
            this.setAttributeConfig('courseId', {
                value: courseId,
                validator: function (value) {
                    return (Lang.isNull(value) || Lang.isNumber(value));
                }
            });
            this.setAttributeConfig('parentSequenceBlockId', {
                value: parentSequenceBlockId,
                validator: function (value) {
                    return (Lang.isNull(value) || Lang.isNumber(value));
                }
            });

            this.createEvent('delete');
        },
        delete: function () {
            this.fireEvent('delete');
            this.unsubscribeAll();
        },

        NAME: 'curriculumInventorySequenceBlockModel',
        ID_ATTRIBUTE_NAME: 'sequence_block_id',
        STATUS_REQUIRED: 1,
        STATUS_OPTIONAL: 2,
        STATUS_REQUIRED_IN_TRACK: 3,
        SORT_ORDERED: 1,
        SORT_UNORDERED: 2,
        SORT_PARALLEL: 3
    });

    ilios.cim.model.AcademicLevelModel= AcademicLevelModel;
    ilios.cim.model.BaseModel = BaseModel;
    ilios.cim.model.CourseModel = CourseModel;
    ilios.cim.model.ReportModel = ReportModel;
    ilios.cim.model.SequenceBlockModel = SequenceBlockModel;
}());
