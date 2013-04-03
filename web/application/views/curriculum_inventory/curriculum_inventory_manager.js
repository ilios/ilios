/**
 * Client-side application code for the curriculum inventory management (cim) module.
 *
 * Defines the following namespaces:
 *     ilios.cim
 *     ilios.cim.dom
 *     ilios.cim.transaction
 *     ilios.cim.event
 *     ilios.cim.widget
 *
 *  Dependencies:
 *     application/views/scripts/ilios_base.js
 *     ilios_i18nVendor
 */
ilios.namespace('cim.dom');
ilios.namespace('cim.transaction');
ilios.namespace('cim.event');
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
 * @param {Object} config module configuration
 * @method init
 *
 */
ilios.cim.init = function (config) {
    // set module configuration
    ilios.cm.config = YAHOO.lang.isObject(config) ? config : {};
    // create widgets

    // create view

    // register dom event handlers

    // load model
};

//
// widgets sub-module
//
(function () {
    /**
     * "Edit a Program" dialog.
     * @namespace ilios.cim.widget
     * @class EditProgramDialog
     * @extends YAHOO.widget.Dialog
     * @constructor
     * @param {HTMLElement|String} el The element or element-ID representing the dialog
     * @param {Object} userConfig The configuration object literal containing
     *     the configuration that should be set for this dialog.
     */
    ilios.cim.widget.EditProgramDialog = function (el, userConfig){
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
        ilios.cim.widget.EditProgramDialog.superclass.constructor.call(this, el, config);

        this.setHeader(ilios_i18nVendor.getI18NString("curriculum_inventory.edit_program"));

        // session model
        this.model = null;
    }

    // inheritance
    YAHOO.lang.extend(ilios.cim.widget.EditProgramDialog, YAHOO.widget.Dialog, {
        /**
         * Sets the internal model for this dialog.
         * @method setModel
         * @param {Object} model
         */
        setModel : function (model) {
            this.model = model;
        }
    });
}());



