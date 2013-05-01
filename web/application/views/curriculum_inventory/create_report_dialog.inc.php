<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @file create_report_dialog.inc.php
 *
 * Renders the markup for the report creation dialog.
 *
 * @see application/views/curriculum_inventory/index.php
 * @see application/views/curriculum_inventory/view.php
 * @see application/views/js/ilios.cim.js
 */
?>
<div class="tabdialog" id="create_report_dialog">
    <div class="hd"><?php echo $this->languagemap->t('curriculum_inventory.create.title', $lang); ?></div>
    <div class="bd">
        <div class="dialog_wrap">
            <span id="report_creation_status" class="dialog-form-status"></span>
            <form method="POST" action="<?php echo $controllerURL; ?>/add">
                <div class="dialog-form-row" >
                    <label for="new_report_name" class="entity_widget_title">
                        <?php echo $this->languagemap->t('curriculum_inventory.create.report_name.label', $lang); ?>:
                    </label><br />
                    <input id="new_report_name" name="report_name" type="text"  value="" size="50"
                           placeholder="<?php echo $this->languagemap->t('curriculum_inventory.create.report_name.hint', $lang, false); ?>" />
                </div>
                <div class="dialog-form-row">
                    <label for="new_report_program" class="entity_widget_title">
                        <?php echo$this->languagemap->t('curriculum_inventory.create.program_name.label', $lang); ?>:
                    </label><br />
                    <select name="program_id" id="new_report_program"></select>
                </div>
                <div class="dialog-form-row">
                    <label for="new_report_year" class="entity_widget_title">
                        <?php echo $this->languagemap->t('curriculum_inventory.create.report_year.label', $lang); ?>:
                    </label><br />
                    <input id="new_report_year" name="report_year" type="text" value=""
                        maxlength="5" size="5" placeholder="yyyy">
                </div>
                <div class="clear"></div>
            </form>
        </div>
     </div>
    <div class="ft"></div>
</div>
