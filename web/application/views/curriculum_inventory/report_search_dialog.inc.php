<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @file report_search_dialog.inc.php
 *
 * Renders the markup for the report search dialog.
 *
 * @see application/views/curriculum_inventory/index.php
 * @see application/views/curriculum_inventory/view.php
 * @see application/views/curriculum_inventory_curriculum_inventory_manager.js
 */
?>
<div class="tabdialog" id="report_search_picker">
    <div class="hd"><?php echo $this->languagemap->t('curriculum_inventory.search.title', $lang); ?></div>
    <div class="bd">
        <div class="dialog_wrap">
            <span id="report_search_status" class="dialog-form-status"></span>
            <form method="POST" action="<?php echo $controllerURL; ?>/searchReports">
                <fieldset>
                    <legend><?php echo $this->languagemap->t('general.phrases.search_term', $lang); ?></legend>
                    <input type="text" id="report_search_term" name="report_search_term" size="50"
                           placeholder="<?php echo $this->languagemap->t('general.phrases.search.hint', $lang, false); ?>">
                    <span class="search_icon_button" id="search_report_submit_btn"></span>
                </fieldset>
                <fieldset>
                    <legend><?php echo $this->languagemap->t('general.phrases.search_results', $lang); ?></legend>
                    <div class="scroll_list clearfix">
                        <ul id="report_search_results_list" class="search-results">
                            <li></li>
                        </ul>
                    </div>
                </fieldset>
             </form>
        </div>
    </div>
    <div class="ft"></div>
</div>
