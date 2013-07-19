/**
 * Curriculum inventory management (cim) UI widget components.
 *
 * Defines the following namespaces:
 *     ilios.cim.widget
 *
 *  Dependencies:
 *     application/views/scripts/ilios_base.js
 *     ilios_i18nVendor
 *     YUI Dom/Event/Element libs
 *     YUI Container libs
 *     application/views/curriculum_inventory/js/ilios.cim.model.js
 */
(function () {

    ilios.namespace('cim.widget');

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
