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
     *
     * @namespace cim.view
     * @class SequenceBlockView
     * @extends YAHOO.util.Element
     * @constructor
     * @param {ilios.cim.model.SequenceBlockModel} model The sequence block that this view displays.
     * @param {HTMLElement} el The root-element in the DOM that is rendered by this view-instance.
     */
    var SequenceBlockView = function(model, el) {

        SequenceBlockView.superclass.constructor.call(this, el, { cnumber: model.get('id') });

        // set properties
        this._model = model;

        // initialize cnumber and parent cnumber with the corresponding model's id and parent id.
        this._cnumber = model.get('id');

        // subscribe to model changes
        this._model.subscribe(this._model.EVT_DELETE, this.delete, {}, this);
        this._model.subscribe('titleChange', this.onTitleChange, {}, this);
        this._model.subscribe('descriptionChange', this.onDescriptionChange, {}, this);
        this._model.subscribe('requiredChange', this.onRequiredChange, {}, this);
        this._model.subscribe('academicLevelChange', this.onAcademicLevelChange, {}, this);
        this._model.subscribe('courseChange', this.onCourseChange, {}, this);
        this._model.subscribe('childSequenceOrderChange', this.onChildSequenceOrderChange, {}, this);
        this._model.subscribe('orderInSequenceChange', this.onOrderInSequenceChange, {}, this);
        this._model.subscribe('startDateChange', this.onStartDateChange, {}, this);
        this._model.subscribe('endDateChange', this.onEndDateChange, {}, this);
        this._model.subscribe('durationChange', this.onDurationChange, {}, this);
        this._model.subscribe('trackChange', this.onTrackChange, {}, this);

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
         * Enables and shows "draft mode" view-controls (e.g. "add", "delete" and "edit" buttons).
         *
         * @method enableDraftMode
         */
        enableDraftMode: function () {
            var i, n, elIds, el;
            elIds = ['addBtnEl', 'editBtnEl', 'deleteBtnEl'];
            for (i = 0, n = elIds.length; i < n; i++) {
                el = this.get(elIds[i]);
                el.disabled = false;
                Dom.removeClass(el, 'hidden');
            }
            Dom.removeClass(this.get('topButtonsRowEl'), 'hidden');
            Dom.removeClass(this.get('bottomButtonsRowEl'), 'hidden');
        },

        /**
         * Lock and hide "draft mode" view-controls (e.g. "add", "delete" and "edit" buttons).
         *
         * @method disableDraftMode
         */
        disableDraftMode: function () {
            var i, n, elIds, el;
            elIds = ['addBtnEl', 'editBtnEl', 'deleteBtnEl'];
            for (i = 0, n = elIds.length; i < n; i++) {
                el = this.get(elIds[i]);
                Dom.setAttribute(el, 'disabled', 'disabled');
                Dom.addClass(el, 'hidden');
            }
            Dom.addClass(this.get('topButtonsRowEl'), 'hidden');
            Dom.addClass(this.get('bottomButtonsRowEl'), 'hidden');
        },

        /*
         * @override
         * @see YAHOO.util.Element.initAttributes
         */
        initAttributes: function (config) {
            SequenceBlockView.superclass.initAttributes.call(this, config);
            var cnumber = config.cnumber;

            /**
             * The buttons row element at the top of the view container body.
             *
             * @attribute buttonRowEl
             * @type {HTMLElement}
             * @writeOnce
             */
            this.setAttributeConfig('topButtonsRowEl', {
                writeOnce: true,
                value: Dom.get('sequence-block-view-top-buttons-row-' + cnumber)
            });

            /**
             * The buttons row element at the bottom of the view container body.
             *
             * @attribute buttonRowEl
             * @type {HTMLElement}
             * @writeOnce
             */
            this.setAttributeConfig('bottomButtonsRowEl', {
                writeOnce: true,
                value: Dom.get('sequence-block-view-bottom-buttons-row-' + cnumber)
            });

            /**
             * The "toggle display" button of the view.
             *
             * @attribute toggleBtnEl
             * @type {HTMLElement}
             * @writeOnce
             */
            this.setAttributeConfig('toggleBtnEl', {
               writeOnce: true,
               value: Dom.get('sequence-block-view-toggle-btn-' + cnumber)
            });

            /**
             * The "delete" button of the view.
             *
             * @attribute deleteBtnEl
             * @type {HTMLElement}
             * @writeOnce
             */
            this.setAttributeConfig('deleteBtnEl', {
                writeOnce: true,
                value: Dom.get('sequence-block-view-delete-btn-' + cnumber)
            });

            /**
             * The "edit" button of the view.
             *
             * @attribute editBtnEl
             * @type {HTMLElement}
             * @writeOnce
             */
            this.setAttributeConfig('editBtnEl', {
                writeOnce: true,
                value: Dom.get('sequence-block-view-edit-btn-' + cnumber)
            });

            /**
             * The "add" button of the view.
             *
             * @attribute addBtnEl
             * @type {HTMLElement}
             * @writeOnce
             */
            this.setAttributeConfig('addBtnEl', {
                writeOnce: true,
                value: Dom.get('sequence-block-view-add-btn-' + cnumber)
            });

            /**
             * The body container-element of the view.
             *
             * @attribute bodyEl
             * @type {HTMLElement}
             * @writeOnce
             */
            this.setAttributeConfig('bodyEl', {
                writeOnce: true,
                value: Dom.get('sequence-block-view-body-' + cnumber)
            });

            /**
             * The display-element for the view's title attribute.
             *
             * @attribute titleEl
             * @type {HTMLElement}
             * @writeOnce
             */
            this.setAttributeConfig('titleEl', {
                writeOnce: true,
                value: Dom.get('sequence-block-view-title-' + cnumber)
            });

            /**
             * The display-element for the view's description attribute.
             *
             * @attribute descriptionEl
             * @type {HTMLElement}
             * @writeOnce
             */
            this.setAttributeConfig('descriptionEl', {
                writeOnce: true,
                value: Dom.get('sequence-block-view-description-' + cnumber)
            });

            /**
             * The display-element of the view's required attribute.
             *
             * @attribute requiredEl
             * @type {HTMLElement}
             * @writeOnce
             */
            this.setAttributeConfig('requiredEl', {
                writeOnce: true,
                value: Dom.get('sequence-block-view-required-' + cnumber)
            });

            /**
             * The display-element of the view's academic level attribute.
             *
             * @attribute academicLevelEl
             * @type {HTMLElement}
             * @writeOnce
             */
            this.setAttributeConfig('academicLevelEl', {
                writeOnce: true,
                value: Dom.get('sequence-block-view-academic-level-' + cnumber)
            });

            /**
             * The display-element of the view's course attribute.
             *
             * @attribute courseEl
             * @type {HTMLElement}
             * @writeOnce
             */
            this.setAttributeConfig('courseEl', {
                writeOnce: true,
                value: Dom.get('sequence-block-view-course-' + cnumber)
            });

            /**
             * The display-element of the view's start date attribute.
             *
             * @attribute startDateEl
             * @type {HTMLElement}
             * @writeOnce
             */
            this.setAttributeConfig('startDateEl', {
                writeOnce: true,
                value: Dom.get('sequence-block-view-start-date-' + cnumber)
            });

            /**
             * The display-element of the view's end date attribute.
             *
             * @attribute endDateEl
             * @type {HTMLElement}
             * @writeOnce
             */
            this.setAttributeConfig('endDateEl', {
                writeOnce: true,
                value: Dom.get('sequence-block-view-end-date-' + cnumber)
            });

            /**
             * The display-element of the view's duration attribute.
             *
             * @attribute durationEl
             * @type {HTMLElement}
             * @writeOnce
             */
            this.setAttributeConfig('durationEl', {
                writeOnce: true,
                value: Dom.get('sequence-block-view-duration-' + cnumber)
            });


            /**
             * The display-element of the view's order-in-sequence attribute.
             *
             * @attribute orderInSequenceEl
             * @type {HTMLElement}
             * @writeOnce
             */
            this.setAttributeConfig('orderInSequenceEl', {
                writeOnce: true,
                value: Dom.get('sequence-block-view-order-in-sequence-' + cnumber)
            });

            /**
             * The display-element of the view's child-sequence-order attribute.
             *
             * @attribute childSequenceOrderEl
             * @type {HTMLElement}
             * @writeOnce
             */
            this.setAttributeConfig('childSequenceOrderEl', {
                writeOnce: true,
                value: Dom.get('sequence-block-view-child-sequence-order-' + cnumber)
            });


            /**
             * The display-element of the view's track attribute.
             *
             * @attribute trackEl
             * @type {HTMLElement}
             * @writeOnce
             */
            this.setAttributeConfig('trackEl', {
                writeOnce: true,
                value: Dom.get('sequence-block-view-track-' + cnumber)
            });

            /**
             * The view's title attribute.
             *
             * @attribute title
             * @type {String}
             * @default ""
             */
            this.setAttributeConfig('title', {
                validator: Lang.isString,
                method: function (value) {
                    var el = this.get('titleEl');
                    if (el) {
                        el.innerHTML = value;
                    }
                },
                value: ""
            });
            /**
             * The view's description attribute.
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
             * The view's required attribute.
             *
             * @attribute required
             * @type {Number}
             */
            this.setAttributeConfig('required', {
                method: function (value) {
                    var el = this.get('requiredEl');
                    var str = '';
                    switch (value) {
                        case ilios.cim.model.SequenceBlockModel.prototype.REQUIRED:
                            str = ilios_i18nVendor.getI18NString('general.terms.yes');
                            break;
                        case ilios.cim.model.SequenceBlockModel.prototype.OPTIONAL:
                            str = ilios_i18nVendor.getI18NString('general.terms.no');
                            break;
                        case ilios.cim.model.SequenceBlockModel.prototype.REQUIRED_IN_TRACK:
                            str = ilios_i18nVendor.getI18NString('general.phrases.required_in_track');
                            break;
                    }
                    el.innerHTML = str;
                },
                validator: Lang.isNumber

            });

            /**
             * The view's course attribute.
             *
             * @attribute course
             * @type {ilios.cim.model.CourseModel|null}
             */
            this.setAttributeConfig('course', {
                method: function (value) {
                    var el = this.get('courseEl');
                    var str = '';
                    if (value) {
                        str = value.get('title');
                        // @todo flesh this out
                    } else {
                        str = ilios_i18nVendor.getI18NString('general.terms.not_applicable');
                    }
                    el.innerHTML = str;
                },
                validator: function (value) {
                    return (Lang.isNull(value) || (value instanceof ilios.cim.model.CourseModel));
                }
            });

            /**
             * The view's child-sequence-order attribute.
             *
             * @attribute childSequenceOrder
             * @type {Number}
             */
            this.setAttributeConfig('childSequenceOrder', {
                method: function (value) {
                    var el = this.get('childSequenceOrderEl');
                    var str = '';
                    switch (value) {
                        case ilios.cim.model.SequenceBlockModel.prototype.ORDERED:
                            str = ilios_i18nVendor.getI18NString('general.terms.ordered');
                            break;
                        case ilios.cim.model.SequenceBlockModel.prototype.UNORDERED:
                            str = ilios_i18nVendor.getI18NString('general.terms.unordered');
                            break;
                        case ilios.cim.model.SequenceBlockModel.prototype.PARALLEL:
                            str = ilios_i18nVendor.getI18NString('general.terms.parallel');
                            break;
                    }
                    el.innerHTML = str;
                },
                validator: Lang.isNumber
            });

            /**
             * The view's academic level attribute.
             *
             * @attribute academicLevel
             * @type {ilios.cim.model.AcademicLevelModel}
             */
            this.setAttributeConfig('academicLevel', {
                validator: function (value) {
                    return (value instanceof ilios.cim.model.AcademicLevelModel);
                },
                method: function (value) {
                    var el = this.get('academicLevelEl');
                    if (el) {
                        el.innerHTML = value.get('name');
                    }
                }
            });

            /**
             * The view's order-in-sequence attribute.
             *
             * @attribute orderInSequence
             * @type {Number}
             */
            this.setAttributeConfig('orderInSequence', {
                validator: Lang.isNumber,
                method: function (value) {
                    var el = this.get('orderInSequenceEl');
                    if (! value) {
                        el.innerHTML = ilios_i18nVendor.getI18NString('general.terms.not_applicable');
                    } else {
                        el.innerHTML = value;
                    }
                }
            });

            /**
             * The view's start date attribute.
             *
             * @attribute startDate
             * @type {String|null}
             */
            this.setAttributeConfig('startDate', {
                validator: function (value) {
                    return Lang.isNull(value) || Lang.isString(value);
                },
                method: function (value) {
                    var el = this.get('startDateEl');
                    el.innerHTML = value ? value : ilios_i18nVendor.getI18NString('general.terms.not_applicable');
                }
            });

            /**
             * The view's end date attribute.
             *
             * @attribute endDate
             * @type {String|null}
             */
            this.setAttributeConfig('endDate', {
                validator: function (value) {
                    return Lang.isNull(value) || Lang.isString(value);
                },
                method: function (value) {
                    var el = this.get('endDateEl');
                    el.innerHTML = value ? value : ilios_i18nVendor.getI18NString('general.terms.not_applicable');
                }
            });

            /**
             * The view's duration attribute.
             *
             * @attribute duration
             * @type {Number}
             */
            this.setAttributeConfig('duration', {
                validator: Lang.isNumber,
                method: function (value) {
                    var el = this.get('durationEl');
                    if (! value) {
                        el.innerHTML = ilios_i18nVendor.getI18NString('general.terms.not_applicable');
                    } else {
                        el.innerHTML = "" + value + " ";
                        el.innerHTML += (1 == value) ? ilios_i18nVendor.getI18NString('general.terms.minute') :
                            ilios_i18nVendor.getI18NString('general.terms.minutes');
                    }
                }
            });

            /**
             * The view's track attribute.
             *
             * @attribute track
             * @type {Boolean}
             */
            this.setAttributeConfig('track', {
                validator: Lang.isBoolean,
                method: function (value) {
                    var el = this.get('trackEl');
                    if (value) {
                        el.innerHTML = ilios_i18nVendor.getI18NString('general.terms.yes');
                    } else {
                        el.innerHTML = ilios_i18nVendor.getI18NString('general.terms.no');
                    }
                }
            });
        },

        /**
         * Lifecycle management method.
         * "Deletes" the view from the page.
         * This includes unsubscribing any event listeners, detaching the view from it's parent element in the page
         * and hiding it from display.
         *
         * @method delete
         * @see YAHOO.util.Element.destroy
         */
        delete: function () {
            var el;
            this.hide();
            el = this.get('element');
            // remove the view from the DOM
            el.parentNode.removeChild(el);
            // @todo unsubscribe all event handlers from any controls (buttons) within the view.
            this._model = null;
        },

        /**
         * Hides the view.
         *
         * @method hide
         */
        hide: function () {
            this.addClass('hidden');
        },

        /**
         *  Makes the view visible.
         *
         * @method show
         */
        show: function () {
            this.removeClass('hidden');
        },

        /**
         * Renders the view.
         * This includes populating the view with the model data and wiring event handling.
         *
         * @method render
         * @param {Boolean} enableDraftMode If TRUE then draft mode controls ("add"/"edit"/"delete" buttons) will be enabled.
         */
        render: function (enableDraftMode) {

            this.set('title', this._model.get('title'));
            this.set('description', this._model.get('description'));
            this.set('academicLevel', this._model.get('academicLevel'));
            this.set('required', this._model.get('required'));
            this.set('course', this._model.get('course'));
            this.set('childSequenceOrder', this._model.get('childSequenceOrder'));
            this.set('orderInSequence', this._model.get('orderInSequence'));
            this.set('startDate', this._model.get('startDate'));
            this.set('endDate', this._model.get('endDate'));
            this.set('duration', this._model.get('duration'));
            this.set('track', this._model.get('track'));


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

            if (enableDraftMode) {
                this.enableDraftMode();
            }
        },

        /**
         * Expands the view-container body.
         *
         * @method expand
         */
        expand: function () {
            Dom.removeClass(this.get('bodyEl'), 'hidden');
            this.removeClass('collapsed');
            this.addClass('expanded');
        },

        /**
         * Collapses the view-container body.
         *
         * @method collapse
         */
        collapse: function () {
            Dom.addClass(this.get('bodyEl'), 'hidden');
            this.removeClass('expanded');
            this.addClass('collapsed');
        },

        /**
         * Retrieves the view's "Delete" button.
         *
         * @method getDeleteButton
         * @return {HTMLElement}
         */
        getDeleteButton: function () {
            return this.get('deleteBtnEl');
        },

        /**
         * Retrieves the view's "Edit" button.
         *
         * @method getEditButton
         * @return {HTMLElement}
         */
        getEditButton: function () {
            return this.get('editBtnEl');
        },

        /**
         * Retrieves the view's "Add" button.
         *
         * @method getAddButton
         * @return {HTMLElement}
         */
        getAddButton: function () {
            return this.get('addBtnEl');
        },

        /**
         * Event listener method.
         *
         * Listens for a title attribute change in the view's model and updates the view's title attribute accordingly.
         *
         * @method onTitleChange
         * @param {Object} evObj An object containing the old and new title value
         */
        onTitleChange: function (evObj) {
            this.set('title', evObj.newValue);
        },

        /**
         * Event listener method.
         *
         * Listens for a description attribute change in the view's model and updates the view's description attribute
         * accordingly.
         *
         * @method onDescriptionChange
         * @param {Object} evObj An object containing the old and new description value.
         */
        onDescriptionChange: function (evObj) {
            this.set('description', evObj.newValue);
        },

        /**
         * Event listener method.
         *
         * Listens for a required attribute change in the view's model and updates the view's required attribute
         * accordingly.
         *
         * @method onRequiredChange
         * @param {Object} evObj An object containing the old and new required value.
         */
        onRequiredChange: function (evObj) {
            this.set('required', evObj.newValue);
        },

        /**
         * Event listener method.
         *
         * Listens for a academic level attribute change in the view's model and updates the view's academic level
         * attribute accordingly.
         *
         * @method onAcademicLevelChange
         * @param {Object} evObj An object containing the old and new academic level value.
         */
        onAcademicLevelChange: function (evObj) {
            this.set('academicLevel', evObj.newValue);
        },

        /**
         * Event listener method.
         *
         * Listens for a course attribute in the view's model and updates the view's course attribute accordingly.
         *
         * @method onCourseChange
         * @param {Object} evObj An  object containing the old and new course value.
         */
        onCourseChange: function (evObj) {
            this.set('course', evObj.newValue);
        },

        /**
         * Event listener method.
         *
         * Listens for a child sequence order attribute in the view's model and updates the view's
         * child sequence order attribute accordingly.
         *
         * @method onChildSequenceOrderChange
         * @param {Object} evObj An object containing the old and new child sequence order value.
         */
        onChildSequenceOrderChange: function (evObj) {
            this.set('childSequenceOrder', evObj.newValue);
        },

        /**
         * Event listener method.
         *
         * Listens for a order in sequence attribute in the view's model and updates the view's
         * order in sequence attribute accordingly.
         *
         * @method onOrderInSequenceChange
         * @param {Object} evObj An object containing the old and new order in sequence value.
         */
        onOrderInSequenceChange: function (evObj) {
            this.set('orderInSequence', evObj.newValue);
        },

        /**
         * Event listener method.
         *
         * Listens for a start date attribute change in the view's model and updates the view's start date attribute
         * accordingly.
         *
         * @method onStartDateChange
         * @param {Object} evObj An object containing the old and new start date value.
         */
        onStartDateChange: function (evObj) {
            this.set('startDate', evObj.newValue);
        },

        /**
         * Event listener method.
         *
         * Listens for a end date attribute change in the view's model and updates the view's end date attribute
         * accordingly.
         *
         * @method onEndDateChange
         * @param {Object} evObj An object containing the old and new end date value.
         */
        onEndDateChange: function (evObj) {
            this.set('endDate', evObj.newValue);
        },

        /**
         * Event listener method.
         *
         * Listens for a duration attribute change in the view's model and updates the view's duration attribute
         * accordingly.
         *
         * @method onDurationChange
         * @param {Object} evObj An object containing the old and new duration value.
         */
        onDurationChange: function (evObj) {
            this.set('duration', evObj.newValue);
        },

        /**
         * Event listener method.
         *
         * Listens for a track attribute change in the view's model and updates the view's track attribute accordingly.
         *
         * @method onTrackChange
         * @param {Object} evObj An object containing the old and new end date value.
         */
        onTrackChange: function (evObj) {
            this.set('track', evObj.newValue);
        }
    });

    /**
     * The view for a given report model.
     * @namespace cim.view
     * @class ReportView
     * @constructor
     * @extends YAHOO.util.Element
     * @param {ilios.cim.model.ReportModel} model The report model.
     */
    var ReportView = function (model) {
        ReportView.superclass.constructor.call(this, document.getElementById('report-details-view-container'));

        // set model
        this._model = model;

        // subscribe to model changes
        this._model.subscribe('nameChange', this.onNameChange, {}, this);
        this._model.subscribe('descriptionChange', this.onDescriptionChange, {}, this);
        this._model.subscribe('startDateChange', this.onStartDateChange, {}, this);
        this._model.subscribe('endDateChange', this.onEndDateChange, {}, this);
        this._model.subscribe('isFinalizedChange', this.onStatusChange, {}, this);

        // create custom events
        this.createEvent(ReportView.EVT_DOWNLOAD_STARTED);
        this.createEvent(ReportView.EVT_DOWNLOAD_COMPLETED);
        this.createEvent(ReportView.EVT_EXPORT_STARTED);
        this.createEvent(ReportView.EVT_EXPORT_COMPLETED);
    };

    Lang.extend(ReportView, Element, {

        /**
         * The view's report model.
         *
         * @property model
         * @type {ilios.cim.model.ReportModel}
         * @protected
         */
        _model: null,
        /**
         * Timer object for the tracking the report download progress.
         * @property _downloadIntervalTimer
         * @type {Object}
         * @protected
         */
        _downloadIntervalTimer: null,

        /**
         * Timer object for the tracking the report export progress.
         * @property _downloadIntervalTimer
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
            this.fireEvent(this.EVT_DOWNLOAD_STARTED);
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
            this.fireEvent(this.EVT_DOWNLOAD_COMPLETED);
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
            this.fireEvent(this.EVT_EXPORT_STARTED);
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
            this.fireEvent(this.EVT_EXPORT_COMPLETED);
        },

        /**
         * Returns the view's model.
         *
         * @method getModel
         * @return {ilios.cim.model.ReportModel}
         */
        getModel: function () {
            return this._model;
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
                value: Dom.get('report-details-view-edit-button')
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

            this.set('name', this._model.get('name'));
            this.set('description', this._model.get('description'));
            this.set('academicYear', this._model.get('academicYear'));
            this.set('startDate', this._model.get('startDate'));
            this.set('endDate', this._model.get('endDate'));
            this.set('program', this._model.get('program'));
            this.set('reportId', this._model.get('id'));
            this.set('isFinalized', this._model.get('isFinalized'));

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

            if (! this.get('isFinalized')) {
                 Event.addListener(this.get('exportFormEl'), 'submit', this._blockUIForExport, {}, this);
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
