<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
	<div id="inspector_pane" style="display: none;">
		<div id="inspector_pane_title_bar" class="om_inspector_title_bar">
				<?php echo $course_title ?> <?php echo $course_year_string ?>: <span id='inspector_pane_title_bar_title'></span>
		</div>

		<div style="padding: 0;">
			<div id='inspector_pane_session_type_title'
					style='float: left; font-size: 11pt; font-weight: bold; padding-left: 9px; margin: 6px 0px;'></div>

			<div style='float: right; margin-right: 6px; margin-top: 8px; font-size: 8pt;'>
				<a href='' onclick='return false;' id='inspector_pane_open_learner_view'><?php echo $see_learner_view ?></a>
			</div>

			<div style='clear: both;'></div>
		</div>

		<div id='inspector_pane_time_and_location'
				style='padding-left: 22px; font-weight: bold; font-size: 8pt; margin-bottom: 15px;'></div>

		<div id='inspector_pane_instructor_groups'
				style='padding-left: 22px; font-weight: bold; font-size: 8pt; margin-bottom: 12px;
						float: left; width: 55%;'>
			<div id='inspector_pane_instructor_groups_title' style='float: left'></div>
			<div id='inspector_pane_instructor_groups_content' style='margin-left: 56px; overflow: auto; height: 40px;'></div>
			<div style='clear: both'></div>
		</div>
		<div id='inspector_pane_offering_attributes'
				style='float: right; padding-right: 18px; font-weight: bold; font-size: 8pt;'>
			<span id='inspector_pane_offering_attributes_supplemental'></span>?
			<span id='inspector_pane_offering_attributes_supplemental_value'></span>
			<br/>
			<span id='inspector_pane_offering_attributes_equipment'></span>?
			<span id='inspector_pane_offering_attributes_equipment_value'></span>
			<br/>
			<span id='inspector_pane_offering_attributes_attire'></span>?
			<span id='inspector_pane_offering_attributes_attire_value'></span>
		</div>
		<div style='clear: both'></div>

		<div id='inspector_pane_learners'
				style='padding-left: 22px; font-weight: bold; font-size: 8pt; margin-bottom: 44px;
						width: 55%;'>
			<div id='inspector_pane_learners_title' style='float: left;'></div>
			<div id='inspector_pane_learners_content' style='margin-left: 56px; overflow: auto; height: 40px;'></div>
			<div style='clear: both'></div>
		</div>

		<div id='inspector_pane_learning_materials'
				style='padding-left: 12px; font-weight: bold; font-size: 9pt; margin-bottom: 24px;'>
			<div id='inspector_pane_learning_materials_title' style='margin-bottom: 4px;'></div>
			<div style='margin-left: 20px; font-size: 8pt; height: 80px; overflow: auto;'>
				<ul id='inspector_pane_learning_materials_content' class='learning_material_list'></ul>
			</div>
		</div>

		<div id='inspector_pane_vocabulary'
				style='padding-left: 12px; font-weight: bold; font-size: 9pt; margin-bottom: 24px;'>
			<div id='inspector_pane_vocabulary_title' style='margin-bottom: 4px;'></div>
			<div id='inspector_pane_vocabulary_content'
					style='font-weight: bold; font-size: 8pt; margin-left: 20px; margin-right: 32px; overflow: auto; height: 40px;'></div>
		</div>

		<div id='inspector_pane_objectives'
				style='padding-left: 12px; font-weight: bold; font-size: 9pt; margin-bottom: 24px;'>
			<div id='inspector_pane_objectives_title'></div>
			<div style='font-weight: bold; font-size: 8pt; margin-left: 20px; margin-right: 32px;'>
				<ul id='inspector_pane_objectives_content' style='margin-top: 2px; padding-left: 3px; overflow: auto; height: 40px;'>
				</ul>
			</div>
		</div>

	</div>
