<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @file create_report_dialog.inc.php
 *
 * Renders the markup for the report creation dialog.
 *
 * @see application/views/curriculum_inventory/index.php
 * @see application/views/curriculum_inventory/view.php
 * @see application/views/curriculum_inventory_curriculum_inventory_manager.js
 */
?>
<div class="tabdialog" id="create_report_dialog">
    <div class="hd"><?php echo $this->languagemap->t('curriculum_inventory.create.title', $lang); ?></div>
    <div class="bd">
        <span id="report_creation_status" class="search-status"></span>
        <div class="dialog_wrap">
            <form method="POST" action="<?php echo $controllerURL; ?>/add">
                <!-- todo implement -->
            </form>
        </div>
    </div>
    <div class="ft"></div>
</div>
