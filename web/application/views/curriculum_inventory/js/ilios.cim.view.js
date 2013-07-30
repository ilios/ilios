/**
 * Curriculum inventory management (cim) view components.
 *
 * Defines the following namespaces:
 *     ilios.cim.view
 *
 *  Dependencies:
 *     application/views/scripts/ilios_base.js
 *     ilios_i18nVendor
 *     YUI Dom/Event/Element libs
 *     YUI Cookie lib
 *     application/views/curriculum_inventory/js/ilios.cim.model.js
 *     application/views/curriculum_inventory/js/ilios.cim.widget.js
 */
(function () {

    /**
     * The "view" module of the curriculum inventory manager (cim) application.
     * @module view
     */

    ilios.namespace('cim.view');

    var Lang = YAHOO.lang,
        Dom = YAHOO.util.Dom,
        Element = YAHOO.util.Element,
        Event = YAHOO.util.Event,
        Cookie = YAHOO.util.Cookie;

    /**
     * The view for a given sequence block model.
     * @namespace cim.view
     * @class SequenceBlockView
     * @constructor
     * @extends YAHOO.util.Element
     * @param {ilios.cim.model.SequenceBlockModel} model The sequence block model that this view displays.
     * @param {HTMLElement} el The root-element in the DOM that is rendered by this view-instance.
     */
    var SequenceBlockView = function(model, el) {

        SequenceBlockView.superclass.constructor.call(this, el, { cnumber: model.get('id') });

        // set properties
        this._model = model;
        // initialize cnumber and parent cnumber with the corresponding model's id and parent id.
        this._cnumber = model.get('id');
        this._parentCnumber = model.get('parentId');

        // subscribe to model changes
        // @todo implement

        // create custom events
        this.createEvent(this.EVT_DELETE);
    };

    Lang.extend(SequenceBlockView, Element, {

        /**
         * The view's model.
         *
         * @property _model
         * @type {ilios.cim.model.SequenceBlockModel}
         * @protected
         */
        _model: null,

        /**
         * The view's container number.
         *
         * @property _cnumber
         * @type {Number}
         * @protected
         */
        _cnumber: null,

        /**
         * The parent view's container number, if applicable.
         *
         * @property parentCnumber
         * @type {Number|null}
         * @protected
         */
        _parentCnumber: null,

        /**
         * Returns the view's model.
         *
         * @method getModel
         * @return {ilios.cim.model.SequenceBlockModel}
         */
        getModel: function () {
            return this._model;
        },

        /**
         * Returns the view's container number.
         *
         * @method getCnumber
         * @return {Number}
         */
        getCnumber: function () {
            return this._cnumber;
        },

        /**
         * Returns the container number of the parent sequence block view.
         * Top level views return <code>NULL</code>.
         *
         * @method getParentCnumber
         * @return {Number|null}
         */
        getParentCnumber: function () {
            return this._cnumber;
        },

        /*
         * @override
         * @see YAHOO.util.Element.initAttributes
         */
        initAttributes: function (config) {
            SequenceBlockView.superclass.initAttributes.call(this, config);
            var cnumber = config.cnumber;

            this.setAttributeConfig('titleEl', {
                writeOnce: true,
                value: Dom.get('sequence-block-view-title-' + cnumber)
            });
            this.setAttributeConfig('toggleBtnEl', {
               writeOnce: true,
               value: Dom.get('sequence-block-view-toggle-btn-' + cnumber)
            });
            this.setAttributeConfig('bodyEl', {
                writeOnce: true,
                value: Dom.get('sequence-block-view-body-' + cnumber)
            });

            this.setAttributeConfig('title', {
                validator: Lang.isString,
                method: function (value) {
                    var el = this.get('titleEl');
                    if (el) {
                        el.innerHTML = value;
                    }
                },
                value: ''
            });
        },

        /**
         * @method delete
         * Lifecycle management method.
         * "Deletes" the view from the page.
         * This includes unsubscribing any event listeners, detaching the view from it's parent element in the page
         * and hiding it from display.
         * Fires the "delete" custom event.
         * @see YAHOO.util.Element.destroy()
         */
        delete: function () {
            this.hide();
            this.fire(this.EVT_DELETE, { cnumber: this._cnumber });
            // Call YAHOO.util.Element.destroy().
            // This method is undocumented, so here is the low-down:
            // - it removes all event listeners registered to this element
            // - it removes all event listeners from the element's children as well
            // - it detaches the element from it's parent in the document.
            // Check the code for details.
            this.destroy();
        },

        /**
         * @method hide
         * Hides the view.
         */
        hide: function () {
            this.addClass('hidden');
        },

        /**
         * @method show
         * Makes the view visible.
         */
        show: function () {
            this.removeClass('hidden');
        },

        /**
         * @method render
         * Renders the view.
         * This includes populating the view with the model data and wiring event handling.
         */
        render: function () {

            this.set('title', this._model.get('title'));

            // wire buttons
            Event.addListener(this.get('toggleBtnEl'), 'click', function (event) {
                if (this.hasClass('collapsed')) {
                    this.expand();
                } else {
                    this.collapse();
                }
                Event.stopEvent(event);
                return false;
            }, {}, this);
        },

        /**
         * @method expand
         * Expands the view-container body.
         */
        expand: function () {
            Dom.removeClass(this.get('bodyEl'), 'hidden');
            this.removeClass('collapsed');
            this.addClass('expanded');
        },

        /**
         * @method collapse
         * Collapses the view-container body.
         */
        collapse: function () {
            Dom.addClass(this.get('bodyEl'), 'hidden');
            this.removeClass('expanded');
            this.addClass('collapsed');
        },


        /**
         * Fired when the view gets deleted.
         * @event delete
         * @param {Number} cnumber The view's container number.
         * @final
         */
        EVT_DELETE: 'delete'
    });

    /**
     * The view for a given report model.
     * @namespace cim.view
     * @class ReportView
     * @constructor
     * @extends YAHOO.util.Element
     * @param {ilios.cim.model.ReportModel} model The report model.
     * @param {Object} oConfig A configuration object.
     */
    var ReportView = function (model, oConfig) {
        ReportView.superclass.constructor.call(this, document.getElementById('report-details-view-container'), oConfig);

        // set properties
        this.config = oConfig;
        this.model = model;

        // subscribe to model changes
        this.model.subscribe('nameChange', this.onNameChange, {}, this);
        this.model.subscribe('descriptionChange', this.onDescriptionChange, {}, this);
        this.model.subscribe('startDateChange', this.onStartDateChange, {}, this);
        this.model.subscribe('endDateChange', this.onEndDateChange, {}, this);
        this.model.subscribe('isFinalizedChange', this.onStatusChange, {}, this);

        // create custom events
        this.createEvent(ReportView.EVT_DOWNLOAD_STARTED);
        this.createEvent(ReportView.EVT_DOWNLOAD_COMPLETED);
        this.createEvent(ReportView.EVT_EXPORT_STARTED);
        this.createEvent(ReportView.EVT_EXPORT_COMPLETED);
 /*
        this.createEvent('finalizeStarted');
        this.createEvent('finalizeSucceeded');
        this.createEvent('finalizeFailed');
        this.createEvent('deleteStarted');
        this.createEvent('deleteSucceeded');
        this.createEvent('deleteFailed');

        // subscribe to own events
        this.subscribe('finalizeStarted', function () {
            this.lockDraftModeControls();
        }, {}, this);
        this.subscribe('finalizeFailed', function () {
            this.unlockDraftModeControls();
        }, {}, this);
        this.subscribe('finalizeSucceeded', function () {
            model.set('isFinalized', true);
        }, {}, this);
        this.subscribe('deleteStarted', function () {
            this.lockDraftModeControls();
        }, {}, this);
        this.subscribe('deleteFailed', function () {
            this.unlockDraftModeControls();
        }, {}, this);
        */
    };

    Lang.extend(ReportView, Element, {

        /**
         * Timer object for the tracking the report download progress.
         * @var _downloadIntervalTimer
         * @type {Object}
         * @protected
         */
        _downloadIntervalTimer: null,

        /**
         * Timer object for the tracking the report export progress.
         * @var _downloadIntervalTimer
         * @type {Object}
         * @protected
         */
        _exportIntervalTimer: null,

        /**
         * Locks the download button during the duration of a report download, and starts a timer for tracking the
         * download process.
         *
         * @method _blockUIForDownload
         * @protected
         */
        _blockUIForDownload: function () {
            var token = (new Date()).getTime();
            Dom.setAttribute(this.get("downloadBtnEl"), 'disabled', 'disabled');
            this.fireEvent(ReportView.EVT_DOWNLOAD_STARTED);
            this.set('downloadToken', token);
            this._downloadIntervalTimer = Lang.later(1000, this, function () {
                var cookieValue = Cookie.get('download-token');
                if (cookieValue == token) {
                    this._finishDownload();
                }
            }, [], true);
        },

        /**
         * Unlocks the download button once a response has been received from the server.
         *
         * @method _finishDownload
         * @protected
         */
        _finishDownload: function () {
            var el = this.get("downloadBtnEl");
            this._downloadIntervalTimer.cancel();
            Cookie.remove('fileDownloadToken');
            el.disabled = false;
            this.fireEvent(ReportView.EVT_DOWNLOAD_COMPLETED);
        },

        /**
         * Locks the export button during the duration of a report export, and starts a timer for tracking the
         * export process.
         *
         * @method _blockUIForExport
         * @protected
         */
        _blockUIForExport: function () {
            var token = (new Date()).getTime();
            Dom.setAttribute(this.get("exportBtnEl"), 'disabled', 'disabled');
            this.fireEvent(ReportView.EVT_EXPORT_STARTED);
            this.set('exportToken', token);
            this._exportIntervalTimer = Lang.later(1000, this, function () {
                var cookieValue = Cookie.get('download-token');
                if (cookieValue == token) {
                    this._finishExport();
                }
            }, [], true);
        },

        /**
         * Unlocks the export button once a response has been received from the server.
         *
         * @method _finishExport
         * @protected
         */
        _finishExport: function () {
            var el = this.get("exportBtnEl");
            this._exportIntervalTimer.cancel();
            Cookie.remove('fileExportToken');
            el.disabled = false;
            this.fireEvent(ReportView.EVT_EXPORT_COMPLETED);
        },

        /**
         * Disables "report in draft"-mode view controls.
         *
         * @method lockDraftModeControls
         */
        lockDraftModeControls: function () {
            Dom.setAttribute(this.getEditButton(), 'disabled', 'disabled');
            Dom.setAttribute(this.getFinalizeButton(), 'disabled', 'disabled');
            Dom.setAttribute(this.getDeleteButton(), 'disabled', 'disabled');
            Dom.setAttribute(this.get("exportBtnEl"), 'disabled', 'disabled');
        },

        /**
         * Enables "report in draft"-mode view controls.
         *
         * @method unlockDraftModeControls
         */
        unlockDraftModeControls: function () {
            var el = this.getEditButton();
            el.disabled = false;
            el = this.getFinalizeButton();
            el.disabled = false;
            el = this.getDeleteButton();
            el.disabled = false;
            el = this.get("exportBtnEl");
            el.disabled = false;
        },

        /**
         * Hides "report in draft"-mode view controls.
         *
         * @method hideDraftModeControls
         */
        hideDraftModeControls: function () {
            Dom.addClass(this.getEditButton(), 'hidden');
            Dom.addClass(this.getFinalizeButton(), 'hidden');
            Dom.addClass(this.getDeleteButton(), 'hidden');
            Dom.addClass(this.get('exportFormEl'), 'hidden');
        },

        /**
         * Makes "report in draft"-mode view controls visible.
         *
         * @method showDraftModeControls
         */
        showDraftModeControls: function () {
            Dom.removeClass(this.getEditButton(), 'hidden');
            Dom.removeClass(this.getFinalizeButton(), 'hidden');
            Dom.removeClass(this.getDeleteButton(), 'hidden');
            Dom.removeClass(this.get('exportFormEl'), 'hidden');
        },

        /**
         * Disables "finalized report"-mode view controls.
         *
         * @method lockFinalizedModeControls
         */
        lockFinalizedModeControls: function () {
            Dom.setAttribute(this.get("downloadBtnEl"), 'disabled', 'disabled');
        },

        /**
         * Enables "finalized report"-mode view controls.
         *
         * @method unlockFinalizedModeControls
         */
        unlockFinalizedModeControls: function () {
            var el = this.get("downloadBtnEl");
            el.disabled = false;
        },

        /**
         * Hides "finalized report"-mode view controls.
         *
         * @method hideFinalizedModeControls
         */
        hideFinalizedModeControls: function () {
            Dom.addClass(this.get("downloadFormEl"), 'hidden');
        },

        /**
         * Makes "finalized report"-mode view controls visible.
         *
         * @method showFinalizedModeControls
         */
        showFinalizedModeControls: function () {
            Dom.removeClass(this.get("downloadFormEl"), 'hidden');
        },

        /*
         * @override
         * @see YAHOO.util.Element.initAttributes
         */
        initAttributes : function (config) {
            ReportView.superclass.initAttributes.call(this, config);

            /**
             * The display-element of the view's name attribute.
             *
             * @attribute nameEl
             * @type {HTMLElement}
             * @writeOnce
             */
            this.setAttributeConfig('nameEl', {
                writeOnce: true,
                value: Dom.get('report-details-view-name')
            });

            /**
             * The display-element of the view's academic-year attribute.
             *
             * @attribute academicYearEl
             * @type {HTMLElement}
             * @writeOnce
             */
            this.setAttributeConfig('academicYearEl', {
                writeOnce: true,
                value: Dom.get('report-details-view-academic-year')
            });

            /**
             * The display-element of the view's start-date attribute.
             *
             * @attribute startDateEl
             * @type {HTMLElement}
             * @writeOnce
             */
            this.setAttributeConfig('startDateEl', {
                writeOnce: true,
                value: Dom.get('report-details-view-start-date')
            });

            /**
             * The display-element of the view's end-date attribute.
             *
             * @attribute endDateEl
             * @type {HTMLElement}
             * @writeOnce
             */
            this.setAttributeConfig('endDateEl', {
                writeOnce: true,
                value: Dom.get('report-details-view-end-date')
            });

            /**
             * The display-element of the view's description attribute.
             *
             * @attribute descriptionEl
             * @type {HTMLElement}
             * @writeOnce
             */
            this.setAttributeConfig('descriptionEl', {
                writeOnce: true,
                value: Dom.get('report-details-view-description')
            });

            /**
             * The display element of the view's program attribute.
             *
             * @attribute programEl
             * @type {HTMLElement}
             * @writeOnce
             */
            this.setAttributeConfig('programEl', {
                writeOnce: true,
                value: Dom.get('report-details-view-program')
            });

            /**
             * The input element for the view's id attribute in the view's "export report" form.
             *
             * @attribute reportExportIdEl
             * @type {HTMLInputElement}
             * @writeOnce
             */
            this.setAttributeConfig('reportExportIdEl', {
                writeOnce: true,
                value: Dom.get('report-details-view-export-report-id')
            });

            /**
             * The input element for the view's id attribute in the view's "download report" form.
             *
             * @attribute reportDownloadIdEl
             * @type {HTMLInputElement}
             * @writeOnce
             */
            this.setAttributeConfig('reportDownloadIdEl', {
                writeOnce: true,
                value: Dom.get('report-details-view-download-report-id')
            });

            /**
             * The input element for the request-token in the view's "download report" form.
             *
             * @attribute downloadTokenEl
             * @type {HTMLInputElement}
             * @writeOnce
             */
            this.setAttributeConfig('downloadTokenEl', {
                writeOnce: true,
                value: Dom.get('report-details-view-download-download-token')
            });

            /**
             * The input element for the request-token in the view's "export report" form.
             *
             * @attribute exportTokenEl
             * @type {HTMLInputElement}
             * @writeOnce
             */
            this.setAttributeConfig('exportTokenEl', {
                writeOnce: true,
                value: Dom.get('report-details-view-export-download-token')
            });

            /**
             * The display element of the view's status attribute.
             *
             * @attribute statusEl
             * @type {HTMLElement}
             * @writeOnce
             */
            this.setAttributeConfig('statusEl', {
                writeOnce: true,
                value: Dom.get('report-details-status')
            });

            /**
             * The view's "Edit" button element.
             *
             * @attribute editBtnEl
             * @type {HTMLElement}
             * @writeOnce
             */
            this.setAttributeConfig('editBtnEl', {
                writeOnce: true,
                value: Dom.get(this.get('editBtnEl'))
            });

            /**
             * The view's "Finalize" button element.
             *
             * @attribute finalizeBtnEl
             * @type {HTMLElement}
             * @writeOnce
             */
            this.setAttributeConfig('finalizeBtnEl', {
                writeOnce: true,
                value: Dom.get('report-details-view-finalize-button')
            });

            /**
             * The view's "Download" button element.
             *
             * @attribute downloadBtnEl
             * @type {HTMLElement}
             * @writeOnce
             */
            this.setAttributeConfig('downloadBtnEl', {
                writeOnce: true,
                value: Dom.get('report-details-view-download-button')
            });

            /**
             * The view's "Download" form element.
             *
             * @attribute editFormEl
             * @type {HTMLFormElement}
             * @writeOnce
             */
            this.setAttributeConfig('downloadFormEl', {
                writeOnce: true,
                value: Dom.get('report-details-view-download-form')
            });

            /**
             * The view's "Export" button element.
             *
             * @attribute exportBtnEl
             * @type {HTMLElement}
             * @writeOnce
             */
            this.setAttributeConfig('exportBtnEl', {
                writeOnce: true,
                value: Dom.get('report-details-view-export-button')
            });

            /**
             * The view's "Export" form element.
             *
             * @attribute exportFormEl
             * @type {HTMLFormElement}
             * @writeOnce
             */
            this.setAttributeConfig('exportFormEl', {
                writeOnce: true,
                value: Dom.get('report-details-view-export-form')
            });

            /**
             * The view's "Delete" button element.
             *
             * @attribute deleteBtnEl
             * @type {HTMLElement}
             * @writeOnce
             */
            this.setAttributeConfig('deleteBtnEl', {
                writeOnce: true,
                value: Dom.get('report-details-view-delete-button')
            });

            /**
             * The report-name displayed by the view.
             *
             * @attribute name
             * @type {String}
             * @default ""
             */
            this.setAttributeConfig('name', {
                validator: Lang.isString,
                method: function (value) {
                    var el = this.get('nameEl');
                    if (el) {
                        el.innerHTML = value;
                    }
                },
                value: ""
            });

            /**
             * The academic year of a report displayed by the view.
             *
             * @attribute academicYear
             * @type {String}
             * @default ""
             */
            this.setAttributeConfig('academicYear', {
                validator: Lang.isString,
                method: function (value) {
                    var el = this.get('academicYearEl');
                    if (el) {
                        el.innerHTML = value;
                    }
                },
                value: ""
            });

            /**
             * The report start-date displayed by the view.
             *
             * @attribute startDate
             * @type {String}
             * @default ""
             */
            this.setAttributeConfig('startDate', {
                validator: Lang.isString,
                method: function (value) {
                    var el = this.get('startDateEl');
                    if (el) {
                        el.innerHTML = value;
                    }
                },
                value: ""
            });

            /**
             * The report end-date displayed by the view.
             *
             * @attribute endDate
             * @type {String}
             * @default ""
             */
            this.setAttributeConfig('endDate', {
                validator: Lang.isString,
                method: function (value) {
                    var el = this.get('endDateEl');
                    if (el) {
                        el.innerHTML = value;
                    }
                },
                value: ""
            });

            /**
             * The report description displayed by the view.
             *
             * @attribute description
             * @type {String}
             * @default ""
             */
            this.setAttributeConfig('description', {
                validator: Lang.isString,
                method: function (value) {
                    var el = this.get('descriptionEl');
                    if (el) {
                        el.innerHTML = value;
                    }
                },
                value: ""
            });

            /**
             * The program of a report displayed by the view.
             *
             * @attribute program
             * @type {Object}
             */
            this.setAttributeConfig('program', {
                validator: Lang.isObject,
                method: function (value) {
                    var el = this.get('programEl');
                    if (el) {
                        el.innerHTML = value.title + " (" + value.short_title + ")"
                    }
                }
            });

            /**
             * The id of the report displayed by the view.
             *
             * @attribute reportId
             * @type {String}
             */
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

            /**
             * The request-token used in the view's download form.
             *
             * @attribute downloadToken
             * @type {String}
             */
            this.setAttributeConfig('downloadToken', {
                method: function (value) {
                    var el = this.get('downloadTokenEl');
                    if (el) {
                        el.value = value;
                    }
                }
            });

            /**
             * The request-token used in the view's export form.
             *
             * @attribute exportToken
             * @type {String}
             */
            this.setAttributeConfig('exportToken', {
                method: function (value) {
                    var el = this.get('exportTokenEl');
                    if (el) {
                        el.value = value;
                    }
                }
            });

            /**
             * The finalized flag (which is a status indicator) of this view. Setting this value controls the display
             * and access of various input elements/forms ("controls") of this view.
             * Setting it to TRUE will hide/lock the "draft mode" controls and show/enable the "report finalized" controls.
             * Setting it to FALSE will hide/lock the "report finalized" buttons and show/enable the "draft mode" controls.
             *
             * @attribute isFinalized
             * @type {Boolean}
             */
            this.setAttributeConfig('isFinalized', {
                method: function (value) {
                    var el;
                    if (value) {
                        el = this.get('statusEl');
                        Dom.removeClass(el, 'is-draft');
                        Dom.addClass(el, 'is-locked');
                        el.innerHTML = ilios_i18nVendor.getI18NString('general.terms.finalized');
                        this.lockDraftModeControls();
                        this.hideDraftModeControls();
                        this.unlockFinalizedModeControls();
                        this.showFinalizedModeControls();
                    } else {
                        el = this.get('statusEl');
                        Dom.removeClass(el, 'is-locked');
                        Dom.addClass(el, 'is-draft');
                        el.innerHTML = ilios_i18nVendor.getI18NString('general.terms.draft');
                        this.unlockDraftModeControls();
                        this.showDraftModeControls();
                        this.lockFinalizedModeControls();
                        this.hideFinalizedModeControls();
                    }
                }
            });
        },

        /**
         *  Returns the view's "Edit" button.
         *
         * @method getEditButton
         * @return {HTMLElement}
         */
        getEditButton: function () {
            return this.get('editBtnEl');
        },

        /**
         * Returns the view's "Finalize" button.
         *
         * @method getFinalizeButton
         * @return {HTMLElement}
         */
        getFinalizeButton: function () {
            return this.get('finalizeBtnEl');
        },

        /**
         * Returns the view's "Delete" button.
         *
         * @method getDeleteButton
         * @return {HTMLElement}
         */
        getDeleteButton: function () {
            return this.get('deleteBtnEl');
        },

        /**
         * Renders the view. This entails populating the view from the corresponding model and wiring up event handling.
         *
         * @method render
         */
        render: function () {

            this.set('name', this.model.get('name'));
            this.set('description', this.model.get('description'));
            this.set('academicYear', this.model.get('academicYear'));
            this.set('startDate', this.model.get('startDate'));
            this.set('endDate', this.model.get('endDate'));
            this.set('program', this.model.get('program'));
            this.set('reportId', this.model.get('id'));
            this.set('isFinalized', this.model.get('isFinalized'));

            //
            // wire and show applicable dialog buttons
            //

            // always wire the finalized mode buttons first
            Event.addListener('report-details-view-toggle', 'click', function (event) {
                ilios.utilities.toggle('report-details-view-content-wrapper', this);
                Event.stopEvent(event);
                return false;
            });
            Event.addListener(this.get("downloadFormEl"), 'submit', this._blockUIForDownload, {}, this);

            // if the report view gets rendered in draft mode then wire all of its buttons too.
            if (! this.get('status')) {
                Event.addListener(this.getEditButton(), 'click', function(event) {
                    if (! this.editReportDialog) {
                        this.editReportDialog = new ilios.cim.widget.EditReportDialog('edit_report_dialog', this.model);
                    }
                    this.editReportDialog.show();
                    Event.stopEvent(event);
                    return false;
                }, {}, this);
                Event.addListener(this.get('exportFormEl'), 'submit', this._blockUIForExport, {}, this);

                // @todo decouple server interaction from view.
                Event.addListener(this.getDeleteButton(), 'click', function (event, args) {
                    var continueStr = ilios_i18nVendor.getI18NString('curriculum_inventory.delete.confirm.warning')
                        + '<br /><br />' + ilios_i18nVendor.getI18NString('general.phrases.want_to_continue');
                    var yesStr = ilios_i18nVendor.getI18NString('general.terms.yes');
                    ilios.alert.inform(continueStr, yesStr, function (event, args) {
                        var model = args.model;
                        var url = args.url;
                        var view = args.view;
                        var postData = 'report_id=' + encodeURIComponent(model.get('id'));
                        var callback = {
                            success: function (o) {
                                var response, msg;
                                try {
                                    response = YAHOO.lang.JSON.parse(o.responseText);
                                } catch (e) {
                                    view.fireEvent('deleteFailed');
                                    ilios.global.defaultAJAXFailureHandler(null, e);
                                    return;
                                }
                                if (response.error) {
                                    view.fireEvent('deleteFailed');
                                    msg = ilios_i18nVendor.getI18NString('curriculum_inventory.delete.error.general');
                                    ilios.alert.alert(msg + ": " + response.error);
                                    return;
                                }
                                view.fireEvent('deleteSucceeded');
                            },
                            failure: function (o) {
                                view.fireEvent('deleteFailed');
                                ilios.global.defaultAJAXFailureHandler(o);
                            }
                        };

                        this.hide(); // hide the calling dialog

                        view.fireEvent('deleteStarted');
                        YAHOO.util.Connect.initHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
                        YAHOO.util.Connect.asyncRequest("POST", url, callback, postData);
                    }, args);
                }, {
                    model: this.model,
                    url: this.config.deleteUrl,
                    view: this
                },
                this);


                Event.addListener(this.getFinalizeButton(), 'click', function (event, args) {
                    var continueStr = ilios_i18nVendor.getI18NString('curriculum_inventory.finalize.confirm.warning')
                        + '<br /><br />' + ilios_i18nVendor.getI18NString('general.phrases.want_to_continue');
                    var yesStr = ilios_i18nVendor.getI18NString('general.terms.yes');
                    ilios.alert.inform(continueStr, yesStr, function (event, args) {
                        var model = args.model;
                        var url = args.url;
                        var view = args.view;
                        var postData = 'report_id=' + encodeURIComponent(model.get('id'));
                        var callback = {
                            success: function (o) {
                                var response, msg;
                                try {
                                    response = YAHOO.lang.JSON.parse(o.responseText);
                                } catch (e) {
                                    view.fireEvent('finalizeFailed');
                                    ilios.global.defaultAJAXFailureHandler(null, e);
                                    return;
                                }
                                if (response.error) {
                                    view.fireEvent('finalizeFailed');
                                    msg = ilios_i18nVendor.getI18NString('curriculum_inventory.finalize.error.general');
                                    ilios.alert.alert(msg + ": " + response.error);
                                    return;
                                }
                                view.fireEvent('finalizeSucceeded');
                            },
                            failure: function (o) {
                                view.fireEvent('finalizeFailed');
                                ilios.global.defaultAJAXFailureHandler(o);
                            }
                        };

                        this.hide(); // hide the calling dialog

                        view.fireEvent('finalizeStarted');
                        YAHOO.util.Connect.initHeader("Content-Type", "application/x-www-form-urlencoded; charset=UTF-8");
                        YAHOO.util.Connect.asyncRequest("POST", url, callback, postData);
                    }, args);
                }, {
                    model: this.model,
                    url: this.config.finalizeUrl,
                    view: this
                },
                this);
            }

        },

        /**
         * Makes the view visible in the page.
         *
         * @method show
         */
        show: function () {
            this.setStyle('display', 'block');
        },

        /**
         * Event listener method.
         *
         * Listens for a name change in the view's model and updates the view's name attribute accordingly.
         *
         * @method onNameChange
         * @param {Object} evObj An  object containing the old and new report title.
         */
        onNameChange: function (evObj) {
            this.set('name', evObj.newValue);
        },

        /**
         * Event listener method.
         *
         * Listens for a description change in the view's model and updates the view's description attribute accordingly.
         *
         * @method onDescriptionChange
         * @param {Object} evObj An  object containing the old and new report description.
         */
        onDescriptionChange: function (evObj) {
            this.set('description', evObj.newValue);
        },

        /**
         * Event listener method.
         *
         * Listens for a start date change in the view's model and updates the view's start date attribute accordingly.
         *
         * @method onStartDateChange
         * @param {Object} evObj An  object containing the old and new report start date.
         */
        onStartDateChange: function (evObj) {
            this.set('startDate', evObj.newValue);
        },

        /**
         * Event listener method.
         *
         * Listens for a end date change in the view's model and updates the view's end date attribute accordingly.
         *
         * @method onEndDateChange
         * @param {Object} evObj An  object containing the old and new report end date.
         */
        onEndDateChange: function (evObj) {
            this.set('endDate', evObj.newValue);
        },

        /**
         * Event listener method.
         *
         * Listens for a status change in the view's model and updates the view's end isFinalized attribute accordingly.
         *
         * @method onStatusChange
         * @param {Object} evObj An  object containing the old and new report status.
         */
        onStatusChange: function (evObj) {
            this.set('isFinalized', evObj.newValue);
        },

        /**
         * Fired when a report download request has been sent to the server.
         * @event downloadStarted
         * @final
         */
        EVT_DOWNLOAD_STARTED: "downloadStarted",

        /**
         * Fired when a report has been downloaded.
         * @event downloadCompleted
         * @final
         */
        EVT_DOWNLOAD_COMPLETED: "downloadCompleted",

        /**
         * Fired when a report export request has been sent to the server.
         * @event exportStarted
         * @final
         */
        EVT_EXPORT_STARTED: "exportStarted",

        /**
         * Fired when a report has been exported.
         * @event exportCompleted
         * @final
         */
        EVT_EXPORT_COMPLETED: "exportCompleted"

    });

    ilios.cim.view.ReportView = ReportView;
    ilios.cim.view.SequenceBlockView = SequenceBlockView;
}());
