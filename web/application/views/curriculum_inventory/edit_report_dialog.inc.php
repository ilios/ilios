<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @file edit_report_dialog.inc.php
 *
 * Renders the markup for the report editing dialog widget.
 *
 * Available template variables:
 *
 *    $lang ... The language key.
 *    $reports ... An array of existing inventory reports.
 *    $controllerURL ... The page controller URL.
 *
 * @see application/views/curriculum_inventory/index.php
 * @see application/views/js/ilios.cim.widget.js
 */
?>
<div class="tabdialog hidden" id="edit_report_dialog">
    <div class="hd"><?php echo $this->languagemap->t('curriculum_inventory.edit.title', $lang); ?></div>
    <div class="bd">
        <div class="dialog_wrap">
            <span id="report_update_status" class="dialog-form-status"></span>
            <form method="POST" action="<?php echo $controllerURL; ?>/update">
                <input type="hidden" value="" id="edit_report_id" name="report_id" />
                <div class="dialog-form-row" >
                    <label for="edit_report_name" class="entity_widget_title">
                        <?php echo $this->languagemap->t('general.terms.name', $lang); ?>:
                    </label><br />
                    <input id="edit_report_name" name="report_name" type="text"  value="" size="50"
                           placeholder="<?php echo $this->languagemap->t('curriculum_inventory.create.report_name.hint', $lang, false); ?>" />
                </div>
                <div class="dialog-form-row" >
                    <label for="edit_report_description" class="entity_widget_title">
                        <?php echo $this->languagemap->t('general.terms.description', $lang); ?>:
                    </label><br />
                    <textarea id="edit_report_description" name="report_description" type="text" cols="80" rows="10"
                              placeholder="<?php echo $this->languagemap->t('curriculum_inventory.create.report_description.hint', $lang, false); ?>"></textarea>
                </div>
                <div class="dialog-form-row" >
                    <label for="edit_report_description" class="entity_widget_title">
                        <?php echo $this->languagemap->t('general.phrases.start_date', $lang); ?>:
                    </label><br />
                    <input id="edit_report_start_date" name="start_date" type="text" size="10"
                           placeholder="YYYY-MM-DD" readonly="readonly"/>
                    <span class="calendar_button" id="edit_report_start_date_button"></span>
                </div>
                <div id="edit_report_start_date_calendar_container" style="display:none;"></div>
                <div class="clear"></div>
                <div class="dialog-form-row">
                    <label for="edit_report_description" class="entity_widget_title">
                        <?php echo $this->languagemap->t('general.phrases.end_date', $lang); ?>:
                    </label><br />
                    <input id="edit_report_end_date" name="end_date" type="text" size="10"
                           placeholder="YYYY-MM-DD" readonly="readonly"/>
                    <span class="calendar_button" id="edit_report_end_date_button"></span>
                </div>
                <div id="edit_report_end_date_calendar_container" style="display:none;"></div>
                <div class="clear"></div>
            </form>
        </div>
    </div>
    <div class="ft"></div>
</div>
