<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @file create_sequence_block_dialog.inc.php
 *
 * Renders the markup for the "create a sequence block" dialog widget.
 *
 * Available template variables:
 *
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
    <div class="hd"><?php echo t('curriculum_inventory.sequence_block.add_dialog'); ?></div>
    <div class="bd">
        <div class="dialog_wrap">
            <span id="create-sequence-block-dialog--status" class="dialog-form-status"></span>
            <form method="POST" action="<?php echo $controllerURL; ?>/createSequenceBlock">
                <input type="hidden" value="" id="create-sequence-block-dialog--report-id" name="report_id" />
                <input type="hidden" value="" id="create-sequence-block-dialog--parent-block-id" name="parent_sequence_block_id" />
                <div class="dialog-form-row" >
                    <label for="create-sequence-block-dialog--title" class="entity_widget_title">
                        <?php echo t('general.terms.title'); ?>:
                    </label><br />
                    <input id="create-sequence-block-dialog--title" name="title" type="text" value="" size="50"
                           placeholder="<?php echo t('curriculum_inventory.sequence_block.add_dialog.title.hint', false); ?>" />
                </div>
                <div class="dialog-form-row" >
                    <label for="create-sequence-block-dialog--description" class="entity_widget_title">
                        <?php echo t('general.terms.description'); ?>:
                    </label><br />
                    <textarea id="create-sequence-block-dialog--description" name="description" type="text" cols="80" rows="3"
                              placeholder="<?php echo t('curriculum_inventory.sequence_block.add_dialog.description.hint', false); ?>"></textarea>
                </div>
                <div class="dialog-form-row" >
                    <label for="create-sequence-block-dialog--required" class="entity_widget_title">
                        <?php echo t('general.terms.required'); ?> ?
                    </label><br />
                    <select id="create-sequence-block-dialog--required" name="required">
                        <option value="<?php echo Curriculum_Inventory_Sequence_Block::REQUIRED; ?>">
                            <?php echo t('general.terms.yes'); ?></option>
                        <option value="<?php echo Curriculum_Inventory_Sequence_Block::OPTIONAL; ?>">
                            <?php echo t('general.terms.no'); ?></option>
                        <option value="<?php echo Curriculum_Inventory_Sequence_Block::REQUIRED_IN_TRACK; ?>">
                            <?php echo t('general.phrases.required_in_track'); ?>
                        </option>
                    </select>
                </div>
                <div class="dialog-form-row" >
                    <label for="create-sequence-block-dialog--academic-level" class="entity_widget_title">
                        <?php echo t('general.phrases.academic_level'); ?>:
                    </label><br />
                    <select id="create-sequence-block-dialog--academic-level" name="academic_level">
<?php foreach ($academic_levels as $level) : ?>
                        <option value="<?php echo htmlentities($level['academic_level_id'], ENT_COMPAT, 'utf-8'); ?>">
                            <?php echo htmlentities($level['name'], ENT_COMPAT, 'utf-8'); ?></option>
<?php endforeach; ?>
                    </select>
                </div>
                <div class="dialog-form-row">
                    <label for="create-sequence-block-dialog--course" class="entity_widget_title">
                        <?php echo t('general.terms.course'); ?>:
                    </label><br />
                    <select id="create-sequence-block-dialog--course" name="course_id">
                        <option value="">&lt;<?php echo t('general.terms.none'); ?>&gt;</option>
                    </select>
                    <div id="create-sequence-block-dialog--course-details"></div>
                </div>
                <div class="dialog-form-row" >
                    <label class="entity_widget_title" for="create-sequence-block-dialog--child-sequence-order">
                        <?php echo t('curriculum_inventory.sequence_block.child_sequence_order'); ?>:
                    </label><br />
                    <select id="create-sequence-block-dialog--child-sequence-order" name="child_sequence_order">
                        <option value="<?php echo Curriculum_Inventory_Sequence_Block::ORDERED; ?>">
                            <?php echo t('general.terms.ordered'); ?></option>
                        <option value="<?php echo Curriculum_Inventory_Sequence_Block::UNORDERED; ?>">
                            <?php echo t('general.terms.unordered'); ?></option>
                        <option value="<?php echo Curriculum_Inventory_Sequence_Block::PARALLEL; ?>">
                            <?php echo t('general.terms.parallel'); ?>
                        </option>
                    </select>
                </div>
                <div class="dialog-form-row" id="create-sequence-block-dialog--order-in-sequence-row" class="hidden">
                    <label for="create-sequence-block-dialog--order-in-sequence" class="entity_widget_title">
                        <?php echo t('curriculum_inventory.sequence_block.order_in_sequence'); ?>:
                    </label><br />
                    <select id="create-sequence-block-dialog--order-in-sequence" name="order_in_sequence">
                        <option value="1">1</option>
                    </select>
                </div>
                <div class="dialog-form-row">
                    <label class="entity_widget_title" for="create-sequence-block-dialog--start-date">
                        <?php echo t('general.phrases.start_date'); ?>:
                    </label>
                    <input id="create-sequence-block-dialog--start-date" name="start_date" type="text" size="11"
                           placeholder="YYYY-MM-DD" readonly="readonly"/>
                    <span class="calendar_button" id="create-sequence-block-dialog--start-date-button"></span>
                    <label class="entity_widget_title" for="create-sequence-block-dialog--end-date">
                        <?php echo t('general.phrases.end_date'); ?>:
                    </label>
                    <input id="create-sequence-block-dialog--end-date" name="end_date" type="text" size="11"
                           placeholder="YYYY-MM-DD" readonly="readonly"/>
                    <span class="calendar_button" id="create-sequence-block-dialog--end-date-button"></span>
                    <button id="create-sequence-block-dialog--clear-dates-button">
                        <?php echo t('general.phrases.clear_dates'); ?></button>
                </div>
                <div id="create-sequence-block-dialog--start-date-calendar-container" style="display:none;"></div>
                <div id="create-sequence-block-dialog--end-date-calendar-container" style="display:none;"></div>
                <div class="clear"></div>
                <div class="dialog-form-row">
                    <label class="entity_widget_title" for="create-sequence-block-dialog--duration">
                        <?php echo t('general.phrases.duration.in_days'); ?>:
                    </label><br />
                    <input id="create-sequence-block-dialog--duration" name="duration" type="text" size="4" value="0" />
                </div>
                <div class="dialog-form-row">
                    <label class="entity_widget_title" for="create-sequence-block-dialog--track">
                        <?php echo t('general.phrases.is_track'); ?> ?
                    </label><br />
                    <select id="create-sequence-block-dialog--track" name="track">
                        <option value="0"><?php echo t('general.terms.no'); ?></option>
                        <option value="1"><?php echo t('general.terms.yes'); ?></option>
                    </select>
                </div>
            </form>
        </div>
    </div>
    <div class="ft"></div>
</div>
