/*
 * DEPENDENCY: YUI lib (at least YAHOO.util and its dependencies)
 * DEPENDENCY: DHTMLX Scheduler
 * DEPENDENCY: scripts/ilios_ui.js
 * DEPENDENCY: scripts/ilios_utilities.js
 */

// This data source is not used for account management - only permissions (because the use case
//      for user searching is different - the former wants to include disabled users, the latter -
//      not)
ilios.management.userDataSource = new YAHOO.util.XHRDataSource(controllerURL + 'searchEnabledUsers');
ilios.management.userDataSource.responseType = YAHOO.util.XHRDataSource.TYPE_XML;
ilios.management.userDataSource.responseSchema
    = {resultNode: "Result",
        fields: ["user_id", "last_name", "first_name", "middle_name", "phone", "email",
                 "added_via_ilios", "uc_uid", "other_id", "primary_school_id"]};

ilios.management.appendContinueOrCancelButtons = function (parentContainer, continueAction,
    cancelAction, continueIdStr, cancelI18NStr) {
    var Event = YAHOO.util.Event;
    var container = document.createElement('div');
    var element = document.createElement('button');

    container.setAttribute('style', 'position: absolute; right: 16px; bottom: 9px;');

    if (continueAction != null) {
        element.setAttribute('id', continueIdStr)
        element.innerHTML = ilios_i18nVendor.getI18NString('general.terms.continue');
        Event.addListener(element, 'click', function () {
            continueAction();
        });
        ilios.dom.setElementEnabled(element, false);
        container.appendChild(element);

        element = document.createElement('button');
    }

    element.innerHTML = ilios_i18nVendor.getI18NString(cancelI18NStr);
    Event.addListener(element, 'click', function () {
        cancelAction();
    });
    container.appendChild(element);

    parentContainer.appendChild(container);
};

ilios.management.displayOptionAsSelected = function (selectedIdStr) {
    var ulElement = document.getElementById('option_links_list');
    var children = ulElement.children;
    var liElement = null;

    for (var i = 0; i < children.length; i++) {
        liElement = children[i];

        if (selectedIdStr == liElement.getAttribute('id')) {
            ilios.management.setEnableForAElement(liElement.children[0], false);
        }
        else {
            ilios.management.setEnableForAElement(liElement.children[0], true);
        }
    }
};

/**
 * @method ilios.management.setEnableForAElement
 * Toggles a given link between "enabled" and "disabled".
 * ACHTUNG! Disabling a given link will remove it's href attribute. Re-enabling the link won't bring it back.
 * @param {HTMLElement} aElement The link.
 * @param {Boolean} enabled Set to TRUE to enable the link, or FALSE to disable it.
 * @todo What a horrendous way to (temporarily) "disable" a link. Find a more benign method to handle this. [ST 2013/07/22]
 */
ilios.management.setEnableForAElement = function (aElement, enabled) {
    var el = new YAHOO.util.Element(aElement);
    if (enabled) {
        if (aElement.getAttribute('href') == null) {
            aElement.setAttribute('href', '');
        }
        el.removeClass('disabled_a');
    } else {
        aElement.removeAttribute('href');
        el.addClass('disabled_a');
    }
};

ilios.management.getSchoolForId = function (schoolId) {
    var length = ilios.management.schoolTree.length;
    var school = null;

    for (var i = 0; i < length; i++) {
        school = ilios.management.schoolTree[i];

        if (school.school_id == schoolId) {
            return ilios_i18nVendor.getI18NString('general.phrases.school_of') + ' ' + school.title;
        }
    }

    return ilios_i18nVendor.getI18NString('general.phrases.unknown_school');
};
