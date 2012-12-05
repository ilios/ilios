<?php
/**
 * This is for just the parent level content (i.e 'program' of the program & program years).
 *
 * The controller which initiates this view should have
 *			$this->populateI18NStringsForContentContainerGenerator($data, $lang);
 *	invoked as part of its index() lest there be no label and button strings displayed in this UI
 */
function createContentContainerMarkup ($title, $formPrefix, $addNewEntityLink, $searchNewEntityLink,
									   $entityContainerHeader, $entityContainerContent, $addNewSomethingId,
									   $addNewSomethingAction, $addNewSomethingDisplayText,
									   $suffixingContent, $saveDraftAction, $publishAction,
									   $revertAction, $shouldShowSavePublishRevertButtons,
									   $showPublishDraftStatus, $showPublishAllButton,$showSaveAllLink,
									   $saveAllString, $saveAllDraftString, $saveDraftString, $publishAllString,
									   $publishNowString, $resetFormString,
                                       $showArchivingLinkDiv = false,
                                       $showRolloverLinkDiv = false) {


?>

	<div class="content_container">
		<div class="master_button_container clearfix">
			<ul class="buttons left">
				<li>

<?php
	if ($searchNewEntityLink) {
		echo $searchNewEntityLink;
?>
 				</li>
<?php }

	if ($addNewEntityLink != '') {
?>
 				<li>
<?php	echo $addNewEntityLink;
 	}
?>
				</li>
			</ul>

			<ul class="buttons right">

<?php 	if ($showRolloverLinkDiv) { ?>
     			<li id="rollover_link_div" class="rollover_link_div"></li>
<?php }
    	if ($showArchivingLinkDiv) { ?>
				<li id="archiving_link_div" class="archiving_link_div"></li>
<?php } ?>
				<li>
<?php 	if ($showSaveAllLink) { ?>

					<button id="save_all_dirty_to_draft" class="medium radius button" disabled='disabled'><?php echo $saveAllString ?></button>
<?php } else {?>
					<button id="save_all_dirty_to_draft" class="medium radius button" disabled='disabled'><?php echo $saveAllDraftString ?></button>
<?php } ?>
				</li>

<?php if ($showPublishAllButton) { ?>
				<li>
					<button id="publish_all" class="medium radius button" disabled='disabled'><i class="icon-checkmark"></i><?php echo $publishAllString ?></button>
				</li>
<?php } ?>
			</ul>
		</div>

		<?php echo $formPrefix ?>
            <div class="entity_container level-1">
                <div class="hd clearfix">
                    <div class="toggle">
                        <a href="#" id="show_more_or_less_link"
                            onclick="ilios.utilities.toggle('course_more_or_less_div', this); return false;" >
                            <i class="icon-plus" aria-hidden = "true"> </i>
                        </a>
                    </div>
					<ul>
<?php					echo $entityContainerHeader;

	if ($showPublishDraftStatus) { ?>
						<li class="publish-status">
							<span class="data-type">Publish Status</span>
							<span class="data" id="parent_publish_status_text"></span>
						</li>

<?php  	} ?>

					</ul>
				</div>
                <div id="course_more_or_less_div" class="bd" style="display:none">


<?php
		echo $entityContainerContent;
?>
<?php if ($shouldShowSavePublishRevertButtons) { ?>
                    <div class="buttons bottom">
                        <button id="draft_button" class="medium radius button" disabled="disabled" onClick="<?php echo $saveDraftAction ?>"><?php echo $saveDraftString ?></button>
                        <button id="publish_button" class="medium radius button" disabled="disabled" onClick="<?php echo $publishAction ?>"><?php echo $publishNowString ?></button>
                        <button id="reset_button" class="reset_button small secondary radius button" disabled="disabled" onClick="<?php echo $revertAction ?>"><?php echo $resetFormString ?></button>
                    </div>
<?php
		}
?>
                </div><!--close div.bd-->
            </div><!-- entity_container close -->
        </form>
<?php if ($addNewSomethingId != '') { ?>
		<button class="small secondary radius button" disabled="disabled" id="<?php echo $addNewSomethingId ?>" onClick="<?php echo $addNewSomethingAction ?>"><?php echo $addNewSomethingDisplayText ?></button>

<?php
	}

	echo $suffixingContent;
?>

	</div><!-- content_container close -->

<?php
}
?>
