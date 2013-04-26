/**
 * Client-side application code for the curriculum inventory management (cim) module.
 *
 * Defines the following namespaces:
 *     ilios.cim
 *     ilios.cim.dom
 *     ilios.cim.event
 *     ilios.cim.page
 *     ilios.cim.transaction
 *     ilios.cim.widget
 *
 *  Dependencies:
 *     application/views/scripts/ilios_base.js
 *     ilios_i18nVendor
 *     YUI Dom/Event/Element
 *     YUI Container libs
 */
ilios.namespace('cim.dom');
ilios.namespace('cim.event');
ilios.namespace('cim.page');
ilios.namespace('cim.transaction');
ilios.namespace('cim.widget');

/**
 * Module-level configuration.
 * @property config
 * @type {Object}
 */
ilios.cim.config = {};

/**
 * Entry point to the client-side application.
 * Initializes the page, loads the model, widgets etc.
 * @param {Object} config The module configuration.
 * @param {Number} [reportId] The Id of the report to display.
 * @method init
 *
 */
ilios.cim.page.init = function (config, reportId) {

    var Event = YAHOO.util.Event;

    // set module configuration
    ilios.cim.config = YAHOO.lang.isObject(config) ? config : {};
    reportId = reportId || false;

    // instantiate dialogs
    ilios.cim.page.reportSearchDialog = new ilios.cim.widget.ReportSearchDialog('report_search_picker');
    ilios.cim.page.reportSearchDialog.render();
    ilios.cim.page.createReportDialog = new ilios.cim.widget.CreateReportDialog('create_report_dialog');
    ilios.cim.page.createReportDialog.render();

    // wire dialogs to buttons
    Event.addListener('search_reports_btn', 'click', function (event) {
        ilios.cim.page.reportSearchDialog.show();
        Event.stopEvent(event);
        return false;
    });

    Event.addListener('create_report_btn', 'click', function (event) {
        ilios.cim.page.createReportDialog.show();
        Event.stopEvent(event);
        return false;
    });

    // wire up report details view
    if (reportId) {
        // @todo
    }
};

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
     */
    ilios.cim.widget.CreateReportDialog = function (el, userConfig){
        var defaultConfig = {
            width: "640px",
            modal: true,
            fixedcenter: true,
            visible: false,
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
        ilios.cim.widget.CreateReportDialog.superclass.constructor.call(this, el, config);

        // report model
        this.model = null;
    };

    // inheritance
    YAHOO.lang.extend(ilios.cim.widget.CreateReportDialog, YAHOO.widget.Dialog, {
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
     * "Edit Report" dialog.
     * @namespace ilios.cim.widget
     * @class EditReportDialog
     * @extends YAHOO.widget.Dialog
     * @constructor
     * @param {HTMLElement|String} el The element or element-ID representing the dialog
     * @param {Object} userConfig The configuration object literal containing
     *     the configuration that should be set for this dialog.
     */
    ilios.cim.widget.EditReportDialog = function (el, userConfig){
        var defaultConfig = {
            width: "640px",
            modal: true,
            fixedcenter: true,
            visible: false,
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
        ilios.cim.widget.EditReportDialog.superclass.constructor.call(this, el, config);

        // session model
        this.model = null;
    };

    // inheritance
    YAHOO.lang.extend(ilios.cim.widget.EditReportDialog, YAHOO.widget.Dialog, {
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
     * @class ReportSearchDialog
     * @extends YAHOO.widget.Dialog
     * @constructor
     * @param {HTMLElement|String} el The element or element-ID representing the dialog
     * @param {Object} userConfig The configuration object literal containing
     *     the configuration that should be set for this dialog.
     */
    ilios.cim.widget.ReportSearchDialog = function (el, userConfig) {

        var Event = YAHOO.util.Event;
        var KEY = YAHOO.util.KeyListener.KEY;

        var defaultConfig = {
            width: "600px",
            modal: true,
            visible: false,
            constraintoviewport: false,
            hideaftersubmit: false,
            buttons: [
                {
                    text: ilios_i18nVendor.getI18NString('general.terms.cancel'),
                    handler: function () {
                        this.cancel();
                    }
                },
                {
                    text: ilios_i18nVendor.getI18NString('general.phrases.search.clear'),
                    handler: function () {
                        this.emptySearchDialogForViewing();
                        return false;
                    }
                }
            ]
        };
        // merge the user config with the default configuration
        userConfig = userConfig || {};
        var config = YAHOO.lang.merge(defaultConfig, userConfig);

        // call the parent constructor with the merged config
        ilios.cim.widget.ReportSearchDialog.superclass.constructor.call(this, el, config);

        // clear out the dialog and center it before showing it.
        this.beforeShowEvent.subscribe(function () {
            this.emptySearchDialogForViewing();
            this.center();
        });

        this.beforeSubmitEvent.subscribe(function () {
            document.getElementById('report_search_status').innerHTML = "Searching...";
        });

        this.validate = function () {
            var data = this.getData();
            if (ilios.lang.trim(data.report_search_term).length < 2) {
                document.getElementById('report_search_status').innerHTML
                    = ilios_i18nVendor.getI18NString('general.error.query_length');
                return false;
            }
            return true;
        };

        /*
         * Form submission success handler.
         * @param {Object} resultObject
         */
        this.callback.success = function (resultObject) {
            var Element = YAHOO.util.Element;
            var parsedResponse, searchResultsContainer;
            var i, n;
            var reports;
            var liEl, wrapperEl, linkEl;

            try {
                parsedResponse = YAHOO.lang.JSON.parse(resultObject.responseText);
            } catch (e) {
                document.getElementById('report_search_status').innerHTML
                    = ilios_i18nVendor.getI18NString('general.error.must_retry');
                return;
            }

            searchResultsContainer = document.getElementById('report_search_results_list');
            ilios.utilities.removeAllChildren(searchResultsContainer);
            document.getElementById('report_search_status').innerHTML = '';

            if (parsedResponse.hasOwnProperty('error')) {
                document.getElementById('report_search_status').innerHTML = parsedResponse.error;
                return;
            }

            reports = parsedResponse.reports;
            if (! reports.length) {
                document.getElementById('report_search_status').innerHTML
                    = ilios_i18nVendor.getI18NString('general.phrases.search.no_match');
            }
            for (i =0, n = reports.length; i < n; i++) {
                liEl = new Element(document.createElement('li'));
                wrapperEl = new Element(document.createElement('span'));
                wrapperEl.addClass(wrapperEl, 'title');
                linkEl = new Element(document.createElement('a'));
                linkEl.set('href', window.location.protocol + "//" + window.location.host +
                    window.location.pathname + "?report_id=" + reports[i].report_id);
                linkEl.appendChild(document.createTextNode(reports[i].name));
                wrapperEl.appendChild(linkEl);
                liEl.appendChild(wrapperEl);
                liEl.appendTo(searchResultsContainer);
            }
        };

        /*
         * Form submission error handler.
         * @param {Object} resultObject
         */
        this.callback.failure = function (resultObject) {
            ilios.global.defaultAJAXFailureHandler(resultObject);
            document.getElementById('report_search_status').innerHTML
                = ilios_i18nVendor.getI18NString('general.error.must_retry');
        }

        // wire event handlers for input field and search button
        Event.addListener('report_search_term', 'keypress', function (event, dialog) {
            var charCode = event.keyCode ? event.keyCode : (event.which ? event.which : event.charCode);
            if (KEY.ENTER === charCode) {
                dialog.submit();
                Event.stopEvent(event);
                return false;
            }
            return true;
        }, this);

        Event.addListener('search_report_submit_btn', 'click', function (event, dialog) {
            dialog.submit();
            Event.stopEvent(event);
            return false;
        }, this);
    };

    // inheritance
    YAHOO.lang.extend(ilios.cim.widget.ReportSearchDialog, YAHOO.widget.Dialog, {
        emptySearchDialogForViewing: function () {
            var element = document.getElementById('report_search_results_list');
            ilios.utilities.removeAllChildren(element);
            element = document.getElementById('report_search_status');
            element.innerHTML = '';
            element = document.getElementById('report_search_term');
            element.value = '';
            element.focus();
        }
    });
}());



