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
    <div class="hd"><?php echo $this->languagemap->t('curriculum_inventory.sequence_block.add_dialog', $lang); ?></div>
    <div class="bd">
        <div class="dialog_wrap">
            <span id="create-sequence-block-dialog--status" class="dialog-form-status"></span>
            <form method="POST" action="<?php echo $controllerURL; ?>/createSequenceBlock">
                <input type="hidden" value="" id="create-sequence-block-dialog--report-id" name="report_id" />
                <input type="hidden" value="" id="create-sequence-block-dialog--parent-id" name="parent_id" />
                <div class="dialog-form-row" >
                    <label for="create-sequence-block-dialog--title" class="entity_widget_title">
                        <?php echo $this->languagemap->t('general.terms.title', $lang); ?>:
                    </label><br />
                    <input id="create-sequence-block-dialog--title" name="title" type="text" value="" size="50"
                           placeholder="<?php echo $this->languagemap->t('curriculum_inventory.sequence_block.add_dialog.title.hint', $lang, false); ?>" />
                </div>
                <div class="dialog-form-row" >
                    <label for="create-sequence-block-dialog--description" class="entity_widget_title">
                        <?php echo $this->languagemap->t('general.terms.description', $lang); ?>:
                    </label><br />
                    <textarea id="create-sequence-block-dialog--description" name="description" type="text" cols="80" rows="3"
                              placeholder="<?php echo $this->languagemap->t('curriculum_inventory.sequence_block.add_dialog.description.hint', $lang, false); ?>"></textarea>
                </div>
                <div class="dialog-form-row" >
                    <label for="create-sequence-block-dialog--required" class="entity_widget_title">
                        <?php echo $this->languagemap->t('general.terms.required', $lang); ?>:
                    </label><br />
                    <select id="create-sequence-block-dialog--required" name="required">
                        <option value="1">Required</option>
                        <option value="2">Optional</option>
                        <option value="3">Required in Track</option>
                    </select>
                </div>
                <div class="dialog-form-row" >
                    <label for="create-sequence-block-dialog--academic-level" class="entity_widget_title">
                        <?php echo $this->languagemap->t('general.phrases.academic_level', $lang); ?>:
                    </label><br />
                    <select id="create-sequence-block-dialog--academic-level" name="academic_level">
<?php foreach ($academic_levels as $level) : ?>
                        <option value="<?php echo htmlentities($level['level'], ENT_COMPAT, 'utf-8'); ?>">
                            <?php echo htmlentities($level['name'], ENT_COMPAT, 'utf-8'); ?></option>
<?php endforeach; ?>
                    </select>
                </div>
                <div class="dialog-form-row">
                    <label for="create-sequence-block-dialog--course" class="entity_widget_title">
                        <?php echo $this->languagemap->t('general.terms.courses', $lang); ?>:
                    </label><br />
                    <select id="create-sequence-block-dialog--course" name="course_id"></select>
                    <br />
                    <div id="create-sequence-block-dialog--course-view-container" class="hidden"></div>
                </div>
                <div class="dialog-form-row">
                    <label class="entity_widget_title" for="create-sequence-block-dialog--child-sequence-order">
                        <?php echo $this->languagemap->t('curriculum_inventory.sequence_block.child_sequence_order', $lang); ?>:
                    </label><br />
                    <select id="create-sequence-block-dialog--child-sequence-order" name="child_sequence_order">
                        <option value="1">Ordered</option>
                        <option value="2">Unordered</option>
                        <option value="3">Parallel</option>
                    </select>
                </div>
                <div class="dialog-form-row" id="create-sequence-block-dialog--order-in-sequence-row" class="hidden">
                    <label for="create-sequence-block-dialog--order-in-sequence" class="entity_widget_title">
                        <?php echo $this->languagemap->t('curriculum_inventory.sequence_block.order_in_sequence', $lang); ?>:
                    </label><br />
                    <select id="create-sequence-block-dialog--order-in-sequence" name="order_in_sequence"></select>
                </div>
            </form>
        </div>
    </div>
    <div class="ft"></div>
</div>
