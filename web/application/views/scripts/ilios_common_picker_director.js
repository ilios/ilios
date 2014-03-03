ilios.namespace('common.picker.director');


ilios.common.picker.director.idUserMap = null;
ilios.common.picker.director.directorPickerModel = null;
ilios.common.picker.director.directorSelectListElement = null;

ilios.common.picker.director.createSelectedElementForUserModel = function (userModel) {
    var element = document.createElement('li');
    var textNode = document.createTextNode(userModel.getFormattedName(ilios.utilities.UserNameFormatEnum.LAST_FIRST));

    element.iliosModel = userModel;
    element.appendChild(textNode);
    // tooltip
    element.setAttribute('title', userModel.getEmailAddress());

    return element;
};

ilios.common.picker.director.directorAutoCompleteFilterer = function (queryString, fullResponse,
                                                                      parsedResponse, callback,
                                                                      autoCompleter) {
    var len = parsedResponse.results.length;
    var selectedList = document.getElementById(autoCompleter.target);
    var filteredResults = [];
    var i = 0;
    var userModel = null;
    var populateUserMap = (ilios.common.picker.director.idUserMap.length === 0);

    for (; i < len; i++) {
        userModel = new UserModel(parsedResponse.results[i]);

         userModel.setLastName(parsedResponse.results[i].last_name);
         userModel.clearDirtyState();

        if (populateUserMap) {
            ilios.common.picker.director.idUserMap[userModel.getDBId()] = userModel;
        }

        if (! ilios.utilities.searchListElementForModel(selectedList, userModel)) {
            filteredResults.push(parsedResponse.results[i]);
        }
    }

    parsedResponse.results = filteredResults;

    return parsedResponse;
};

ilios.common.picker.director.directorAutoCompleteFormatter = function (data,
                                                                       queryString, resultMatch,
                                                                         autoCompleter) {
    var rhett = '<span uid="' + data.user_id + '" title="' + data.email + '">';

    rhett += ilios.utilities.createFormattedUserName(data.first_name,
                                                      data.middle_name,
                                                      data.last_name, 0);

    rhett += '</span>';

    return rhett;
};
