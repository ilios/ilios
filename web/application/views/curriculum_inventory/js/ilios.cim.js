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
 *     YUI Cookie lib
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

        this.statusView = new ilios.cim.view.StatusView();
        this.statusView.render('status-toolbar');

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
            this.reportView = new ilios.cim.view.ReportView(this.reportModel, {
                finalizeUrl: this.config.controllerUrl + 'finalize',
                deleteUrl: this.config.controllerUrl + 'delete'
            });
            this.reportView.subscribe('exportStarted', function() {
                this.show('Started Report Export &hellip;', true);
            }, this.statusView, true);
            this.reportView.subscribe('exportFinished', function () {
                this.reset();
            }, this.statusView, true);
            this.reportModel.subscribe('afterUpdate', function () {
                this.show('Report updated.', false);
            }, this.statusView, true);
            this.academicLevels = payload.academic_levels;
            this.sequenceBlock = payload.sequence_block;
            this.sequenceBlocks = payload.sequence_blocks;
            this.linkableCourses = payload.linkable_courses;
            this.linkedCourses = payload.linked_courses;
            this.reportView.render();
            this.reportView.show();
        }
    };

    App.getStatusView = function () {
        return this.statusView;
    };

    App.getConfig = function () {
        return this.config;
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

//
// views sub-module
//
(function () {
    var Lang = YAHOO.lang,
        Dom = YAHOO.util.Dom,
        Element = YAHOO.util.Element,
        Event = YAHOO.util.Event,
        Cookie = YAHOO.util.Cookie;

    var StatusView = function (oConfig) {
        StatusView.superclass.constructor.call(this, document.createElement('div'), oConfig);
    };

    Lang.extend(StatusView, Element, {
        initAttributes: function (config) {
            StatusView.superclass.initAttributes.call(this, config);

            var container = this.get('element');

            this.setAttributeConfig('progressEl', {
                writeOnce: true,
                value: container.appendChild(document.createElement('div'))
            });

            this.setAttributeConfig('messageEl', {
                writeOnce: true,
                value: container.appendChild(document.createElement('span'))
            });

            this.setAttributeConfig('message', {
                validator: Lang.isString,
                method: function (value) {
                    var el = this.get('messageEl');
                    if (el) {
                        el.innerHTML = value;
                    }
                },
                value: ''
            });
        },
        render: function (parentEl) {
            parentEl = Dom.get(parentEl);
            var containerEl = this.get('element');
            this.addClass('status-view-container');
            this.setStyle('display', 'none');
            var progressEl = this.get('progressEl');
            Dom.addClass(progressEl, 'in-progress-indicator');
            Dom.setStyle(progressEl, 'display', 'none');
            parentEl.appendChild(containerEl);
        },
        reset: function () {
            this.show('', false)
        },
        show: function (message, showProgressIndicator) {
            showProgressIndicator = showProgressIndicator || false;
            Dom.setStyle(this.get('progressEl'), 'display', (showProgressIndicator ? 'inline-block' : 'none'));
            this.set('message', message);
            this.setStyle('display', 'block');
        },
        hide: function () {
            this.setStyle('display', 'none');
        }
    });

    ilios.cim.view.StatusView = StatusView;

    /**
     * The view for a given report model.
     * @namespace ilios.cim.view
     * @class ReportView
     * @constructor
     * @extends YAHOO.util.Element
     * @param {ilios.cim.model.ReportModel} model The report model.
     * @param {Object} oConfig A configuration object.
     */
    var ReportView = function (model, oConfig) {
        ReportView.superclass.constructor.call(this, document.getElementById('report-details-view-container'));

        this.config = oConfig;
        this.model = model;

        // subscribe to model changes
        this.model.subscribe('nameChange', this.onNameChange, {}, this);
        this.model.subscribe('descriptionChange', this.onDescriptionChange, {}, this);
        this.model.subscribe('startDateChange', this.onStartDateChange, {}, this);
        this.model.subscribe('endDateChange', this.onEndDateChange, {}, this);
        this.model.subscribe('isFinalizedChange', this.onStatusChange, {}, this);
    };

    Lang.extend(ReportView, Element, {
        _downloadIntervalTimer: null,
        _exportIntervalTimer: null,
        _blockUIForDownload: function () {
            var token = (new Date()).getTime();
            (new Element('report-details-view-download-button')).set('disabled', true);
            this.fireEvent('downloadStarted');
            this.set('downloadToken', token);
            this._downloadIntervalTimer = Lang.later(1000, this, function () {
                var cookieValue = Cookie.get('download-token');
                if (cookieValue == token) {
                    this._finishDownload();
                }
            }, [], true);
        },
        _finishDownload: function () {
            this._downloadIntervalTimer.cancel();
            Cookie.remove('fileDownloadToken');
            (new Element('report-details-view-download-button')).set('disabled', false);
            this.fireEvent('downloadFinished');
        },
        _blockUIForExport: function () {
            var token = (new Date()).getTime();
            (new Element('report-details-view-export-button')).set('disabled', true);
            this.fireEvent('exportStarted');
            this.set('exportToken', token);
            this._exportIntervalTimer = Lang.later(1000, this, function () {
                var cookieValue = Cookie.get('download-token');
                if (cookieValue == token) {
                    this._finishExport();
                }
            }, [], true);
        },
        _finishExport: function () {
            this._exportIntervalTimer.cancel();
            Cookie.remove('fileExportToken');
            (new Element('report-details-view-export-button')).set('disabled', false);
            this.fireEvent('exportFinished');
        },
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
            this.setAttributeConfig('reportExportIdEl', {
                writeOnce: true,
                value: Dom.get('report-details-view-export-report-id')
            });
            this.setAttributeConfig('reportDownloadIdEl', {
                writeOnce: true,
                value: Dom.get('report-details-view-download-report-id')
            });
            this.setAttributeConfig('downloadTokenEl', {
                writeOnce: true,
                value: Dom.get('report-details-view-download-download-token')
            });
            this.setAttributeConfig('exportTokenEl', {
                writeOnce: true,
                value: Dom.get('report-details-view-export-download-token')
            });
            this.setAttributeConfig('statusEl', {
                writeOnce: true,
                value: Dom.get('report-details-status')
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
            this.setAttributeConfig('reportId', {
                validator: Lang.isString,
                method: function (value) {
                    var el = this.get('reportExportIdEl');
                    if (el) {
                        el.value = value;
                    }
                    el = this.get('reportDownloadIdEl');
                    if (el) {
                        el.value = value;
                    }
                },
                value: ''
            });
            this.setAttributeConfig('downloadToken', {
                method: function (value) {
                    var el = this.get('downloadTokenEl');
                    if (el) {
                        el.value = value;
                    }
                }
            });
            this.setAttributeConfig('exportToken', {
                method: function (value) {
                    var el = this.get('exportTokenEl');
                    if (el) {
                        el.value = value;
                    }
                }
            });

            this.setAttributeConfig('status', {
                method: function (value) {
                    var el;
                    if (value) {
                        el = this.get('statusEl');
                        Dom.removeClass(el, 'is-draft');
                        Dom.addClass(el, 'is-locked');
                        el.innerHTML = ilios_i18nVendor.getI18NString('general.terms.finalized');
                        // enabled/show buttons and forms
                        (new Element('report-details-view-download-button')).set('disabled', false);
                        Dom.removeClass('report-details-view-download-form', 'hidden');
                        // disabled/hide buttons and forms
                        el = new Element('report-details-view-edit-button');
                        el.set('disabled', false);
                        el.addClass('hidden');
                        el = new Element('report-details-view-delete-button');
                        el.set('disabled', false);
                        el.addClass('hidden');
                        el = new Element('report-details-view-delete-button');
                        el.set('disabled', false);
                        el.addClass('hidden');
                        el = new Element('report-details-view-finalize-button');
                        el.set('disabled', false);
                        el.addClass('hidden');
                        el = new Element('report-details-view-export-button');
                        el.set('disabled', false);
                        Dom.addClass('report-details-view-export-form', 'hidden');
                    } else {
                        el = this.get('statusEl');
                        Dom.removeClass(el, 'is-locked');
                        Dom.addClass(el, 'is-draft');
                        el.innerHTML = ilios_i18nVendor.getI18NString('general.terms.draft');
                        // enabled/show buttons and forms
                        el = new Element('report-details-view-edit-button');
                        el.set('disabled', false);
                        el.removeClass('hidden');
                        el = new Element('report-details-view-finalize-button');
                        el.set('disabled', false);
                        el.removeClass('hidden');
                        el = new Element('report-details-view-delete-button');
                        el.set('disabled', false);
                        el.removeClass('hidden');
                        el = new Element('report-details-view-export-button');
                        el.set('disabled', false);
                        Dom.removeClass('report-details-view-export-form', 'hidden');
                        // disabled/hide buttons and forms
                        (new Element('report-details-view-download-button')).set('disabled', true);
                        Dom.addClass('report-details-view-download-form', 'hidden');
                    }
                }
            });
            this.createEvent('exportStarted');
            this.createEvent('exportFinished');
            this.createEvent('downloadStarted');
            this.createEvent('downloadFinished');
        },
        render: function () {

            this.set('name', this.model.get('name'));
            this.set('description', this.model.get('description'));
            this.set('academicYear', this.model.get('academicYear'));
            this.set('startDate', this.model.get('startDate'));
            this.set('endDate', this.model.get('endDate'));
            this.set('program', this.model.get('program'));
            this.set('reportId', this.model.get('id'));
            this.set('status', this.model.get('isFinalized'));

            //
            // wire dialog buttons
            //
            if (! this.model.get('isFinalized')) {
                Event.addListener('report-details-view-edit-button', 'click', function(event) {
                    if (! this.editReportDialog) {
                        this.editReportDialog = new ilios.cim.widget.EditReportDialog('edit_report_dialog', this.model);
                    }
                    this.editReportDialog.show();
                    Event.stopEvent(event);
                    return false;
                }, {}, this);
                Event.addListener('report-details-view-export-form', 'submit', this._blockUIForExport, {}, this);

                Event.addListener('report-details-view-finalize-button', 'click', function (event, args) {
                    var continueStr = ilios_i18nVendor.getI18NString('general.phrases.want_to_continue');
                    var yesStr = ilios_i18nVendor.getI18NString('general.terms.yes');
                    ilios.alert.inform(continueStr, yesStr, function (event, args) {
                        var model = args.model;
                        var url = args.url;
                        var postData = 'report_id=' + encodeURIComponent(model.get('id'));
                        var callback = {
                            success: function (o) {
                                var response;
                                try {
                                    response = YAHOO.lang.JSON.parse(o.responseText);
                                } catch (e) {
                                    ilios.global.defaultAJAXFailureHandler(null, e);
                                    return;
                                }
                                if (response.error) {
                                    var msg = ilios_i18nVendor.getI18NString('curriculum_inventory.finalize.error.general');
                                    ilios.alert.alert(msg + ": " + response.error);
                                    return;
                                }
                                model.set('isFinalized', true);

                            },

                            failure: function (resultObject) {
                                ilios.global.defaultAJAXFailureHandler(resultObject);
                            },
                            argument: {model : model}
                        };

                        this.hide(); // hide the calling dialog

                        YAHOO.util.Connect.initHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
                        YAHOO.util.Connect.asyncRequest("POST", url, callback, postData);
                    }, args);
                }, {
                    model: this.model,
                    url: this.config.finalizeUrl
                },
                this);
            }
            Event.addListener('report-details-view-toggle', 'click', function (event) {
                ilios.utilities.toggle('report-details-view-content-wrapper', this);
                Event.stopEvent(event);
                return false;
            });
            Event.addListener('report-details-view-download-form', 'submit', this._blockUIForDownload, {}, this);
        },
        show: function () {
            this.setStyle('display', 'block');
        },
        onNameChange: function (evObj) {
            this.set('name', evObj.newValue);
        },

        onDescriptionChange: function (evObj) {
            this.set('description', evObj.newValue);
        },
        onStartDateChange: function (evObj) {
            this.set('startDate', evObj.newValue);
        },
        onEndDateChange: function (evObj) {
            this.set('endDate', evObj.newValue);
        },
        onStatusChange: function (evObj) {
            this.set('status', evObj.newValue);
        }
    });
    ilios.cim.view.ReportView = ReportView;
}());

//
// widgets sub-module
//
(function () {

    var Event = YAHOO.util.Event,
        CustomEvent = YAHOO.util.CustomEvent,
        Dom = YAHOO.util.Dom,
        Lang = YAHOO.lang;

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
                        this.cancel();
                    }
                }
            ]
        };

        this.programs = Lang.isObject(programs) ? programs : {};

        // merge the user config with the default configuration
        userConfig = userConfig || {};
        var config = Lang.merge(defaultConfig, userConfig);

        // call the parent constructor with the merged config
        CreateReportDialog.superclass.constructor.call(this, el, config);

        // clear out the dialog and center it before showing it.
        this.beforeShowEvent.subscribe(function () {
            this.reset();
            this.center();
        });

        // clear out the dialog and center it before showing it.
        this.cancelEvent.subscribe(function () {
            this.reset();
        });

        // append the program as options to the dropdown
        this.renderEvent.subscribe(function () {
            var key, program,
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
                parsedResponse = Lang.JSON.parse(resultObject.responseText);
            } catch (e) {
                document.getElementById('report_creation_status').innerHTML
                    = ilios_i18nVendor.getI18NString('curriculum_inventory.create.error.general');
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
                = ilios_i18nVendor.getI18NString('curriculum_inventory.create.error.general');
        }

        // form validation function
        this.validate = function () {
            var data = this.getData();
            var msgs = [];
            if ('' === Lang.trim(data.report_name)) {
                msgs.push(ilios_i18nVendor.getI18NString('curriculum_inventory.create.validate.report_name'));
                Dom.addClass('new_report_name', 'validation-failed');
            }
            if ('' === Lang.trim(data.report_description)) {
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

    Lang.extend(CreateReportDialog, YAHOO.widget.Dialog, {
        // clear out form, reset status field etc.
        reset : function () {
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
     * @param {ilios.cim.model.ReportModel} model The report model.
     * @param {Object} userConfig The configuration object literal containing
     *     the configuration that should be set for this dialog.
     */
    var EditReportDialog = function (el, model, userConfig){

        var defaultConfig = {
            width: "640px",
            modal: true,
            fixedcenter: true,
            visible: false,
            hideaftersubmit: false,
            zIndex: 999,
            buttons: [
                {
                    text: ilios_i18nVendor.getI18NString('general.terms.save'),
                    handler: function () {
                       this.submit();
                    },
                    isDefault: true
                },
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
        var config = Lang.merge(defaultConfig, userConfig);

        // call the parent constructor with the merged config
        EditReportDialog.superclass.constructor.call(this, el, config);

        // report model
        this.model = model;

        // calendar widgets
        this.cal1 = new YAHOO.widget.Calendar(null, 'edit_report_start_date_calendar_container', {
            selected: this.model.get('startDate'),
            iframe: false,
            close: true,
            pagedate: new Date(this.model.get('startDate'))
        });
        this.cal2 = new YAHOO.widget.Calendar(null, 'edit_report_end_date_calendar_container', {
            selected: this.model.get('endDate'),
            iframe: false,
            close: true,
            pagedate: new Date(this.model.get('endDate'))
        });

        this.beforeRenderEvent.subscribe(function () {

            this.cal1.selectEvent.subscribe(this.selectCalendar, {
                calendar: this.cal1,
                targetEl: document.getElementById('edit_report_start_date')
            });
            this.cal1.render();

            this.cal2.selectEvent.subscribe(this.selectCalendar, {
                calendar: this.cal2,
                targetEl: document.getElementById('edit_report_end_date')
            });
            this.cal2.render();

            Event.addListener('edit_report_start_date_button', 'click', this.onCalendarButtonClick, {
                calendar: this.cal1
            }, this);
            Event.addListener('edit_report_end_date_button', 'click', this.onCalendarButtonClick, {
                calendar: this.cal2
            }, this);
        });

        // clear out the dialog and center it before showing it.
        this.beforeShowEvent.subscribe(function () {
            this.reset();
            this.populateForm();
            this.center();
        });

        this.beforeSubmitEvent.subscribe(function () {
            document.getElementById('report_update_status').innerHTML = ilios_i18nVendor.getI18NString('general.terms.updating') + '...';
        });

        this.cancelEvent.subscribe(function () {
            this.resetCalendars();
            this.reset();
        });

        this.validate = function () {
            var data = this.getData();
            var msgs = [];
            if ('' === Lang.trim(data.report_name)) {
                msgs.push(ilios_i18nVendor.getI18NString('curriculum_inventory.create.validate.report_name'));
                Dom.addClass('edit_report_name', 'validation-failed');
            }
            if ('' === Lang.trim(data.report_description)) {
                msgs.push(ilios_i18nVendor.getI18NString('curriculum_inventory.create.validate.report_description'));
                Dom.addClass('edit_report_description', 'validation-failed');
            }

            if (msgs.length) {
                document.getElementById('report_update_status').innerHTML = msgs.join('<br />') + '<br />';
                return false;
            }
            return true;
        };

        this.callback.success = function (resultObject) {
            var dialog = resultObject.argument;
            var model = dialog.model;
            var parsedResponse;
            try {
                parsedResponse = Lang.JSON.parse(resultObject.responseText);
            } catch (e) {
                document.getElementById('report_update_status').innerHTML
                    = ilios_i18nVendor.getI18NString('curriculum_inventory.update.error.general');
                return;
            }

            document.getElementById('report_update_status').innerHTML = '';

            if (parsedResponse.hasOwnProperty('error')) {
                document.getElementById('report_update_status').innerHTML = parsedResponse.error;
                return;
            }
            // update the model
            model.set('name', parsedResponse.report.name);
            model.set('description', parsedResponse.report.description);
            model.set('endDate', parsedResponse.report.end_date);
            model.set('startDate', parsedResponse.report.start_date);
            model.fireEvent('afterUpdate');
            dialog.cancel();
        };

        this.callback.failure = function (resultObject) {
            ilios.global.defaultAJAXFailureHandler(resultObject);
            document.getElementById('report_update_status').innerHTML
                = ilios_i18nVendor.getI18NString('curriculum_inventory.update.error.general');
        };

        this.callback.argument = this;

        this.selectCalendar = function (type, args, obj) {
            var cal = obj.calendar;
            var el = obj.targetEl;
            var dt;
            if (args[0]) {
                dt = new Date(args[0][0][0], args[0][0][1], args[0][0][2]);
                el.value = YAHOO.util.Date.format(dt, {format: "%Y-%m-%d"});
            }
            cal.hide();
        };

        this.onCalendarButtonClick = function (event, obj) {
            obj.calendar.show();
        };

        this.render();
    };

    // inheritance
    Lang.extend(EditReportDialog, YAHOO.widget.Dialog, {
        populateForm: function () {
            document.getElementById('edit_report_name').value = this.model.get('name');
            document.getElementById('edit_report_description').value = this.model.get('description');
            document.getElementById('edit_report_id').value = this.model.get('id');
            document.getElementById('edit_report_start_date').value = this.model.get('startDate');
            document.getElementById('edit_report_end_date').value = this.model.get('endDate');
        },
        reset: function () {
            document.getElementById('report_update_status').innerHTML = '';
        },
        resetCalendars: function () {
            this.cal1.cfg.setProperty('selected', this.model.get('startDate'), false);
            this.cal1.cfg.setProperty('pagedate', new Date(this.model.get('startDate')), false);
            this.cal1.render();
            this.cal1.hide();

            this.cal2.cfg.setProperty('selected', this.model.get('endDate'), false);
            this.cal2.cfg.setProperty('pagedate', new Date(this.model.get('endDate')), false);
            this.cal2.render();
            this.cal2.hide();
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
        var config = Lang.merge(defaultConfig, userConfig);

        // call the parent constructor with the merged config
        ReportPickerDialog.superclass.constructor.call(this, el, config);

        // center dialog before showing it.
        this.beforeShowEvent.subscribe(function () {
            this.center();
        });

        this.render();
    };

    // inheritance
    Lang.extend(ReportPickerDialog, YAHOO.widget.Dialog);

    ilios.cim.widget.CreateReportDialog = CreateReportDialog;
    ilios.cim.widget.ReportPickerDialog = ReportPickerDialog;
    ilios.cim.widget.EditReportDialog = EditReportDialog;
}());
