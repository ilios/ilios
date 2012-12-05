<?php
/**
 * This file is meant to be included from instructor_group_manager.php and is responsible for
 * 	generating the HTML and JavaScript.
 *
 * DEPENDENCIES:
 * 		YUI toolkit
 * 		scripts/ilios_dom.js
 * 		scripts/ilios_utilities.js
 */

include(getServerFilePath('views') . 'common/add_new_members_dialog.php');

?>

		<script type="text/javascript">

			YAHOO.util.Event.onDOMReady(ilios.common.assembleAddNewMembersDialog,
										{ trigger: 'add_new_members_picker_show_dialog',
										  display_handler: ilios.igm.resetAddNewMembersDialog,
										  upload_handler: ilios.igm.handleAddMemberUploadClick,
										  manual_add_handler: ilios.igm.handleManualUserAdd });

		</script>
