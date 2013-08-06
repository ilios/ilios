<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @file create_sequence_block_dialog.inc.php
 *
 * Renders the markup for the "create a sequence block" dialog widget.
 *
 * Available template variables:
 *
 *    $lang ... The language key.
 *    $reports ... An array of existing inventory reports.
 *    $controllerURL ... The page controller URL.
 *    $academic_levels ... An array of the academic levels in this report. Each item is an associative array representing
 *                        an academic level record.
 *
 * @see application/views/curriculum_inventory/index.php
 * @see application/views/js/ilios.cim.widget.js
 */
?>
<div class="tabdialog hidden" id="create-sequence-block-dialog">
    <div class="hd"><?php echo $this->languagemap->t('curriculum_inventory.sequence_block.add_dialog.title', $lang); ?></div>
    <div class="bd">
        <div class="dialog_wrap">
            <span id="create-sequence-block-dialog-status" class="dialog-form-status"></span>
            <form method="POST" action="<?php echo $controllerURL; ?>/update">
                <input type="hidden" value="" id="create-sequence-block-dialog-report-id" name="report_id" />
                <div class="dialog-form-row" >
                    <label for="create-sequence-block-dialog--title" class="entity_widget_title">
                        <?php echo $this->languagemap->t('curriculum_inventory.sequence_block.add_dialog.title.label', $lang); ?>:
                    </label><br />
                    <input id="create-sequence-block-dialog--title" name="title" type="text" value="" size="50"
                           placeholder="<?php echo $this->languagemap->t('curriculum_inventory.sequence_block.add_dialog.title.hint', $lang, false); ?>" />
                </div>
                <div class="dialog-form-row" >
                    <label for="create-sequence-block-dialog--description" class="entity_widget_title">
                        <?php echo $this->languagemap->t('curriculum_inventory.sequence_block.add_dialog.description.label', $lang); ?>:
                    </label><br />
                    <textarea id="create-sequence-block-dialog--description" name="description" type="text" cols="80" rows="10"
                              placeholder="<?php echo $this->languagemap->t('curriculum_inventory.sequence_block.add_dialog.description.hint', $lang, false); ?>"></textarea>
                </div>
                <div class="dialog-form-row" >
                    <label for="create-sequence-block-dialog--academic-level" class="entity_widget_title">
                        <?php echo $this->languagemap->t('curriculum_inventory.sequence_block.add_dialog.academic-level.label', $lang); ?>:
                    </label><br />
                    <select id="create-sequence-block-dialog--academic-level" name="academic_level">
<?php foreach ($academic_levels as $level) : ?>
                        <option value="<?php echo htmlentities($level['level'], ENT_COMPAT, 'utf-8'); ?>">
                            <?php echo htmlentities($level['name'], ENT_COMPAT, 'utf-8'); ?></option>
<?php endforeach; ?>
                    </select>
                </div>
            </form>
        </div>
    </div>
    <div class="ft"></div>
</div>
