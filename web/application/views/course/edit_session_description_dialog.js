/*
 * Defines the "Edit a Session Description" dialog.
 *
 * Dependency: YUI utilities
 * Dependency: YUI Container Family
 * Dependency: ilios.ui
 * Dependency: SessionModel
 * Dependency: ilios_i18nVendor
 *
 *
 */
(function () {

    ilios.namespace("cm.widget");

    /**
    * "Edit a Session Description" dialog.
    * @namespace ilios.cm.widget
    * @class EditSessionDescriptionDialog
    * @extends YAHOO.widget.Dialog
    * @constructor
    * @param {String} el The element ID representing the dialog
    * <em>OR</em>
    * @param {HTMLElement} el The element representing the dialog
    * @param {Object} userConfig The configuration object literal containing
    * the configuration that should be set for this dialog.
    */
    ilios.cm.widget.EditSessionDescriptionDialog = function (el, userConfig){
        var defaultConfig = {
            width: "640px",
            modal: true,
            fixedcenter: true,
            visible: false,
            buttons: [
                {
                    text: ilios_i18nVendor.getI18NString('general.terms.done'),
                    handler: function () {
                        var value;
                        if (this.model) {
                            value = this.rte.get("textarea").value;
                            // KLUDGE!
                            // Update the session within the current course model directly.
                            // This seems redundant, but in fact is not since at this point the session object
                            // attached to the current course model is NOT the same object as THIS
                            // session model object.
                            // In other words, we have two copies of the same data point that need to be kept in sync.
                            // TODO: consolidate model objects, there should really be only one. [ST 2014/08/28]
                            var sessionModel2 = ilios.cm.currentCourseModel.getSessionWithId(this.model.getDBId());
                            sessionModel2.setDescription(value);
                            this.model.setDescription(value);
                        }
                        this.model = null;
                        this.cancel();
                    },
                    isDefault: true
                },
                {
                    text: ilios_i18nVendor.getI18NString('general.terms.cancel'),
                    handler: function () {
                        this.model = null;
                        this.cancel();
                    }
                }
            ]
        };

        // merge the user config with the default configuration
        userConfig = userConfig || {};
        var config = YAHOO.lang.merge(defaultConfig, userConfig);

        // call the parent constructor with the merged config
        ilios.cm.widget.EditSessionDescriptionDialog.superclass.constructor.call(this, el, config);

        // internal Rich Text Editor (RTE) instance
        this.rte = new ilios.ui.RichTextEditor('esd_textarea', {
            height: '200px'
        });

        this.setHeader(ilios_i18nVendor.getI18NString("course_management.session_description_edit_description"));

        // session model
        this.model = null;

        // ACHTUNG!
        // attempt to set the the editor's html output
        // from the internal model
        // once the editor is ready.
        this.rte.on("editorContentLoaded", function () {
            if (this.model) {
                this.rte.setEditorHTML(this.model.getDescription());
            }
        }, this, true);

        this.rte.render(); // render this thang

        // keep the visibility of the internal RTE in sync with the dialog's visibility
        this.showEvent.subscribe(this.rte.show, this.rte, true);
        this.hideEvent.subscribe(this.rte.hide, this.rte, true);
    }

    // inheritance
    YAHOO.lang.extend(ilios.cm.widget.EditSessionDescriptionDialog, YAHOO.widget.Dialog, {
        /**
         * Sets the internal session model for this dialog.
         * @method setSessionModel
         * @param {SessionModel} model
         * @param {Boolean} updateRte if TRUE then the internal editor gets updated on set.
         */
        setSessionModel : function (model, updateRte) {
            this.model = model;
            var description = this.model.getDescription();
            if (updateRte) {
                this.rte.setEditorHTML(description);
            }
        }
    });
}());
