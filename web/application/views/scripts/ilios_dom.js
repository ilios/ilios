/**
 * Dom-manipulation functionality.
 *
 * Defined namespaces:
 *
 * ilios.dom
 *
 * Dependencies:
 *
 * YUI utilities
 * YUI container family
 * scripts/ilios_base.js
 * scripts/ilios_alert.js
 */

/**
 * Namespace object
 * @class dom
 * @static
 */
ilios.namespace('dom');


/**
 * The session add/edit dialog; a modal dialog panel. This constructs the dialog and does
 *      the appropriate behind the scenes wiring; it hands back nothing. That the dialog
 *      instance itself has scope only of this method belies some of the wire-and-forget
 *      methodology supporting the YUI toolkit.
 *
 * @param args
 *          input:              the id string of the input text element into which the selected
 *                                  contents from this dialog get dumped
 *          hidden:             the id string of a hidden form element into which the
 *                                  innerHTML of the DOM element named via the target argument
 *                                  is stuffed just prior to form submission
 *          trigger:            the id string of the element which should invoke the display of
 *                                  this panel; clicking on this id string must be wired
 *                                  elsewhere to fire thusly:
 *                                      IEvent.fire({action: 'default_dialog_open', ...});
 *                                  where 'default_dialog_open' is the salient action.
 *          target:             the id string for the container which lists the user selected
 *                                  items of the dialog
 *          container:          the id string of the div which wraps the DOM for this dialog
 *          submit_override:    if this is non-null the function is subscribed to the manual
 *                                  submit event of the dialog and the dialog is configured
 *                                  to fire that event when the user clicks the 'Done' button
 *                                  on the dialog.
 *          display_handler:    if this is non-null the function is messaged just prior to the
 *                                  dialog being displayed to the user; the function will
 *                                  receive one argument which is a reference to the dialog
 *                                  instance itself. the function should return false if it
 *                                  wants to veto the display of the dialog.
 *          hide_done:          if this is non-null, then the resulting dialog will have no
 *                                  'Done' button - only a cancel button (which will then take
 *                                  on the role of the default button for the dialog)
 *          panel_width:        if this is non-null, it is expected to a valid CSS measurement,
 *                                  else '600px' will be used.
 */
ilios.dom.buildDialogPanel = function (use, less, args) {

    // Define various event handlers for Dialog
    var handleSubmit = function () { // Done button
        IEvent.fire({object: 'modal_dialog_panel', action: 'submit', event: 'closing'});

        // move data from picked list of items in div to an input form element
        // todo loki sez: this type of stuffing [was here before me and] is a bit abgefickt
        document.getElementById(args['hidden']).value
        = document.getElementById(args['target']).innerHTML;

        this.submit();
    };

    var handleCancel = function () {
        IEvent.fire({object: 'modal_dialog_panel', action: 'cancel', event: 'closing'});

        this.cancel();
    };

    var handleSuccess = function (o) {
        var response = o.responseText;

        response = response.split("<!")[0];

        IEvent.fire({ object: 'modal_dialog_panel', action: 'succeed', event: 'ajaxresponse',
                      target: args['target'], data: response });
    };

    var handleFailure = function (o) {
        IEvent.fire({object: 'modal_dialog_panel', action: 'fail', event: 'ajaxresponse'});

        ilios.alert.alert('HTTP Request connection failure for ' + args['hidden']
        + ' form element.\nStatus: ' + o.status);
    };

    var cancelStr = ilios_i18nVendor.getI18NString('general.terms.cancel');
    var doneStr = ilios_i18nVendor.getI18NString('general.terms.done');
    var buttonArray = null;

    if (args['hide_done'] != null) {
        buttonArray = [{text: cancelStr, handler: handleCancel, isDefault: true}];
    }
    else {
        buttonArray = [{text: doneStr, handler: handleSubmit, isDefault: true},
                       {text: cancelStr, handler: handleCancel}];
    }

    // Instantiate the Dialog
    var panelWidth = (args['panel_width'] != null) ? args['panel_width'] : "600px";
    var dialog = new YAHOO.widget.Dialog(args['container'],
            {width: panelWidth,
        modal: true, visible: false,
        constraintoviewport: false,
        buttons: buttonArray});

    dialog.showDialogPane = function () {
        IEvent.fire({object: 'modal_dialog_panel', action: 'show', event: 'opening',
                     target: args['target']});

        dialog.center();
        dialog.show();
    };

    // Wire up the success and failure handlers
    dialog.callback = {success: handleSuccess, failure: handleFailure};
    dialog.selectListId = args['target'];

    // Render the Dialog
    dialog.render();

    if (args['submit_override'] != null) {
        dialog.cfg.setProperty('postmethod', 'manual');
        dialog.manualSubmitEvent.subscribe(args['submit_override'], null);
    }

    // register a listener on some page element to trigger the display of this popup dialog
    var displayOnTriggerHandler = function (type, handlerArgs) {
        if ((handlerArgs[0].event == args['trigger'])
                            && (handlerArgs[0].action == 'default_dialog_open')) {
            var handlerHasVetoedDisplay = false;

            dialog.containerNumber = handlerArgs[0].container_number;

            if (args['display_handler'] != null) {
                var handler = args['display_handler'];

                handlerHasVetoedDisplay = (! handler(dialog));
            }

            if (! handlerHasVetoedDisplay) {
                dialog.showDialogPane();
            }
        }
    };
    IEvent.subscribe(displayOnTriggerHandler);
};

/**
 * Generates the markup for the modal dialog which contains an autocomplete filterable list and
 *  potentially a MeSH tree tab.
 *
 * TODO this whole sort of "I write HTML in string content in javascript land and then cram
 *              into an element via its innerHTML" feels totally amateurish.. rewrite this
 *              [inherited] code using real DOM construction like seen in the methods i've
 *              written elsewhere
 *
 * @param args
 *          action:             action invoked upon dialog form submit, if null then
 *                                  (controllerURL + 'insertTreeData') is used (todo - for some
 *                                  totally archaic reason)
 *          onsubmit:           if this is non-null the code is defined for onsubmit attribute
 *                                  of the form tag
 *          hidden:             hidden input for passing selected items on dialog form submit
 *          picked:             div id for list of items selected
 *          deselect_handler:   if non-null, this function will be messaged with the click
 *                                  event as its single argument
 *          acinput:            input textbox for entering search item
 *          aclist:             div id for holding autocomplete selection list
 *          container:          div id for holding the dialog form html
 *          tabs/autocomplete:  div id for autocomplete tab
 */
ilios.dom.generateAutoCompleteDialogMarkup = function (args) {
    var Dom = YAHOO.util.Dom;
    var contents = '';
    var i18nStr = ilios_i18nVendor.getI18NString('general.phrases.select_items');
    var uniqueId = 'tabbed_view_div_' + args['picked'];

    contents += '<div class="hd">' + i18nStr + '</div>\n';
    contents += '<div class="bd">\n';
    contents += '<div class="dialog_wrap">';
    contents += '<p>' + ilios_i18nVendor.getI18NString(args['instructions']) + '</p>';
    contents += '<div class="dialog_left">';

    contents += '<label class="picked_label" for="' + args['picked'] + '">';
    contents += ilios_i18nVendor.getI18NString(args['selected_label']);
    contents += '</label>';
    contents += '<ul class="picked" id="' + args['picked'] + '"></ul>\n';

    contents += '</div>\n';
    contents += '<div class="dialog_right">';

    contents += '<form method="POST" action="';
    if (args['action'] != null) {
        contents += args['action'];
    }
    else {
        contents += controllerURL + 'insertTreeData';
    }

    if (args['onsubmit'] != null) {
        contents += '" onsubmit="' + args['onsubmit'];
    }
    contents += '">\n';

    contents += '<input id="' + args['hidden'] + '" name="' + args['hidden']
                    + '" type="hidden" />\n';

    contents += '<div id="' + uniqueId + '" class="yui-navset">\n';
    contents += '<ul class="yui-nav">\n';
    if (args['tabs']['autocomplete'] != '') {
        contents += '<li class="selected"><a href="#' + args['tabs']['autocomplete']
                        + '"><em>Autocomplete</em></a></li>\n';
    }

    contents += '</ul><div class="yui-content">\n';
    if (args['tabs']['autocomplete'] != '') {
        contents += '<div class="autocomplete_tab" id="' + args['tabs']['autocomplete']
                        + '"><input id="' + args['acinput'] + '" name="' + args['acinput']
                        + '" type="text"><div class="autolist" id="' + args['aclist']
                        + '" /></div></div>\n';
    }

    // close yui-content class div and close encompassing yui-navset class div
    contents += '</div></div>\n';
    contents += '<div class="clear"></div></form>\n';

    contents += '</div>'; // close dialog_right div\

    // text below form could go here
    contents += '</div>\n'; // close dialog_wrap div
    contents += '</div>\n'; // close bd div
    Dom.get(args['container']).innerHTML = contents;

    Dom.get(args['picked']).onclick = function (e) {
        ilios.dom.removeLIElement(e);
        // we fire this event to trigger the force refresh handler defined in
        //          ilios.ui.setupDialogAutoComplete method
        IEvent.fire({state: 'acquery', target: args['picked']});

        if (args['deselect_handler'] != null) {
            var handler = args['deselect_handler'];

            handler(e);
        }
    };
};

/**
 * Generates the markup for a panel which contains a generic container that the caller is
 *  responsible for populating via a listener hanging off the autocomplete mechanism. The
 *  autocomplete container is hidden offscreen and is 1px by 1px so hopefully, no matter
 *  what sort of mutator the YUI lib throws its way, it will never be seen by the user. NOTE: As
 *  the consumer of this method for the control of the results displaying widget, the selection
 *  handler mechanism must be handled outside of this method.
 *
 * The element id for the selection list will be available via dialog.selectListId
 *
 * TODO this whole sort of "I write HTML in string content in javascript land and then cram
 *              into an element via its innerHTML" feels totally amateurish.. rewrite this
 *              [quasi-inherited] code using real DOM construction like seen in the methods i've
 *              written elsewhere
 *
 * @param args
 *          id_uniquer:                 a prefix for certain element ids to unique them across
 *                                          the DOM; this value will also be stored in
 *                                          dialog.idUniquer
 *          filter_results_handler:     a function which is called during the autocomplete's
 *                                          results filtering cycle; this can be null. If not
 *                                          null, the function will receive 6 parameters:
 *                                              queryString, fullResponse, parsedResponse,
 *                                              callback, autoCompleter, idUniquer
 *                                          The first four being the 4 parameters that the YUI
 *                                          framework already passes to its autocompleter for
 *                                          filterResults(...); autoCompleter is the owning
 *                                          autoCompleter instance; idUniquer is the value
 *                                          passed to this method as args key 'id_uniquer'.
 *                                          This method must return the modified-as-appropriate
 *                                          parsedResponse object.
 *          format_results_handler:     a function which is called during the autocomplete's
 *                                          results fetch; this is THE tie-in point for your
 *                                          code to grab the results and populate the view
 *                                          correctly. This -could- be null, but in that case
 *                                          i'm not sure what use this component hierarchy is.
 *                                          This function will receive 5 parameters:
 *                                              resultDataObject, queryString, resultMatch,
 *                                              autoCompleter, idUniquer
 *                                          The first four being the 3 parameters that the YUI
 *                                          framework already passes to its autocompleter for
 *                                          formatResults(...); autoCompleter is the owning
 *                                          autoCompleter instance; idUniquer is the value
 *                                          passed to this method as args key 'id_uniquer'.
 *                                          Whatever this method returns is ignored.
 *                                          NOTE: resultMatch is actually the value for the
 *                                          first key listed in the schema fields array of the
 *                                          data source.
 *          remote_data:                the remote data source for the autocompleter, if this
 *                                          is null then there will be no autocompleter as part
 *                                          of the generated dialog
 *          deselect_handler:           if non-null, this function will be messaged with the
 *                                          click event as its single argument; if it returns
 *                                          false, the element will not be removed from the UI
 *                                          view
 *          submit_override:            if this is non-null the function is subscribed to the
 *                                          manual submit event of the dialog and the dialog is
 *                                          configured to fire that event when the user clicks
 *                                          the 'Done' button on the dialog.
 *          load_finish_listener:       if non-null, this will get messaged when the
 *                                          autocompleter finishes its data load
 *          container:                  the container listing the present possible choices (as
 *                                          possibly filtered via autocompleter input)
 *          max_displayed_results:      if non-null, defines the maximum number of results to
 *                                          display in the container; if null, 125 is used.
 *          request_generator:          if non-null, this is expected to be a function which
 *                                          takes one argument (the query string), and return a
 *                                          request including at least the key-value pair of
 *                                          'query={query string}'.
 *          display_handler:            a function which is called just prior to the dialog
 *                                          being displayed
 *          widget_dom_generator:       a function which inserts DOM elements into an element
 *                                          whose id is provided; the elements represent the UI
 *                                          single-selection widget
 *          hide_autocomplete_input:    if non-null, then the autocomplete input will be hidden
 *          tab_title:                  the title for the tab containing the selection widget (optional, defaults to "Available items")
 *          selected_items_title:       the title for the selected items list (optional, defaults to "Selected items")
 *          title:                      the widget title (optional, defaults to "Please select items")
 *          panel_title_text:           the header title text for the panel (optional, not displayed when not given)
 *          dom_root:                   the id of the DOM element into which this markup should
 *                                          be inserted
 *          panel_width:                if this is non-null, it is expected to a valid CSS
 *                                          measurement, else '600px' will be used.
 *          trigger:                    the id string of the element which should invoke the
 *                                          display of this panel; clicking on this id string
 *                                          must be wired elsewhere to fire thusly:
                                                    IEvent.fire({action: 'gen_dialog_open', ...});
 *                                          where 'gen_dialog_open' is the salient action, and
 *                                          the event may also contain a key for
 *                                          'container_number' the value of which will get
 *                                          assigned as property 'containerNumber' of the dialog
 *
 * @return the DOM id for the panel div which holds the display of selected items (TODO - since
 *              this is usually called via an onReady event, a return is kind of useless)
 */
ilios.dom.generateGenericDialogMarkupAndWireContent = function (use, less, args) {
    var contents = '';
    var widgetParentDivId = args['id_uniquer'] + 'widget_div';
    var domGenerator = args['widget_dom_generator'];
    var handleCancel = function () {
        IEvent.fire({object: 'modal_gen_dialog_panel', action: 'cancel', event: 'closing'});

        this.cancel();
    };
    var handleSubmit = function () {
        IEvent.fire({object: 'modal_gen_dialog_panel', action: 'submit', event: 'closing'});

        this.submit();
    };
    var cancelStr = ilios_i18nVendor.getI18NString('general.terms.cancel');
    var doneStr = ilios_i18nVendor.getI18NString('general.terms.done');
    var buttonArray = [{text: doneStr, handler: handleSubmit, isDefault: true},
                       {text: cancelStr, handler: handleCancel}];
    var panelWidth = (args['panel_width'] != null) ? args['panel_width'] : "600px";
    var dialog = null;
    var displayOnTriggerHandler = null;
    var autoCompleteHiddenContainerId = args['id_uniquer'] + '_hid_auto_comp';
    var autoCompleteTextFieldId = args['id_uniquer'] + '_auto_comp_input';
    var selectedItemListElement = args['id_uniquer'] + '_selected_item_list';
    var autoCompleter = null;
    var aStr = args['title'] || ilios_i18nVendor.getI18NString('general.phrases.select_items');
    var noAutoCompleter = (args['remote_data'] == null);
    var showAutoCompleteInput = (args['hide_autocomplete_input'] == null);
    var panelTitle = args['panel_title_text'] || '';

    contents += '<div class="visuallyhidden" id="' + autoCompleteHiddenContainerId + '"></div>\n';

    contents += '<div class="hd">' + aStr + '</div>\n';
    contents += '<div class="bd">\n';
    contents += '<div class="dialog_wrap">';

    if (panelTitle) {
        contents += '<p>' + panelTitle + '</p>';
    }

    if (args['indeterminate_loading_id'] != null) {
        contents += '<div id="' + args['indeterminate_loading_id']
                        + '" style="position: absolute; top: 15px; right: 12px; display: none;"'
                        + ' class="indeterminate_progress_text">';
        contents += '<div class="indeterminate_progress" style="display: inline-block;"></div> '
        contents += ilios_i18nVendor.getI18NString('general.terms.loading') + '...</div>';
    }

    contents += '<div class="dialog_left">';

    aStr = args['selected_items_title'] || ilios_i18nVendor.getI18NString('general.phrases.selected_items');
    contents += '<label class="picked_label" for="' + selectedItemListElement + '">';
    contents += aStr;
    contents += '</label>';
    contents += '<ul class="picked" id="' + selectedItemListElement + '"></ul>\n';

    contents += '</div>\n';


    contents += '<div class="dialog_right">';

    // YUI -- IF THERE'S NO FORM INSIDE A DIALOG DOM, IT REVEALS A YUI BUG
    contents += '<form method="POST">\n';


    contents += '<div id="' + args['id_uniquer'] + 'nav_tabs" class="yui-navset">\n';

    contents += '<ul class="yui-nav">\n';
    contents += '<li class="selected"><a href="#' + args['id_uniquer'] + 'selector_tab"><em>'
                    + args['tab_title'] + '</em></a></li>\n';

    contents += '</ul>\n';

    contents += '<div class="yui-content">\n';

    contents += '<div class="single_selector_tab clearfix" id="' + args['id_uniquer']
                    + 'selector_tab">\n';
    if (! noAutoCompleter) {
        contents += '<input id="' + autoCompleteTextFieldId + '" name="' + autoCompleteTextFieldId
                        + '" type="text" style="width: 90%;';

        if (showAutoCompleteInput) {
        }
        else {
            contents +='display: none;';

        }

        contents +='"/>';
    }
    else {
    }
    contents += '<div id="' + widgetParentDivId + '" class="default_tree_div" >\n';

    contents += '</div></div>\n';   // closes through single_selector_tab div

    // close yui-content class div and close encompassing yui-navset class div
    contents += '</div></div>';

    // this was two clearing divs - now just one
    contents += '<div class="clear"></div>\n';

    // YUI -- IF THERE'S NO FORM INSIDE A DIALOG DOM, IT REVEALS A YUI BUG
    contents += "</form>\n";

    contents += '</div>'; // close dialog_right div


    // text below form could go here
    contents += '</div>\n'; // close dialog_wrap div

    contents += '</div>\n'; // close bd div

    YAHOO.util.Dom.get(args['dom_root']).innerHTML = contents;

    // this has to be called after the DOM has been altered to include the widget parent div
    domGenerator(widgetParentDivId);

    dialog = new YAHOO.widget.Dialog(args['dom_root'],
                                     { width: panelWidth, modal: true,
                                       visible: false, constraintoviewport: false,
                                       buttons: buttonArray});

    dialog.idUniquer = args['id_uniquer'];
    dialog.selectListId = selectedItemListElement;
    dialog.autoCompleteTextFieldId = autoCompleteTextFieldId;

    dialog.showDialogPane = function () {
        var displayHandler = args['display_handler'];

        if (displayHandler != null) {
            displayHandler(dialog);
        }

        if (showAutoCompleteInput) {
            document.getElementById(autoCompleteTextFieldId).value = '';
        }

        if (! noAutoCompleter) {
            // TODO the validity of this assignment is suspect - but may be ok due to scope
            //          timing
            document.getElementById(selectedItemListElement).autoCompleter = autoCompleter;

            autoCompleter.sendQuery('');
        }

        dialog.center();
        dialog.show();
    };

    // Render the Dialog
    dialog.render();

    if (args['submit_override'] != null) {
        dialog.cfg.setProperty('postmethod', 'manual');
        dialog.manualSubmitEvent.subscribe(args['submit_override'], null);
    }

    // register a listener on some page element to trigger the display of this popup dialog
    displayOnTriggerHandler = function (type, handlerArgs) {
        if (handlerArgs[0].event == args['trigger']) {
            if (handlerArgs[0].action == 'gen_dialog_open') {
                dialog.containerNumber = handlerArgs[0].container_number;

                if (! noAutoCompleter) {
                    autoCompleter.containerNumber = handlerArgs[0].container_number;
                }

                dialog.showDialogPane();
            }
        }
    };
    IEvent.subscribe(displayOnTriggerHandler);

    document.getElementById(selectedItemListElement).onclick = function (e) {
        var shouldRemove = true;

        if (args['deselect_handler'] != null) {
            var handler = args['deselect_handler'];
            var rhett = handler(e);

            if (rhett != null) {
                shouldRemove = (rhett != false);
            }
        }

        if (shouldRemove) {
            ilios.dom.removeLIElement(e);
        }
    };

    // MAY RETURN THIS BLOCK
    if (noAutoCompleter) {
        return selectedItemListElement;
    }

    autoCompleter = new YAHOO.widget.AutoComplete(autoCompleteTextFieldId,
                                                  autoCompleteHiddenContainerId,
                                                  args['remote_data']);

    autoCompleter.alwaysShowContainer = true;
    autoCompleter.allowBrowserAutocomplete = false;
    autoCompleter.minQueryLength = 0;
    if (args['max_displayed_results'] != null) {
        autoCompleter.maxResultsDisplayed = args['max_displayed_results'];
    }
    else {
        autoCompleter.maxResultsDisplayed = 500;
    }

    autoCompleter.autoHighlight = false; // Auto-highlighting interferes with adding new tags

    if (args['request_generator'] != null) {
        autoCompleter.generateRequest = args['request_generator'];
    }

    autoCompleter.target = selectedItemListElement;

    autoCompleter.applyLocalFilter = true;
    autoCompleter.filterResults = function (queryString, fullResponse, parsedResponse, callback) {
        var rhett = parsedResponse;

        if (args['filter_results_handler'] != null) {
            var handler = args['filter_results_handler'];

            rhett = handler(queryString, fullResponse, parsedResponse, callback, autoCompleter,
                            args['id_uniquer']);
        }

        return rhett;
    }

    autoCompleter.resultTypeList = false;

    autoCompleter.formatResult = function (resultDataObject, queryString, resultMatch) {
        if (args['format_results_handler'] != null) {
            var handler = args['format_results_handler'];

            handler(resultDataObject, queryString, resultMatch, autoCompleter,
                    args['id_uniquer']);
        }

        return '';
    };

    if (args['load_finish_listener'] != null) {
        autoCompleter.containerPopulateEvent.subscribe(args['load_finish_listener']);
    }

    // NUKE: slated for removal
    // when switching tabs / removing selected items
    var forceCandidateListRefreshHandler = function (type, i2Args) {
        // otherwise all instances would respond to the event
        if ((i2Args[0].state == 'acquery') && (autoCompleter.target == i2Args[0].target)) {
            autoCompleter.sendQuery('');
        }
    };
    IEvent.subscribe(forceCandidateListRefreshHandler);

    return selectedItemListElement;
};

/**
 * Generates the markup for a tree selection panel which contains a generic container that the
 *  caller is responsible for populating via a listener hanging off the autocomplete mechanism.
 *  The autocomplete container is hidden offscreen and is 1px by 1px so hopefully, no matter
 *  what sort of mutator the YUI lib throws its way, it will never be seen by the user. NOTE: As
 *  the consumer of this method for the control of the results displaying widget, the selection
 *  handler mechanism must be handled outside of this method.
 *
 * The element id for the selection list will be available via dialog.selectListId
 *
 * TODO this whole sort of "I write HTML in string content in javascript land and then cram
 *              into an element via its innerHTML" feels totally amateurish.. rewrite this
 *              [quasi-inherited] code using real DOM construction like seen in the methods i've
 *              written elsewhere
 *
 * @param args
 *          id_uniquer:                 a prefix for certain element ids to unique them across
 *                                          the DOM; this value will also be stored in
 *                                          dialog.idUniquer
 *          filter_results_handler:     a function which is called during the autocomplete's
 *                                          results filtering cycle; this can be null. If not
 *                                          null, the function will receive 6 parameters:
 *                                              queryString, fullResponse, parsedResponse,
 *                                              callback, autoCompleter, idUniquer
 *                                          The first four being the 4 parameters that the YUI
 *                                          framework already passes to its autocompleter for
 *                                          filterResults(...); autoCompleter is the owning
 *                                          autoCompleter instance; idUniquer is the value
 *                                          passed to this method as args key 'id_uniquer'.
 *                                          This method must return the modified-as-appropriate
 *                                          parsedResponse object.
 *          format_results_handler:     a function which is called during the autocomplete's
 *                                          results fetch; this is THE tie-in point for your
 *                                          code to grab the results and populate the view
 *                                          correctly. This -could- be null, but in that case
 *                                          i'm not sure what use this component hierarchy is.
 *                                          This function will receive 5 parameters:
 *                                              resultDataObject, queryString, resultMatch,
 *                                              autoCompleter, idUniquer
 *                                          The first four being the 3 parameters that the YUI
 *                                          framework already passes to its autocompleter for
 *                                          formatResults(...); autoCompleter is the owning
 *                                          autoCompleter instance; idUniquer is the value
 *                                          passed to this method as args key 'id_uniquer'.
 *                                          Whatever this method returns is ignored.
 *                                          NOTE: resultMatch is actually the value for the
 *                                          first key listed in the schema fields array of the
 *                                          data source.
 *          remote_data:                the remote data source for the autocompleter, if this
 *                                          is null then there will be no autocompleter as part
 *                                          of the generated dialog
 *          submit_override:            if this is non-null the function is subscribed to the
 *                                          manual submit event of the dialog and the dialog is
 *                                          configured to fire that event when the user clicks
 *                                          the 'Done' button on the dialog.
 *          load_finish_listener:       if non-null, this will get messaged when the
 *                                          autocompleter finishes its data load
 *          container:                  the container listing the present possible choices (as
 *                                          possibly filtered via autocompleter input)
 *          max_displayed_results:      if non-null, defines the maximum number of results to
 *                                          display in the container; if null, 125 is used.
 *          request_generator:          if non-null, this is expected to be a function which
 *                                          takes one argument (the query string), and return a
 *                                          request including at least the key-value pair of
 *                                          'query={query string}'.
 *          display_handler:            a function which is called just prior to the dialog
 *                                          being displayed
 *          selected_div_dom_generator: a function which inserts DOM elements into an element
 *                                          whose id is provided; the elements represent the UI
 *                                          single-selection widget
 *          unselected_div_dom_generator:
 *                                      a function which inserts DOM elements into an element
 *                                          whose id is provided; the elements represent the UI
 *                                          single-selection widget
 *          hide_autocomplete_input:    if non-null, then the autocomplete input will be hidden
 *          tab_title:                  the title for the tab containing the selection widget;
 *          panel_title_text:           the header title text for the panel (not to be
 *                                          confused with the title bar text which will
 *                                          be "Please select items")
 *          dom_root:                   the id of the DOM element into which this markup should
 *                                          be inserted
 *          panel_width:                if this is non-null, it is expected to a valid CSS
 *                                          measurement, else '600px' will be used.
 *          single_selection:           if this is non-null, there will be no selection div.
 *          trigger:                    the id string of the element which should invoke the
 *                                          display of this panel; clicking on this id string
 *                                          must be wired elsewhere to fire thusly:
 *                                              IEvent.fire({action: 'gen_dialog_open', ...});
 *                                          where 'gen_dialog_open' is the salient action, and
 *                                          the event may also contain a key for
 *                                          'container_number' the value of which will get
 *                                          assigned as property 'containerNumber' of the dialog
 *
 * @return the DOM id for the panel div which holds the display of selected items (TODO - since
 *              this is usually called via an onReady event, a return is kind of useless)
 */
ilios.dom.generateTreeSelectionDialogMarkupAndWireContent = function (use, less, args) {
    var contents = '';
    var widgetParentDivId = args['id_uniquer'] + 'widget_div';
    var selectedDOMGenerator = args['selected_div_dom_generator'];
    var unselectedDOMGenerator = args['unselected_div_dom_generator'];
    var handleCancel = function () {
        IEvent.fire({object: 'modal_tree_dialog_panel', action: 'cancel', event: 'closing'});

        this.cancel();
    };
    var handleSubmit = function () {
        IEvent.fire({object: 'modal_tree_dialog_panel', action: 'submit', event: 'closing'});

        this.submit();
    };
    var cancelStr = ilios_i18nVendor.getI18NString('general.terms.cancel');
    var doneStr = ilios_i18nVendor.getI18NString('general.terms.done');
    var buttonArray = [{text: doneStr, handler: handleSubmit, isDefault: true},
                       {text: cancelStr, handler: handleCancel}];
    var panelWidth = (args['panel_width'] != null) ? args['panel_width'] : "600px";
    var dialog = null;
    var displayOnTriggerHandler = null;
    var autoCompleteHiddenContainerId = args['id_uniquer'] + '_hid_auto_comp';
    var autoCompleteTextFieldId = args['id_uniquer'] + '_auto_comp_input';
    var selectedItemTreeElement = args['id_uniquer'] + '_selected_item_tree';
    var autoCompleter = null;
    var i18nStr = ilios_i18nVendor.getI18NString('general.phrases.select_items');
    var noAutoCompleter = (args['remote_data'] == null);
    var showAutoCompleteInput = (args['hide_autocomplete_input'] == null);

    contents += '<div class="visuallyhidden" id="' + autoCompleteHiddenContainerId + '"></div>\n';

    contents += '<div class="hd">' + i18nStr + '</div>\n';
    contents += '<div class="bd">\n';
    contents += '<div class="dialog_wrap">';

    contents += '<p>' + args['panel_title_text'] + '</p>';

    if (args['indeterminate_loading_id'] != null) {
        contents += '<div id="' + args['indeterminate_loading_id']
                        + '" style="position: absolute; top: 15px; right: 12px; display: none;"'
                        + ' class="indeterminate_progress_text">';
        contents += '<div class="indeterminate_progress" style="display: inline-block;"></div> '
        contents += ilios_i18nVendor.getI18NString('general.terms.loading') + '...</div>';
    }
    if (args['single_selection'] == null) {
        contents += '<div class="dialog_left">';

        i18nStr = ilios_i18nVendor.getI18NString('general.phrases.selected_items');
        contents += '<label class="picked_label" for="' + selectedItemTreeElement + '">';
        contents += i18nStr;
        contents += '</label>';
        contents += '<div id="' + selectedItemTreeElement
                        + '" style="background-color: #EEEEDD; height: 300px; margin:12px 0 0 6px; '
                        + 'border: 1px solid #A2A1A2; overflow: auto;"></div>\n';

        contents += '</div>\n';


        contents += '<div class="dialog_right">';
    }
    else {
        contents += '<div class="dialog_center">';//unnecessary?
    }

    // YUI -- IF THERE'S NO FORM INSIDE A DIALOG DOM, IT REVEALS A YUI BUG
    contents += '<form method="POST">\n';


    contents += '<div id="' + args['id_uniquer'] + 'nav_tabs" class="yui-navset">\n';

    contents += '<ul class="yui-nav">\n';
    contents += '<li class="selected"><a href="#' + args['id_uniquer'] + 'selector_tab"><em>'
                    + args['tab_title'] + '</em></a></li>\n';

    contents += '</ul>\n';

    contents += '<div class="yui-content">\n';

    contents += '<div class="single_selector_tab clearfix" id="' + args['id_uniquer'] + 'selector_tab">\n';
    if (showAutoCompleteInput && (! noAutoCompleter)) {
        contents += '<input id="' + autoCompleteTextFieldId + '" name="' + autoCompleteTextFieldId
                        + '" type="text" style="width: 90%;"/>';

        i18nStr = 'input_tree_div';
    }
    else {
        i18nStr = 'default_tree_div';
    }
    contents += '<div id="' + widgetParentDivId + '" class="'+ i18nStr + '">\n'

    contents += '</div></div>\n';   // closes through single_selector_tab div

    // close yui-content class div and close encompassing yui-navset class div
    contents += '</div></div>';

    // this was two clearing divs - now just one
    contents += '<div class="clear"></div>\n';

    // YUI -- IF THERE'S NO FORM INSIDE A DIALOG DOM, IT REVEALS A YUI BUG
    contents += "</form>\n";

    contents += '</div>'; // close dialog_right div


    // text below form could go here
    contents += '</div>\n'; // close dialog_wrap div
    contents += '</div>\n'; // close bd div
    YAHOO.util.Dom.get(args['dom_root']).innerHTML = contents;

    // these have to be called after the DOM has been altered to include the widget parent div
    if (args['single_selection'] == null) {
        selectedDOMGenerator(selectedItemTreeElement);
    }
    unselectedDOMGenerator(widgetParentDivId);

    dialog = new YAHOO.widget.Dialog(args['dom_root'],
                                     { width: panelWidth, modal: true,
                                       visible: false, constraintoviewport: false,
                                       buttons: buttonArray});

    dialog.idUniquer = args['id_uniquer'];
    dialog.selectListId = selectedItemTreeElement;
    dialog.autoCompleteTextFieldId = autoCompleteTextFieldId;

    dialog.showDialogPane = function () {
        var displayHandler = args['display_handler'];

        if (displayHandler != null) {
            displayHandler(dialog);
        }

        if (showAutoCompleteInput) {
            document.getElementById(autoCompleteTextFieldId).value = '';
        }

        if (! noAutoCompleter) {
            autoCompleter.sendQuery('');
        }

        dialog.center();
        dialog.show();
    };

    // Render the Dialog
    dialog.render();

    if (args['submit_override'] != null) {
        dialog.cfg.setProperty('postmethod', 'manual');
        dialog.manualSubmitEvent.subscribe(args['submit_override'], null);
    }

    // register a listener on some page element to trigger the display of this popup dialog
    displayOnTriggerHandler = function (type, handlerArgs) {
        if (handlerArgs[0].event == args['trigger']) {
            if (handlerArgs[0].action == 'gen_dialog_open') {
                dialog.containerNumber = handlerArgs[0].container_number;

                if (! noAutoCompleter) {
                    autoCompleter.containerNumber = handlerArgs[0].container_number;
                }

                dialog.showDialogPane();
            }
        }
    };
    IEvent.subscribe(displayOnTriggerHandler);

    // MAY RETURN THIS BLOCK
    if (noAutoCompleter) {
        return selectedItemTreeElement;
    }

    autoCompleter = new YAHOO.widget.AutoComplete(autoCompleteTextFieldId,
                                                  autoCompleteHiddenContainerId,
                                                  args['remote_data']);

    autoCompleter.alwaysShowContainer = true;
    autoCompleter.allowBrowserAutocomplete = false;
    autoCompleter.minQueryLength = 0;
    if (args['max_displayed_results'] != null) {
        autoCompleter.maxResultsDisplayed = args['max_displayed_results'];
    }
    else {
        autoCompleter.maxResultsDisplayed = 500;
    }

    autoCompleter.autoHighlight = false; // Auto-highlighting interferes with adding new tags

    if (args['request_generator'] != null) {
        autoCompleter.generateRequest = args['request_generator'];
    }

    autoCompleter.target = selectedItemTreeElement;

    autoCompleter.applyLocalFilter = true;
    autoCompleter.filterResults = function (queryString, fullResponse, parsedResponse, callback) {
        var rhett = parsedResponse;

        if (args['filter_results_handler'] != null) {
            var handler = args['filter_results_handler'];

            rhett = handler(queryString, fullResponse, parsedResponse, callback, autoCompleter,
                            args['id_uniquer']);
        }

        return rhett;
    }

    autoCompleter.resultTypeList = false;

    autoCompleter.formatResult = function (resultDataObject, queryString, resultMatch) {
        if (args['format_results_handler'] != null) {
            var handler = args['format_results_handler'];

            handler(resultDataObject, queryString, resultMatch, autoCompleter,
                    args['id_uniquer']);
        }

        return '';
    };

    if (args['load_finish_listener'] != null) {
        autoCompleter.containerPopulateEvent.subscribe(args['load_finish_listener']);
    }

    dialog.autoCompleter = autoCompleter;

    // NUKE: slated for removal
    // when switching tabs / removing selected items
    var forceCandidateListRefreshHandler = function (type, i2Args) {
        // otherwise all instances would respond to the event
        if ((i2Args[0].state == 'acquery') && (autoCompleter.target == i2Args[0].target)) {
            autoCompleter.sendQuery('');
        }
    };
    IEvent.subscribe(forceCandidateListRefreshHandler);

    return selectedItemTreeElement;
};

/**
 * Generates the markup for a panel which contains some sort of single-selection widget (for
 *  example a tree) and a cancel button.
 *
 * TODO this whole sort of "I write HTML in string content in javascript land and then cram
 *              into an element via its innerHTML" feels totally amateurish.. rewrite this
 *              [inherited] code using real DOM construction like seen in the methods i've
 *              written elsewhere
 *
 * @param args
 *          id_uniquer:                 a prefix for certain element ids to unique them across
 *                                          the DOM; this value will also be stored in
 *                                          dialog.idUniquer
 *          display_handler:            a function which is called just prior to the dialog
 *                                          being displayed - this function should take care
 *                                          of whatever remote data fetching and population
 *                                          of the widget's data is needed
 *          widget_dom_generator:       a function which inserts DOM elements into an element
 *                                          whose id is provided; the elements represent the UI
 *                                          single-selection widget
 *          tab_title:                  the title for the tab containing the selection widget
 *          title:                      the widget title (optional, defaults to "Please select items")
 *          panel_title_text:           the header title text for the panel (optional, not displayed when not given)
 *          dom_root:                   the id of the DOM element into which this markup should
 *                                          be inserted
 *          panel_width:                if this is non-null, it is expected to a valid CSS
 *                                          measurement, else '600px' will be used.
 *          trigger:                    the id string of the element which should invoke the
 *                                          display of this panel; clicking on this id string
 *                                          must be wired elsewhere to fire thusly:
                                                    IEvent.fire({action: 'sac_dialog_open', ...});
 *                                          where 'sac_dialog_open' is the salient action.
 */
ilios.dom.generateSelectAndCloseDialogMarkupAndWireContent = function (use, less, args) {
    var contents = '';
    var widgetParentDivId = args['id_uniquer'] + 'widget_div';
    var domGenerator = args['widget_dom_generator'];
    var handleCancel = function () {
        IEvent.fire({object: 'modal_sac_dialog_panel', action: 'cancel', event: 'closing'});

        this.cancel();
    };
    var cancelStr = ilios_i18nVendor.getI18NString('general.terms.cancel');
    var buttonArray = [{text: cancelStr, handler: handleCancel}];
    var panelWidth = (args['panel_width'] != null) ? args['panel_width'] : "600px";
    var dialog = null;
    var displayOnTriggerHandler = null;
    var i18nStr = args['title'] || ilios_i18nVendor.getI18NString('general.phrases.select_items');
    var panelTitleText = args['panel_title_text'] || '';

    contents += '<div class="hd">' + i18nStr + '</div>\n';
    contents += '<div class="bd">\n';

    if (panelTitleText) {
        contents += '<p>' + panelTitleText + '</p>';
    }

    // YUI -- IF THERE'S NO FORM INSIDE A DIALOG DOM, IT REVEALS A YUI BUG
    contents += '<form method="POST">\n';


    contents += '<div id="' + args['id_uniquer'] + 'nav_tabs" class="yui-navset">\n';

    contents += '<ul class="yui-nav">\n';
    contents += '<li class="selected"><a href="#' + args['id_uniquer'] + 'selector_tab"><em>'
    + args['tab_title'] + '</em></a></li>\n';

    contents += '</ul>\n';

    contents += '<div class="yui-content">\n';

    contents += '<div class="single_selector_tab clearfix" id="' + args['id_uniquer'] + 'selector_tab">\n';
    contents += '<div id="' + widgetParentDivId + '" class="default_tree_div">\n';

    contents += '</div></div>\n';   // closes through single_selector_tab div

    // close yui-content class div and close encompassing yui-navset class div
    contents += '</div></div>';

    // this was two clearing divs - now just one
    contents += '<div class="clear"></div>\n';

    // YUI -- IF THERE'S NO FORM INSIDE A DIALOG DOM, IT REVEALS A YUI BUG
    contents += "</form>\n";

    // text below form could go here
    contents  += '</div>\n'; // close bd div

    contents  += '<div class="ft"></div>\n'; // dialog footer div

    YAHOO.util.Dom.get(args['dom_root']).innerHTML = contents;

    // this has to be called after the DOM has been altered to include the widget parent div
    domGenerator(widgetParentDivId);

    dialog = new YAHOO.widget.Dialog(args['dom_root'],
                                     { width: panelWidth, modal: true,
                                       visible: false, constraintoviewport: false,
                                       buttons: buttonArray});

    dialog.idUniquer = args['id_uniquer'];

    dialog.showDialogPane = function () {
        var displayHandler = args['display_handler'];

        displayHandler(dialog);

        dialog.center();
        dialog.show();
    };

    // Render the Dialog
    dialog.render();

    // register a listener on some page element to trigger the display of this popup dialog
    displayOnTriggerHandler = function (type, handlerArgs) {
        if (handlerArgs[0].event == args['trigger']) {
            if (handlerArgs[0].action == 'sac_dialog_open') {
                dialog.showDialogPane();
            }
        }
    };
    IEvent.subscribe(displayOnTriggerHandler);
};

ilios.dom.removeLIElement = function (e) {
    var target = ilios.utilities.getEventTarget(e);

    while ((target != null) && (target.tagName.toLowerCase() != 'li')) {
        target = target.parentNode;
    }

    if (target != null) {
        target.parentNode.removeChild(target);
    }
};

ilios.dom.setDisplayForAllChildrenOfClass = function (container, className, displayValue) {
    var Element = YAHOO.util.Element;
    var elements = container.getElementsByClassName(className);
    var el;

    for (var i = 0; i < elements.length; i++) {
        el = new Element(elements[i]);
        el.setStyle('display', displayValue);
    }
};

ilios.dom.childContainerSaveButtonIdForContainerNumber = function (containerNumber) {
    return '' + containerNumber + '_child_save';
};

ilios.dom.childCollapsingContainerIdForContainerNumber = function (containerNumber) {
    return '' + containerNumber + '_collapser';
};

ilios.dom.childCollapsingWidgetIdForContainerNumber = function (containerNumber) {
    return '' + containerNumber + '_collapse_widget';
};

ilios.dom.childCollapsingSummaryTextIdForContainerNumber = function (containerNumber) {
    return '' + containerNumber + '_collapse_summary_text';
};

/**
 * @todo flesh out code docs
 * @method collapseChildForContainerNumber
 * @param {String} containerNumber
 * @param {Number} toggleCollapseExpand
 *   0 == toggle current state
 *   1 == collapse no matter what
 *   2 == expand no matter what
 * @param {Function || null} collapseHandler
 * @param {Function || null} expandHandler
 */
ilios.dom.collapseChildForContainerNumber = function (containerNumber, toggleCollapseExpand,
                                                      collapseHandler, expandHandler) {
    var Element = YAHOO.util.Element;
    var collapseId = ilios.dom.childCollapsingContainerIdForContainerNumber(containerNumber);
    var collapsingDiv = new Element(document.getElementById(collapseId));
    var collapseParent = new Element(collapsingDiv.get('element').parentNode);
    var toggle = (toggleCollapseExpand == 0);
    var collapse = (toggleCollapseExpand == 1);
    var isShowing = collapsingDiv.hasClass('display_show');
    var summaryTextId = ilios.dom.childCollapsingSummaryTextIdForContainerNumber(containerNumber);
    var summaryText = document.getElementById(summaryTextId);

    if (summaryText != null) {
        summaryText = new Element(summaryText);
    }

    if ((toggle && isShowing) || collapse) {
        if (summaryText != null) {
            if (collapseHandler) {
                collapseHandler(containerNumber, summaryText.get('element'));
            }
        }
        collapsingDiv.removeClass('display_show');
        collapsingDiv.addClass('display_hide');
        collapseParent.addClass('collapsed');
        collapseParent.removeClass('expanded');

    } else {
        if (summaryText != null) {
            if (expandHandler) {
                expandHandler(containerNumber, summaryText.get('element'));
            }
        }
        collapsingDiv.removeClass('display_hide');
        collapsingDiv.addClass('display_show');
        collapseParent.addClass('expanded');
        collapseParent.removeClass('collapsed');
    }
};

/**
 * This builds the DOM tree for a child container (for example, the entire splotch of what
 *  is UI-represented for a program year); it returns the YUI Element wrapper around the
 *  root node of the tree
 *
 * todo as this function gets built, the parameter requirements to keep this generalized will
 *          come to light
 *
 * @param saveText if null, "Save Draft" will be used
 * @param saveHandler if null, there will be no publish button included
 * @param saveButtonDOMExtra if non null, it is expected to be an HTMLElement which will be
 *                                  added prior to the textNode in the DOM of the save button
 * @param publishHandler if null, there will be no publish button included
 * @param publishButtonDOMExtra if non null, it is expected to be an HTMLElement which will be
 *                                  added prior to the textNode in the DOM of the publish button
 * @param collapseHandler will be messaged just before the summary text is displayed to the
 *              user on a div collapse
 * @param expandHandler will be messaged just before the summary text is displayed to the
 *              user on a div expansion
 * @param formDOMElement the YUI element wrapper around the form element which will wrap all
 *              of the input elements featured in this container
 * @param contentGeneratorFunction a function which is assumed to take a DOM element as its
 *              first argument and will append children to that element as appropriate in order
 *              to render the body content of the container; its second argument will be the
 *              containerNumber
 * @param {Array} buttonPlaceholders  a list of class names used to create targetable placeholder elements in the button row.
 */
ilios.dom.buildChildContainerDOMTree = function (containerNumber, saveText, saveButtonDOMExtra,
    saveHandler, publishButtonDOMExtra, publishHandler, deleteHandler,
    collapseHandler, expandHandler, formDOMElement, contentGeneratorFunction, buttonPlaceholders) {
    var Element = YAHOO.util.Element;
    var rhett = new Element(document.createElement('div'));
    var collapseId = ilios.dom.childCollapsingContainerIdForContainerNumber(containerNumber);
    var collapsibleDiv = new Element(document.createElement('div'), { id: collapseId });
    var summaryTextId = ilios.dom.childCollapsingSummaryTextIdForContainerNumber(containerNumber);
    var summaryTextDiv = new Element(document.createElement('div'), { id: summaryTextId });
    var scratchElement = null;
    var textNode = null;
    var elementId = null;
    var i18nStr = ilios_i18nVendor.getI18NString('general.phrases.save_draft');
    var hdElement = new Element(document.createElement('div'));
    var btnBar = new Element(document.createElement('div'));
    var scratchList = new Element(document.createElement('ul'));
    var scratchItem, deleteEl, toggleEl;
    var i, n;

    if (! YAHOO.lang.isArray(buttonPlaceholders)) {
        buttonPlaceholders = [];
    }

    rhett.addClass('entity_container');
    rhett.addClass('expanded');
    if (YAHOO.lang.isNumber(containerNumber)) {
        rhett.get('element').setAttribute("cnumber", containerNumber);
    }

    collapsibleDiv.addClass('collapsible_container bd');
    collapsibleDiv.addClass('display_show');

    summaryTextDiv.addClass('collapsed_summary_text_div');

    collapsibleDiv.appendChild(formDOMElement.get('element'));

    // header div
    hdElement.addClass('hd');
    hdElement.addListener('click', function (e) {
        ilios.dom.collapseChildForContainerNumber(containerNumber, 0, collapseHandler, expandHandler);
        return false;
    }, null, this);

    // Expand/collapse widget
    toggleEl = new Element(document.createElement('div'));
    toggleEl.addClass('toggle');
    hdElement.appendChild(toggleEl.get('element'));

    // Summary text
    hdElement.appendChild(summaryTextDiv.get('element'));

    // Draft v. Publish text
    scratchElement = new Element(document.createElement('div'), {
        id: '' + containerNumber + '_child_draft_text'
    });
    scratchElement.addClass('child_publish_status');
    hdElement.appendChild(scratchElement.get('element'))

    // Delete widget
    deleteEl = new Element(document.createElement('div'));
    deleteEl.addClass('delete_widget icon-cancel');
    deleteEl.get('element').setAttribute('cnumber', containerNumber);
    hdElement.appendChild(deleteEl.get('element'));
    deleteEl.addListener('click', function (e) {
        deleteHandler(e);
        e.cancelBubble = true;
        e.returnValue = false;
        return false;
    }, null, this);

    rhett.appendChild(hdElement.get('element'));

    //button bar & list
    btnBar.addClass('row');
    scratchList.addClass('buttons right');

    // add button placeholders
    for (i = 0, n = buttonPlaceholders.length; i < n; i++) {
        scratchItem = new Element(document.createElement('li'));
        scratchItem.addClass(buttonPlaceholders[i]);
        scratchList.appendChild(scratchItem.get('element'));
    }

    // Save draft button
    // why doesn't instructors use this?
    if (saveHandler != null) {
        scratchItem = new Element(document.createElement('li'));

        elementId = ilios.dom.childContainerSaveButtonIdForContainerNumber(containerNumber);
        scratchElement = new Element(document.createElement('button'), { id: elementId });
        scratchElement.addClass('small radius button child_save_draft');
        scratchElement.get('element').setAttribute('onclick', 'return false;');
        scratchElement.get('element').setAttribute('disabled', 'disabled');
        textNode = document.createTextNode((saveText != null) ? saveText : i18nStr);
        if (saveButtonDOMExtra != null) {
            scratchElement.appendChild(saveButtonDOMExtra);
        }
        scratchElement.appendChild(textNode);
        scratchElement.addListener('click', saveHandler, null, this);
        scratchItem.appendChild(scratchElement.get('element'));
        scratchList.appendChild(scratchItem.get('element'));
    }

    // Publish button
    if (publishHandler != null) {
        scratchItem = new Element(document.createElement('li'));

        i18nStr = ilios_i18nVendor.getI18NString('general.terms.publish');
        scratchElement = new Element(document.createElement('button'), {
            id: '' + containerNumber + '_child_publish'
        });
        scratchElement.addClass('small radius button child_publish');
        scratchElement.get('element').setAttribute('onclick', 'return false;');
        scratchElement.get('element').setAttribute('disabled', 'disabled');
        textNode = document.createTextNode(i18nStr);
        if (publishButtonDOMExtra != null) {
            scratchElement.appendChild(publishButtonDOMExtra);
        }
        scratchElement.appendChild(textNode);
        scratchElement.addListener('click', publishHandler, null, this);
        scratchItem.appendChild(scratchElement.get('element'));
        scratchList.appendChild(scratchItem.get('element'));
    }

    btnBar.appendChild(scratchList.get('element'));
    formDOMElement.appendChild(btnBar.get('element'));

    // hand off for the rest of the content
    contentGeneratorFunction(formDOMElement, containerNumber);

    rhett.appendChild(collapsibleDiv.get('element'));

    return rhett;
};

ilios.dom.createExpandCollapseWidgetForContainerNumber = function (containerNumber, collapseHandler, expandHandler) {
    // Expand/collapse widget
    var elementId = ilios.dom.childCollapsingWidgetIdForContainerNumber(containerNumber);
    var scratchElement = new YAHOO.util.Element(document.createElement('div'), { id: elementId });
    var textNode = document.createTextNode("+ / -");

    scratchElement.addClass('child_expand_collapse');
    scratchElement.appendChild(textNode);
    scratchElement.addListener('click', function (e) {
        ilios.dom.collapseChildForContainerNumber(containerNumber, 0, collapseHandler, expandHandler);
        e.cancelBubble = true;
        e.returnValue = false;
        return false;
    }, null, this);
    return scratchElement.get('element');
};

ilios.dom.addNewLIElementWithIliosModel = function (container, model, displayString) {
    var newLI = document.createElement('li');
    var textNode = null;

    newLI.iliosModel = model;

    textNode = document.createTextNode(displayString);
    newLI.appendChild(textNode);
    if (model instanceof UserModel) {
        newLI.setAttribute('title', model.getEmailAddress());
    }

    container.appendChild(newLI);
};

// selectedList's LI elements assumed to be created by ilios.dom.addNewLIElementWithIliosModel(...)
ilios.dom.iliosModeledLIElementsContainMatchingModel = function (selectedList, candidateModel) {
    var liElements = selectedList.childNodes;
    var len = (liElements != null) ? liElements.length : -1;
    var i = 0;
    var liModel = null;

    for (; i < len; i++) {
        liModel = liElements[i].iliosModel;

        if (liModel != null) {
            if (liModel.isGroup == candidateModel.isGroup) {
                if (liModel instanceof UserModel) {
                    if (liModel.compareTo(candidateModel) == 0) {
                        return true;
                    }
                }
                else {
                    if (liModel.dbId == candidateModel.dbId) {
                        return true;
                    }
                }
            }
        }
    }

    return false;
};

ilios.dom.setElementEnabled = function (element, enabled) {
    // MAY RETURN THIS BLOCK
    if (element == null) {
        return;
    }

    if (enabled) {
        element.removeAttribute('disabled');
    }
    else {
        element.setAttribute('disabled', 'true');
    }
};

/**
 * @param aElement expected to be a DOM element of type A which is able to receive a click
 *                      in the original-browser-world HREF fashion, or the element that was the
 *                      replacement of that DOM element when this method was last called. The
 *                      element must be currently in the DOM, and if the replacement, this method
 *                      must have been called previously with the A element as aElement.
 */
ilios.dom.setEnableForAElement = function (aElement, enabled) {
    var parent = aElement.parentNode;

    if (enabled) {
        if (aElement.tagName.toLowerCase() != 'a') {
            parent.replaceChild(parent.previousAElement, aElement);
        }
    }
    else if (aElement.tagName.toLowerCase() == 'a') {
        var replacement = document.createElement('span');
        replacement.setAttribute('style', (aElement.getAttribute('style')
                                            + '; color: #A8A8A8; cursor: default;'));
        replacement.setAttribute('class', 'disabled');
        replacement.setAttribute('id', aElement.getAttribute('id'));
        replacement.innerHTML = aElement.innerHTML;

        parent.previousAElement = aElement;

        parent.replaceChild(replacement, aElement);
    }
};

/**
 *  this one can probably be deleted - replaced with ilios.dom.toggleWidget
 */
ilios.dom.toggleWidgetCollapse = function (coupletIdPrefix, auxillaryElement) {
    var Element = YAHOO.util.Element;
    var collapseDiv = new Element(document.getElementById(coupletIdPrefix + 'collapse'));
    var contentDiv = new Element(document.getElementById(coupletIdPrefix + 'content'));
    var auxElement = null;

    if (typeof auxillaryElement != 'undefined') {
        auxElement = new Element(document.getElementById(auxillaryElement));
    }

    if (collapseDiv.hasClass('dashboard_expand_widget')) {
        collapseDiv.removeClass('dashboard_expand_widget');
        collapseDiv.addClass('dashboard_collapse_widget');
        contentDiv.setStyle('display', 'block');
        if (auxElement != null) {
            auxElement.setStyle('display', 'none');
        }
    }
    else {
        collapseDiv.removeClass('dashboard_collapse_widget');
        collapseDiv.addClass('dashboard_expand_widget');
        contentDiv.setStyle('display', 'none');
        if (auxElement != null) {
            auxElement.setStyle('display', 'block');
        }
    }
};
/**
 * more generic version...
 * to do: remove dependency on parentNode
 */
ilios.dom.toggleWidget = function (elem,auxillaryElement) {
    var Dom = YAHOO.util.Dom;
    var container = elem.parentNode;
    var contentDiv = Dom.getElementsByClassName('bd','div', container)[0];
    var auxElement = null;

    if (typeof auxillaryElement != 'undefined') {
        auxElement = Dom.get(auxillaryElement);
    }

    if (Dom.hasClass(elem,'expand')) {
        ilios.utilities.show(contentDiv);
        Dom.removeClass(elem,'expand');
        Dom.addClass(elem,'collapse');
        if (auxElement != null) {
            Dom.setStyle.setStyle(auxElement,'display', 'none');
        }
    }
    else {
        ilios.utilities.hide(contentDiv);
        Dom.removeClass(elem,'collapse');
        Dom.addClass(elem,'expand');
        if (auxElement != null) {
            Dom.setStyle(auxElement,'display', 'block');
        }
    }
};

/*
 * a basic 3-column row for common entity_container: label, data, actions
 * append rowElement to parent container
 */
ilios.dom.createEntityContainerInputRow = function(){
    var rowEl = document.createElement('div');
    rowEl.setAttribute('class','row');
    return rowEl;
};

ilios.dom.createLabelCol = function(rowEl,labelEl){
    labelCol = document.createElement('div');
    labelCol.setAttribute('class', 'label column');
    labelCol.appendChild(labelEl);
    rowEl.appendChild(labelCol);
    return labelCol;
};

ilios.dom.createDataCol = function(rowEl, dataEl){
    dataCol = document.createElement('div');
    dataCol.setAttribute('class', 'data column');
    dataCol.appendChild(dataEl);
    rowEl.appendChild(dataCol);
    return dataCol;
};

ilios.dom.createActionCol = function(rowEl, actionEl){
    actionCol = document.createElement('div');
    actionCol.setAttribute('class', 'actions column');
    actionCol.appendChild(actionEl);
    rowEl.appendChild(actionCol);
    return actionCol;
};
