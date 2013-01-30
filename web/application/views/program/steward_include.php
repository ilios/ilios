<?php
/**
 * Program-steward picker dialog.
 *
 * DEPENDENCIES:
 *  scripts/ilios_dom.js
 *  program/steward_dialog_support.js"
 */
?>
<div class="tabdialog" id="steward_pick_dialog"></div>

<script type="text/javascript">
    YAHOO.util.Event.onDOMReady(ilios.dom.generateTreeSelectionDialogMarkupAndWireContent, {
        trigger: 'steward_picker_show_dialog',
        remote_data: ilios.pm.stewardDataSource,
        display_handler: ilios.pm.resetStewardTree,
        submit_override: ilios.pm.stewardSubmitMethod,
        filter_results_handler: ilios.pm.stewardTreeFilterResults,
        format_results_handler: ilios.pm.stewardTreeHandleResults,
        selected_div_dom_generator: ilios.pm.stewardTreeSelectedDOMContentGenerator,
        unselected_div_dom_generator: ilios.pm.stewardTreeDOMContentGenerator,
        tab_title: ilios_i18nVendor.getI18NString('program_management.steward_dialog.tab_title'),
        id_uniquer: 'scde_',
        panel_title_text: ilios_i18nVendor.getI18NString('program_management.steward_dialog.panel_title'),
        dom_root: 'steward_pick_dialog',
        max_displayed_results: 750,
        load_finish_listener: ilios.pm.stewardTreeFinishedPopulation
    });
</script>
