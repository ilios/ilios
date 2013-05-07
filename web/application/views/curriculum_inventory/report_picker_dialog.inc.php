<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @file report_picker_dialog.inc.php
 *
 * Renders the markup for the report search dialog.
 *
 * Available template variables:
 *
 *    $lang ... The language key.
 *    $reports ... An array of existing inventory reports.
 *    $controllerURL ... The page controller URL.
 *
 * @see application/views/curriculum_inventory/index.php
 * @see application/views/curriculum_inventory/view.php
 * @see application/views/curriculum_inventory_curriculum_inventory_manager.js
 */
?>
<div class="tabdialog" id="report_picker_dialog">
    <div class="hd"><?php echo $this->languagemap->t('curriculum_inventory.select.title', $lang); ?>:</div>
    <div class="bd">
        <div class="dialog_wrap">
            <form method="POST" action="#">
                <fieldset>
                    <legend><?php echo $this->languagemap->t('general.terms.curriculum_inventory_reports', $lang); ?></legend>
                    <div class="scroll_list clearfix">
                        <ul id="report_picker_results_list" class="search-results">
                        <?php foreach ($reports as $report) : ?>
                            <li><a href="<?php echo $controllerURL; ?>?report_id=<?php echo $report['report_id']; ?>">
                                <?php echo htmlspecialchars($report['name']); ?> (<?php echo $report['year']; ?>)
                            </a></li>
                        <?php endforeach; ?>
                        </ul>
                    </div>
                </fieldset>
             </form>
        </div>
    </div>
    <div class="ft"></div>
</div>
