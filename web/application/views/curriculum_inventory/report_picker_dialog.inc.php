<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @file report_picker_dialog.inc.php
 *
 * Renders the markup for the report search dialog widget.
 *
 * Available template variables:
 *
 *    $reports ... An array of existing inventory reports.
 *    $controllerURL ... The page controller URL.
 *
 * @see application/views/curriculum_inventory/index.php
 * @see application/views/js/ilios.cim.widget.js
 */
?>
<div class="tabdialog hidden" id="report_picker_dialog">
    <div class="hd"><?php echo $this->languagemap->t('curriculum_inventory.select.title'); ?>:</div>
    <div class="bd">
        <div class="dialog_wrap">
            <form method="POST" action="#">
                <fieldset>
                    <legend><?php echo $this->languagemap->t('general.terms.curriculum_inventory_reports'); ?></legend>
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
