/**
 * Client-side application code for the curriculum inventory management (cim) module.
 *
 * Defines the following namespaces:
 *     ilios.cim
 *     ilios.cim.model
 *     ilios.cim.view
 *     ilios.cim.widget
 *
 *  Dependencies:
 *     application/views/scripts/ilios_base.js
 *     ilios_i18nVendor
 *     YUI Dom/Event/Element libs
 *     YUI Container libs
 */
ilios.namespace('cim.model');
ilios.namespace('cim.view');
ilios.namespace('cim.widget');


(function () {

    var Lang = YAHOO.lang,
        Event = YAHOO.util.Event;
    /**
     * Creates a client-side application object.
     * It sets up data model, instantiates and wires up views and dialogs.
     * @param {Object} config App. configuration.
     * @param {Object} [payload] The initial page payload. It may have these data points as properties:
     *     "programs" ... an object holding the programs available for reporting on.
     *     "report" ... (optional) an object representing the currently selected report.
     *     "courses" ... (optional) An lookup object (keyed off by course id) holding linked and linkable courses
     *         with/for sequence blocks in this report.
     *     "sequence" ... (optional) An object representing the report sequence.
     *     "sequence_blocks" ... (optional) An array of report sequence blocks.
     *     "academic_levels" ... (optional) An array of academic levels available in the given report.
     * @constructor
     */
    var App = function (config, payload) {

        // set module configuration
        this.config = Lang.isObject(config) ? config : {};

        // wire dialogs to buttons
        Event.addListener('pick_reports_btn', 'click', function (event) {
            if (! this.reportPickerDialog) { // instantiate on demand
                this.reportPickerDialog = new ilios.cim.widget.ReportPickerDialog('report_picker_dialog');
            }
            this.reportPickerDialog.show();
            Event.stopEvent(event);
            return false;
        }, {}, this);

        Event.addListener('create_report_btn', 'click', function (event) {
            if (! this.createReportDialog) {
                this.createReportDialog = new ilios.cim.widget.CreateReportDialog('create_report_dialog', {}, this.programs);
            }
            this.createReportDialog.show();
            Event.stopEvent(event);
            return false;
        }, {}, this);

        this.programs = payload.programs;

        if (payload.hasOwnProperty('report')) {
            this.reportModel = new ilios.cim.model.ReportModel(payload.report);
            this.reportView = new ilios.cim.view.ReportView(this.reportModel);
            this.academicLevels = payload.academic_levels;
            this.sequenceBlock = payload.sequence_block;
            this.sequenceBlocks = payload.sequence_blocks;
            this.linkableCourses = payload.linkable_courses;
            this.linkedCourses = payload.linked_courses;
            this.reportView.render();
            this.reportView.show();
        }
    };
    ilios.cim.App = App;
}());

//
// model sub-module
//
(function () {

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

            var id = data.hasOwnProperty('id') ? data.id : null;

            this.setAttributeConfig('id', {
                value: id
            });

            var clientId = this.generateClientId();
            this.setAttributeConfig('clientId', {
                writeOnce: true,
                validator: Lang.isString,
                value: clientId
            });

            this.createEvent('change');
        },
        NAME: 'baseModel',
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
        },
        NAME: 'curriculumInventoryReport'
    });

    ilios.cim.model.BaseModel = BaseModel;
    ilios.cim.model.ReportModel = ReportModel;
}());

//
// views sub-module
//
(function () {
    var Lang = YAHOO.lang,
        Dom = YAHOO.util.Dom,
        Element = YAHOO.util.Element,
        Event = YAHOO.util.Event;

    var ReportView = function (model, oConfig) {
        ReportView.superclass.constructor.call(this, document.getElementById('report-details-view-container'), oConfig);
        this.model = model;
    };

    Lang.extend(ReportView, Element, {
        initAttributes : function (config) {
            ReportView.superclass.initAttributes.call(this, config);

            this.setAttributeConfig('nameEl', {
                writeOnce: true,
                value: Dom.get('report-details-view-name')
            });
            this.setAttributeConfig('academicYearEl', {
                writeOnce: true,
                value: Dom.get('report-details-view-academic-year')
            });
            this.setAttributeConfig('startDateEl', {
                writeOnce: true,
                value: Dom.get('report-details-view-start-date')
            });
            this.setAttributeConfig('endDateEl', {
                writeOnce: true,
                value: Dom.get('report-details-view-end-date')
            });
            this.setAttributeConfig('descriptionEl', {
                writeOnce: true,
                value: Dom.get('report-details-view-description')
            });
            this.setAttributeConfig('programEl', {
                writeOnce: true,
                value: Dom.get('report-details-view-program')
            });

            this.setAttributeConfig('name', {
                validator: Lang.isString,
                method: function (value) {
                    var el = this.get('nameEl');
                    if (el) {
                        el.innerHTML = value;
                    }
                },
                value: ''
            });
            this.setAttributeConfig('academicYear', {
                validator: Lang.isString,
                method: function (value) {
                    var el = this.get('academicYearEl');
                    if (el) {
                        el.innerHTML = value;
                    }
                },
                value: ''
            });
            this.setAttributeConfig('startDate', {
                validator: Lang.isString,
                method: function (value) {
                    var el = this.get('startDateEl');
                    if (el) {
                        el.innerHTML = value;
                    }
                },
                value: ''
            });
            this.setAttributeConfig('endDate', {
                validator: Lang.isString,
                method: function (value) {
                    var el = this.get('endDateEl');
                    if (el) {
                        el.innerHTML = value;
                    }
                },
                value: ''
            });
            this.setAttributeConfig('description', {
                validator: Lang.isString,
                method: function (value) {
                    var el = this.get('descriptionEl');
                    if (el) {
                        el.innerHTML = value;
                    }
                },
                value: ''
            });
            this.setAttributeConfig('program', {
                validator: Lang.isObject,
                method: function (value) {
                    var el = this.get('programEl');
                    if (el) {
                        el.innerHTML = value.title + " (" + value.short_title + ")"
                    }
                }
            });
        },
        render: function () {
            //
            this.set('name', this.model.get('name'));
            this.set('description', this.model.get('description'));
            this.set('academicYear', this.model.get('academicYear'));
            this.set('startDate', this.model.get('startDate'));
            this.set('endDate', this.model.get('endDate'));
            this.set('program', this.model.get('program'));
            //this.set('exportLink', this.model.get('id'));

            //
            // wire dialog buttons
            //
            Event.addListener('report-details-view-toggle', 'click', function (event) {
                ilios.utilities.toggle('report-details-view-content-wrapper', this);
                return false;
            });
            Event.addListener('report-details-view-edit-button', 'click', function(event, obj) {
                if (! this.editReportDialog) {
                    this.editReportDialog = new ilios.cim.widget.EditReportDialog('edit_report_dialog');
                }
                this.editReportDialog.setModel(this.model);
                this.editReportDialog.show();
                Event.stopEvent(event);
                return false;
            },{}, this);

            // enable buttons
            (new YAHOO.util.Element('report-details-view-edit-button')).set('disabled', false);
            (new YAHOO.util.Element('report-details-view-export-button')).set('disabled', false);
         },
        show: function () {
            this.setStyle('display', 'block');
        }
    });
    ilios.cim.view.ReportView = ReportView;
}());

//
// widgets sub-module
//
(function () {

    /**
     * "Create Report" dialog.
     * @namespace ilios.cim.widget
     * @class CreateReportDialog
     * @extends YAHOO.widget.Dialog
     * @constructor
     * @param {HTMLElement|String} el The element or element-ID representing the dialog
     * @param {Object} userConfig The configuration object literal containing
     *     the configuration that should be set for this dialog.
     * @param {Object} programs a lookup object of programs, used to populate the "program" drop-down.
     */
    var CreateReportDialog = function (el, userConfig, programs) {
        var defaultConfig = {
            width: "640px",
            modal: true,
            fixedcenter: true,
            visible: false,
            hideaftersubmit: false,
            zIndex: 999,
            buttons: [
                {
                    text: ilios_i18nVendor.getI18NString('general.terms.create'),
                    handler: function () {
                        this.submit();
                    },
                    isDefault: true
                },
                {
                    text: ilios_i18nVendor.getI18NString('general.terms.cancel'),
                    handler: function () {
                        this.reset();
                        this.cancel();
                    }
                }
            ]
        };

        this.programs = YAHOO.lang.isObject(programs) ? programs : {};

        // merge the user config with the default configuration
        userConfig = userConfig || {};
        var config = YAHOO.lang.merge(defaultConfig, userConfig);

        // call the parent constructor with the merged config
        CreateReportDialog.superclass.constructor.call(this, el, config);

        // clear out the dialog and center it before showing it.
        this.beforeShowEvent.subscribe(function () {
            this.reset();
            this.center();
        });

        // append the program as options to the dropdown
        this.renderEvent.subscribe(function () {
            var Dom = YAHOO.util.Dom,
                key, program,
                el, parentEl;

            var parentEl = document.getElementById('new_report_program');
            for (key in this.programs) {
                if (this.programs.hasOwnProperty(key)) {
                    program = this.programs[key];
                    el = document.createElement('option');
                    Dom.setAttribute(el, 'value', program.program_id);
                    el.innerHTML = program.title;
                    parentEl.appendChild(el);
                }
            }
        });

        this.beforeSubmitEvent.subscribe(function () {
            document.getElementById('report_creation_status').innerHTML = ilios_i18nVendor.getI18NString('general.terms.creating') + '...';
        });

        /*
         * Form submission success handler.
         * @param {Object} resultObject
         */
        this.callback.success = function (resultObject) {
            var parsedResponse;
            try {
                parsedResponse = YAHOO.lang.JSON.parse(resultObject.responseText);
            } catch (e) {
                document.getElementById('report_creation_status').innerHTML
                    = ilios_i18nVendor.getI18NString('general.error.must_retry');
                return;
            }

            document.getElementById('report_creation_status').innerHTML = '';

            if (parsedResponse.hasOwnProperty('error')) {
                document.getElementById('report_creation_status').innerHTML = parsedResponse.error;
                return;
            }
            // redirect to report details view
            document.getElementById('report_creation_status').innerHTML
                = ilios_i18nVendor.getI18NString('general.terms.created') + '.';
            window.location = window.location.protocol + "//" + window.location.host + window.location.pathname
                + '?report_id=' + parsedResponse.report_id;
        };

        /*
         * Form submission error handler.
         * @param {Object} resultObject
         */
        this.callback.failure = function (resultObject) {
            ilios.global.defaultAJAXFailureHandler(resultObject);
            document.getElementById('report_creation_status').innerHTML
                = ilios_i18nVendor.getI18NString('general.error.must_retry');
        }

        // form validation function
        this.validate = function () {
            var Dom = YAHOO.util.Dom;
            var data = this.getData();
            var msgs = [];
            if ('' === YAHOO.lang.trim(data.report_name)) {
                msgs.push(ilios_i18nVendor.getI18NString('curriculum_inventory.create.validate.report_name'));
                Dom.addClass('new_report_name', 'validation-failed');
            }
            if ('' === YAHOO.lang.trim(data.report_description)) {
                msgs.push(ilios_i18nVendor.getI18NString('curriculum_inventory.create.validate.report_description'));
                Dom.addClass('new_report_description', 'validation-failed');
            }

            if (! /^[1-9][0-9]{3}$/.test(data.report_year)) {
                msgs.push(ilios_i18nVendor.getI18NString('curriculum_inventory.create.validate.report_year'));
                Dom.addClass('new_report_year', 'validation-failed');
            }

            if (! data.program_id[0]) {
                msgs.push(ilios_i18nVendor.getI18NString('curriculum_inventory.create.validate.program'));
                Dom.addClass('new_report_program', 'validation-failed');
            }

            if (msgs.length) {
                document.getElementById('report_creation_status').innerHTML = msgs.join('<br />') + '<br />';
                return false;
            }
            return true;
        };

        this.render();
    };

    YAHOO.lang.extend(CreateReportDialog, YAHOO.widget.Dialog, {
        // clear out form, reset status field etc.
        reset : function () {
            var Dom = YAHOO.util.Dom;
            document.getElementById('report_creation_status').innerHTML = '';
            document.getElementById('new_report_name').value = '';
            document.getElementById('new_report_description').value = '';
            document.getElementById('new_report_year').value = '';
            document.getElementById('new_report_program').selectedIndex = 0;
            Dom.removeClass('new_report_name', 'validation-failed');
            Dom.removeClass('new_report_description', 'validation-failed');
            Dom.removeClass('new_report_year', 'validation-failed');
            Dom.removeClass('new_report_program', 'validation-failed');
        }
    });

    /**
     * "Edit Report" dialog.
     * @namespace ilios.cim.widget
     * @class EditReportDialog
     * @extends YAHOO.widget.Dialog
     * @constructor
     * @param {HTMLElement|String} el The element or element-ID representing the dialog
     * @param {Object} userConfig The configuration object literal containing
     *     the configuration that should be set for this dialog.
     */
    EditReportDialog = function (el, userConfig){
        var defaultConfig = {
            width: "640px",
            modal: true,
            fixedcenter: true,
            visible: false,
            zIndex: 999,
            buttons: [
                {
                    text: ilios_i18nVendor.getI18NString('general.terms.done'),
                    handler: function () {
                        // @todo implement
                        this.cancel();
                    },
                    isDefault: true
                },
                {
                    text: ilios_i18nVendor.getI18NString('general.terms.cancel'),
                    handler: function () {
                       // @todo implement
                        this.cancel();
                    }
                }
            ]
        };

        // merge the user config with the default configuration
        userConfig = userConfig || {};
        var config = YAHOO.lang.merge(defaultConfig, userConfig);

        // call the parent constructor with the merged config
        EditReportDialog.superclass.constructor.call(this, el, config);

        // session model
        this.model = null;

        this.render();
    };

    // inheritance
    YAHOO.lang.extend(EditReportDialog, YAHOO.widget.Dialog, {
        /**
         * Sets the internal model for this dialog.
         * @method setModel
         * @param {Object} model
         */
        setModel : function (model) {
            this.model = model;
        }
    });

    /**
     * "Search Reports" dialog.
     * @namespace ilios.cim.widget
     * @class ReportPickerDialog
     * @extends YAHOO.widget.Dialog
     * @constructor
     * @param {HTMLElement|String} el The element or element-ID representing the dialog
     * @param {Object} userConfig The configuration object literal containing
     *     the configuration that should be set for this dialog.
     */
    var ReportPickerDialog = function (el, userConfig) {

        var Event = YAHOO.util.Event;
        var KEY = YAHOO.util.KeyListener.KEY;

        var defaultConfig = {
            width: "600px",
            modal: true,
            visible: false,
            constraintoviewport: false,
            hideaftersubmit: false,
            zIndex: 999,
            buttons: [
                {
                    text: ilios_i18nVendor.getI18NString('general.terms.cancel'),
                    handler: function () {
                        this.cancel();
                    }
                }
            ]
        };
        // merge the user config with the default configuration
        userConfig = userConfig || {};
        var config = YAHOO.lang.merge(defaultConfig, userConfig);

        // call the parent constructor with the merged config
        ReportPickerDialog.superclass.constructor.call(this, el, config);

        // center dialog before showing it.
        this.beforeShowEvent.subscribe(function () {
            this.center();
        });

        this.render();
    };

    // inheritance
    YAHOO.lang.extend(ReportPickerDialog, YAHOO.widget.Dialog);

    ilios.cim.widget.CreateReportDialog = CreateReportDialog;
    ilios.cim.widget.ReportPickerDialog = ReportPickerDialog;
    ilios.cim.widget.EditReportDialog = EditReportDialog;
}());
