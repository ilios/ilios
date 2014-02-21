<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * DEPENDENCIES:
 *     YUI toolkit
 *     scripts/ilios_dom.js
 *     scripts/ilios_utilities.js
 */
?>

<div class="tabdialog" id="instructor_picker_dialog">
    <div class="hd"><?php echo $instructor_picker_title; ?></div>
    <div class="bd">
        <div class="dialog_wrap">
            <form method="GET">
                <div class="dialog_left">
                    <ul class="picked" id="instructor_picker_selected"></ul>
                </div>
                <div class="dialog_right">
                    <div class="autocomplete_tab clearfix" id="instructor_picker_ac_div">
                        <label for="instructor_picker_ac_input"><?php echo $word_filter_string; ?></label>:<br />
                        <input id="instructor_picker_ac_input" name="instructor_picker_ac_input" type="text">
                        <div class="autolist" id="instructor_picker_autolist"></div>
                        <div id="instructor_picker_ac_progress" class="invisible"
                            style="position:absolute; left: 0; top: 326px;">
                            <img src="<?php echo $viewsUrlRoot . 'images/loading.gif' ?>" border="0" alt="Loading&hellip;" />
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="clear"></div>
    </div>
    <div class="ft"></div>
</div> <!-- end #instructor_picker_dialog -->

<script type="text/javascript">

    var instructorGroupAutoCompleter = null;

    var instructorPickerDialog = null;

    ilios.gm.instructorGroupDataSource = new YAHOO.util.XHRDataSource(controllerURL + 'searchInstructors');
    ilios.gm.instructorGroupDataSource.responseType = YAHOO.util.XHRDataSource.TYPE_JSON;
    ilios.gm.instructorGroupDataSource.responseSchema = {
        resultsList: "results",
        fields: [
            {key: "user_id"},
            {key: "last_name"},
            {key: "first_name"},
            {key: "middle_name"},
            {key: "phone"},
            {key: "email"},
            {key: "uc_uid"},
            {key: "other_id"},
            {key: "instructor_group_id"},
            {key: "title"},
            {key: "school_id"}
        ]
    };

    /**
     * Parses the JSON data in a given response into a response object.
     * Overrides the YAHOO.util.DataSourceBase.parseJSONData().
     * See http://developer.yahoo.com/yui/docs/YAHOO.util.DataSourceBase.html#method_parseJSONData
     * @method parseJSONData
     * @param {Object} oRequest
     * @param {Object} oFullResponse
     * @return {Object}
     */
    ilios.gm.instructorGroupDataSource.parseJSONData = function (oRequest , oFullResponse) {
        var rhett = {results: [], error: false};
        var i, n, o, userModel, groupObj;
        if (oFullResponse.results && oFullResponse.results.length) {
            for (i = 0, n = oFullResponse.results.length; i < n; i++) {
                o = oFullResponse.results[i];
                if (o.instructor_group_id) { // instructor group
                    groupObj = {};
                    groupObj.isGroup = true;
                    groupObj.dbId = o.instructor_group_id;
                    groupObj.title = o.title;
                    rhett.results.push(groupObj);
                } else { // instructor
                    userModel = new UserModel(o);
                    userModel.isGroup = false;
                    rhett.results.push(userModel);
                }
            }
        } else if (oFullResponse.error) { // error handling
            rhett.error = true;
        }
        return rhett;
    };

    /*
     * Does a comparison between the LI elements in a specified list (assumed to have been
     * generated via the ilios.dom.addNewLIElementWithIliosModel(...) method) and a given
     * model.
     *
     * Should be considered @private.
     */
    ilios.gm.augmentedInspectorLIElementsContainMatchingModel = function (selectedList, candidateModel) {
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
                    } else {
                        if (liModel.dbId == candidateModel.dbId) {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    };

    /*
     * This method filters the instructors for the autocompleter; this filtering is
     * done pre-markup of the list and, here, takes out elements in the full list which
     * have already been selected.
     *
     * Should be considered @private
     */
    ilios.gm.filterInstructors = function (queryString, fullResponse, parsedResponse, callback) {
        var len = parsedResponse.results.length;
        var selectedList = document.getElementById('instructor_picker_selected');
        var filteredResults = [];
        var i = 0;

        for (; i < len; i++) {
            if (! ilios.gm.augmentedInspectorLIElementsContainMatchingModel(selectedList, parsedResponse.results[i])) {
                filteredResults.push(parsedResponse.results[i]);
            }
        }

        parsedResponse.results = filteredResults;

        return parsedResponse;
    };

    // @private
    ilios.gm.wireUpInstructorPicker = function () {
        var itemSelectHandler = null;

        var handleSave = function () {
            ilios.gm.userAcceptedInstructorPickerChanges();
            this.hide();
        };

        var handleCancel = function () {
            ilios.gm.userCanceledInstructorPickerChanges();
            this.cancel();
        };

        var cancelStr = ilios_i18nVendor.getI18NString('general.terms.cancel');
        var saveStr = ilios_i18nVendor.getI18NString('general.terms.save');
        var buttonArray = [
            {text : saveStr, handler : handleSave, isDefault : true},
            {text : cancelStr, handler : handleCancel}
        ];

        var panelWidth = "600px";
        var dialog = new YAHOO.widget.Dialog('instructor_picker_dialog', {
            width : panelWidth,
            modal : true,
            visible : false,
            constraintoviewport : false,
            buttons : buttonArray
        });

        var element = document.getElementById('instructor_picker_selected');

        YAHOO.util.Event.addListener(element, 'click', function (event) {
            ilios.gm.handleInstructorGroupDeselection(event);
        });

        dialog.showDialogPane = function () {
            dialog.center();
            dialog.show();
        };

        dialog.showEvent.subscribe(function() {
            // refresh the autocomplete list
            document.getElementById('instructor_picker_ac_input').value = '';
            instructorGroupAutoCompleter.sendQuery('');
        });

        dialog.render();

        instructorPickerDialog = dialog;

        instructorGroupAutoCompleter = new YAHOO.widget.AutoComplete(
            'instructor_picker_ac_input',
            'instructor_picker_autolist',
            ilios.gm.instructorGroupDataSource
        );

        instructorGroupAutoCompleter.alwaysShowContainer = true;
        instructorGroupAutoCompleter.allowBrowserAutocomplete = false;
        instructorGroupAutoCompleter.minQueryLength = 0;
        instructorGroupAutoCompleter.maxResultsDisplayed = 2500;

        instructorGroupAutoCompleter.autoHighlight = false;

        instructorGroupAutoCompleter.target = element;

        instructorGroupAutoCompleter.applyLocalFilter = true;
        instructorGroupAutoCompleter.filterResults = ilios.gm.filterInstructors;

        instructorGroupAutoCompleter.resultTypeList = false;

        instructorGroupAutoCompleter.formatResult = function (resultDataObject, queryString, resultMatch) {
            if (resultDataObject instanceof UserModel) {
                var rhett = '<span title="' + resultDataObject.getEmailAddress() + '">';

                rhett += resultDataObject.getFormattedName(ilios.utilities.USER_NAME_FORMAT_LAST_FIRST);
                rhett += '</span>';

                return rhett;
            }

            return resultDataObject.title;
        };

        instructorGroupAutoCompleter.dataReturnEvent.subscribe(function (sType, aArgs) {
            YAHOO.util.Dom.setStyle('instructor_picker_ac_progress', 'visibility', 'hidden');
        });

        instructorGroupAutoCompleter.dataRequestEvent.subscribe(function (sType, aArgs) {
            YAHOO.util.Dom.setStyle('instructor_picker_ac_progress', 'visibility', 'visible');
            var myAC = aArgs[0];
            myAC.clearList();
        });


        itemSelectHandler = function (selectionType, selectionArgs) {
            ilios.gm.handleInstructorGroupSelection(selectionArgs[2]);
            selectionArgs[1].parentNode.removeChild(selectionArgs[1]);
            document.getElementById('instructor_picker_ac_input').value = '';
        };
        instructorGroupAutoCompleter.itemSelectEvent.subscribe(itemSelectHandler);
    };

    YAHOO.util.Event.onDOMReady(ilios.gm.wireUpInstructorPicker);
</script>
