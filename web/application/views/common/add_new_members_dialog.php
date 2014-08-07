<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @file add_new_members_dialog.php
 *
 * DEPENDENCIES:
 *         YUI toolkit
 *         scripts/ilios_dom.js
 *         scripts/ilios_utilities.js
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
                            <td><?php echo t('general.user.other_id'); ?>:<br/>
                                <input id="em_other_id" style="width:95%" value="" type="text" /></td>
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
            <form method="post" accept-charset="utf-8" id="em_upload_form" enctype="multipart/form-data">
                <input type="hidden" name="instructor_group_id" id="em_instructor_group_id" value="" />
                <input type="hidden" name="container_number" id="em_container_number" value="" />
                <input type="hidden" name="cohort_id" id="em_cohort_id" value="" />
                <input type="hidden" name="full_file_path" id="em_filepath_value" value="" />
                <input type="file" name="userfile" id="em_csv_file_field" />
                <button id="em_upload_button" style="margin-left: 18px;" onclick="return false;">
                    <?php echo t('general.terms.upload'); ?></button>
            </form>
            </div>
        </div>
        <div id="em_transaction_status" style="position: absolute; left: 12px; bottom: 2px; color: #800000; font-weight: bold;"></div>
    </div>
    <div class="ft"></div>
</div>
<script src="<?php echo appendRevision($viewsUrlRoot . "scripts/ilios_common.js"); ?>"></script>
