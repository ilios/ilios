<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @file program_content_container_include.php
 *
 * Includes-template.
 *
 * Renders a given program onto to the page in a container element.
 *
 * Template variables expected to be present are:
 *
 * @var boolean $disabled Set to TRUE if the container's form is in read-only mode.
 * @var array $program_row An associative array containing the program data to be rendered in the container.
 */
?>
<!-- content_container start -->
<div class="content_container">
    <div class="master_button_container clearfix">
        <ul class="buttons left">
            <li>
                <a href="" class="small radius button"
                   onclick="ilios.pm.cs.displayProgramSearchPanel(); return false;"><?php echo t('general.terms.search'); ?></a>
            </li>
            <li>
                <a id="add_new_program" href="" class="small secondary radius button"
                   onClick="ilios.pm.displayAddNewProgramDialog(); return false;"><?php echo t('program_management.add_program'); ?></a>
            </li>
        </ul>
        <ul class="buttons right">
            <li>
            </li>
        </ul>
    </div>
    <form id="program_form" method="POST" onsubmit="return false;">
        <input id="working_program_id" name="program_id"
               value="<?php echo htmlentities($program_row['program_id'], ENT_COMPAT, 'UTF-8'); ?>" type="hidden" />
        <div class="entity_container level-1">
            <div class="hd clearfix">
                <div class="toggle">
                    <a href="#" id="show_more_or_less_link"
                       onclick="ilios.utilities.toggle('course_more_or_less_div', this); return false;" >
                        <i class="icon-plus" aria-hidden="true"> </i>
                    </a>
                </div>
                <ul>
                    <li class="title">
                        <span class="data-type"><?php echo t('general.phrases.program_title_full'); ?></span>
                        <span class="data" id=""><?php echo htmlentities($program_row['title'], ENT_COMPAT, 'UTF-8'); ?></span>
                    </li>
                    <li class="course-id">
                        <span class="data-type"><?php echo t('general.phrases.program_title_short'); ?></span>
                        <span class="data" id=""><?php echo htmlentities($program_row['short_title'], ENT_COMPAT, 'UTF-8'); ?></span>
                    </li>
                    <li class="duration">
                        <span class="data-type"><?php echo t('general.phrases.duration.in_years'); ?></span>
                        <span class="data" id=""><?php echo htmlentities($program_row['duration'], ENT_COMPAT, 'UTF-8'); ?></span>
                    </li>
                    <li class="publish-status">
                        <span class="data-type"><?php echo t('general.phrases.publish_status'); ?></span>
                        <span class="data" id="parent_publish_status_text"></span>
                    </li>
                </ul>
            </div>
            <div id="course_more_or_less_div" class="bd" style="display:none">
                <div id="edit_program_inputfields" class="bd" style="display:none">

                    <div class="row">
                        <div class="column label">
                            <label for="program_title"><?php echo t('general.phrases.program_title_full'); ?></label>
                        </div>
                        <div class="column data">
                            <input type="text" id="program_title" name="program_title" value="" disabled="disabled" size="50" />
                        </div>
                        <div class="column actions">
                        </div>
                    </div>

                    <div class="row">
                        <div class="column label">
                            <label for="short_title"><?php echo t('general.phrases.program_title_short'); ?></label>
                        </div>
                        <div class="column data">
                            <input type="text"
                                   id="short_title"
                                   name="short_title" maxlength="10"
                                   value="<?php echo htmlentities($program_row['short_title'], ENT_COMPAT, 'UTF-8'); ?>"
                                   <?php if ($disabled) : ?>disabled="disabled"<?php endif; ?>
                                />
                        </div>
                        <div class="column actions"></div>
                    </div>

                    <div class="row">
                        <div class="column label">
                            <label for=""><?php echo t('general.phrases.duration.in_years'); ?></label>
                        </div>
                        <div class="column data">
                            <select name="duration"
                                    id="duration_selector"<?php if ($disabled): ?> disabled="disabled"<?php endif; ?>>
<?php
    // no given duration default to "4"
    $duration = $program_row['duration'] ? (int) $program_row['duration'] : 4;
    for ($i = 1, $n = 10; $i <= $n; $i++) :
?>
    <option value="<?php echo $i; ?>" <?php if ($i === $duration): ?> selected="selected"<?php endif; ?>><?php echo $i; ?></option>
<?php
    endfor;
?>
                            </select>
                        </div>
                        <div class="column actions"></div>
                    </div>
                </div>
                <div class="buttons bottom">
                    <button id="draft_button" class="medium radius button" disabled="disabled" onClick="ilios.pm.transaction.performProgramSave(false);">Save Draft</button>
                    <button id="publish_button" class="medium radius button" disabled="disabled" onClick="ilios.pm.transaction.performProgramSave(true);">Publish Now</button>
                    <button id="reset_button" class="reset_button small secondary radius button" disabled="disabled" onClick="ilios.pm.revertChanges();">Reset Form</button>
                </div>
            </div><!--close div.bd-->
        </div><!-- entity_container close -->
    </form>
    <div class="collapse_children_toggle_link">
        <button class="small secondary radius button" onclick="ilios.pm.collapseOrExpandProgramYears(false); return false;"
                id="expand_program_years_link" style="display: none;"><?php echo t('general.phrases.collapse_all'); ?></button>
    </div>

    <div style="clear: both;"></div>

    <div id="program_year_container"></div>

    <div class="add_primary_child_link">
        <button class="small secondary radius button" onclick="ilios.pm.addNewProgramYear();"
                id="add_new_program_year_link" disabled="disabled"><?php echo t('program_management.add_program_year'); ?></button>
    </div>
</div>
<!-- content_container end -->
