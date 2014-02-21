<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @file add_new_members_dialog.php
 *
 * DEPENDENCIES:
 *         YUI toolkit
 *         scripts/ilios_dom.js
 *         scripts/ilios_utilities.js
 *
 * @todo separate template from JS code. [ST 2013/11/23]
 */
?>
<div class="tabdialog" id="add_new_members_dialog">
    <div class="hd"><?php echo t('general.phrases.add_members'); ?></div>
        <div class="bd">
            <form method="post">
                <div style="padding: 9px; border: 1px solid #3c3c3c;">
                    <p><?php echo t('add_members_dialog.manual_entry'); ?></p>
                    <table style="width: 100%; align: center;">
                        <tr>
                            <td><?php echo t('general.user.last_name'); ?>:<br/>
                                <input id="em_last_name" style="width:95%" value="" type="text" /></td>
                            <td><?php echo t('general.user.first_name'); ?>:<br/>
                                <input id="em_first_name" style="width:95%" value="" type="text" /></td>
                            <td><?php echo t('general.user.middle_name'); ?>:<br/>
                                <input id="em_middle_name" style="width:95%" value="" type="text" /></td>
                            <td><?php echo t('general.user.phone'); ?>:<br/>
                                <input id="em_phone" style="width:95%" value="" type="text" /></td>
                            <td><?php echo t('general.user.email'); ?>:<br/>
                                <input id="em_email" style="width:95%" value="" type="text" /></td>
                            <td><?php echo t('general.user.uc_id'); ?>:<br/>
                                <input id="em_uc_id" style="width:95%" value="" type="text" /></td>
                        </tr>
                    </table>
                    <button id="em_add_user_button" style="float: right;" onclick="return false;">
                        <?php echo t('add_members_dialog.add_user'); ?></button>
                    <div class="clear"></div>
                </div>
            </form>
            <div style="padding: 9px; margin-top: 9px; border: 1px solid #3c3c3c; position: relative;">
            <p><?php echo t('add_members_dialog.from_csv'); ?></p>
            <div style="padding: 3px; position: absolute; width: 415px; top: 3px; right: 1px; font-size: 8pt;">
                <?php echo t('general.text.csv_user_upload_1'); ?>
                <br/>
                <strong><?php echo t('general.text.csv_user_upload_2'); ?></strong>
            </div>
            <div style="margin-left: 9px; margin-right: 9px;">
<?php
    $attributes = array('id' => 'em_upload_form');
    echo form_open_multipart('NOT_USED/BOGUS_URL', $attributes);
?>
                <input type="hidden" name="instructor_group_id" id="em_instructor_group_id" value="" />
                <input type="hidden" name="container_number" id="em_container_number" value="" />
                <input type="hidden" name="cohort_id" id="em_cohort_id" value="" />
                <input type="hidden" name="full_file_path" id="em_filepath_value" value="" />
                <input type="file" name="userfile" id="em_csv_file_field" />
                <button id="em_upload_button" style="margin-left: 18px;" onclick="return false;">
                    <?php echo t('general.terms.upload'); ?></button>
 <?php
    // done as an 'echo' here to avoid witty IDEs complaining about a closing tag without
    // an opening one (the opening tag is generated in the php block above
    echo "</form>\n";
?>
            </div>
        </div>
        <div id="em_transaction_status" style="position: absolute; left: 12px; bottom: 2px; color: #800000; font-weight: bold;"></div>
    </div>
    <div class="ft"></div>
</div>

<script type="text/javascript">

    ilios.namespace('common');
    /**
     * @param args
     *    submit_override:
     *        if this is non-null the function is subscribed to
     *        the manual submit event of the dialog and the
     *        dialog is configured to fire that event when the
     *        user clicks the 'Done' button on the dialog.
     *    container:
     *        the container listing the present possible choices
     *        (as possibly filtered via autocompleter input)
     *    display_handler:
     *        a function which is called just prior to the dialog
     *        being displayed
     *    display_denier:
     *        if non-null, this boolean returning function is
     *        called during display; if it returns true the
     *        display will be aborted
     *    upload_handler:
     *        the method which will be invoked when the
     *        upload button is clicked
     *    manual_add_handler:
     *        the method which will be invoked when the
     *        manual add button is clicked
     *    panel_width:
     *        if this is non-null, it is expected to a valid CSS
     *        measurement, else '880px' will be used.
     *     trigger:
     *        the id string of the element which should invoke the
     *        display of this panel; clicking on this id
     *        string must be wired elsewhere to fire thusly:
     *        IEvent.fire({action: 'em_dialog_open', ...});
     *        where 'em_dialog_open' is the salient action,
     *        and the event may also contain a key for
     *        'container_number' the value of which will get
     *        assigned as property 'containerNumber' as
     *        dialog.containerNumber
     */
    ilios.common.assembleAddNewMembersDialog = function (unused1, unused2, args) {
        var Event = YAHOO.util.Event;
        var handleCancel = function () {
            IEvent.fire({object: 'modal_em_dialog_panel', action: 'cancel', event: 'closing'});
            this.cancel();
        };
        var buttonArray = [{text: ilios_i18nVendor.getI18NString('general.terms.done'), handler: handleCancel, isDefault: true}];
        var panelWidth = (args['panel_width'] != null) ? args['panel_width'] : "880px";
        var displayOnTriggerHandler = null;
        var element = null;
        var dialog = new YAHOO.widget.Dialog('add_new_members_dialog', {
            width : panelWidth,
            x : 140,
            y : 50,
            modal : true,
            visible : false,
            constraintoviewport : false,
            buttons : buttonArray
        });

        dialog.showDialogPane = function () {
            var displayHandler = args['display_handler'];
            if (displayHandler != null) {
                displayHandler(dialog);
            }
            dialog.center();
            dialog.show();
        };

        // Render the Dialog
        dialog.render();

        element = document.getElementById('em_upload_button');
        Event.addListener(element, 'click', function (e) {
            var uploadHandler = args['upload_handler'];
            uploadHandler(this);
        });

        element = document.getElementById('em_add_user_button');
        Event.addListener(element, 'click', function (e) {
            var addHandler = args['manual_add_handler'];
            addHandler(this);
        });

        if (args['submit_override'] != null) {
            dialog.cfg.setProperty('postmethod', 'manual');
            dialog.manualSubmitEvent.subscribe(args['submit_override'], null);
        }

        // register a listener on some page element to trigger the display of this popup dialog
        displayOnTriggerHandler = function (type, handlerArgs) {
            if (handlerArgs[0].event == args['trigger']) {
                if (handlerArgs[0].action == 'em_dialog_open') {
                    var element = null;

                    if (args['display_denier'] != null) {
                        var denier = args['display_denier'];
                        if (denier()) {
                            return;
                        }
                    }

                    element = document.getElementById('em_last_name');
                    element.setAttribute('cnumber', handlerArgs[0].container_number);

                    element = document.getElementById('em_upload_button');
                    element.setAttribute('cnumber', handlerArgs[0].container_number);

                    dialog.showDialogPane();
                }
            }
        };

        IEvent.subscribe(displayOnTriggerHandler);
    }
</script>
