<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

            // This form action is actually used in the code as the url for the AJAX-ian save
            $formPrefix = '<form id="program_form" method="POST" action="no matter" onsubmit="return false;">
                <input id="working_program_id" name="program_id" value="' . $program_row['program_id'] . '"';

            $formPrefix .= ' type="hidden" />';


            $addNewEntityLink = '<a id="add_new_program" href="" class="small secondary radius button" onClick="ilios.pm.displayAddNewProgramDialog(); return false;">' . $add_program_string . '</a>';

            $searchNewEntityLink = '<a href="" class="small radius button" onclick="ilios.pm.cs.displayProgramSearchPanel(); return false;">'
                            . $word_search_string . '</a>';

            $entityContainerHeader = '
                        <li class="title">
                            <span class="data-type">' . $program_title_full_string . '</span>
                            <span class="data" id="">' . $program_row['title'] . '</span>
                        </li>
                        <li class="course-id">
                            <span class="data-type">' . $program_title_short_string . '</span>
                            <span class="data" id="">' . $program_row['short_title'] . '</span>
                        </li>
                        <li class="duration">
                            <span class="data-type">' . $duration_string . '</span>
                            <span class="data" id="">' . $program_row['duration'] . '</span>
                        </li>
                        ';

            $entityContainerContent = '
                    <div id="edit_program_inputfields" class="bd" style="display:none">

                        <div class="row">
                            <div class="column label">
                                <label for="program_title">' . $program_title_full_string . '</label>
                            </div>
                            <div class="column data">
                                <input type="text" id="program_title" name="program_title" value="" disabled="disabled" size="50" />
                            </div>
                            <div class="column actions">
                            </div>
                        </div>

                        <div class="row">
                            <div class="column label">
                                <label for="short_title">' . $program_title_short_string . '</label>
                            </div>
                            <div class="column data">
                                <input type="text" id="short_title" name="short_title" maxlength="10" value="' . $program_row['short_title'] . '" ';

                            if ($disabled) {
                                $entityContainerContent .= 'disabled="disabled" ';
                            }
                            $entityContainerContent .= ' />
                            </div>
                            <div class="column actions"></div>
                        </div>

                        <div class="row">
                            <div class="column label">
                                <label for="">' . $duration_string . '</label>
                            </div>
                            <div class="column data">
                                <select ';
                                if ($disabled) {
                                    $entityContainerContent .= 'disabled="uh-huh" ';
                                }

                                $entityContainerContent .= 'name="duration" id="duration_selector">
                                            <option value="1"';

                                if ($program_row['duration'] == '1') {
                                    $entityContainerContent .= ' selected';
                                }

                                $entityContainerContent .= '>1</option>
                                            <option value="2"';

                                if ($program_row['duration'] == '2') {
                                    $entityContainerContent .= ' selected';
                                }

                                $entityContainerContent .= '>2</option>
                                            <option value="3"';

                                if (($program_row['duration'] == '3')) {
                                    $entityContainerContent .= ' selected';
                                }

                                $entityContainerContent .= '>3</option>
                                            <option value="4"';

                                if ($program_row['duration'] == '4' || ($program_row['duration'] == '')) {
                                    $entityContainerContent .= ' selected';
                                }

                                $entityContainerContent .= '>4</option>
                                            <option value="5"';

                                if ($program_row['duration'] == '5') {
                                    $entityContainerContent .= ' selected';
                                }

                                $entityContainerContent .= '>5</option>
                                            <option value="6"';

                                if ($program_row['duration'] == '6') {
                                    $entityContainerContent .= ' selected';
                                }

                                $entityContainerContent .= '>6</option>
                                            <option value="7"';

                                if ($program_row['duration'] == '7') {
                                    $entityContainerContent .= ' selected';
                                }

                                $entityContainerContent .= '>7</option>
                                                        <option value="8"';

                                if ($program_row['duration'] == '8') {
                                    $entityContainerContent .= ' selected';
                                }

                                $entityContainerContent .= '>8</option>
                                                        <option value="9"';

                                if ($program_row['duration'] == '9') {
                                    $entityContainerContent .= ' selected';
                                }

                                $entityContainerContent .= '>9</option>
                                                        <option value="10"';

                                if ($program_row['duration'] == '10') {
                                    $entityContainerContent .= ' selected';
                                }

                                $entityContainerContent .= '>10</option>
                                </select>
                            </div>
                            <div class="column actions"></div>
                        </div>
                </div>';

            $addNewSomethingId = '';
            $addNewSomethingAction = '';
            $addNewSomethingDisplayText = '';

            $suffixingContent
                = '<div class="collapse_children_toggle_link">
                        <button class="small secondary radius button" onclick="ilios.pm.collapseOrExpandProgramYears(false); return false;"
                                id="expand_program_years_link" style="display: none;">' . $collapse_program_years_string . '</button>
                    </div>

                    <div style="clear: both;"></div>

                    <div id="program_year_container"></div>

                    <div class="add_primary_child_link">
                        <button class="small secondary radius button" onclick="ilios.pm.addNewProgramYear();"
                                    id="add_new_program_year_link" disabled="disabled">' . $add_program_year_string . '</button>
                    </div>';

            $saveDraftAction = 'ilios.pm.transaction.performProgramSave(false);';
            $publishAction = 'ilios.pm.transaction.performProgramSave(true);';
            $revertAction = 'ilios.pm.revertChanges();';

            createContentContainerMarkup($formPrefix, $addNewEntityLink, $searchNewEntityLink, $entityContainerHeader,
                $entityContainerContent, $addNewSomethingId, $addNewSomethingAction, $addNewSomethingDisplayText,
                $suffixingContent, $saveDraftAction, $publishAction, $revertAction, true, true, false, false, '',
                $this->languagemap->getI18NString('general.phrases.save_draft', $lang), '',
                $this->languagemap->getI18NString('general.phrases.publish_now', $lang),
                $this->languagemap->getI18NString('general.phrases.reset_form', $lang));
?>

    <script type="text/javascript">

        // @private
        ilios.pm.disableAddProgramYearLink = function (un, deux, trois) {
            var element = document.getElementById('add_new_program_year_link');

            ilios.dom.setEnableForAElement(element, false);
        };

        YAHOO.util.Event.onDOMReady(ilios.pm.disableAddProgramYearLink, {});

    </script>

