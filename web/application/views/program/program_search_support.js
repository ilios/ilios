/**
 * Defines the following namespaces:
 *
 * ilios.pm.cs
 *
 * Dependencies:
 *
 * YUI libs (at least YAHOO.util and its dependencies)
 * scripts/ilios_ui.js
 * scripts/ilios_utilities.js
 */

ilios.namespace('pm.cs');

ilios.pm.cs.displayProgramSearchPanel = function () {
	if ((ilios.pm.currentProgramModel != null) && ilios.pm.currentModelIsDirty()) {
		var notification = ilios_i18nVendor.getI18NString('general.warning.dirty_state_lose_changes');
        var continueStr = ilios_i18nVendor.getI18NString('general.phrases.want_to_continue');
        var yesStr = ilios_i18nVendor.getI18NString('general.terms.yes');

        ilios.alert.inform(('<p style="margin-bottom:9px; text-align:justify;">' + notification
				+ '</p><center><b>' + continueStr + '</b></center>'), yesStr,
               ilios.pm.cs.continueDisplayProgramSearchPanel);

		return;
	}

    ilios.pm.cs.programSearchDialog.showDialogPane();
};

// @private
ilios.pm.cs.continueDisplayProgramSearchPanel = function () {
    this.cancel();      // (alert panel dismiss)

    ilios.pm.cs.programSearchDialog.showDialogPane();
};

ilios.pm.cs.performProgramSearch = function (searchTerms) {
    var searchInfoElement = document.getElementById('program_search_status');

	if (YAHOO.lang.trim(searchTerms).length < 2) {
		searchInfoElement.innerHTML = ilios_i18nVendor.getI18NString('general.error.query_length');
	}
	else {
    	var url = controllerURL + "getProgramListForQuery";
        var method = "POST";
    	var paramString = "query=" + searchTerms;
        var ajaxCallback = {
			success: function (resultObject) {
				var parsedObject = null;
				var programModel = null;
				var searchResultsContainer = null;
                var element = null;

                try {
                    parsedObject = YAHOO.lang.JSON.parse(resultObject.responseText);
                }
                catch (e) {
        			document.getElementById('program_search_status').innerHTML
                                       = ilios_i18nVendor.getI18NString('general.error.must_retry');

                    return;
                }

                searchResultsContainer = document.getElementById('program_search_results_list');
                ilios.utilities.removeAllChildren(searchResultsContainer);


                for (var key in parsedObject) {
                    element = document.createElement('li');
                    programModel = new ProgramModel(parsedObject[key]);
                    element.iliosModel = programModel;
                    element.innerHTML = '<span class="title">' + programModel.getTitle(); + '</span>'
                    YAHOO.util.Event.addListener(element, 'click', function (e) { ilios.pm.cs.programSelected(this); });
                    searchResultsContainer.appendChild(element);
                }


                if (parsedObject.length == 0) {
                    document.getElementById('program_search_status').innerHTML
                                = ilios_i18nVendor.getI18NString('general.phrases.search.no_match');
                }
                else {
                    document.getElementById('program_search_status').innerHTML = '';
                }
			},

			failure: function (resultObject) {
				ilios.global.defaultAJAXFailureHandler(resultObject);

        		document.getElementById('program_search_status').innerHTML
                                       = ilios_i18nVendor.getI18NString('general.error.must_retry');

                return;
			}};

        searchInfoElement.innerHTML = ilios_i18nVendor.getI18NString('general.terms.searching')
        									+ '&hellip;';

        YAHOO.util.Connect.asyncRequest(method, url, ajaxCallback, paramString);
    }
};

ilios.pm.cs.emptySearchDialogForViewing = function () {
    var element = document.getElementById('program_search_terms');

    element.value = '';

    element = document.getElementById('program_search_results_list');
    ilios.utilities.removeAllChildren(element);
    element = document.getElementById('program_search_terms');
    element.focus();
};

ilios.pm.cs.programSelected = function (divElement) {
    var model = divElement.iliosModel; // ProgramModel
    // construct new course url and reload page with it.
    var newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname
        + "?program_id=" + model.getDBId();
    window.location.href = newUrl;
};
