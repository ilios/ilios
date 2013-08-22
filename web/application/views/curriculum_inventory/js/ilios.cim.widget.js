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
        Lang = YAHOO.lang,
        CustomEvent = YAHOO.util.CustomEvent;

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
                dt = new Date(args[0][0][0], args[0][0][1] - 1, args[0][0][2]);
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
            constraintoviewport: true,
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
     * "Create a sequence block" dialog.
     *
     * @namespace cim.widget
     * @class CreateSequenceBlockDialog
     * @extends YAHOO.widget.Dialog
     * @constructor
     * @param {HTMLElement|String} el The element or element-ID representing the dialog
     * @param {ilios.cim.CourseRepository} courseRepo A course repo object. Used to populate course dropdown and to
     *     look up course details on selection.
     * @param {Object} userConfig The configuration object literal containing the configuration that should be set for
     *     this dialog.
     */
    var CreateSequenceBlockDialog = function (el, courseRepo, userConfig) {

        var date = new Date(); // get current date to initialize the calendar widgets.

        var defaultConfig = {
            width: "680px",
            y: 0,
            modal: true,
            visible: false,
            constraintoviewport: true,
            hideaftersubmit: false,
            zIndex: 999,
            buttons: [
                {
                    text: ilios_i18nVendor.getI18NString('general.terms.save'),
                    handler: function () {
                        this.submit();
                    }
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
        CreateSequenceBlockDialog.superclass.constructor.call(this, el, config);

        this._courseRepo = courseRepo;

        // custom events
        this.sequenceBlockCreationSucceededEvent = new CustomEvent(this.EVT_SEQUENCE_BLOCK_CREATION_SUCCEEDED, this);

        // calendar widgets
        this.cal1 = new YAHOO.widget.Calendar(null, 'create-sequence-block-dialog--start-date-calendar-container', {
            selected: date,
            iframe: false,
            close: true,
            pagedate: date,
            title: ilios_i18nVendor.getI18NString('general.phrases.start_date')
        });
        this.cal2 = new YAHOO.widget.Calendar(null, 'create-sequence-block-dialog--end-date-calendar-container', {
            selected: date,
            iframe: false,
            close: true,
            pagedate: date,
            title: ilios_i18nVendor.getI18NString('general.phrases.end_date')
        });

        //
        // subscribe to the dialog's various events.
        //
        this.beforeRenderEvent.subscribe(function () {

            this.cal1.selectEvent.subscribe(this.selectCalendar, {
                calendar: this.cal1,
                targetEl: document.getElementById('create-sequence-block-dialog--start-date')
            });
            this.cal1.render();

            this.cal2.selectEvent.subscribe(this.selectCalendar, {
                calendar: this.cal2,
                targetEl: document.getElementById('create-sequence-block-dialog--end-date')
            });
            this.cal2.render();

            Event.addListener('create-sequence-block-dialog--start-date-button', 'click', this.onCalendarButtonClick, {
                calendar: this.cal1
            }, this);
            Event.addListener('create-sequence-block-dialog--end-date-button', 'click', this.onCalendarButtonClick, {
                calendar: this.cal2
            }, this);

            Event.addListener('create-sequence-block-dialog--clear-dates-button', 'click', function (event) {
                this._resetDateFields();
                Event.stopEvent(event);
            }, {}, this);

            Event.addListener(document.getElementById('create-sequence-block-dialog--course'), 'change', function (event) {
                var courseId, str, el, course;
                el = document.getElementById('create-sequence-block-dialog--course-details');
                courseId = event.target.value;
                str = '';
                if ('' != courseId) {
                    course = this._courseRepo.get(courseId);
                    str += '<span class="small">';
                    str += ilios_i18nVendor.getI18NString('general.terms.level') + ': ' + course.get('level');
                    str += ", " + ilios_i18nVendor.getI18NString('general.phrases.start_date') + ': ' + course.get('startDate');
                    str += ", " + ilios_i18nVendor.getI18NString('general.phrases.end_date') + ': ' + course.get('endDate');
                    str += '</span>';
                }
                el.innerHTML = str;
                Event.stopEvent(event);
            }, {}, this);
        });

        this.beforeShowEvent.subscribe(function () {
            Dom.removeClass(el, 'hidden');
            this.center();
        });

        this.hideEvent.subscribe(function () {
            Dom.addClass(el, 'hidden');
        });
        this.beforeSubmitEvent.subscribe(function () {
            document.getElementById('create-sequence-block-dialog--status').innerHTML = ilios_i18nVendor.getI18NString('general.terms.saving') + '...';
        });

        this.cancelEvent.subscribe(function () {
            this.resetCalendars();
            this.reset();
        });

        /*
         * Form submission success handler.
         */
        this.callback.success = function (resultObject) {
            var parsedResponse;
            var dialog = resultObject.argument;
            try {
                parsedResponse = Lang.JSON.parse(resultObject.responseText);
            } catch (e) {
                document.getElementById('create-sequence-block-dialog--status').innerHTML
                    = ilios_i18nVendor.getI18NString('curriculum_inventory.sequence_block.create.error.general');
                return;
            }

            document.getElementById('create-sequence-block-dialog--status').innerHTML = '';

            if (parsedResponse.hasOwnProperty('error')) {
                document.getElementById('create-sequence-block-dialog--status').innerHTML = parsedResponse.error;
                return;
            }

            // At this point we can assume that the transaction was a success.
            // We peel the new block record (key: "sequence_block") and the passed map of updated sequence block orders
            // (key: "updated_siblings_order")off the payload, and fire it off to subscribers of our "creation succeeded" event.
            dialog.sequenceBlockCreationSucceededEvent.fire({
                data: parsedResponse.sequence_block,
                updated_siblings_order: parsedResponse.updated_siblings_order
            });
            dialog.cancel();
        };

        /*
         * Form submission failure handler.
         */
        this.callback.failure = function (resultObject) {
            ilios.global.defaultAJAXFailureHandler(resultObject);
            document.getElementById('create-sequence-block-dialog--status').innerHTML
                = ilios_i18nVendor.getI18NString('curriculum_inventory.sequence_block.create.error.general');
        };

        this.callback.argument = this;

        this.beforeSubmitEvent.subscribe(function () {
            document.getElementById('create-sequence-block-dialog--status').innerHTML = ilios_i18nVendor.getI18NString('general.terms.creating') + '...';
        });


    };

    Lang.extend(CreateSequenceBlockDialog, YAHOO.widget.Dialog, {

        /**
         * The calendar widget for selecting the "start date" in this dialog's form.
         *
         * @property cal1
         * @type {YAHOO.widget.Calendar}
         */
        cal1: null,

        /**
         * The calendar widget for selecting the "end date" in this dialog's form.
         *
         * @property cal2
         * @type {YAHOO.widget.Calendar}
         */
        cal2: null,

        /**
         * The course repository.
         * Used to look up linkable courses for populating the "course" dropdown in this dialog's form.
         *
         * @property _courseRepo
         * @type {ilios.cim.CourseRepository}
         * @protected
         */
        _courseRepo: null,

        /**
         * Populates the "courses" dropdown with linkable courses retrieved from the course repo.
         *
         * @method _populateCourseDropdown
         * @protected
         */
        _populateCourseDropdown: function () {
            var i, n, selectEl, optionEl, course, courses;
            courses = this._courseRepo.listLinkable();
            selectEl = document.getElementById('create-sequence-block-dialog--course');
            for (i = 0, n = courses.length; i < n; i++) {
                course = courses[i];
                optionEl = selectEl.appendChild(document.createElement('option'));
                optionEl.value = course.getId();
                optionEl.innerHTML = course.get('title');
            }
        },

        /**
         * Populates the "order in sequence" dropdown with <code>n</code> options.
         * Each option has an increment of 1 as its text and value, starting at 1 and ending
         * at a given upper boundary n.
         * E.g., if n = 3 is given then this method will add three option elements with the values
         * 1, 2 and 3 to the dropdown.
         *
         * @method _populateOrderInSequenceDropdown
         * @param {Number} n The upper inclusive) boundary.
         * @protected
         */
        _populateOrderInSequenceDropdown: function (n) {
            var i, selectEl, optionEl;
            selectEl = document.getElementById('create-sequence-block-dialog--order-in-sequence');
            for (i = 1; i <= n; i++) {
                optionEl = selectEl.appendChild(document.createElement('option'));
                optionEl.value = i;
                optionEl.innerHTML = i;
            }
        },

        /**
         * Clears out the "course" dropdown and re-adds the default "None" option.
         *
         * @method _resetCourseDropdown
         * @protected
         */
        _resetCourseDropdown: function () {
            var selectEl, optionEl;
            selectEl = document.getElementById('create-sequence-block-dialog--course');
            selectEl.innerHTML = ''; // gut the drop-down
            optionEl = selectEl.appendChild(document.createElement('option'));
            optionEl.value = "";
            optionEl.innerHTML = '&lt;' + ilios_i18nVendor.getI18NString('general.terms.none') + '&gt;';
        },

        /**
         * Clears out the start/end-date input fields.
         *
         * @method _resetDateFields
         * @protected
         */
        _resetDateFields: function () {
            document.getElementById('create-sequence-block-dialog--start-date').value = "";
            document.getElementById('create-sequence-block-dialog--end-date').value = "";
        },

        /**
         * Clears out the "order in sequence" dropdown.
         *
         * @method _resetOrderInSequenceDropdown
         * @protected
         */
        _resetOrderInSequenceDropdown: function () {
            var el = document.getElementById('create-sequence-block-dialog--order-in-sequence');
            el.innerHTML = '';
        },

        /**
         * Resets the dialog's form to its initial state.
         *
         * @method reset
         */
        reset: function () {
            document.getElementById('create-sequence-block-dialog--status').value = "";
            document.getElementById('create-sequence-block-dialog--report-id').value = "";
            document.getElementById('create-sequence-block-dialog--parent-block-id').value = "";
            document.getElementById('create-sequence-block-dialog--title').value = "";
            document.getElementById('create-sequence-block-dialog--description').value = "";
            document.getElementById('create-sequence-block-dialog--course-details').innerHTML = "";
            document.getElementById('create-sequence-block-dialog--duration').value = "0";
            document.getElementById('create-sequence-block-dialog--required').options[0].selected = 'selected';
            document.getElementById('create-sequence-block-dialog--academic-level').options[0].selected = 'selected';
            document.getElementById('create-sequence-block-dialog--child-sequence-order').options[0].selected = 'selected';
            document.getElementById('create-sequence-block-dialog--track').options[0].selected = 'selected';

            this._clearValidationErrorStyles();
            this._resetDateFields();
            this._resetCourseDropdown();
            this._resetOrderInSequenceDropdown();
            Dom.addClass('create-sequence-block-dialog--order-in-sequence-row', 'hidden');
        },

        /**
         * Removes the CSS classes for highlighting form validation errors from input elements.
         *
         * @method _clearValidationErrorStyles
         * @protected
         */
         _clearValidationErrorStyles: function () {
             Dom.removeClass('create-sequence-block-dialog--title', 'validation-failed');
             Dom.removeClass('create-sequence-block-dialog--description', 'validation-failed');
             Dom.removeClass('create-sequence-block-dialog--end-date', 'validation-failed');
             Dom.removeClass('create-sequence-block-dialog--duration', 'validation-failed');
         },

        /**
         * Populates the dialog's form with the given data.
         *
         * @param {Number} reportId The report id.
         * @param {ilios.cim.model.SequenceBlockModel|null} parent The parent sequence block, or NULL if there is no parent.
         */
        populateForm: function (reportId, parent) {
            this._populateCourseDropdown();
            document.getElementById('create-sequence-block-dialog--report-id').value = reportId;
            if (parent) {
                document.getElementById('create-sequence-block-dialog--parent-block-id').value = parent.getId();
                // if the parent sequence is unordered or in parallel
                // then hide the "order in sequence" input field
                if (parent.ORDERED == parent.get('childSequenceOrder')) {
                    this._populateOrderInSequenceDropdown(parent.get('children').size() + 1);
                    Dom.removeClass('create-sequence-block-dialog--order-in-sequence-row', 'hidden');
                }
            }
        },

        /**
         * Validates the data entered into the dialog's form.
         *
         * @method validate
         * @return {Boolean} TRUE on success, FALSE on error.
         */
        validate: function () {
            this._clearValidationErrorStyles();
            var data = this.getData();
            var msgs = [];
            var hasStartDate = data.start_date ? true : false;

            if ('' === Lang.trim(data.title)) {
                msgs.push(ilios_i18nVendor.getI18NString('curriculum_inventory.sequence_block.validate.error.title_missing'));
                Dom.addClass('create-sequence-block-dialog--title', 'validation-failed');
            }
            if ('' === Lang.trim(data.description)) {
                msgs.push(ilios_i18nVendor.getI18NString('curriculum_inventory.sequence_block.validate.error.description_missing'));
                Dom.addClass('create-sequence-block-dialog--description', 'validation-failed');
            }
            if (hasStartDate) {
                if (! data.end_date) {
                    msgs.push(ilios_i18nVendor.getI18NString('curriculum_inventory.sequence_block.validate.error.missing_end_date'));
                    Dom.addClass('create-sequence-block-dialog--end-date', 'validation-failed');
                }
                if (Date.parse(data.start_date) > Date.parse(data.end_date)) {
                    msgs.push(ilios_i18nVendor.getI18NString('curriculum_inventory.sequence_block.validate.error.start_date_gt_end_date'));
                    Dom.addClass('create-sequence-block-dialog--end-date', 'validation-failed');
                }

                // if a duration is given then it must be valid
                if ("" !== Lang.trim(data.duration) && 0 > parseInt(data.duration, 10)) {
                    msgs.push(ilios_i18nVendor.getI18NString('curriculum_inventory.sequence_block.validate.error.invalid_duration'));
                    Dom.addClass('create-sequence-block-dialog--duration', 'validation-failed');
                }
            } else {
                // duration is required and must be larger that zero
                if ("" === Lang.trim(data.duration) || 0 >= parseInt(data.duration, 10)) {
                    msgs.push(ilios_i18nVendor.getI18NString('curriculum_inventory.sequence_block.validate.error.missing_duration'));
                    Dom.addClass('create-sequence-block-dialog--duration', 'validation-failed');
                }
            }

            if (msgs.length) {
                document.getElementById('create-sequence-block-dialog--status').innerHTML = msgs.join('<br />') + '<br />';
                return false;
            }
            return true;
        },

        /**
         * Resets the start/end date calendar widgets and hides them from view.
         *
         * @method resetCalendars
         */
        resetCalendars: function () {
            var date = new Date();
            this.cal1.cfg.setProperty('selected', date, false);
            this.cal1.cfg.setProperty('pagedate', date, false);
            this.cal1.render();
            this.cal1.hide();

            this.cal2.cfg.setProperty('selected', date, false);
            this.cal2.cfg.setProperty('pagedate', date, false);
            this.cal2.render();
            this.cal2.hide();
        },

        /**
         * Makes the dialog visible, after resetting and populating it with the given data.
         *
         * @override
         * @method show
         * @param {Number} reportId The report id.
         * @param {ilios.cim.model.SequenceBlockModel|null} parent The parent sequence block, or NULL if there is no parent.
         * @see YAHOO.widget.Module.show
         */
        show: function (reportId, parent) {
            this.reset();
            this.populateForm(reportId, parent);
            CreateSequenceBlockDialog.superclass.show.call(this);

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
                dt = new Date(args[0][0][0], args[0][0][1] - 1, args[0][0][2]);
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
        },

        /**
         * Event type.
         * @property EVT_SEQUENCE_BLOCK_CREATION_SUCCEEDED
         * @type {String}
         * @final
         * @see ilios.cim.widget.CreateSequenceBlockDialog.sequenceBlockCreationSucceededEvent
         */
        EVT_SEQUENCE_BLOCK_CREATION_SUCCEEDED: 'sequenceBlockCreationSucceeded',

        /**
         * Fired when the server response following form post for sequence block creation
         * and indicating success has been received, and the payload from that response has been parsed
         * into a data map object.
         *
         * @event sequenceBlockCreationSucceededEvent
         * @param {Object} data A plain data object containing the properties of the newly created sequence block record.
         * @param {Object} updated_siblings_order A map containing sequence-block-ids/order-in-sequence values as
         *      key/value pairs. The referenced blocks are siblings in a sequence to the created block, and had
         *      their order-in-sequence value changed as a side-effect of the block creation.
         */
        sequenceBlockCreationSucceededEvent: null
    });

    /**
     * "Edit a sequence block" dialog.
     *
     * @namespace cim.widget
     * @class EditSequenceBlockDialog
     * @extends YAHOO.widget.Dialog
     * @constructor
     * @param {HTMLElement|String} el The element or element-ID representing the dialog
     * @param {ilios.cim.CourseRepository} courseRepo A course repo object. Used to populate course dropdown and to
     *     look up course details on selection.
     * @param {Object} userConfig The configuration object literal containing the configuration that should be set for
     *     this dialog.
     */
    var EditSequenceBlockDialog = function (el, courseRepo, userConfig) {

        var date = new Date(); // get current date to initialize the calendar widgets.

        var defaultConfig = {
            width: "680px",
            y: 0,
            modal: true,
            visible: false,
            constraintoviewport: true,
            hideaftersubmit: false,
            zIndex: 999,
            buttons: [
                {
                    text: ilios_i18nVendor.getI18NString('general.terms.save'),
                    handler: function () {
                        this.submit();
                    }
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
        EditSequenceBlockDialog.superclass.constructor.call(this, el, config);

        this._courseRepo = courseRepo;

        // custom events
        this.sequenceBlockUpdateSucceededEvent = new CustomEvent(this.EVT_SEQUENCE_BLOCK_UPDATE_SUCCEEDED, this);

        // calendar widgets
        this.cal1 = new YAHOO.widget.Calendar(null, 'edit-sequence-block-dialog--start-date-calendar-container', {
            selected: date,
            iframe: false,
            close: true,
            pagedate: date,
            title: ilios_i18nVendor.getI18NString('general.phrases.start_date')
        });
        this.cal2 = new YAHOO.widget.Calendar(null, 'edit-sequence-block-dialog--end-date-calendar-container', {
            selected: date,
            iframe: false,
            close: true,
            pagedate: date,
            title: ilios_i18nVendor.getI18NString('general.phrases.end_date')
        });

        //
        // subscribe to the dialog's various events.
        //
        this.beforeRenderEvent.subscribe(function () {

            this.cal1.selectEvent.subscribe(this.selectCalendar, {
                calendar: this.cal1,
                targetEl: document.getElementById('edit-sequence-block-dialog--start-date')
            });
            this.cal1.render();

            this.cal2.selectEvent.subscribe(this.selectCalendar, {
                calendar: this.cal2,
                targetEl: document.getElementById('edit-sequence-block-dialog--end-date')
            });
            this.cal2.render();

            Event.addListener('edit-sequence-block-dialog--start-date-button', 'click', this.onCalendarButtonClick, {
                calendar: this.cal1
            }, this);
            Event.addListener('edit-sequence-block-dialog--end-date-button', 'click', this.onCalendarButtonClick, {
                calendar: this.cal2
            }, this);

            Event.addListener('edit-sequence-block-dialog--clear-dates-button', 'click', function (event) {
                this._resetDateFields();
                Event.stopEvent(event);
            }, {}, this);

            Event.addListener(document.getElementById('edit-sequence-block-dialog--course'), 'change', function (event) {
                courseId = event.target.value;
                if ('' != courseId) {
                    course = this._courseRepo.get(courseId);
                    this._showCourseDetails(course);
                } else {
                    this._clearCourseDetails();
                }
                Event.stopEvent(event);
            }, {}, this);
        });

        this.beforeShowEvent.subscribe(function () {
            Dom.removeClass(el, 'hidden');
            this.center();
        });

        this.hideEvent.subscribe(function () {
            Dom.addClass(el, 'hidden');
        });
        this.beforeSubmitEvent.subscribe(function () {
            document.getElementById('edit-sequence-block-dialog--status').innerHTML = ilios_i18nVendor.getI18NString('general.terms.saving') + '...';
        });

        this.cancelEvent.subscribe(function () {
            this.resetCalendars();
            this.reset();
        });

        /*
         * Form submission success handler.
         */
        this.callback.success = function (resultObject) {
            var parsedResponse;
            var dialog = resultObject.argument;
            try {
                parsedResponse = Lang.JSON.parse(resultObject.responseText);
            } catch (e) {
                document.getElementById('edit-sequence-block-dialog--status').innerHTML
                    = ilios_i18nVendor.getI18NString('curriculum_inventory.sequence_block.update.error.general');
                return;
            }

            document.getElementById('edit-sequence-block-dialog--status').innerHTML = '';

            if (parsedResponse.hasOwnProperty('error')) {
                document.getElementById('edit-sequence-block-dialog--status').innerHTML = parsedResponse.error;
                return;
            }

            // At this point we can assume that the transaction was a success.
            // We peel the updated block record (key: "sequence_block") and the passed maps of updated
            // siblings/child-sequence block orders (keys: "updated_siblings_order" and "updated_children_order")
            // off the payload, and fire it off to subscribers of our "update succeeded" event.
            dialog.sequenceBlockUpdateSucceededEvent.fire({
                data: parsedResponse.sequence_block,
                updated_siblings_order: parsedResponse.updated_siblings_order,
                updated_children_order: parsedResponse.updated_children_order
            });
            dialog.cancel();
        };

        /*
         * Form submission failure handler.
         */
        this.callback.failure = function (resultObject) {
            ilios.global.defaultAJAXFailureHandler(resultObject);
            document.getElementById('edit-sequence-block-dialog--status').innerHTML
                = ilios_i18nVendor.getI18NString('curriculum_inventory.sequence_block.update.error.general');
        };

        this.callback.argument = this;

        this.beforeSubmitEvent.subscribe(function () {
            document.getElementById('edit-sequence-block-dialog--status').innerHTML = ilios_i18nVendor.getI18NString('general.terms.updating') + '...';
        });
    };

    Lang.extend(EditSequenceBlockDialog, YAHOO.widget.Dialog, {

        /**
         * The calendar widget for selecting the "start date" in this dialog's form.
         *
         * @property cal1
         * @type {YAHOO.widget.Calendar}
         */
        cal1: null,

        /**
         * The calendar widget for selecting the "end date" in this dialog's form.
         *
         * @property cal2
         * @type {YAHOO.widget.Calendar}
         */
        cal2: null,

        /**
         * The course repository.
         * Used to look up linkable courses for populating the "course" dropdown in this dialog's form.
         *
         * @property _courseRepo
         * @type {ilios.cim.CourseRepository}
         * @protected
         */
        _courseRepo: null,

        /**
         * Populates the "courses" dropdown with linkable courses retrieved from the course repo,
         * and the currently selected course (if applicable).
         *
         * @method _populateCourseDropdown
         * @property {ilios.cim.model.CourseModel|null} value The currently selected course, or NULL if none is selected.
         * @protected
         */
        _populateCourseDropdown: function (value) {
            var i, n, selectEl, optionEl, course, courses;
            courses = this._courseRepo.listLinkable();
            selectEl = document.getElementById('edit-sequence-block-dialog--course');
            // if applicable, add the currently selected course to the top of the dropdown.
            if (value) {
                optionEl = selectEl.appendChild(document.createElement('option'));
                optionEl.value = value.getId();
                if (value.get('deleted')) {
                    optionEl.innerHTML = "[" + ilios_i18nVendor.getI18NString('general.terms.deleted') + "]";
                } else {
                    optionEl.innerHTML = value.get('title');
                }
                Dom.setAttribute(optionEl, 'selected', 'selected');
                this._showCourseDetails(value);

            }
            for (i = 0, n = courses.length; i < n; i++) {
                course = courses[i];
                optionEl = selectEl.appendChild(document.createElement('option'));
                optionEl.value = course.getId();
                optionEl.innerHTML = course.get('title');
            }
        },

        /**
         * Prints course details onto the dialog.
         *
         * @method _showCourseDetails
         * @param {ilios.cim.model.CourseModel} course The course
         * @protected
         */
        _showCourseDetails: function (course) {
            var el = document.getElementById('edit-sequence-block-dialog--course-details');
            var str = '';
            if (! course.get('deleted')) {
                str += '<span class="small">';
                str += ilios_i18nVendor.getI18NString('general.terms.level') + ': ' + course.get('level');
                str += ", " + ilios_i18nVendor.getI18NString('general.phrases.start_date') + ': ' + course.get('startDate');
                str += ", " + ilios_i18nVendor.getI18NString('general.phrases.end_date') + ': ' + course.get('endDate');
                str += '</span>';
            }
            el.innerHTML = str;
        },

        /**
         * Clears out the course details display-element.
         *
         * @protected
         */
        _clearCourseDetails: function () {
            document.getElementById('edit-sequence-block-dialog--course-details').innerHTML = '';
         },

        /**
         * Populates the "order in sequence" dropdown with <code>n</code> options.
         * Each option has an increment of 1 as its text and value, starting at 1 and ending
         * at a given upper boundary n.
         * E.g., if n = 3 is given then this method will add three option elements with the values
         * 1, 2 and 3 to the dropdown.
         *
         * @method _populateOrderInSequenceDropdown
         * @param {Number} n The upper inclusive) boundary.
         * @param {Number} value The currently selected order-in-sequence value.
         * @protected
         */
        _populateOrderInSequenceDropdown: function (n, value) {
            var i, selectEl, optionEl;
            selectEl = document.getElementById('edit-sequence-block-dialog--order-in-sequence');
            for (i = 1; i <= n; i++) {
                optionEl = selectEl.appendChild(document.createElement('option'));
                optionEl.value = i;
                optionEl.innerHTML = i;
                if (value == i) {
                    optionEl.selected = "selected";
                }
            }
        },

        /**
         * Clears out the "course" dropdown and re-adds the default "None" option.
         *
         * @method _resetCourseDropdown
         * @protected
         */
        _resetCourseDropdown: function () {
            var selectEl, optionEl;
            selectEl = document.getElementById('edit-sequence-block-dialog--course');
            selectEl.innerHTML = ''; // gut the drop-down
            optionEl = selectEl.appendChild(document.createElement('option'));
            optionEl.value = "";
            optionEl.innerHTML = '&lt;' + ilios_i18nVendor.getI18NString('general.terms.none') + '&gt;';
        },

        /**
         * Clears out the start/end-date input fields.
         *
         * @method _resetDateFields
         * @protected
         */
        _resetDateFields: function () {
            document.getElementById('edit-sequence-block-dialog--start-date').value = "";
            document.getElementById('edit-sequence-block-dialog--end-date').value = "";
        },

        /**
         * Clears out the "order in sequence" dropdown.
         *
         * @method _resetOrderInSequenceDropdown
         * @protected
         */
        _resetOrderInSequenceDropdown: function () {
            var el = document.getElementById('edit-sequence-block-dialog--order-in-sequence');
            el.innerHTML = '';
        },

        /**
         * Resets the dialog's form to its initial state.
         *
         * @method reset
         */
        reset: function () {
            document.getElementById('edit-sequence-block-dialog--sequence-block-id').value = "";
            document.getElementById('edit-sequence-block-dialog--title').value = "";
            document.getElementById('edit-sequence-block-dialog--description').value = "";
            document.getElementById('edit-sequence-block-dialog--duration').value = "0";
            document.getElementById('edit-sequence-block-dialog--required').options[0].selected = 'selected';
            document.getElementById('edit-sequence-block-dialog--academic-level').options[0].selected = 'selected';
            document.getElementById('edit-sequence-block-dialog--child-sequence-order').options[0].selected = 'selected';
            document.getElementById('edit-sequence-block-dialog--track').options[0].selected = 'selected';

            this._clearCourseDetails();
            this._clearValidationErrorStyles();
            this._resetDateFields();
            this._resetCourseDropdown();
            this._resetOrderInSequenceDropdown();
            Dom.addClass('edit-sequence-block-dialog--order-in-sequence-row', 'hidden');
        },

        /**
         * Removes the CSS classes for highlighting form validation errors from input elements.
         *
         * @method _clearValidationErrorStyles
         * @protected
         */
        _clearValidationErrorStyles: function () {
            Dom.removeClass('edit-sequence-block-dialog--title', 'validation-failed');
            Dom.removeClass('edit-sequence-block-dialog--description', 'validation-failed');
            Dom.removeClass('edit-sequence-block-dialog--end-date', 'validation-failed');
            Dom.removeClass('edit-sequence-block-dialog--duration', 'validation-failed');
        },

        /**
         * Populates the dialog's form with the data of a given sequence block.
         *
         * @param {ilios.cim.model.SequenceBlockModel} block The given sequence block.
         */
        populateForm: function (block) {

            var dt, duration, required, i, track, childSequenceOrder, startDate, endDate;
            document.getElementById('edit-sequence-block-dialog--sequence-block-id').value = block.getId();
            document.getElementById('edit-sequence-block-dialog--title').value = block.get('title');
            document.getElementById('edit-sequence-block-dialog--description').value = block.get('description');
            startDate = block.get('startDate');
            if (startDate) {
                document.getElementById('edit-sequence-block-dialog--start-date').value = startDate;
                dt = new Date(startDate);
                this.cal1.cfg.setProperty('selected', dt, false);
                this.cal1.cfg.setProperty('pagedate', dt, false);
                this.cal1.render();
            }
            endDate = block.get('endDate');
            if (endDate) {
                document.getElementById('edit-sequence-block-dialog--end-date').value = endDate;
                dt = new Date(endDate);
                this.cal2.cfg.setProperty('selected', dt, false);
                this.cal2.cfg.setProperty('pagedate', dt, false);
                this.cal2.render();
            }
            duration = block.get('duration');
            if (duration) {
                document.getElementById('edit-sequence-block-dialog--duration').value = duration;
            }
            i = block.get('required') - 1;
            document.getElementById('edit-sequence-block-dialog--required').options[i].selected = 'selected';
            i = block.get('academicLevel').get('level') - 1;
            document.getElementById('edit-sequence-block-dialog--academic-level').options[i].selected = 'selected';
            i = block.get('childSequenceOrder') - 1;
            document.getElementById('edit-sequence-block-dialog--child-sequence-order').options[i].selected = 'selected';
            i = block.get('track') ? 1 : 0;
            document.getElementById('edit-sequence-block-dialog--track').options[i].selected = 'selected';

            var parent = block.get('parent');
            this._populateCourseDropdown(block.get('course'));
            document.getElementById('edit-sequence-block-dialog--sequence-block-id').value = block.getId();
            if (parent) {
                // if the parent sequence is unordered or in parallel
                // then hide the "order in sequence" input field
                if (parent.ORDERED == parent.get('childSequenceOrder')) {
                    this._populateOrderInSequenceDropdown(parent.get('children').size(), block.get('orderInSequence'));
                    Dom.removeClass('edit-sequence-block-dialog--order-in-sequence-row', 'hidden');
                }
            }
        },

        /**
         * Validates the data entered into the dialog's form.
         *
         * @method validate
         * @return {Boolean} TRUE on success, FALSE on error.
         */
        validate: function () {
            this._clearValidationErrorStyles();
            var data = this.getData();
            var msgs = [];
            var hasStartDate = data.start_date ? true : false;

            if ('' === Lang.trim(data.title)) {
                msgs.push(ilios_i18nVendor.getI18NString('curriculum_inventory.sequence_block.validate.error.title_missing'));
                Dom.addClass('edit-sequence-block-dialog--title', 'validation-failed');
            }
            if ('' === Lang.trim(data.description)) {
                msgs.push(ilios_i18nVendor.getI18NString('curriculum_inventory.sequence_block.validate.error.description_missing'));
                Dom.addClass('edit-sequence-block-dialog--description', 'validation-failed');
            }
            if (hasStartDate) {
                if (! data.end_date) {
                    msgs.push(ilios_i18nVendor.getI18NString('curriculum_inventory.sequence_block.validate.error.missing_end_date'));
                    Dom.addClass('edit-sequence-block-dialog--end-date', 'validation-failed');
                }
                if (Date.parse(data.start_date) > Date.parse(data.end_date)) {
                    msgs.push(ilios_i18nVendor.getI18NString('curriculum_inventory.sequence_block.validate.error.start_date_gt_end_date'));
                    Dom.addClass('edit-sequence-block-dialog--end-date', 'validation-failed');
                }

                // if a duration is given then it must be valid
                if ("" !== Lang.trim(data.duration) && 0 > parseInt(data.duration, 10)) {
                    msgs.push(ilios_i18nVendor.getI18NString('curriculum_inventory.sequence_block.validate.error.invalid_duration'));
                    Dom.addClass('edit-sequence-block-dialog--duration', 'validation-failed');
                }
            } else {
                // duration is required and must be larger that zero
                if ("" === Lang.trim(data.duration) || 0 >= parseInt(data.duration, 10)) {
                    msgs.push(ilios_i18nVendor.getI18NString('curriculum_inventory.sequence_block.validate.error.missing_duration'));
                    Dom.addClass('edit-sequence-block-dialog--duration', 'validation-failed');
                }
            }

            if (msgs.length) {
                document.getElementById('edit-sequence-block-dialog--status').innerHTML = msgs.join('<br />') + '<br />';
                return false;
            }
            return true;
        },

        /**
         * Resets the start/end date calendar widgets and hides them from view.
         *
         * @method resetCalendars
         */
        resetCalendars: function () {
            var date = new Date();
            this.cal1.cfg.setProperty('selected', date, false);
            this.cal1.cfg.setProperty('pagedate', date, false);
            this.cal1.render();
            this.cal1.hide();

            this.cal2.cfg.setProperty('selected', date, false);
            this.cal2.cfg.setProperty('pagedate', date, false);
            this.cal2.render();
            this.cal2.hide();
        },

        /**
         * Makes the dialog visible, after resetting and populating it with the given data.
         *
         * @override
         * @method show
         * @param {ilios.cim.model.SequenceBlockModel} block The sequence block to edit.
         * @see YAHOO.widget.Module.show
         */
        show: function (block) {
            this.reset();
            this.populateForm(block);
            EditSequenceBlockDialog.superclass.show.call(this);

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
                dt = new Date(args[0][0][0], args[0][0][1] - 1, args[0][0][2]);
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
        },

        /**
         * Event type.
         * @property EVT_SEQUENCE_BLOCK_UPDATE_SUCCEEDED
         * @type {String}
         * @final
         * @see ilios.cim.widget.EditSequenceBlockDialog.sequenceBlockUpdateSucceededEvent
         */
        EVT_SEQUENCE_BLOCK_UPDATE_SUCCEEDED: 'sequenceBlockUpdateSucceeded',

        /**
         * Fired when the server response following form post for updating a sequence block indicating success
         * has been received, and the payload from that response has been parsed into a data map object.
         *
         * @event sequenceBlockUpdateSucceededEvent
         * @param {Object} data A plain data object containing the properties of updated sequence block record.
         * @param {Object} updated_siblings_order A map containing sequence-block-ids/order-in-sequence values as
         *      key/value pairs. The referenced blocks are siblings in a sequence to the updated block, and had
         *      their order-in-sequence value changed as a side-effect of the block update.
         * @param {Object} updated_children_order A map containing sequence-block-ids/order-in-sequence values as
         *      key/value pairs. The referenced blocks are children of the updated block, and had their order-in-sequence
         *      values changed as a side-effect of the block's "child-sequence-order" property update.
         */
        sequenceBlockUpdateSucceededEvent: null
    });


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

        /*
         * @override
         * @see YAHOO.util.Element.initAttributes
         */
        initAttributes: function (config) {

            /**
             * The "add new sequence block" button element in the toolbar.
             *
             * @attribute addBtnEl
             * @type {HTMLElement}
             * @writeOnce
             */
            this.setAttributeConfig('addBtnEl', {
                writeOnce: true,
                value: Dom.get('add-new-sequence-block-btn')
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
         *  @method hide
         */
        hide: function () {
            this.addClass('hidden');
        },

        /**
         * Renders the toolbar.
         *
         * @method render.
         * @param {Boolean} enableButtons If TRUE then all buttons contained within the toolbar will be enabled.
         */
        render: function (enableButtons) {

            if (enableButtons) {
                this.enableButtons();
            }
        },

        /**
         * Enables all buttons in the toolbar.
         *
         * @method enableButtons
         */
        enableButtons: function () {
            var el = this.get('addBtnEl');
            el.disabled = false;
        },

        /**
         * Disables all buttons in the toolbar.
         *
         * @method disableButtons
         */
        disableButtons: function () {
            var el = this.get('addBtnEl');
            Dom.setAttribute(el, 'disabled', 'disabled');
        },

        /**
         * Retrieves the toolbar's "add new sequence block" button.
         *
         * @method getAddButton
         * @returns {HTMLElement} The button element.
         */
        getAddButton: function () {
            return this.get('addBtnEl');
        }
    });

    /**
     * The status-indicator bar.
     * It's purpose is to display given (status-)messages on the page.
     *
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
    ilios.cim.widget.CreateSequenceBlockDialog = CreateSequenceBlockDialog;
    ilios.cim.widget.EditSequenceBlockDialog = EditSequenceBlockDialog;
}());
