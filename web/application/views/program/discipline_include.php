    <div class="tabdialog" id="discipline_picker_dialog"></div>

    <script type="text/javascript">
    ;
    var disc_currentlySelectedModels = [];
    var disc_dataSource = new YAHOO.util.XHRDataSource(controllerURL + 'getDisciplineList');
    var disc_hiddenFormElement = "discipline_hidden_input";
    var disc_listingTextField = "discipline_picker_selected_text_list";
    var disc_selectedItemContainer = "discipline_picked";


    disc_dataSource.responseType = YAHOO.util.XHRDataSource.TYPE_XML;
    disc_dataSource.responseSchema = { resultNode: "Result", fields: ["title", "discipline_id"] };



/*
 * dialog has an attribute set on it through its display handler which represents the
 * container number for which it is about to display.
 */
var getProgramYearModelAssociatedToDialog = function (dialog) {
    var containerNumber = dialog.containerNumber;
    return ilios.pm.currentProgramModel.getProgramYearForContainerNumber(containerNumber);
};

    /*
     * This will get messaged just prior to the dialog being displayed to the user.
     *
     * @param dialog a handle to the actual dialog instance which is about to be
     *            displayed
     * @see ilios.dom.buildDialogPanel
     */
    var disc_handleDialogDisplay = function (dialog) {

    var i, n;
    var parentModel = getProgramYearModelAssociatedToDialog(dialog);
    var picker = document.getElementById( disc_selectedItemContainer);
    var localModels = parentModel.getDisciplineArray();
    var selectedModels = [];
    disc_currentlySelectedModels;
    picker.innerHTML = "";

    selectedModels = null;

    if (localModels != null) {
        var model = null;
        var liElement = null;
        selectedModels = localModels.slice(0);

        // repopulate picker list to reflect the display
        for (i = 0, n = selectedModels.length; i < n; i++) {
            model = selectedModels[i];
            liElement = document.createElement("li");
            liElement.iliosModel = model;
            liElement.innerHTML = model.title;
            picker.appendChild(liElement);
        }
    }
    disc_currentlySelectedModels = selectedModels;
    return true;
    }; // end function


    /*
     * This will get messaged when the user clicks the submit button on the dialog (this
     *        button is currently display-text'd as "Done")
     *
     * @see ilios.dom.buildDialogPanel
     */
    var disc_submitMethod = function () {

    var textFieldContent = "";
    var modelTitles = [];
    var i, n;
    var containerNumber = this.containerNumber; // "this" should be the Dialog instance
    var inputTextId = containerNumber + "_" + disc_listingTextField;
    var parentModel = getProgramYearModelAssociatedToDialog(this);
    var element = null;
    var selectedModels = disc_currentlySelectedModels;

    parentModel.setDisciplineArray(selectedModels);

    for (i = 0, n = selectedModels.length; i < n; i++) {
        modelTitles.push(selectedModels[i].getTitle());
    }
    modelTitles.sort();
    textFieldContent = modelTitles.join(";");

    element = document.getElementById(inputTextId + "_full");
    if (element != null) {
        element.innerHTML = textFieldContent;
        element = document.getElementById(inputTextId);
        element.innerHTML = ilios.lang.ellipsisedOfLength(textFieldContent, 75);
    } else {
        element = document.getElementById(inputTextId);
        element.innerHTML = textFieldContent;
    }
    }; // end function


    /*
     * We'll receive notification via this when the user click-deletes an item from the
     *    selected list.
     *
     * @param event actual click event
     * @see ilios.dom.generateAutoCompleteDialogMarkup
     */
    var disc_handleDeselect = function (event) {

    var i, n;
    var model;
    var selectedModels = disc_currentlySelectedModels;
    var target = ilios.utilities.getEventTarget(event);
    var listElement = document.getElementById("discipline_picked");
    if ("li" === target.tagName.toLowerCase()) {
        model = target.iliosModel;
        for (i = 0, n = selectedModels.length; i < n; i++) {
            if (model.getDBId() === selectedModels[i].getDBId()) {
                selectedModels.splice(i, 1);
                break;
            }
        }
    }
    }; // end function


    /*
     * We'll receive notification via this when the user click-selects an item from the
     *    candidate list.
     *
     * @param rowSelection a map of the db row data representation for the user's selection
     * @see ilios.ui.setupDialogAutoComplete
     */
    var disc_handleSelect = function (rowSelection) {

    var model = new DisciplineModel();
    model.setDBId(rowSelection.discipline_id);
    model.setTitle(rowSelection.title);

    var listElement = document.getElementById("discipline_picked");
    var liElement = document.createElement("li");
    var textNode = document.createTextNode(model.getTitle());
    var selectedModels = disc_currentlySelectedModels;

    selectedModels.push(model);

    liElement.appendChild(textNode);
    liElement.iliosModel = model;

    listElement.appendChild(liElement);

    return liElement;
    }; // end function

    /**
     * @method initDialog()
     * Initializes the dialog and rigs event handling up to it.
     */
    var disc_initDialog = function (who, knows, args) {
        var autolistContainer = "discipline_autolist";
        var textInputFieldForAutoComplete = "discipline_name_input";

        ilios.dom.generateAutoCompleteDialogMarkup({
            deselect_handler: disc_handleDeselect,
            selected_label: "general.terms.topics",
            instructions: "general.text.discipline_search_instructions",
            container: args['container'],
            hidden: disc_hiddenFormElement,
            tabs: {autocomplete: "discipline_autocomplete_tab"},
            acinput: textInputFieldForAutoComplete,
            aclist: autolistContainer,
            picked: disc_selectedItemContainer
        });

        ilios.dom.buildDialogPanel({}, {}, {
            trigger: args['trigger'],
            target: disc_selectedItemContainer,
            hidden: disc_hiddenFormElement,
            input: disc_listingTextField,
            submit_override: disc_submitMethod,
            display_handler: disc_handleDialogDisplay,
            container: args['container']
        });

        ilios.ui.setupDialogAutoComplete({
            target: disc_selectedItemContainer,
            input: textInputFieldForAutoComplete,
            container: autolistContainer,
            remote_data: disc_dataSource,
            select_handler: disc_handleSelect,
                        max_displayed_results: 150        });
    }; // end function


    YAHOO.util.Event.onDOMReady(disc_initDialog, {
        // unique event that triggers opening of the dialog fired
        // from search link near course mesh form element
        trigger: "discipline_picker_show_dialog",
        // unique id of the div where the dialog xhtml can be
        // generated (once)
        container: "discipline_picker_dialog"
    });
    </script>
