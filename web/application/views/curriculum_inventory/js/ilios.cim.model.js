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
        isNew: function () {
            return Lang.isNull(this.get('id'));
        },
        update: function (oData) {
            var data = oData || {};
        },
        init: function (oData) {
            var data = oData || {};

            var id = data.hasOwnProperty(this.ID_ATTRIBUTE_NAME) ? data[this.ID_ATTRIBUTE_NAME] : null;

            this.setAttributeConfig('id', {
                value: id
            });

            var clientId = this.generateClientId();
            this.setAttributeConfig('clientId', {
                writeOnce: true,
                validator: Lang.isString,
                value: clientId
            });

            // this.createEvent('change');
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
        NAME: 'curriculumInventoryReport',
        ID_ATTRIBUTE_NAME: 'report_id'
    });

    ilios.cim.model.BaseModel = BaseModel;
    ilios.cim.model.ReportModel = ReportModel;
}());
