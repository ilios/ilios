<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @file course_container_include.php
 *
 * Includes template.
 *
 * Renders the container element for a course onto the page.
 *
 * Template variables assumed to be present are:
 *
 * @var array $clerkship_types An associative array, holding clerkship-id and clerkship-title key/value pairs.
 * @var int $course_id The record id of the currently loaded course.
 *
 */
?>
<!-- content_container start -->
<div class="content_container">
<div class="master_button_container clearfix">
    <ul class="buttons left">
        <li>
            <button class="small radius button" onclick="ilios.cm.cs.displayCourseSearchPanel();"><?php echo t('general.terms.search'); ?></button>
        </li>
        <li>
            <button id="add_new_course" class="small secondary radius button" onClick="ilios.cm.displayAddNewCourseDialog();">
                <?php echo t('course_management.add_course'); ?>
            </button>
        </li>
    </ul>
    <ul class="buttons right">
        <li id="rollover_link_div" class="rollover_link_div"></li>
        <li id="archiving_link_div" class="archiving_link_div"></li>
        <li>
            <button id="save_all_dirty_to_draft" class="medium radius button" disabled='disabled'>Save All as Draft
            </button>
        </li>
        <li>
            <button id="publish_all" class="medium radius button" disabled='disabled'><i class="icon-checkmark"></i>
                <?php echo t('general.phrases.publish_all'); ?>
            </button>
        </li>
    </ul>
</div>
<form id="course_form" method="POST" onsubmit="return false;">
<div class="entity_container level-1">
<div class="hd clearfix">
    <div class="toggle">
        <a href="#" id="show_more_or_less_link"
           onclick="ilios.utilities.toggle('course_more_or_less_div', this); return false;">
            <i class="icon-plus" aria-hidden="true"> </i>
        </a>
    </div>
    <ul>
        <li class="title">
            <span class="data-type"><?php echo t('general.phrases.course_title'); ?></span>
            <span class="data" id="summary-course-title"></span>
        </li>
        <li class="course-id">
            <span class="data-type"><?php echo t('course_management.external_course_id'); ?></span>
            <span class="data" id="summary-course-id"></span>
        </li>
        <li class="course-year">
            <span class="data-type"><?php echo t('general.phrases.course_year'); ?></span>
            <span class="data" id="summary-course-year"></span>
        </li>
        <li class="course-level">
            <span class="data-type"><?php echo t('general.phrases.course_level'); ?></span>
            <span class="data" id="summary-course-level"></span>
        </li>
        <li class="publish-status">
            <span class="data-type"><?php echo t('general.phrases.publish_status'); ?></span>
            <span class="data" id="parent_publish_status_text"></span>
        </li>

    </ul>
</div>
<div id="course_more_or_less_div" class="bd" style="display:none">

    <div class="row">
        <div class="column label">
            <label class="entity_widget_title" for="course_title"><?php echo t('general.phrases.course_name'); ?></label>
        </div>
        <div class="column data">
            <input type="text" id="course_title" name="course_title" value="" disabled="disabled" size="50"/>
        </div>
        <div class="column actions">
            <a href=""
               class="tiny white radius button"
               onclick="ilios.course_summary.showCourseSummary(ilios.cm.currentCourseModel); return false;"
               id="show_course_summary_link"><?php echo t('general.phrases.show_course_summary'); ?></a>
        </div>
    </div>

    <div class="row">
        <div class="column label">
            <label for="external_course_id"><?php echo t('course_management.external_course_id'); ?></label>
        </div>
        <div class="column data">
            <input type="text" id="external_course_id" value=""/>
            <input type="text" readonly="readonly" id="course_unique_id" class="readonly-text note" value=""/>
        </div>
        <div class="column actions"></div>
    </div>

    <div class="row">
        <div class="column label">
            <label><?php echo t('general.phrases.course_year'); ?></label>
        </div>
        <div class="column data">
            <span id="course_year_start" class="read_only_data"></span>
        </div>
    </div>

    <div class="row">
        <div class="column label">
            <label for="course_level_selector"><?php echo t('general.phrases.course_level'); ?></label>
        </div>
        <div class="column data">
            <select id="course_level_selector" name="course_level">
                <option value="1" selected="selected">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
            </select>
        </div>
    </div>
    <div class="row">
        <div class="column label">
            <label for="clerkship_type_selector"><?php echo t('course_management.clerkship_type'); ?></label>
        </div>
        <div class="column data">
            <select id="clerkship_type_selector">
                <option value=""><?php echo t('course_management.not_a_clerkship'); ?></option>
<?php foreach ($clerkship_types as $clerkshipTypeId => $clerkshipTypeTitle) : ?>
                <option value="<?php echo $clerkshipTypeId; ?>"><?php echo htmlentities($clerkshipTypeTitle, ENT_COMPAT, 'UTF-8'); ?></option>
<?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="row">
        <div class="column label">
            <label><?php echo t('general.phrases.program_cohorts'); ?></label>
        </div>
        <div class="column data">
            <div id="cohort_level_table_div"></div>
        </div>
        <div class="column actions">
            <a href=""
               class="tiny radius button"
               onclick="ilios.ui.onIliosEvent.fire({action: 'gen_dialog_open', event: 'find_cohort_and_program'}); return false;"
               id="select_cohorts_link"><?php echo t('course_management.select_cohorts'); ?></a>
        </div>
    </div>

    <div class="row">
        <div class="column label">
            <label><?php echo t('general.phrases.associated_learners'); ?></label>
        </div>
        <div class="column data">
            <div id="course_associated_learners" class="read_only_data scroll_list"></div>
        </div>
    </div>

    <div class="row">
        <div class="column label">
            <label for="start_date_calendar_button"><?php echo t('general.phrases.start_date'); ?></label>
        </div>
        <div class="column data">
            <span id="course_start_date" class="read_only_data"><?php echo t('general.phrases.no_date_selected'); ?></span>
            <span id="start_date_calendar_button" class="calendar_button"></span>
        </div>
        <div class="column actions"></div>
    </div>

    <div class="row">
        <div class="column label">
            <label for="end_date_calendar_button"><?php echo t('general.phrases.end_date'); ?></label>
        </div>
        <div class="column data">
            <span id="course_end_date" class="read_only_data"><?php echo t('general.phrases.no_date_selected'); ?></span>
            <span id="end_date_calendar_button" class="calendar_button"></span>
        </div>
        <div class="column actions"></div>
    </div>

    <div class="row">
        <div class="column label">
            <label for=""><?php echo t('general.terms.competencies'); ?></label>
        </div>
        <div class="column data">
            <div id="-1_competency_picker_selected_text_list" class="read_only_data scroll_list"></div>
        </div>
        <div class="column actions"></div>
    </div>

    <div class="row">
        <div class="column label">
            <label for=""><?php echo t('general.terms.topics'); ?></label>
        </div>
        <div class="column data">
            <div id="-1_discipline_picker_selected_text_list" class="read_only_data scroll_list"></div>
        </div>
        <div class="column actions">
            <a href=""
               class="tiny radius button"
               onclick="ilios.ui.onIliosEvent.fire({action: 'default_dialog_open', event: 'discipline_picker_show_dialog', container_number: -1}); return false;"
               id="disciplines_search_link"><?php echo t('general.terms.search'); ?></a>
        </div>
    </div>

    <div class="row">
        <div class="column label">
            <label for=""><?php echo t('general.terms.directors'); ?></label>
        </div>
        <div class="column data">
            <div id="-1_director_picker_selected_text_list" class="read_only_data scroll_list"></div>
        </div>
        <div class="column actions">
            <a href="" class="tiny radius button"
               onclick="ilios.ui.onIliosEvent.fire({action: 'default_dialog_open', event: 'director_picker_show_dialog', container_number: -1}); return false;"
               id="directors_search_link"><?php echo t('general.terms.search'); ?></a>
        </div>
    </div>

    <div class="row">
        <div class="column label">
            <label for=""><?php echo t('general.phrases.mesh_terms'); ?></label>
        </div>
        <div class="column data">
            <div id="-1_mesh_terms_picker_selected_text_list" class="read_only_data scroll_list"></div>
        </div>
        <div class="column actions">
            <a href="" class="tiny radius button"
               onclick="ilios.cm.displayMeSHDialogForCourse(); return false;"
               id="mesh_search_link"><?php echo t('general.terms.search'); ?></a>
        </div>
    </div>
    <div class="row">
        <div class="column label">
            <div class="collapsed_widget" id="-1_learning_materials_container_expand_widget"></div>
            <label id="-1_learning_materials_container_label" for=""><?php echo t('general.phrases.learning_materials'); ?> (0)</label>
        </div>
        <div class="column data">
            <div id="-1_learning_materials_container" style="display: none;"></div>
        </div>
        <div class="column actions">
            <a href="" class="tiny radius button"
               onclick="ilios.cm.lm.addNewLearningMaterial('-1');"
               id="add_learning_material_link"><?php echo t('general.phrases.add_learning_material_link'); ?></a>
        </div>
    </div>
    <div class="row">
        <div class="column label">
            <div class="collapsed_widget" id="-1_objectives_container_expand_widget"></div>
            <label id="-1_objectives_container_label" for=""><?php echo t('general.phrases.learning_objectives'); ?> (0)</label>
        </div>
        <div class="column data">
            <div id="-1_objectives_container" style="display: none;"></div>
        </div>
        <div class="column actions">
            <a href="" class="tiny radius button"
               onclick="ilios.cm.addNewCourseObjective('-1'); return false;"
               id="add_objective_link"><?php echo t('general.phrases.add_objective'); ?></a>
        </div>
    </div>
    <div class="buttons bottom">
        <button id="draft_button" class="medium radius button" disabled="disabled" onClick="ilios.cm.transaction.saveCourseAsDraft();">
            Save Draft
        </button>
        <button id="publish_button" class="medium radius button" disabled="disabled" onClick="ilios.cm.transaction.performCoursePublish();">
            <div id="-1_publish_warning" class="yellow_warning_icon" style="display: none;"></div>
            <?php echo t('general.phrases.publish_course'); ?>
        </button>
        <button id="reset_button" class="reset_button small secondary radius button" disabled="disabled" onClick="ilios.cm.transaction.revertChanges();">
            Reset Form
        </button>
    </div>
</div>
<!--close div.bd-->
</div>
<!-- entity_container close -->
</form>
<div id="course_session_header_div">
    <div id="sessions_summary"></div>
<?php
// KLUDGE!
// see if a course is about to be loaded in the page by checking if a stub object has been passed to the view.
// if so then display a "loading course details" status progress message in the sessions_summary element.
// @see GitHub Issue #203
// [ST 2013/10/18]
if (-1 === $course_id) :
    echo generateProgressDivMarkup('position: absolute; left: 38%; display: none', ''); // hide it.
else :
    echo generateProgressDivMarkup('position: absolute; left: 38%;', t('general.phrases.loading_all_course_sessions'));
endif;
?>
    <div id="course_sessions_toolbar" class="collapse_children_toggle_link hidden">
        <select id="session_ordering_selector" onchange="ilios.cm.session.reorderSessionDivs(); return false;">
            <option selected="selected"><?php echo t('course_management.session.sort.alpha_asc'); ?></option>
            <option><?php echo t('course_management.session.sort.alpha_desc'); ?></option>
            <option><?php echo t('course_management.session.sort.date_asc'); ?></option>
            <option><?php echo t('course_management.session.sort.date_desc'); ?></option>
        </select>

        <button class="small secondary radius button"
                onclick="ilios.cm.session.collapseOrExpandSessions(false); return false;"
                id="expand_sessions_link"><?php echo t('general.phrases.collapse_all'); ?>
        </button>
    </div>

    <div style="clear: both;"></div>
</div>

<div id="session_container"></div>

<div class="add_primary_child_link">
    <button class="small secondary radius button" onclick="ilios.cm.session.userRequestsSessionAddition();"
            id="add_new_session_link" disabled="disabled"><?php echo t('course_management.add_session'); ?>
    </button>
</div>
</div>
<!-- content_container end -->
