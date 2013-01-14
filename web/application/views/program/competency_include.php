<div class="tabdialog" id="competency_pick_dialog"></div>

<script type="text/javascript">
    YAHOO.util.Event.onDOMReady(ilios.dom.generateTreeSelectionDialogMarkupAndWireContent, {
        trigger: 'competency_picker_show_dialog',
        remote_data: new YAHOO.util.FunctionDataSource(ilios.competencies.getActiveSchoolCompetenciesList),
        display_handler: ilios.pm.resetCompetencyTree,
        submit_override: ilios.pm.competencySubmitMethod,
        filter_results_handler: ilios.pm.competencyTreeFilterResults,
        format_results_handler: ilios.pm.competencyTreeHandleResults,
        selected_div_dom_generator: ilios.pm.competencyTreeSelectedDOMContentGenerator,
        unselected_div_dom_generator: ilios.pm.competencyTreeDOMContentGenerator,
        tab_title: ilios_i18nVendor.getI18NString('program_management.competency_dialog.tab_title'),
        id_uniquer: 'csdpe_',
        panel_title_text: ilios_i18nVendor.getI18NString('program_management.competency_dialog.panel_title'),
        dom_root: 'competency_pick_dialog',
        max_displayed_results: 250,
        load_finish_listener: ilios.pm.competencyTreeFinishedPopulation
    });
</script>
