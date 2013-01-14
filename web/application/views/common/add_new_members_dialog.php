<?php

/**
 * DEPENDENCIES:
 *         YUI toolkit
 *         scripts/ilios_dom.js
 *         scripts/ilios_utilities.js
 */


/**
 * The controller which initiates this view should have
 *    $this->populateForAddNewMembersDialog($data, $lang);
 *    invoked as part of its index() lest there be no label and button strings displayed in this UI
 */

?>
<div class="tabdialog" id="add_new_members_dialog">
    <div class="hd"><?php echo $add_members_string; ?></div>
        <div class="bd">
            <form method="post" action="matters not at all">
                <div style="padding: 9px; border: 1px solid #3c3c3c;">
                    <p><?php echo $manual_entry_string; ?></p>
                    <table style="width: 100%; align: center;">
                        <tr>
                            <td><?php echo $last_name_string; ?>:<br/>
                                <input id="em_last_name" style="width:95%" value="" type="text" /></td>
                            <td><?php echo $first_name_string; ?>:<br/>
                                <input id="em_first_name" style="width:95%" value="" type="text" /></td>
                            <td><?php echo $middle_name_string; ?>:<br/>
                                <input id="em_middle_name" style="width:95%" value="" type="text" /></td>
                            <td><?php echo $phone_string; ?>:<br/>
                                <input id="em_phone" style="width:95%" value="" type="text" /></td>
                            <td><?php echo $email_string; ?>:<br/>
                                <input id="em_email" style="width:95%" value="" type="text" /></td>
                            <td><?php echo $uc_id_string; ?>:<br/>
                                <input id="em_uc_id" style="width:95%" value="" type="text" /></td>
                        </tr>
                    </table>
                    <button id="em_add_user_button" style="float: right;" onclick="return false;">
                        <?php echo $add_user_string; ?></button>
                    <div class="clear"></div>
                </div>
            </form>
            <div style="padding: 9px; margin-top: 9px; border: 1px solid #3c3c3c; position: relative;">
            <p><?php echo $from_csv_string; ?></p>
            <div style="padding: 3px; position: absolute; width: 415px; top: 3px; right: 1px; font-size: 8pt;">
                <?php echo $csv_user_upload_1_string; ?>
                <br/>
                <strong><?php echo $csv_user_upload_2_string; ?></strong>
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
                <input type="file" name="userfile" id="em_csv_file_field" size="50" />
                <button id="em_upload_button" style="margin-left: 18px;" onclick="return false;">
                    <?php echo $upload_string; ?></button>
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
    ilios.common.assembleAddNewMembersDialog = function (use, less, args) {
        var Event = YAHOO.util.Event;
        var handleCancel = function () {
            IEvent.fire({object: 'modal_em_dialog_panel', action: 'cancel', event: 'closing'});
            this.cancel();
        };
        var buttonArray = [{text: "<?php echo $done_string; ?>", handler: handleCancel, isDefault: true}];
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
