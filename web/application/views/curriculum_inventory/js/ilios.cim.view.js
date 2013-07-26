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

    ilios.namespace('cim.view');

    var Lang = YAHOO.lang,
        Dom = YAHOO.util.Dom,
        Element = YAHOO.util.Element,
        Event = YAHOO.util.Event,
        Cookie = YAHOO.util.Cookie;

    /**
     * The view for a given sequence block model.
     * @namespace ilios.cim.view
     * @class SequenceBlockView
     * @constructor
     * @extends YAHOO.util.Element
     * @param {ilios.cim.model.SequenceBlockModel} model The sequence block model.
     * @param {HTMLElement} el The dom element corresponding to this view.
     * @param {Object} oConfig A configuration object. Expected properties are:
     *     'cnumber' ... The container number of this view. Should be unique within the context of the page.
     */
    var SequenceBlockView = function(model, el, oConfig) {

        SequenceBlockView.superclass.constructor.call(this, el, oConfig);

        // set properties
        this.config = oConfig;
        this.cnumber = this.config.cnumber;
        this.model = model;

        // subscribe to model changes
        // @todo implement

        // create custom events
        this.createEvent(this.EVT_DELETE);
    };

    Lang.extend(SequenceBlockView, Element, {

        /**
         * The view's configuration object.
         * @property config
         * @type {Object}
         */
        config: null,

        /**
         * The view's container number.
         * @property cnumber
         * @type {Number}
         */
        cnumber: null,

        /**
         * @property model
         * @type {ilios.cim.model.SequenceBlockModel}
         */
        model: null,

        /**
         * Overrides Element's <code>initAttributes()</code> method.
         * @param {Object} config The view's configuration object.
         * @see YAHOO.util.Element.initAttributes()
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
            this.fire(this.EVT_DELETE, { cnumber: this.cnumber });
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

            this.set('title', this.model.get('title'));

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
        EVT_DELETE : 'delete'
    });

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
        this.createEvent('exportStarted');
        this.createEvent('exportFinished');
        this.createEvent('downloadStarted');
        this.createEvent('downloadFinished');
        this.createEvent('finalizeStarted');
        this.createEvent('finalizeSucceeded');
        this.createEvent('finalizeFailed');
        this.createEvent('deleteStarted');
        this.createEvent('deleteSucceeded');
        this.createEvent('deleteFailed');

        // subscribe to own events
        this.subscribe('finalizeStarted', function () {
            this._lockDraftModeButtons();
        }, {}, this);
        this.subscribe('finalizeFailed', function () {
            this._unlockDraftModeButtons();
        }, {}, this);
        this.subscribe('finalizeSucceeded', function () {
            model.set('isFinalized', true);
        }, {}, this);
        this.subscribe('deleteStarted', function () {
            this._lockDraftModeButtons();
        }, {}, this);
        this.subscribe('deleteFailed', function () {
            this._unlockDraftModeButtons();
        }, {}, this);
    };

    Lang.extend(ReportView, Element, {
        _downloadIntervalTimer: null,
        _exportIntervalTimer: null,
        _blockUIForDownload: function () {
            var token = (new Date()).getTime();
            Dom.setAttribute('report-details-view-download-button', 'disabled', 'disabled');
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
            var el = document.getElementById('report-details-view-download-button');
            this._downloadIntervalTimer.cancel();
            Cookie.remove('fileDownloadToken');
            el.disabled = false;
            this.fireEvent('downloadFinished');
        },
        _blockUIForExport: function () {
            var token = (new Date()).getTime();
            Dom.setAttribute('report-details-view-export-button', 'disabled', 'disabled');
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
            var el = document.getElementById('report-details-view-export-button');
            this._exportIntervalTimer.cancel();
            Cookie.remove('fileExportToken');
            el.disabled = false;
            this.fireEvent('exportFinished');
        },

        _lockDraftModeButtons: function () {
            Dom.setAttribute('report-details-view-edit-button', 'disabled', 'disabled');
            Dom.setAttribute('report-details-view-finalize-button', 'disabled', 'disabled');
            Dom.setAttribute('report-details-view-delete-button', 'disabled', 'disabled');
            Dom.setAttribute('report-details-view-export-button', 'disabled', 'disabled');
        },
        _unlockDraftModeButtons: function () {
            var el = document.getElementById('report-details-view-edit-button');
            el.disabled = false;
            el = document.getElementById('report-details-view-finalize-button');
            el.disabled = false;
            el = document.getElementById('report-details-view-delete-button');
            el.disabled = false;
            el = document.getElementById('report-details-view-export-button');
            el.disabled = false;
        },
        _hideDraftModeButtons: function () {
            Dom.addClass('report-details-view-edit-button', 'hidden');
            Dom.addClass('report-details-view-finalize-button', 'hidden');
            Dom.addClass('report-details-view-delete-button', 'hidden');
            Dom.addClass('report-details-view-export-form', 'hidden');
        },
        _showDraftModeButtons: function () {
            Dom.removeClass('report-details-view-edit-button', 'hidden');
            Dom.removeClass('report-details-view-finalize-button', 'hidden');
            Dom.removeClass('report-details-view-delete-button', 'hidden');
            Dom.removeClass('report-details-view-export-form', 'hidden');
        },
        _lockFinalizedModeButtons: function () {
            Dom.setAttribute('report-details-view-download-button', 'disabled', 'disabled');
        },
        _unlockFinalizedModeButtons: function () {
            var el = document.getElementById('report-details-view-download-button');
            el.disabled = false;
        },
        _hideFinalizedModeButtons: function () {
            Dom.addClass('report-details-view-download-form', 'hidden');
        },
        _showFinalizedModeButtons: function () {
            Dom.removeClass('report-details-view-download-form', 'hidden');
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

            this.setAttributeConfig('isFinalized', {
                method: function (value) {
                    var el;
                    if (value) {
                        el = this.get('statusEl');
                        Dom.removeClass(el, 'is-draft');
                        Dom.addClass(el, 'is-locked');
                        el.innerHTML = ilios_i18nVendor.getI18NString('general.terms.finalized');
                        this._lockDraftModeButtons();
                        this._hideDraftModeButtons();
                        this._unlockFinalizedModeButtons();
                        this._showFinalizedModeButtons();
                    } else {
                        el = this.get('statusEl');
                        Dom.removeClass(el, 'is-locked');
                        Dom.addClass(el, 'is-draft');
                        el.innerHTML = ilios_i18nVendor.getI18NString('general.terms.draft');
                        this._unlockDraftModeButtons();
                        this._showDraftModeButtons();
                        this._lockFinalizedModeButtons();
                        this._hideFinalizedModeButtons();
                    }
                }
            });
        },
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
            Event.addListener('report-details-view-download-form', 'submit', this._blockUIForDownload, {}, this);

            // if the report view gets rendered in draft mode then wire all of its buttons too.
            if (! this.get('status')) {
                Event.addListener('report-details-view-edit-button', 'click', function(event) {
                    if (! this.editReportDialog) {
                        this.editReportDialog = new ilios.cim.widget.EditReportDialog('edit_report_dialog', this.model);
                    }
                    this.editReportDialog.show();
                    Event.stopEvent(event);
                    return false;
                }, {}, this);
                Event.addListener('report-details-view-export-form', 'submit', this._blockUIForExport, {}, this);

                // @todo decouple server interaction from view.
                Event.addListener('report-details-view-delete-button', 'click', function (event, args) {
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


                Event.addListener('report-details-view-finalize-button', 'click', function (event, args) {
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
            this.set('isFinalized', evObj.newValue);
        }
    });


    ilios.cim.view.ReportView = ReportView;
    ilios.cim.view.SequenceBlockView = SequenceBlockView;
}());
