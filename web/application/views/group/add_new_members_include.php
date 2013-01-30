<?php

/**
 * This file is meant to be included from group_manager.php and is responsible for generating
 *     the HTML and JavaScript.
 *
 * DEPENDENCIES:
 *         YUI toolkit
 *         scripts/ilios_dom.js
 *         scripts/ilios_utilities.js
 */

include getServerFilePath('views') . 'common/add_new_members_dialog.php';
?>
<script type="text/javascript">
    ilios.gm.displayDenier = function () {
      return (ilios.gm.currentModel == null);
    };
    YAHOO.util.Event.onDOMReady(ilios.common.assembleAddNewMembersDialog, {
        trigger : 'add_new_members_picker_show_dialog',
        display_handler : ilios.gm.resetAddNewMembersDialog,
        upload_handler : ilios.gm.transaction.handleAddMemberUploadClick,
        manual_add_handler : ilios.gm.transaction.handleManualUserAdd,
        display_denier : ilios.gm.displayDenier
    });
</script>
