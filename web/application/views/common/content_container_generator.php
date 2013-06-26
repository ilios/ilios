<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @file content_container_generator.php
 *
 * Markup generator function for "primary content" container elements (i.e. 'program' of the program & program years).
 *
 * The controller which initiates this view should have
 * <code>$this->populateI18NStringsForContentContainerGenerator($data, $lang);</code>
 * invoked as part of its index() lest there be no label and button strings displayed in this UI.
 *
 * @param string $formPrefix Markup prefixing the container element.
 * @param string $addNewEntityLink Markup containing the "add new <entity>" link or button.
 * @param string $searchNewEntityLink Markup containing the "search <entities>" link or button.
 * @param string $entityContainerHeader Markup that goes into the container header.
 * @param string $entityContainerContent Markup that goes into the container body.
 * @param string $addNewSomethingId Id-attribute of the "add new <related entity>" button.
 *      If a falsy value is provided then the button will not be rendered.
 * @param string $addNewSomethingAction JavaScript code. Goes into the inline "onclick" event handler of the
 *      "add new <related entity>" button.
 * @param string $addNewSomethingDisplayText The label of the "add new <related entity>" button.
 * @param string $suffixingContent Markup that goes below the container body.
 * @param string $saveDraftAction JavaScript code. Goes into the inline "onclick" event handler of the
 *      "save as draft" button.
 * @param string $publishAction JavaScript code. Goes into the inline "onclick" event handler of the
 *      "publish" button.
 * @param string $revertAction JavaScript code. Goes into the inline "onclick" event handler of the
 *      "reset form" button.
 * @param boolean $shouldShowSavePublishRevertButtons If TRUE then the "publish", "save draft" and "reset form"
 *      buttons will be rendered, otherwise not.
 * @param boolean $showPublishDraftStatus If TRUE then the generated container markup will contain a section
 *      for displaying publish-status information.
 * @param boolean $showPublishAllButton If TRUE then "publish all" button will be rendered.
 * @param boolean $showSaveAllLink If TRUE then the "save all" button will be rendered.
 * @param string $saveAllString The label-text of the "save all" button.
 * @param string $saveDraftString The label-text of the "save as draft" button.
 * @param string $publishAllString The label-text of the "publish all" button.
 * @param string $publishNowString The label-text of the "publish now" button.
 * @param string $resetFormString The label-text of the "reset form" button.
 * @param boolean $showArchivingLinkDiv If TRUE then the container element for the "archive" button will be rendered.
 * @param boolean $showRolloverLinkDiv If TRUE then the container element for the "rollover" button will be rendered.
 *
 * @see Ilios_Base_Controller::populateI18NStringsForContentContainerGenerator()
 *
 * @todo Junk this god-awful mess. [ST 2013/06/18]
 */
function createContentContainerMarkup ($formPrefix, $addNewEntityLink, $searchNewEntityLink, $entityContainerHeader,
                                       $entityContainerContent, $addNewSomethingId, $addNewSomethingAction,
                                       $addNewSomethingDisplayText, $suffixingContent, $saveDraftAction,
                                       $publishAction, $revertAction, $shouldShowSavePublishRevertButtons,
                                       $showPublishDraftStatus, $showPublishAllButton,$showSaveAllLink,
                                       $saveAllString, $saveDraftString, $publishAllString,
                                       $publishNowString, $resetFormString, $showArchivingLinkDiv = false,
                                       $showRolloverLinkDiv = false)
{
?>
    <div class="content_container">
        <div class="master_button_container clearfix">
            <ul class="buttons left">
                <li>
<?php
    if ($searchNewEntityLink) :
        echo $searchNewEntityLink;
?>
                 </li>
<?php
    endif;
    if ($addNewEntityLink != '') :
?>
                 <li>
<?php
        echo $addNewEntityLink;
    endif;
?>
                </li>
            </ul>
            <ul class="buttons right">
<?php
    if ($showRolloverLinkDiv) :
?>
                 <li id="rollover_link_div" class="rollover_link_div"></li>
<?php
    endif;
    if ($showArchivingLinkDiv) :
?>
                <li id="archiving_link_div" class="archiving_link_div"></li>
<?php
    endif;
?>
                <li>
<?php
    if ($showSaveAllLink) :
?>
                    <button id="save_all_dirty_to_draft" class="medium radius button" disabled='disabled'><?php echo $saveAllString ?></button>
<?php
    endif;
?>
                </li>
<?php
    if ($showPublishAllButton) :
?>
                <li>
                    <button id="publish_all" class="medium radius button" disabled='disabled'><i class="icon-checkmark"></i><?php echo $publishAllString ?></button>
                </li>
<?php
    endif;
?>
            </ul>
        </div>
        <?php echo $formPrefix; ?>
            <div class="entity_container level-1">
                <div class="hd clearfix">
                    <div class="toggle">
                        <a href="#" id="show_more_or_less_link"
                            onclick="ilios.utilities.toggle('course_more_or_less_div', this); return false;" >
                            <i class="icon-plus" aria-hidden = "true"> </i>
                        </a>
                    </div>
                    <ul>
<?php
    echo $entityContainerHeader;

    if ($showPublishDraftStatus) :
?>
                        <li class="publish-status">
                            <span class="data-type">Publish Status</span>
                            <span class="data" id="parent_publish_status_text"></span>
                        </li>

<?php
    endif;
?>

                    </ul>
                </div>
                <div id="course_more_or_less_div" class="bd" style="display:none">


<?php
    echo $entityContainerContent;
    if ($shouldShowSavePublishRevertButtons) :
?>
                    <div class="buttons bottom">
                        <button id="draft_button" class="medium radius button" disabled="disabled" onClick="<?php echo $saveDraftAction ?>"><?php echo $saveDraftString ?></button>
                        <button id="publish_button" class="medium radius button" disabled="disabled" onClick="<?php echo $publishAction ?>"><?php echo $publishNowString ?></button>
                        <button id="reset_button" class="reset_button small secondary radius button" disabled="disabled" onClick="<?php echo $revertAction ?>"><?php echo $resetFormString ?></button>
                    </div>
<?php
    endif;
?>
                </div><!--close div.bd-->
            </div><!-- entity_container close -->
        </form>
<?php
    if ($addNewSomethingId != '') :
?>
        <button class="small secondary radius button" disabled="disabled" id="<?php echo $addNewSomethingId; ?>" onClick="<?php echo $addNewSomethingAction; ?>"><?php echo $addNewSomethingDisplayText; ?></button>

<?php
    endif;
    echo $suffixingContent;
?>
    </div><!-- content_container close -->
<?php
} // end function
