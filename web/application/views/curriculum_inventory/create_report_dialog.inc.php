<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @file create_report_dialog.inc.php
 *
 * Renders the markup for the report creation dialog widget.
 *
 * Available template variables:
 *    $lang ... The language key.
 *    $reports ... An array of existing inventory reports.
 *    $controllerURL ... The page controller URL.
 *
 * @see application/views/curriculum_inventory/index.php
 * @see application/views/js/ilios.cim.widget.js
 */
?>
<div class="tabdialog hidden" id="create_report_dialog">
    <div class="hd"><?php echo $this->languagemap->t('curriculum_inventory.create.title', $lang); ?></div>
    <div class="bd">
        <div class="dialog_wrap">
            <span id="report_creation_status" class="dialog-form-status"></span>
            <form method="POST" action="<?php echo $controllerURL; ?>/create">
                <div class="dialog-form-row" >
                    <label for="new_report_name" class="entity_widget_title">
                        <?php echo $this->languagemap->t('general.terms.name', $lang); ?>:
                    </label><br />
                    <input id="new_report_name" name="report_name" type="text"  value="" size="50"
                           placeholder="<?php echo $this->languagemap->t('curriculum_inventory.create.report_name.hint', $lang, false); ?>" />
                </div>
                <div class="dialog-form-row" >
                    <label for="new_report_description" class="entity_widget_title">
                        <?php echo $this->languagemap->t('general.terms.description', $lang); ?>:
                    </label><br />
                    <textarea id="new_report_description" name="report_description" type="text" cols="80" rows="10"
                           placeholder="<?php echo $this->languagemap->t('curriculum_inventory.create.report_description.hint', $lang, false); ?>"></textarea>
                </div>
                <div class="dialog-form-row">
                    <label for="new_report_program" class="entity_widget_title">
                        <?php echo$this->languagemap->t('general.terms.program', $lang); ?>:
                    </label><br />
                    <select name="program_id" id="new_report_program">
                        <option value="">&lt;<?php echo $this->languagemap->t('general.phrases.select_one', $lang); ?>&gt;</option>
                    </select>
                </div>
                <div class="dialog-form-row">
                    <label for="new_report_year" class="entity_widget_title">
                        <?php echo $this->languagemap->t('general.phrases.academic_year', $lang); ?>:
                    </label><br />
                    <input id="new_report_year" name="report_year" type="text" value="" maxlength="4" size="4" placeholder="YYYY">
                </div>
                <div class="clear"></div>
            </form>
        </div>
     </div>
    <div class="ft"></div>
</div>
