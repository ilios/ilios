/**
 * UI and event-handling boilerplate.
 *
 * Defines the following namespaces:
 *
 * ilios.ui
 *
 * Dependencies:
 *
 * scripts/ilios_dom.js
 */

ilios.namespace('ui');

ilios.ui.programCohortDialogTreeView = null;

//actual value assignment is at the end of this file
var IEvent = null;


// Defining a custom event to aid in decoupling these library methods
ilios.ui.onIliosEvent = new YAHOO.util.CustomEvent("onIliosEvent");

ilios.ui.hackilySetButtonTitleOnYUIDialog = function (title, dialog, buttonIndex) {
    var buttons = dialog.getButtons();

    if ((buttons != null) && (buttons.length > buttonIndex)) {
        buttons[buttonIndex]._button.innerHTML = title;
    }
};

// @param buttonIndex if defined and a valid index, only that button is affected, otherwise all
//                          buttons are affected
ilios.ui.hackilySetButtonVisibilityOnYUIDialog = function (dialog, visible, buttonIndex) {
    var Element = YAHOO.util.Element;
    var buttons = dialog.getButtons();

    if (buttons != null) {
        var el = null;

        if (typeof buttonIndex == 'undefined') {
            for (var i = 0; i < buttons.length; i++) {
                el = new Element(buttons[i]._button);

                el.setStyle('display', (visible ? 'block' : 'none'));
            }
        } else {
            if ((buttons.length > buttonIndex) && (buttonIndex >= 0)) {
                el = new Element(buttons[buttonIndex]._button);

                el.setStyle('display', (visible ? 'block' : 'none'));
            }
        }
    }
};

ilios.ui.radioButtonSelected = function (inputElement) {
    var Element = YAHOO.util.Element;
    var name = inputElement.getAttribute('name');
    var idStr = inputElement.getAttribute('id');
    var allRadioElements = YAHOO.util.Selector.query('input[name=' + name + ']');
    var element = new Element(document.getElementById(idStr + '_label'));

    element.setStyle('font-weight', 'bold');

    for (var i = 0; i < allRadioElements.length; i++) {
        if (inputElement != allRadioElements[i]) {
            idStr = allRadioElements[i].getAttribute('id');
            element = new Element(document.getElementById(idStr + '_label'));

            element.setStyle('font-weight', 'normal');
        }
    }
};

/**
 * An auto-complete field with fixed size list-box.
 *
 * @param args
 *          remote_data:    if non-null, this will be used as the data source; if null then
 *                              datasource and schema must be defined
 *          filter_results_handler:
 *                          if non-null, this method will be called as the auto-completer's
 *                              filterResults method
 *          format_result_handler:
 *                          if non-null, this method will be called as the auto-completer's
 *                              formatResult method
 *          schema:         'schema' for the data source cited via datasource
 *          target:         the container listing the chosen elements
 *          input:          the input data field used by the autocompleter
 *          select_handler: if non-null, this function will be messaged with a single param
 *                              which is a map representing the db row of the user selection
 *          container:      the container listing the present possible choices (as possibly
 *                              filtered via autocompleter input)
 *          max_displayed_results:
 *                          if non-null, defines the maximum number of results to display in
 *                              the container; if null, this defaults to 125
 *          request_generator:
 *                          if non-null, this is expected to be a function which takes one
 *                              argument (the query string), and return a request including
 *                              at least the key-value pair of 'query={query string}'.
 */
ilios.ui.setupDialogAutoComplete = function (args) {
    var dataSource = args['remote_data'];
    var autoCompleter = new YAHOO.widget.AutoComplete(args['input'], args['container'], dataSource);

    autoCompleter.alwaysShowContainer = true;
    autoCompleter.allowBrowserAutocomplete = false;
    autoCompleter.minQueryLength = 0; // Can be 0, which will return all results
    if (args['max_displayed_results'] != null) {
        autoCompleter.maxResultsDisplayed = args['max_displayed_results'];
    }
    else {
        autoCompleter.maxResultsDisplayed = 500;
    }

//  autoCompleter.delimChar = [",", ";"]; // Enable comma and semi-colon delimiters

    autoCompleter.autoHighlight = false; // Auto-highlighting interferes with adding new tags

    if (args['request_generator'] != null) {
        autoCompleter.generateRequest = args['request_generator'];
    }

    autoCompleter.target = args['target'];

    autoCompleter.applyLocalFilter = true;
    autoCompleter.filterResults = function (queryString, fullResponse, parsedResponse, callback) {
        var filteredResults = null;
        var existingList = null;
        var len = 0;
        var i = 0;
        var displayString = null;
        var entitiedDisplayString = null;

        // MAY RETURN THIS BLOCK
        if (args['filter_results_handler'] != null) {
            var handler = args['filter_results_handler'];

            return handler(queryString, fullResponse, parsedResponse, callback, autoCompleter);
        }

        filteredResults = new Array();
        existingList = YAHOO.util.Dom.get(autoCompleter.target).innerHTML;
        len = parsedResponse.results.length;

        // pR has an attribute pR.results which is an array of each row (obeying schema)
        for (; i < len; i ++) {
            displayString = (parsedResponse.results[i].title != null)
                                ? parsedResponse.results[i].title
                                : parsedResponse.results[i].last_name;

            entitiedDisplayString = ilios.utilities.htmlEntities(displayString);

            if (existingList.indexOf(entitiedDisplayString) == -1) {
                filteredResults.push(parsedResponse.results[i]);
            }
        }

        parsedResponse.results = filteredResults;

        return parsedResponse;
    };

    autoCompleter.resultTypeList = false;

    // resultMatch is actually the value for the first key listed int the schema fields array
    autoCompleter.formatResult = function (resultDataObject, queryString, resultMatch) {
        var rhett = null;

        // MAY RETURN THIS BLOCK
        if (args['format_result_handler'] != null) {
            var handler = args['format_result_handler'];

            return handler(resultDataObject, queryString, resultMatch, autoCompleter);
        }

        rhett = '';

        if (resultDataObject.definition) {
            rhett += '<span title="' + resultDataObject.definition + '">';
        }

        rhett += resultMatch;

        if (resultDataObject.definition) {
            rhett += '</span>';
        }

        return rhett;
    };

    var itemSelectHandler = function (selectionType, selectionArgs) {
        if (args['select_handler'] != null) {
            var handler = args['select_handler'];

            handler(selectionArgs[2]);
        }

        selectionArgs[1].parentNode.removeChild(selectionArgs[1]);

        document.getElementById(args['input']).value = '';
    };
    autoCompleter.itemSelectEvent.subscribe(itemSelectHandler);

    // when switching tabs / removing selected items
    var forceCandidateListRefreshHandler = function (type, i2Args) {
        // otherwise all instances would respond to the event
        if ((i2Args[0].state == 'acquery') && (autoCompleter.target == i2Args[0].target)) {
            autoCompleter.sendQuery('');
        }
    };
    IEvent.subscribe(forceCandidateListRefreshHandler);

    var handleModalDisplay = function (type, hmdArgs) {
        if (autoCompleter.target == hmdArgs[0].target) {
            if ((hmdArgs[0].event == 'opening') && (hmdArgs[0].object == 'modal_dialog_panel')) {
                autoCompleter.sendQuery("");

                document.getElementById(args['input']).value = '';
            }
        }
    };
    IEvent.subscribe(handleModalDisplay);

    return autoCompleter;
};

ilios.ui.createCollapsingBlock = function (parentContainer, title, divId, contentDivId) {
    var container = null;
    var element = null;

    container = document.createElement('div');
    container.setAttribute('id', divId);
    container.setAttribute('class', 'row no-action');

    element = document.createElement('div');
    element.setAttribute('class', 'collapsed_widget');
    element.divIsCollapsed = true;
    YAHOO.util.Event.addListener(element, 'click', function () {
        ilios.ui.toggleCollapsedVisibility(this, contentDivId, true);   // TODO closure
    });
    container.appendChild(element);

    element = document.createElement('div');
    element.setAttribute('class', 'column label');
    element.innerHTML = title;
    container.appendChild(element);

    element = document.createElement('div');
    element.setAttribute('id', contentDivId);
    element.setAttribute('class', 'column data');
    element.setAttribute('style', 'display: none;');
    container.appendChild(element);

    parentContainer.appendChild(container);
};

ilios.ui.toggleCollapsedVisibility = function (widgetElement, contentDivId, showChevrons) {
    var Element = YAHOO.util.Element;
    var widget = new Element(widgetElement);
    var content = new Element(document.getElementById(contentDivId));

    if (content.getStyle('display') == 'none') {
        content.setStyle('display', 'block');

        if (showChevrons) {
            widget.removeClass('collapsed_widget');
            widget.addClass('expanded_widget');
        }

        widgetElement.divIsCollapsed = false;
    } else {
        content.setStyle('display', 'none');

        if (showChevrons) {
            widget.removeClass('expanded_widget');
            widget.addClass('collapsed_widget');
        }

        widgetElement.divIsCollapsed = true;
    }
};

/**
 * Utility method.
 * Wipes out the content of a given container element and replaces it with
 * a progress indicator ("spinner").
 * This is meant to provide a placeholder while that actual content of the container
 * gets (re)-loaded.
 *
 * @param container the container element.
 */
ilios.ui.renderIndeterminateInView = function (container) {
    var progressDiv = document.createElement('div');

    ilios.utilities.removeAllChildren(container);

    progressDiv.setAttribute('class', 'indeterminate_progress');
    progressDiv.setAttribute('style',
                             'margin-left: auto; margin-right: auto;  margin-top: 275px;');

    container.appendChild(progressDiv);
};

ilios.ui.preventEnterKeyPressFromSubmittingForm = function (event) {
    var charCode = event.keyCode ? event.keyCode
                                 : event.which ? event.which
                                               : event.charCode;

    if (charCode == 13) {
        event.cancelBubble = true;
        event.returnValue = false;

        return false;
    }

    return true;
};

// @private
ilios.ui.subscribedToPCLabelClick = false;

ilios.ui.programCohortDialogTreeDOMGenerator = function (parentDivId) {
    if (ilios.ui.programCohortDialogTreeView != null) {
        ilios.ui.programCohortDialogTreeView.destroy();

        ilios.ui.programCohortDialogTreeView = null;

        ilios.ui.subscribedToPCLabelClick = false;
    }

    ilios.ui.programCohortDialogTreeView = new YAHOO.widget.TreeView(parentDivId);
};

/**
 * The consumer of this method must also provide an implementation in its code of
 *              ilios.ui.handleProgramCohortDialogSelection
 *  which will receive the selection event from the tree, and an implementation of
 *              ilios.ui.buildNodeModel
 *  which will receive the cohort row and program title.
 *
 * The controller must implement getCohortProgramTreeContent (the abstract ilios controller
 *  implements this, so unless you've done something screwy, this is a NOOP)
 *
 * @param additionalHandler if non-null, then it will be invoked with dialog at the conclusion
 *                                      of the AJAX-ian transaction
 * @see group/group_manager_dom.js for example implementations of the above mentioned consumer
 *                                      methods
 */
ilios.ui.handleProgramCohortSelectionDialogDisplay = function (dialog, additionalHandler) {
    var url = controllerURL + "getCohortProgramTreeContent";
    var method = "POST";
    var paramString = "";
    var ajaxCallback = {
            success: function (resultObject) {
                var TextNode = YAHOO.widget.TextNode;
                var parsedObject = null;
                var rootNode = ilios.ui.programCohortDialogTreeView.getRoot();
                /*
                 * the results.map is an associative array constructed on the server side featuring
                 *  a key of the program title associated to a value which is an array. that array
                 *  will be a non-associative array of cohort row data, ordered natural-ascending on
                 *  cohort.title
                 */
                var programCohortMap = null;
                var cohortArray = null;
                var i = 0;
                var len = 0;
                var parentNode = null;
                var cohortNode = null;
                var cohort = null;
                var model = null;

                try {
                    parsedObject = YAHOO.lang.JSON.parse(resultObject.responseText);
                } catch (e) {
                    ilios.global.defaultAJAXFailureHandler(null, e);
                    return;
                }

                programCohortMap = parsedObject.map;

                ilios.ui.programCohortDialogTreeView.removeChildren(rootNode);

                for (var programTitle in programCohortMap) {
                    cohortArray = programCohortMap[programTitle];
                    i = 0;
                    len = cohortArray.length;

                    parentNode = new TextNode(programTitle, rootNode, false);
                    for (; i < len; i++) {
                        cohort = cohortArray[i];
                        cohortNode = new TextNode(cohort.title, parentNode, false);
                        cohortNode.parentDialog = dialog;
                        model = ilios.ui.buildNodeModel(cohort, programTitle);
                        cohortNode.iliosModel = model;
                    }
                }

                if (! ilios.ui.subscribedToPCLabelClick) {
                    ilios.ui.programCohortDialogTreeView.subscribe("clickEvent", ilios.ui.handleProgramCohortDialogSelection);
                    ilios.ui.subscribedToPCLabelClick = true;
                }

                ilios.ui.programCohortDialogTreeView.draw();

                if (additionalHandler != null) {
                    additionalHandler(dialog);
                }
            },

            failure: function (resultObject) {
                ilios.global.defaultAJAXFailureHandler(resultObject);
            }};

    YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);
};

IEvent = ilios.ui.onIliosEvent;

/**
 * RichEditor inherits YUI Simple Editor.
 *
 * This is used in places like 'descriptions', 'objectives', 'notes', etc.  We want
 * to provide the same and consistent features across all textarea elements.
 *
 * @param el
 * @param attrs
 *
 */

ilios.ui.RichTextEditor = function(el, attrs) {
    // Set up the default attributes for Ilios Editor
    var defAttrs = {
        toolbar: {
            collapse: true,
            titlebar: false,
            draggable: false,
            buttons: [
                {
                    group: 'fontstyle', label: 'Font Size',
                    buttons: [{
                        type: 'spin',
                        label: '13',
                        value: 'fontsize',
                        range: [9, 45 ],
                        disabled: true
                    }]
                },
                {
                    type: 'separator'
                },
                {
                    group: 'textstyle',
                    label: 'Font Style',
                    buttons: [
                        {
                            type: 'push',
                            label: 'Bold CTRL + SHIFT + B',
                            value: 'bold'
                        },
                        {
                            type: 'push',
                            label: 'Italic CTRL + SHIFT + I',
                            value: 'italic'
                        },
                        {
                            type: 'push',
                            label: 'Underline CTRL + SHIFT + U',
                            value: 'underline'
                        },
                        {
                            type: 'push',
                            label: 'Strike Through',
                            value: 'strikethrough'
                        },
                        {
                            type: 'separator'
                        },
                        {   type: 'color',
                            label: 'Font Color',
                            value: 'forecolor',
                            disabled: true
                        },
                        {
                            type: 'color',
                            label: 'Background Color',
                            value: 'backcolor',
                            disabled: true
                        }
                    ]
                },
                {
                    type: 'separator'
                },
                {
                    group: 'indentlist',
                    label: 'Lists',
                    buttons: [
                        {
                            type: 'push',
                            label: 'Create an Unordered List',
                            value: 'insertunorderedlist'
                        },
                        {
                            type: 'push',
                            label: 'Create an Ordered List',
                            value: 'insertorderedlist'
                        }
                    ]
                }
            ]
        }
    };

    if (attrs) {
        YAHOO.lang.augmentObject(defAttrs, attrs, true);
    }

    ilios.ui.RichTextEditor.superclass.constructor.call(this, el, defAttrs);

    this.on('editorKeyUp', this.saveHTML);
    this.on('afterNodeChange', this.saveHTML);
};

YAHOO.lang.extend( ilios.ui.RichTextEditor, YAHOO.widget.SimpleEditor );
