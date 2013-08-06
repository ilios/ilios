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
        Element = YAHOO.util.Element,
        Dom = YAHOO.util.Dom,
        Lang = YAHOO.lang;

    /**
     * "Create Report" dialog.
     *
     * @namespace cim.widget
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
            Dom.removeClass(el, 'hidden');
            this.reset();
            this.center();
        });


        this.hideEvent.subscribe(function () {
            Dom.addClass(el, 'hidden');
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
    };

    Lang.extend(CreateReportDialog, YAHOO.widget.Dialog, {

        /**
         * A map of programs available for report creation.
         *
         * @property program
         * @type {Object}
         */
        programs: null,

        /**
         * @method reset
         *
         * Resets the dialog's form element.
         */
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
        },

        /*
         * @override
         * @see YAHOO.widget.Dialog.validate
         */
        validate: function () {
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
        }
    });

    /**
     * "Edit Report" dialog.
     *
     * @namespace cim.widget
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
            Dom.removeClass(el, 'hidden');
            this.reset();
            this.populateForm();
            this.center();
        });

        this.hideEvent.subscribe(function () {
            Dom.addClass(el, 'hidden');
        })

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
            model.update(parsedResponse.report);
            dialog.cancel();
        };

        this.callback.failure = function (resultObject) {
            ilios.global.defaultAJAXFailureHandler(resultObject);
            document.getElementById('report_update_status').innerHTML
                = ilios_i18nVendor.getI18NString('curriculum_inventory.update.error.general');
        };

        this.callback.argument = this;
    };

    // inheritance
    Lang.extend(EditReportDialog, YAHOO.widget.Dialog, {

        /**
         * The report model editable through this dialog.
         *
         * @property model
         * @type {ilios.cim.model.ReportModel}
         */
        model: null,

        /**
         * Calendar picker widget for the "start date" input element in the dialog form.
         *
         * @property cal1
         * @type {YAHOO.widget.Calendar}
         */
        cal1: null,

        /**
         * Calendar picker widget for the "end date" input element in the dialog form.
         *
         * @property cal2
         * @type {YAHOO.widget.Calendar}
         */
        cal2: null,

        /**
         * Fills in the form with model-provided data.
         *
         * @method populateForm
         */
        populateForm: function () {
            document.getElementById('edit_report_name').value = this.model.get('name');
            document.getElementById('edit_report_description').value = this.model.get('description');
            document.getElementById('edit_report_id').value = this.model.get('id');
            document.getElementById('edit_report_start_date').value = this.model.get('startDate');
            document.getElementById('edit_report_end_date').value = this.model.get('endDate');
        },

        /**
         * Empties the dialog's status display.
         *
         * @method reset
         */
        reset: function () {
            document.getElementById('report_update_status').innerHTML = '';
        },

        /**
         * Resets the calendar widgets in the dialog's form to the dates provided by the model.
         *
         * @method resetCalendars
         */
        resetCalendars: function () {
            this.cal1.cfg.setProperty('selected', this.model.get('startDate'), false);
            this.cal1.cfg.setProperty('pagedate', new Date(this.model.get('startDate')), false);
            this.cal1.render();
            this.cal1.hide();

            this.cal2.cfg.setProperty('selected', this.model.get('endDate'), false);
            this.cal2.cfg.setProperty('pagedate', new Date(this.model.get('endDate')), false);
            this.cal2.render();
            this.cal2.hide();
        },

        /**
         * Event-listener function.
         * Subscribed to each calendar widget's "selectEvent" event.
         * It takes the selected date as passed from the calendar and writes it to a given input field
         * after reformatting it, then closes/hides the given calendar.
         *
         * @method selectCalendar
         * @param {String} type The name of the fired event.
         * @param {Array} args an array of Date-field arrays in the format [YYYY, MM, DD]
         * @param {Object} obj An argument map containing:
         *    @param {HTMLElement} obj.targetEl The form element to write the picked date to.
         *    @param {YAHOO.widget.Calendar} obj.calendar The calendar to hide.
         * @link http://developer.yahoo.com/yui/docs/YAHOO.widget.Calendar.html#event_selectEvent
         */
        selectCalendar: function (type, args, obj) {
            var cal = obj.calendar;
            var el = obj.targetEl;
            var dt;
            if (args[0]) {
                dt = new Date(args[0][0][0], args[0][0][1], args[0][0][2]);
                el.value = YAHOO.util.Date.format(dt, {format: "%Y-%m-%d"});
            }
            cal.hide();
        },

        /**
         * Event-listener function.
         * Subscribed to each calendar button in this dialog's form.
         * Pops up the given calendar widget.
         *
         * @method onCalendarButtonClick
         * @param {Event} event The fired event.
         * @param {Object} obj An argument map containing:
         *     @param {YAHOO.widget.Calendar} obj.calendar The calendar to pop up.
         */
        onCalendarButtonClick: function (event, obj) {
            obj.calendar.show();
        }
    });

    /**
     * "Search Reports" dialog.
     *
     * @namespace cim.widget
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
            Dom.removeClass(el, 'hidden');
            this.center();
        });

        this.hideEvent.subscribe(function () {
           Dom.addClass(el, 'hidden');
        });
    };

    // inheritance
    Lang.extend(ReportPickerDialog, YAHOO.widget.Dialog);

    /**
     * A toolbar displayed above the sequence block containers.
     * Contains "expand/collapse-all" buttons.
     *
     * @namespace cim.widget
     * @class SequenceBlockTopToolbar
     * @constructor
     * @extends YAHOO.util.Element
     * @param {Object} oConfig A configuration object.
     */
    var SequenceBlockTopToolbar = function (oConfig) {
        SequenceBlockTopToolbar.superclass.constructor.call(this, 'sequence-block-top-toolbar', oConfig);
    };

    Lang.extend(SequenceBlockTopToolbar, Element, {

        /*
         * @override
         * @see YAHOO.util.Element.initAttributes
         */
        initAttributes: function (config) {
            /**
             * The toolbar's "expand all" button.
             *
             * @attribute expandBtnEl
             * @type {HTMLElement}
             * @writeOnce
             */
            this.setAttributeConfig('expandBtnEl', {
                writeOnce: true,
                value: Dom.get('expand-all-sequence-blocks-btn')
            });

            /**
             * The toolbar's "collapse all" button.
             * @attribute collapseBtnEl
             * @type {HTMLElement}
             * @writeOnce
             */
            this.setAttributeConfig('collapseBtnEl', {
                writeOnce: true,
                value: Dom.get('collapse-all-sequence-blocks-btn')
            });
        },

        /**
         * Makes the toolbar visible.
         *
         * @method show
         */
        show: function () {
            this.removeClass('hidden');
        },

        /**
         * Hides the toolbar.
         *
         * @method hide
         */
        hide: function () {
            this.addClass('hidden');
        },

        /**
         * Renders the toolbar.
         *
         * @method render
         */
        render: function () {
            var expandBtn = this.getExpandButton();
            var collapseBtn = this.getCollapseButton();

            // more wiring of event handlers
            Event.addListener(collapseBtn, 'click', function (event) {
                Dom.addClass(this.get('collapseBtnEl'), 'hidden');
                Dom.removeClass(this.get('expandBtnEl'), 'hidden');
                Event.stopEvent(event);
                return false;
            }, {}, this);

            Event.addListener(expandBtn, 'click', function (event) {
                Dom.addClass(this.get('expandBtnEl'), 'hidden');
                Dom.removeClass(this.get('collapseBtnEl'), 'hidden');
                Event.stopEvent(event);
                return false;
            }, {}, this);

            expandBtn.disabled = false;
            collapseBtn.disabled = false;
            Dom.removeClass(expandBtn, 'hidden');
        },

        /**
         * Returns the toolbar's "expand all" button.
         *
         * @method getExpandButton
         * @return {HTMLElement}
         */
        getExpandButton: function () {
            return this.get('expandBtnEl');
        },

        /**
         * Returns the toolbar's "collapse all" button.
         *
         * @method getCollapseButton
         * @return {HTMLElement}
         */
        getCollapseButton: function () {
            return this.get('collapseBtnEl');
        }
    });

    /**
     * A toolbar displayed above the sequence block containers.
     * Contains an "add new sequence block" button.
     *
     * @namespace cim.widget
     * @class SequenceBlockBottomToolbar
     * @constructor
     * @extends YAHOO.util.Element
     * @param {Object} oConfig A configuration object.
     */
    var SequenceBlockBottomToolbar = function (oConfig) {
        SequenceBlockBottomToolbar.superclass.constructor.call(this, 'sequence-block-bottom-toolbar', oConfig);
        this.config = oConfig;
    };

    Lang.extend(SequenceBlockBottomToolbar, Element, {

        /**
         * @method initAttributes
         * Overrides <code>YAHOO.util.Element.initAttributes()</code>.
         * @param {Object} config The view configuration.
         */
        initAttributes: function (config) {
            this.setAttributeConfig('addBtnEl', {
                writeOnce: true,
                value: Dom.get('add-new-sequence-block-btn')
            });
        },

        /**
         * @method show
         * Makes the toolbar visible.
         */
        show: function () {
            this.removeClass('hidden');
        },

        /**
         * @method hide
         * Hides the toolbar.
         */
        hide: function () {
            this.addClass('hidden');
        },

        /**
         * @method render.
         * Renders the toolbar.
         * @param {Boolean} enableButtons If TRUE then all buttons contained within the toolbar will be enabled.
         */
        render: function (enableButtons) {

            if (enableButtons) {
                this.enableButtons();
            }
        },

        /**
         * @method enableButtons
         * Enables all buttons in the toolbar.
         */
        enableButtons: function () {
            var el = this.get('addBtnEl');
            el.disabled = false;
        },

        /**
         * @method disableButtons
         * Disables all buttons in the toolbar.
         */
        disableButtons: function () {
            var el = this.get('addBtnEl');
            Dom.setAttribute(el, 'disabled', 'disabled');
        }
    });

    /**
     * The status-indicator bar.
     * It's purpose is to display given (status-)messages on the page.
     * @namespace cim.widget
     * @class StatusBar
     * @constructor
     * @extends YAHOO.util.Element
     * @param {Object} oConfig A configuration object.
     */
    var StatusBar = function (oConfig) {
        StatusBar.superclass.constructor.call(this, document.createElement('div'), oConfig);
    };

    Lang.extend(StatusBar, Element, {

        /*
         * @override
         * @see YAHOO.util.Element.initAttributes
         */
        initAttributes: function (config) {

            StatusBar.superclass.initAttributes.call(this, config);

            var container = this.get('element');

            /**
             * The display element for showing a progress indicator ("spinner") in the status bar.
             *
             * @attribute progressEl
             * @type {HTMLElement}
             * @writeOnce
             */
            this.setAttributeConfig('progressEl', {
                writeOnce: true,
                value: container.appendChild(document.createElement('div'))
            });

            /**
             * The display element for showing a message in the status bar.
             *
             * @attribute messageEl
             * @type {HTMLElement}
             * @writeOnce
             */
            this.setAttributeConfig('messageEl', {
                writeOnce: true,
                value: container.appendChild(document.createElement('span'))
            });

            /**
             * The message to be shown in the status bar.
             *
             * @attribute message
             * @type {String}
             * @default ""
             */
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

        /**
         * Renders the status bar onto the page.
         *
         * @method render
         * @param {String|HTMLElement} parentEl The parent element that this widget should be attached to.
         */
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
        /**
         * Clears out the status bar's message.
         *
         * @method reset
         */
        reset: function () {
            this.show('', false);
        },
        /**
         * Shows a given message and optionally a progress indicator in the status bar.
         *
         * @method show
         * @param {String} message The message to show.
         * @param {Boolean} showProgressIndicator If TRUE then a spinner icon is shown with the message, otherwise not.
         */
        show: function (message, showProgressIndicator) {
            showProgressIndicator = showProgressIndicator || false;
            Dom.setStyle(this.get('progressEl'), 'display', (showProgressIndicator ? 'inline-block' : 'none'));
            this.set('message', message);
            this.setStyle('display', 'block');
        },
        /**
         * Hides the status bar.
         *
         * @method hide
         */
        hide: function () {
            this.setStyle('display', 'none');
        }
    });

    ilios.cim.widget.StatusBar = StatusBar;
    ilios.cim.widget.SequenceBlockTopToolbar = SequenceBlockTopToolbar;
    ilios.cim.widget.SequenceBlockBottomToolbar = SequenceBlockBottomToolbar;
    ilios.cim.widget.CreateReportDialog = CreateReportDialog;
    ilios.cim.widget.ReportPickerDialog = ReportPickerDialog;
    ilios.cim.widget.EditReportDialog = EditReportDialog;
}());
