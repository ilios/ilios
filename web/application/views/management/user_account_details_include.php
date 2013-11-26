<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * DEPENDENCIES:
 *         YUI toolkit
 *         scripts/ilios_dom.js
 *         scripts/ilios_utilities.js
 */

/**
 * @file
 *
 * Include template for the user accounts details panel.
 */
?>
<!--  User Accounts Details -->
<div class="tabdialog" id="ua_account_details">
    <div class="hd"></div>
    <div class="bd">
        <div class="dialog_wrap">
            <div class="read_only_data title">
            <?php echo $this->languagemap->t('management.user_accounts.details.title'); ?>
            </div>
            <form method="GET" action="#">
                <div class="dialog_inner_wrap">
                    <div class="ua_detail_sync_exceptions" >
                        <h3><?php echo $this->languagemap->t('management.user_accounts.sync_exceptions.title'); ?></h3>
                        <div id="ua_detail_sync_exceptions_messages" class="ua_sync_errors"></div>
                    </div>
                    <h3><?php echo $this->languagemap->t('management.user_accounts.details.subtitle'); ?></h3>
                    <div class="ua_detail_section">
                           <label for="ua_detail_name" class="ua_detail_name"><?php echo $this->languagemap->t('general.user.full_name'); ?>:</label>
                           <div class="ua_detail_value">
                            <span id="ua_detail_name" class="read_only_data"></span>
                           </div>
                        <label for="ua_detail_uid" class="ua_detail_name"><?php echo $this->languagemap->t('general.user.uc_id'); ?>:</label>
                        <div class="ua_detail_value">
                            <span id="ua_detail_uid" class="read_only_data"></span>
                            <span id="ua_detail_directory_uid" class="directory_mismatch_value" style="display:none;"></span>
                        </div>
                        <label for="ua_detail_email" class="ua_detail_name"><?php echo $this->languagemap->t('general.user.email'); ?>:</label>
                        <div class="ua_detail_value">
                            <span id="ua_detail_email" class="read_only_data"></span>
                               <span id="ua_detail_directory_email" class="directory_mismatch_value" style="display:none;"></span>
                           </div>
                    </div>
                    <div class="clear ua_detail_section">
                        <label for="ua_detail_school" class="ua_detail_name"><?php echo $this->languagemap->t('management.user_accounts.details.primary_school'); ?>:</label>
                        <div class="ua_detail_value">
                            <span id="ua_detail_school" class="read_only_data"></span>
                        </div>
                        <label for="ua_detail_ilios_roles" class="ua_detail_name"><?php echo $this->languagemap->t('management.user_accounts.details.roles'); ?>:</label>
                        <div class="ua_detail_value">
                            <span id="ua_detail_ilios_roles" class="read_only_data"></span>
                        </div>
                        </div>
                        <div class="clear ua_detail_section">
                        <label for="ua_detail_cohort_name" class="ua_detail_name"><?php echo $this->languagemap->t('management.user_accounts.details.cohort'); ?>:</label>
                        <div class="ua_detail_value">
                            <span id="ua_detail_cohort_name" class="read_only_data"></span>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="ft"></div>
</div>
<div class="clear"></div>

<script type="text/javascript">
    ilios.management.user_accounts.accountDetailsDialog = null;
    ilios.management.user_accounts.buildAccountDetailsDialogDOM = function () {
        var handleClose = function () {
            this.cancel();
        };
        var closeStr = ilios_i18nVendor.getI18NString('general.terms.close');
        var buttonArray = [{text: closeStr, handler: handleClose, isDefault: true}];
        var dialog = new YAHOO.widget.Dialog(
                'ua_account_details', {
                    modal: true,
                    visible: false,
                    constraintoviewport: false,
                    buttons: buttonArray
                });
        dialog.showDialogPane = function () {
            dialog.center();
            dialog.show();
        };
        dialog.render();
        ilios.management.user_accounts.accountDetailsDialog = dialog;
    };
    YAHOO.util.Event.onDOMReady(ilios.management.user_accounts.buildAccountDetailsDialogDOM);
</script>
