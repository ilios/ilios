<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

            $formPrefix = '<form id="cohort_form" method="POST" action="' . current_url()
                                . '/willNeverSubmit"
                        onsubmit="return false;">';


            $addNewEntityLink = '';

            $searchNewEntityLink = '<a id="find_cohort_and_program" href="" class="small radius button"
                            onClick="ilios.ui.onIliosEvent.fire({action: \'sac_dialog_open\',
                                                                    event: \'find_cohort_and_program\'});
                                     return false;">' . $select_program_link_string . '</a>';

            $entityContainerHeader = '
                        <li class="title">
                            <span class="data-type">' . $program_title_full_string . '</span>
                            <span class="data" id="program_cohort_title"></span>
                        </li>
                        <li class="short-title">
                            <span class="data-type">' . $program_title_short_string . '</span>
                            <span class="data" id="program_title_short"></span>
                        </li>
                        <li class="enrollment">
                            <span class="data-type">' . $current_enrollment_string . '</span>
                            <span class="data" id="current_enrollment"></span>
                        </li>';

            $entityContainerContent = '';

            $addNewSomethingId = 'all_edit_member_link';
            $addNewSomethingAction = 'ilios.ui.onIliosEvent.fire({action: \'em_dialog_open\', event: \'add_new_members_picker_show_dialog\', container_number: \'-1\'});';
            $addNewSomethingDisplayText = $add_to_all_string ;


            $suffixingContent = '
                    <div class="collapse_children_toggle_link">
                        <button class="small secondary radius button"
                            onclick="ilios.gm.subgroup.changeBreadcrumbViewLevelOrSidestep(\'-1\'); return false;"
                                id="open_cohort" style="display: none;">' . $open_cohort_string . '</button>
                        <button class="small secondary radius button groups_collapsed" onclick="ilios.gm.collapseOrExpandGroups(false, false); return false;"
                                id="expand_groups_link"
                                style="display: none;">' . $expand_groups_string . '</button>
                    </div>

                    <div style="clear: both;"></div>

                    <div id="breadcrumb_group_trail"></div>
                    <div id="group_container"></div>
                    <div id="breadcrumbed_suffixed_group_trail"></div>


                    <div class="add_primary_child_link">
                        <button class="small secondary radius button" onclick="ilios.gm.transaction.handleManualGroupAdd();"
                                id="general_new_add_group_link" disabled="disabled">' . $add_group_string . '</button>
                    </div>';


            $saveDraftAction = '';
            $publishAction = '';
            $revertAction = '';

            createContentContainerMarkup($formPrefix, $addNewEntityLink, $searchNewEntityLink, $entityContainerHeader,
                $entityContainerContent, $addNewSomethingId, $addNewSomethingAction, $addNewSomethingDisplayText,
                $suffixingContent, $saveDraftAction, $publishAction, $revertAction, false, false, false, true,
                t('general.phrases.save_all'),
                t('general.phrases.save_draft'),
                t('general.phrases.publish_all'),
                t('general.phrases.publish_now'),
                t('general.phrases.reset_form')
            );

