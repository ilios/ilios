/**
 * Defines the following namespaces:
 *
 * ilios.management.user_accounts
 *
 * Dependencies:
 *
 * YUI lib (at least YAHOO.util and its dependencies)
 * DHTMLX Scheduler
 * scripts/ilios_ui.js
 * scripts/ilios_utilities.js
 */

ilios.namespace('management.user_accounts');

ilios.management.user_accounts.programmaticallyChangingCheckboxes = false;
ilios.management.user_accounts.programCohortSelectedNode = null;
ilios.management.user_accounts.userBeingModifiedIsDisabled = false;

ilios.management.user_accounts.manageLoginCredentials = true;
ilios.management.user_accounts.passwordRequired = true;

ilios.management.user_accounts.currentUserModel = null;

ilios.management.user_accounts.startUserAccountsWorkflow = function () {
    ilios.management.displayOptionAsSelected('users_li');

    if ((ilios.management.cohortlessUserCount > 0)
                                        || (ilios.management.syncExceptionsCount > 0)) {
        ilios.management.user_accounts.buildAlertsDisplayDOM();
    }
    else {
        ilios.management.user_accounts.showSearchOnlyDOM();
    }
};

//get the uid options set in the config file
ilios.management.user_accounts.uidOptions = ilios.global.readJsonFromDom('uidOptions');

/**
 * @see from ilios.ui.handleProgramCohortSelectionDialogDisplay()
 *
 * @param cohort an object returned by the controller method getCohortProgramTreeContent
 * @param programTitle
 * @return an instance of a model -- this instance will be attached to the tree node so that it may
 *                 be referenced later
 */
ilios.ui.buildNodeModel = function (cohort, programTitle) {
    var rhett = new ProgramCohortModel(programTitle, cohort.title, cohort.cohort_id,
                                       cohort.program_year_id, cohort.start_year, null);

    rhett.setProgramShortTitle(cohort.program_short_title);
    rhett.setProgramDuration(cohort.program_duration);
    rhett.setCurrentEnrollment(cohort.enrollment);

    return rhett;
};

/**
 * @see ilios.ui.handleProgramCohortSelectionDialogDisplay()
 */
ilios.ui.handleProgramCohortDialogSelection = function (clickObject) {
    var node = clickObject.node;
    var element = document.getElementById('ua_assign_cohortless_button');

    if (node.children.length > 0) {
        if (ilios.management.user_accounts.programCohortSelectedNode == null) {
            element.setAttribute('disabled', 'true');
        }

        node.enableHighlight = false;

        return false;
    }

    if (document.getElementById('ua_cohortless_user_list').childNodes.length > 1) {
        element.removeAttribute('disabled');
    }

    ilios.ui.programCohortDialogTreeView.onEventToggleHighlight(clickObject);

    node.enableHighlight = true;

    ilios.management.user_accounts.programCohortSelectedNode = node;

    return true;
};

/**
 * Creates markup for displaying actionable user accounts and populates them with data.
 * @private
 * @see ilios.management.user_accounts.startUserAccountsWorkflow()
 */
ilios.management.user_accounts.buildAlertsDisplayDOM = function () {
    var Dom = YAHOO.util.Dom, Event = YAHOO.util.Event;
    var container = Dom.get('management_center_content');
    var element = document.createElement('h3');
    var sectionContainer = null;


    ilios.utilities.removeAllChildren(container);

    Dom.addClass(element, 'mc_title');
    element.innerHTML = ilios_i18nVendor.getI18NString('management.user_accounts.set_title');
    container.appendChild(element);

    subContainer = document.createElement('div');
    subContainer.setAttribute('class', 'admin-tools');
    element = document.createElement('a');
    element.setAttribute('class', 'small radius button');
    element.setAttribute('href', '');
    element.setAttribute('onclick', 'return false;');
    Event.addListener(element, 'click', function () {
        ilios.management.user_accounts.showSearchOnlyDOM();
        return false;
    });
    element.innerHTML = ilios_i18nVendor.getI18NString('management.user_accounts.go_to_search');
    subContainer.appendChild(element);
    container.appendChild(subContainer);

    // if applicable then build and populate markup for user sync exception lists
    if (0 < ilios.management.syncExceptionsCount) {
        // section container
        sectionContainer = document.createElement('div');
        Dom.addClass(sectionContainer, 'mc_section_container');
        container.appendChild(sectionContainer);
        element = document.createElement('div'); // description
        Dom.addClass(element, 'mc_section_description');
        Dom.setAttribute(element, 'id', 'ua_sync_exception_section_description');
        element.innerHTML = ilios_i18nVendor.getI18NString('management.user_accounts.sync_exception_description_pre')
                            + " <em>" + ilios.management.syncExceptionsCount + "<\/em> "
                            + ilios_i18nVendor.getI18NString('management.user_accounts.sync_exception_description_post');
        sectionContainer.appendChild(element);

        // container(s) for unprocessed sync exceptions
        var syncExceptionsContainer = document.createElement('div');
        syncExceptionsContainer.setAttribute('id', 'ua_sync_exceptions_container');
        syncExceptionsContainer.appendChild(ilios.management.user_accounts.generateActionRequiredSectionTitleNode());
        sectionContainer.appendChild(syncExceptionsContainer);
        element = document.createElement('div'); // inner container element
        element.setAttribute('id', 'ua_sync_exceptions');
        Dom.addClass(element, 'mc_section_body_container');
        syncExceptionsContainer.appendChild(element);
        ilios.ui.renderIndeterminateInView(element);

        // holding tank for sync exceptions that have actions staged for processing
        var processedExceptionsContainer = document.createElement('div');
        processedExceptionsContainer.setAttribute('id', 'ua_processed_sync_exceptions');
        container.appendChild(processedExceptionsContainer);

        // container(s) for queued action items to be taken on sync exceptions
        var queuedActionsContainer = document.createElement('div');
        queuedActionsContainer.setAttribute('id', 'ua_queued_actions_container');
        sectionContainer.appendChild(queuedActionsContainer);
        queuedActionsContainer.appendChild(ilios.management.user_accounts.generateQueuedActionsSectionTitleNode());
        element = document.createElement('div'); // inner container element
        element.setAttribute('id', 'ua_queued_actions');
        Dom.addClass(element, 'mc_section_body_container');
        queuedActionsContainer.appendChild(element);

        ilios.utilities.appendClearingDivToContainer(sectionContainer);

        // form button.
        // when clicked this triggers a XHR request, posting the queued-up action items to the server.
        var processQueuedActionsBtn = document.createElement('button');
        processQueuedActionsBtn.disabled = true;
        processQueuedActionsBtn.setAttribute('id', 'ua_process_queued_actions_btn');
        processQueuedActionsBtn.innerHTML = ilios_i18nVendor.getI18NString('management.user_accounts.process_accounts_button');
        sectionContainer.appendChild(processQueuedActionsBtn);
        Event.on(processQueuedActionsBtn, 'click', function () {
                this.disabled = true; // disable the clicked button
                ilios.management.user_accounts.processActionQueueOnClick(this);
                return false;
            }
        );
        ilios.utilities.appendClearingDivToContainer(sectionContainer);

        // populates the exceptions list with user data
        ilios.management.transaction.loadUsersWithSyncExceptions();


    }
    // if applicable then build and populate markup for cohortless user list
    if (ilios.management.cohortlessUserCount > 0) {
        sectionContainer = document.createElement('div');
        YAHOO.util.Dom.addClass(sectionContainer, 'mc_section_container');
        container.appendChild(sectionContainer);

        var linkElement = document.createElement('a');

        element = document.createElement('div');
        Dom.addClass(element, 'mc_section_description');
        element.innerHTML = ilios_i18nVendor.getI18NString('management.user_accounts.cohortless_description') + ':';
        sectionContainer.appendChild(element);

        sectionContainer.appendChild(ilios.management.user_accounts.generateActionRequiredSectionTitleNode());

        element = document.createElement('div');
        Dom.addClass(element, 'mc_section_body_container');
        linkElement.setAttribute('href', '#');
        linkElement.setAttribute('onclick', 'return false;');
        linkElement.setAttribute('style', 'font-size: 10pt; font-weight: bold;');
        linkElement.innerHTML = '' + ilios.management.cohortlessUserCount
                                        + ' '
                                        + ilios_i18nVendor.getI18NString('management.user_accounts.cohortless_link_suffix');
        Event.addListener(linkElement, 'click',
                           function () {ilios.management.user_accounts.addressCohortlessUsers();
                                         return false;});
        element.appendChild(linkElement);

        sectionContainer.appendChild(element);
    }

    // temporarily alter the style of the master container which other workflows rely upon or
    // hackily through in a fixed size div at the bottom.. the lesser seems less evil
    element = document.createElement('div');
    element.setAttribute('class', 'indeterminate_progress');
    element.setAttribute('id', 'ua_indeterminate_progress');
    container.appendChild(element);
};

/**
 * @private
 * @see ilios.management.user_accounts.generateSectionTitleNode
 */
ilios.management.user_accounts.generateActionRequiredSectionTitleNode = function () {
    var title = '- ' + ilios_i18nVendor.getI18NString('management.user_accounts.action_required').toLowerCase() + '! -';
    return ilios.management.user_accounts.generateSectionTitleNode(title, 'alert');
};

/**
 * @private
 * @see ilios.management.user_accounts.generateSectionTitleNode
 */
ilios.management.user_accounts.generateQueuedActionsSectionTitleNode = function () {
    var title = '- ' + ilios_i18nVendor.getI18NString('management.user_accounts.selected_accounts').toLowerCase() + ' -';
    return ilios.management.user_accounts.generateSectionTitleNode(title, 'info');
};


/**
 * Creates a DOM node for a management console section title.
 * @param string title the title to display
 * @param cssClass the style to apply to the title element
 * @return HTMLElement the generated DOM node
 * @private
 */
ilios.management.user_accounts.generateSectionTitleNode = function (title, cssClass) {
    var element = document.createElement('div');
    var subElement = null;
    var spanElement = null;

    YAHOO.util.Dom.addClass(element, 'mc_section_title_container');

    subElement = document.createElement('div');
    YAHOO.util.Dom.addClass(subElement, 'title');
    YAHOO.util.Dom.addClass(subElement, cssClass);
    subElement.innerHTML = title;
    element.appendChild(subElement);
    return element;
};

/**
 *
 */
ilios.management.user_accounts.createRowForCohortlessUser = function (userModel) {
    var rhett = document.createElement('div');
    var element = null;
    var checkboxIdStr = 'cb_' + userModel.last_name + userModel.first_name + userModel.uc_uid;
    var label = document.createElement('label');

    rhett.setAttribute('style', 'margin-left: 9px; width: 100%;');
    rhett.isUserRow = true;

    rhett.checkbox = document.createElement('input');
    rhett.checkbox.setAttribute('type', 'checkbox');
    rhett.checkbox.setAttribute('id', checkboxIdStr);

    rhett.appendChild(rhett.checkbox);

    label.setAttribute('for', checkboxIdStr);

    element = document.createElement('span');
    element.setAttribute('style', 'margin-left: 6px;');
    element.innerHTML = ilios.management.user_accounts.getFullNameFromUserModel(userModel, true);
    label.appendChild(element);

    element = document.createElement('span');
    element.setAttribute('style', 'margin-left: 6px; color: #666666; font-size: 8pt;');
    element.innerHTML = '(' + userModel.uc_uid + ')';
    label.appendChild(element);

    rhett.appendChild(label);
    rhett.iliosModel = userModel;

    return rhett;
};

/**
 * Populates container element with a given list of user data.
 * @param array users a nested array of users with sync exceptions
 */
ilios.management.user_accounts.populateSyncExceptionsList = function (users) {
    var container = document.getElementById('ua_sync_exceptions');
    var element = null;

    ilios.utilities.removeAllChildren(container);
    for (var i = 0, n = users.length; i < n; i++) {
        element = ilios.management.user_accounts.createRowForUserWithSyncException(users[i]);
        container.appendChild(element);
    }
}



ilios.management.user_accounts.populateUserSearchResultList = function (users) {
    var container = document.getElementById('user_search_results_list');
    var element = null;
    var subElement = null;
    var length = users.length;

    ilios.utilities.removeAllChildren(container);

    for (var i = 0; i < length; i++) {
        element = ilios.management.user_accounts.createRowForSearchResultUser(users[i]);

        container.appendChild(element);
    }
};

ilios.management.user_accounts.createRowForSearchResultUser = function (user) {
    var Event = YAHOO.util.Event;
    var userModel = new UserManagementModel(user);
    var rhett = document.createElement('div');
    var element = document.createElement('div');
    var subElement = document.createElement('div');

    rhett.iliosModel = userModel;
    rhett.setAttribute('style', 'cursor: pointer; padding: 6px 12px;');

    element.setAttribute('style', 'float: left;');
    subElement.setAttribute('style', 'font-weight: bold; float: left;');
    subElement.innerHTML = userModel.getFormattedName(ilios.utilities.UserNameFormatEnum.LAST_FIRST);
    element.appendChild(subElement);


    if (! userModel.isEnabled()) {
        subElement = document.createElement('div');
        subElement.setAttribute('style', 'float: left; font-style: italic; padding-left: 10px; color: #e00');
        subElement.innerHTML = "(" + ilios_i18nVendor.getI18NString('general.terms.disabled').toLowerCase() + ")";
        element.appendChild(subElement);
    }
    rhett.appendChild(element);

    element = document.createElement('div');
    element.setAttribute('style', 'float: right; font-size: 8pt; color: #808080; width: 47%;');
    subElement = document.createElement('div');
    subElement.setAttribute('style', 'float: left;')

    subElement.innerHTML = userModel.getEmailAddress();
    element.appendChild(subElement);
    subElement = document.createElement('div');
    subElement.setAttribute('style', 'float: right;')
    subElement.innerHTML = ilios.management.getSchoolForId(userModel.getSchoolId());
    element.appendChild(subElement);

    ilios.utilities.appendClearingDivToContainer(element);
    rhett.appendChild(element);

    ilios.utilities.appendClearingDivToContainer(rhett);

    Event.addListener(rhett, 'mouseover', function (e) {
        (new YAHOO.util.Element(this)).setStyle('background-color', '#00FFFF');
    });
    Event.addListener(rhett, 'mouseout', function (e) {
        (new YAHOO.util.Element(this)).setStyle('background-color', null);
    });
    Event.addListener(rhett, 'click', function (e) {
        ilios.management.user_accounts.searchResultSelected(this);
    });

    return rhett;
};

// @private
ilios.management.user_accounts.searchResultSelected = function (clickedDiv) {
    var userModel = clickedDiv.iliosModel;
    ilios.management.user_accounts.buildUserAddAndRolesDOM(userModel);
};

ilios.management.user_accounts.populateCohortlessUserList = function (users) {
    var container = document.getElementById('ua_cohortless_user_list');
    var length = users.length;
    var subContainer = document.createElement('div');
    var element = document.createElement('a');
    var i;

    ilios.utilities.removeAllChildren(container);

    element.innerHTML = '+ ' + ilios_i18nVendor.getI18NString('management.user_accounts.select_all').toLowerCase();
    element.setAttribute('href', '');
    element.setAttribute('onclick', 'return false;');
    element.currentlySelected = false;
    YAHOO.util.Event.addListener(element, 'click', function () {
        var children = document.getElementById('ua_cohortless_user_list').childNodes;
        var actionString = null;
        var i, n;

        this.currentlySelected = (! this.currentlySelected);
        if (this.currentlySelected) {
            actionString = ilios_i18nVendor.getI18NString('management.user_accounts.select_none');
        } else {
            actionString = ilios_i18nVendor.getI18NString('management.user_accounts.select_all');
        }
        this.innerHTML = '+ ' + actionString.toLowerCase();

        for (i = 0, n = children.length; i < n; i++) {
            if (children[i].isUserRow) {
                children[i].checkbox.checked = this.currentlySelected;
            }
        }
    });

    subContainer.setAttribute('style', 'margin: 3px 0px 6px 3px;');
    subContainer.appendChild(element);

    container.appendChild(subContainer);

    for (i = 0; i < length; i++) {
        container.appendChild(ilios.management.user_accounts.createRowForCohortlessUser(users[i]));
    }
};

/**
 * Resets and re-populates the user sync list container and queued action container.
 */
ilios.management.user_accounts.rebuildSyncExceptionsDisplayDOM = function () {
    var Dom = YAHOO.util.Dom;
    var queuedActionsContainer = Dom.get('ua_queued_actions'),
        syncExceptionsContainer = Dom.get('ua_sync_exceptions'),
        processedSyncExceptionsContainer = Dom.get('ua_processed_sync_exceptions'),
        processQueuedActionsBtn = Dom.get('ua_process_queued_actions_btn'),

        syncExceptionsSectionDescription = Dom.get('ua_sync_exception_section_description');

    // clear out the list containers
    ilios.utilities.removeAllChildren(queuedActionsContainer);
    ilios.utilities.removeAllChildren(syncExceptionsContainer);
    ilios.utilities.removeAllChildren(processedSyncExceptionsContainer);

    // reset the process action items button to be disabled
    processQueuedActionsBtn.disabled = true;

    // update the description to accurately reflect on the updated # of remaining unresolved exceptions.
    syncExceptionsSectionDescription.innerHTML = ilios_i18nVendor.getI18NString('management.user_accounts.sync_exception_description_pre')
                        + " <em>" + ilios.management.syncExceptionsCount + "<\/em> "
                        + ilios_i18nVendor.getI18NString('management.user_accounts.sync_exception_description_post');

    // depending on whether there are any outstanding sync exceptions or not,
    // we load in the exceptions or display a "done-hansen" message
    if (ilios.management.syncExceptionsCount) {
        ilios.management.transaction.loadUsersWithSyncExceptions();
    } else {
        processQueuedActionsBtn.disabled = true;
        var elem = document.createElement('div');
        elem.setAttribute('style',
                           'margin-top: 40px; margin-left: 39%; font-weight: bold;');
        elem.innerHTML = ilios_i18nVendor.getI18NString('general.phrases.you_are_done');
        syncExceptionsContainer.appendChild(elem);
    }
}


/**
 * @param array userModel nested array of user data incl. sync exceptions
 * @return HTMLElement the rendered user data as markup
 *
 * @private
 * @see ilios.management.user_accounts.populateSyncExceptionsList()
 *
 */
ilios.management.user_accounts.createRowForUserWithSyncException = function (userModel) {
    var Dom =YAHOO.util.Dom, Event = YAHOO.util.Event;
    var rhett = document.createElement('div');
    var syncExceptionContainer = document.createElement('div');
    var optionsContainer = document.createElement('div');
    var optionContainer = null;
    var element = null;
    var updateCheckbox, ignoreCheckbox, disableCheckbox = null;


    // set the id of the container elem and add some class.
    rhett.setAttribute('id', 'ua_sync_exception_user_' + userModel.user_id);
    Dom.addClass(rhett, 'ua_sync_exception');

    // build and populate actionable options container
    optionsContainer.setAttribute('style', 'float: right; margin-right: 48px;');
    // add "update" checkbox
    if (userModel.update_option) {
        updateCheckbox = document.createElement('input');
        updateCheckbox.setAttribute('type', 'checkbox');
        updateCheckbox.setAttribute('id', 'ua_update_user_chkbox_' + userModel.user_id);
        updateCheckbox.value = 'update';
        Event.addListener(updateCheckbox, 'click', function () {
            this.checked = false; // reset checkbox
            ilios.management.user_accounts.addSyncExceptionToActionQueueOnClick(this);
            return false;
        });
        optionContainer = document.createElement('div');
        optionContainer.appendChild(updateCheckbox);
        element = document.createElement('label');
        element.setAttribute('for', updateCheckbox.getAttribute('id'));
        element.innerHTML = ilios_i18nVendor.getI18NString('management.user_accounts.update_account');
        optionContainer.appendChild(element);
        optionsContainer.appendChild(optionContainer);
    }
    // add "leave and ignore" checkbox
    ignoreCheckbox = document.createElement('input');
    ignoreCheckbox.setAttribute('type', 'checkbox');
    ignoreCheckbox.value = 'ignore';
    ignoreCheckbox.setAttribute('id', 'ua_ignore_user_chkbox_' + userModel.user_id);
    Event.addListener(ignoreCheckbox, 'click', function () {
        this.checked = false; // reset checkbox
        ilios.management.user_accounts.addSyncExceptionToActionQueueOnClick(this);
        return false;
    });
    optionContainer = document.createElement('div');
    optionContainer.appendChild(ignoreCheckbox);
    element = document.createElement('label');
    element.setAttribute('for', ignoreCheckbox.getAttribute('id'));
    element.innerHTML = ilios_i18nVendor.getI18NString('management.user_accounts.ignore_account');
    optionContainer.appendChild(element);
    optionsContainer.appendChild(optionContainer);
    // add "disable account" checkbox
    disableCheckbox = document.createElement('input');
    disableCheckbox.setAttribute('type', 'checkbox');
    disableCheckbox.value = 'disable';
    disableCheckbox.setAttribute('id', 'ua_disable_user_chkbox_' + userModel.user_id);
    Event.addListener(disableCheckbox, 'click', function () {
        this.checked = false; // reset checkbox
        ilios.management.user_accounts.addSyncExceptionToActionQueueOnClick(this);
        return false;
    });
    optionContainer = document.createElement('div');
    optionContainer.appendChild(disableCheckbox);
    element = document.createElement('label');
    element.setAttribute('for', disableCheckbox.getAttribute('id'));
    element.innerHTML = ilios_i18nVendor.getI18NString('management.user_accounts.disable_account');
    optionContainer.appendChild(element);
    optionsContainer.appendChild(optionContainer);

    // build and populate user data container element
    syncExceptionContainer.setAttribute('style', 'float: left; margin-left: 48px; color: #800000; padding-top: 9px;');
    element = document.createElement('span');
    element.setAttribute('style', 'margin-right: 4px; font-weight: bold; font-size: 10pt;');
    element.innerHTML = ilios.management.user_accounts.getFullNameFromUserModel(userModel, false);
    syncExceptionContainer.appendChild(element);
    element = document.createElement('a');
    element.setAttribute('href', '#');
    element.setAttribute('id', 'ua_sync_exception_user_' + userModel.user_id);
    element.setAttribute('style', 'font-size: 8pt;');
    Event.addListener(element, 'click', function () {
        ilios.management.user_accounts.showUserDetailOnClick(this);
        return false;
    });
    element.innerHTML = '(' + ilios_i18nVendor.getI18NString('management.user_accounts.click_details').toLowerCase() + ')';
    syncExceptionContainer.appendChild(element);

    rhett.appendChild(syncExceptionContainer);
    rhett.appendChild(optionsContainer);
    ilios.utilities.appendClearingDivToContainer(rhett);

    // this is important!
    // attach the user data to the container element
    rhett.iliosModel = userModel;

    return rhett; // return the element
}

/**
 * Event handler callback function.
 * Creates and displays markup for an action item based on a given user sync exception for processing.
 * @param Input inputElem the element that triggered this event
 */
ilios.management.user_accounts.addSyncExceptionToActionQueueOnClick = function (inputElem) {
    var Dom = YAHOO.util.Dom, Event = YAHOO.util.Event;
    var action = inputElem.value;
    // extract the user id of the associated user sync exception from the
    // given input element but tokenizing its element-id (underscore is the delimiter)
    // the pattern is XXX_XXX_XXX_..._NN, where NN representis the user id
    var userId = inputElem.getAttribute('id').split('_').pop();
    var processedSyncExceptionsContainer, queuedActionsContainer, queuedActionContainer = null;
    var element = null;
    // get the user model and associated container element
    var syncExceptionContainer = document.getElementById('ua_sync_exception_user_' + userId);
    var userModel = syncExceptionContainer.iliosModel;
    var actionItemElems = null;
    var processQueuedActionsBtn = Dom.get("ua_process_queued_actions_btn");
    var actionLabelValue, actionLabelKey = null;

    // build a new action item element and append it to the list of queued action items
    queuedActionsContainer = document.getElementById('ua_queued_actions');
    queuedActionContainer = document.createElement('div');
    Dom.addClass(queuedActionContainer, 'ua_queued_action');
    queuedActionContainer.setAttribute('id', 'ua_queued_action_' + userId);
    element = document.createElement('span'); // create element for action item
    Dom.addClass(element, 'ua_queued_action_user');
    element.innerHTML = ilios.management.user_accounts.getFullNameFromUserModel(userModel, false);
    queuedActionContainer.appendChild(element);

    element = document.createElement('a');
    element.href = '#';
    element.setAttribute('onclick', 'return false;');
    element.innerHTML = 'x';
    element.setAttribute('id', 'ua_queued_action_remove_btn_' + userId);
    Dom.addClass(element, 'ua_queued_action_remove_btn');
    Event.addListener(element, 'click', function () {
        ilios.management.user_accounts.removeSyncExceptionFromActionQueueOnClick(this);
        return false;
    });

    queuedActionContainer.appendChild(element);

    element = document.createElement('span');

    actionLabelKey = 'management.user_accounts.actions.' + action;
    actionLabelValue = ilios_i18nVendor.getI18NString(actionLabelKey);
    element.innerHTML = "(" + actionLabelValue +(")");
    Dom.addClass(element, 'ua_queued_action_name');
    queuedActionContainer.appendChild(element);

    ilios.utilities.appendClearingDivToContainer(queuedActionContainer);

    queuedActionContainer.iliosModel = syncExceptionContainer.iliosModel;
    queuedActionContainer.iliosQueuedAction = action;

    queuedActionsContainer.appendChild(queuedActionContainer);

    // remove the user container from the list of actionable sync exceptions
    // and attach it to the list of processed sync exceptions
    processedSyncExceptionsContainer = document.getElementById('ua_processed_sync_exceptions');
    syncExceptionContainer = syncExceptionContainer.parentNode.removeChild(syncExceptionContainer);
    processedSyncExceptionsContainer.appendChild(syncExceptionContainer);

    // disable the "process actions button", if applicable
    actionItemElems = Dom.getElementsByClassName('ua_queued_action', 'div', 'ua_queued_actions');
    if (actionItemElems.length) {
        processQueuedActionsBtn.disabled = false;
    }
}

/**
 * Event handler callback function.
 * Removes an action item from the queue of sync exceptions to be processed, and re-attaches
 * the corresponding user sync exception element to the list of un-processed items.
 * @param HTMLElement inputElem the element that triggered this event
 */
ilios.management.user_accounts.removeSyncExceptionFromActionQueueOnClick = function (inputElem) {
    var Dom = YAHOO.util.Dom;
    var userId = inputElem.getAttribute('id').split('_').pop();
    var syncExceptionsContainer = Dom.get('ua_sync_exceptions');
    var syncExceptionContainer = Dom.get('ua_sync_exception_user_' + userId);
    var processedSyncExceptionsContainer = Dom.get('ua_process_sync_exceptions');
    var queuedActionContainer = Dom.get('ua_queued_action_' + userId);
    var actionItemElems = null;
    var processQueuedActionsBtn = Dom.get("ua_process_queued_actions_btn");

    // remove the action item from the queue
    queuedActionContainer = queuedActionContainer.parentNode.removeChild(queuedActionContainer);

    // remove the corresponding user sync exception element from the processed bucket
    // and re-attached to the list of un-processed items.
    syncExceptionContainer = syncExceptionContainer.parentNode.removeChild(syncExceptionContainer);
    syncExceptionsContainer.appendChild(syncExceptionContainer);

    // disable the "process actions button", if applicable
    actionItemElems = Dom.getElementsByClassName('ua_queued_action', 'div', 'ua_queued_actions');
    if (! actionItemElems.length) {
        processQueuedActionsBtn.disabled = true;
    }
}

/**
 * Event handler callback function.
 * Triggers the processing of queued action items for user sync exceptions when the user clicks
 * on the "Process Accounts" button.
 * @private
 * @param Input clickedBtn
 * @see ilios.management.user_accounts.getQueuedActionItemsFromDOM()
 * @see ilios.management.transaction.processActionItems()
 */
ilios.management.user_accounts.processActionQueueOnClick = function (clickedBtn) {
    var Dom = YAHOO.util.Dom;
    var hasAtLeastOneUpdateAction = false,
        msg = '',
        element = null,
        userId = null;


    // get action items
    var actionItems = ilios.management.user_accounts.getQueuedActionItemsFromDOM();
    // check if at least one "update" action is amongst the retrieved action items.
    for (userId in actionItems) {
        if (actionItems.hasOwnProperty(userId) && "update" == actionItems[userId]) {
            hasAtLeastOneUpdateAction = true;
            break;
        }
    }
    if (hasAtLeastOneUpdateAction) { // CYA by displaying confirmation dialogue
        msg = ilios_i18nVendor.getI18NString('management.user_accounts.sync_update_action_warn_1');
        msg += '<br/><br/>';
        msg += ilios_i18nVendor.getI18NString('management.user_accounts.sync_update_action_warn_2');
        msg += '<br/><br/>';
        msg += ilios_i18nVendor.getI18NString('management.user_accounts.sync_update_action_warn_3');

        ilios.alert.inform('<center><strong>' + msg + '</strong></center>',
            ilios_i18nVendor.getI18NString('general.terms.continue'),
            function (dialogClickSource, actionItems) {
                this.hide();
                ilios.management.transaction.processActionItems(actionItems);
            },
            actionItems,
            ilios_i18nVendor.getI18NString('general.terms.cancel'),
            function () {
                clickedBtn.disabled = false;
                this.hide();
            }
        );
    } else {
        element = Dom.get('ua_indeterminate_progress');
        Dom.setStyle('ua_indeterminate_progress', 'display', 'block');
        ilios.management.transaction.processActionItems(actionItems);
    }
}

/**
 * Retrieves a map of staged action items for user sync exceptions by traversing the DOM.
 * @private
 * @return Object an object of user-id/action pairs as property-name/values.
 */
ilios.management.user_accounts.getQueuedActionItemsFromDOM = function () {
    var Dom = YAHOO.util.Dom;
    var rhett = {},
        actionItems = [],
        i, n = 0;

    actionItemElems = Dom.getElementsByClassName('ua_queued_action', 'div', 'ua_queued_actions');

    for (i = 0, n = actionItemElems.length; i < n; i++) {
        rhett[actionItemElems[i].iliosModel.user_id] = actionItemElems[i].iliosQueuedAction;
    }
    return rhett;
}

// @private
ilios.management.user_accounts.addressCohortlessUsers = function () {
    var Event = YAHOO.util.Event;
    var container = document.getElementById('management_center_content');
    var element = document.createElement('div');

    ilios.utilities.removeAllChildren(container);

    element.setAttribute('style', 'font-weight: bold; margin: 9px 6px 15px;');
    element.innerHTML
                   = ilios_i18nVendor.getI18NString('management.user_accounts.cohort_assign_title');
    container.appendChild(element);

    element = document.createElement('div');
    element.setAttribute('style', 'overflow: auto; margin: 54px 6px 40px 44px; border: 1px solid #9393A3;'
                                           + 'float: left; width: 350px; height: 375px;'
                                           + 'background-color: #F3F6FF; overflow-x: hidden;');
    element.setAttribute('id', 'ua_cohortless_user_list');
    container.appendChild(element);
    ilios.ui.renderIndeterminateInView(element);

    ilios.management.transaction.loadCohortlessUsers();


    element = document.createElement('div');
    element.setAttribute('style', 'overflow: auto; margin: 54px 44px 40px 0px; border: 1px solid #9393A3;'
                                           + 'float: right; width: 350px; height: 375px;'
                                           + 'background-color: #F3F6FF;');
    element.setAttribute('id', 'ua_assignment_cohort_tree_list');
    element.setAttribute('class', 'ygtv-highlight');
    container.appendChild(element);

    ilios.management.user_accounts.programCohortSelectedNode = null;

    ilios.ui.programCohortDialogTreeDOMGenerator(element);
    ilios.ui.programCohortDialogTreeView.singleNodeHighlight = true;

    // kick off the ajax load
    ilios.ui.handleProgramCohortSelectionDialogDisplay(null, null);

    ilios.utilities.appendClearingDivToContainer(container);

    element = document.createElement('button');
    element.innerHTML
                  = ilios_i18nVendor.getI18NString('management.user_accounts.cohort_assign_button');
    element.setAttribute('id', 'ua_assign_cohortless_button');
    element.setAttribute('disabled', 'true');
    element.setAttribute('style', 'position: absolute; bottom: 12px; right: 140px; width: 200px;');
    Event.addListener(element, 'click', function () {
        ilios.management.user_accounts.performCohortAssociations();
    });
    container.appendChild(element);

    element = document.createElement('button');
    element.innerHTML = ilios_i18nVendor.getI18NString('general.terms.cancel');
    element.setAttribute('style', 'position: absolute; bottom: 12px; right: 44px; width: 90px;');
    Event.addListener(element, 'click', function () {
        ilios.management.user_accounts.startUserAccountsWorkflow();
    });
    container.appendChild(element);

    element = document.createElement('div');
    element.setAttribute('class', 'indeterminate_progress');
    element.setAttribute('id', 'ua_indeterminate_progress');
    element.setAttribute('style', 'position: absolute; left: 12px; bottom: 12px; display: none;');
    container.appendChild(element);

    element = document.createElement('div');
    element.setAttribute('id', 'ua_assignment_result_status');
    element.setAttribute('class', 'read_only_data');
    element.setAttribute('style', 'position: absolute; right: 12px; top: 12px;');
    container.appendChild(element);
};


/**
 * Resets the user details panel.
 * @private
 * @see ilios.management.user_accounts.showUserDetail()
 */
ilios.management.user_accounts.resetUserDetails = function () {
    // clear out the following elements
    var elementIds = ['ua_detail_name', 'ua_detail_email', 'ua_detail_uid',
                      'ua_detail_school', 'ua_detail_ilios_roles',
                      'ua_detail_cohort_name', 'ua_detail_directory_uid',
                      'ua_detail_directory_email', 'ua_detail_sync_exceptions_messages'];
    var element = null;
    for (var i = 0, n = elementIds.length; i < n; i++) {
        element = document.getElementById(elementIds[i]);
        if (element) {
            element.innerHTML = '';
        }
    }
    // hide the following elements
    elementIds = ['ua_detail_directory_email', 'ua_detail_directory_uid',
                  'ua_detail_sync_exceptions'];
    YAHOO.util.Dom.setStyle(elementIds, 'display', 'none');
}


/**
 * Event Handler callback function.
 * Starts the process of populating and displaying details for a given user with sync exceptions.
 * @private
 * @param HTMLElement linkElement the element that triggered this event
 */

ilios.management.user_accounts.showUserDetailOnClick = function (linkElement) {
    var userId = linkElement.getAttribute('id').split('_').pop();
    var syncExceptionContainer = document.getElementById('ua_sync_exception_user_' + userId);
    ilios.management.user_accounts.showUserDetail(syncExceptionContainer.iliosModel);
}

/**
 * Populates the user details pane for a given user with sync exceptions, and opens it.
 * @param Object userModel encapsulates the user data and associated sync exceptions
 * @private
 */
ilios.management.user_accounts.showUserDetail = function (userModel) {
    var element = document.getElementById('ua_detail_name');
    var roleModel = null;
    var roleString = '';
    var i,n = 0;
    var exceptionModel = null;
    var exceptionMsgs = [];
    var exceptionMsg = null;

    // reset panel
    ilios.management.user_accounts.resetUserDetails();

    // populate panel with user attributes as Ilios has them on file
    element.innerHTML = ilios.management.user_accounts.getFullNameFromUserModel(userModel, false);
    element = document.getElementById('ua_detail_uid');
    element.innerHTML = userModel.uc_uid;
    element = document.getElementById('ua_detail_email');
    element.innerHTML = userModel.email;
    element = document.getElementById('ua_detail_school');
    element.innerHTML = userModel.school_name;
    element = document.getElementById('ua_detail_ilios_roles');
    if (0 < userModel.roles.length) {
        element.innerHTML = userModel.roles.join(', ');
    } else {
        element.innerHTML = ilios_i18nVendor.getI18NString('general.terms.none');
    }
    element = document.getElementById('ua_detail_cohort_name');
    element.innerHTML = userModel.cohort_name;

    // print sync exceptions and display mismatches as applicable
    if (0 < userModel.exceptions.length) {
        YAHOO.util.Dom.setStyle('ua_detail_sync_exceptions', 'display', 'block');
        for (i = 0, n = userModel.exceptions.length; i < n; i++ ) {
            exceptionModel = userModel.exceptions[i];
            exceptionMsg = ilios_i18nVendor.getI18NString('management.user_accounts.sync_exceptions.' + exceptionModel.exception_code);
            exceptionMsgs.push(exceptionMsg);
            if ('email' == exceptionModel.mismatched_property_name || 'uid' == exceptionModel.mismatched_property_name) {
                element = document.getElementById('ua_detail_directory_' + exceptionModel.mismatched_property_name);
                element.innerHTML = '(' + ilios_i18nVendor.getI18NString('management.user_accounts.details.directory') + ' ' + exceptionModel.mismatched_property_value + ')';
                YAHOO.util.Dom.setStyle(element, 'display', 'inline');
            }
        }
        element = document.getElementById('ua_detail_sync_exceptions_messages');
        element.innerHTML = exceptionMsgs.join('<br/>');
    }

    ilios.management.user_accounts.accountDetailsDialog.showDialogPane(); // show the darned thing
};

// @private
ilios.management.user_accounts.performCohortAssociations = function () {
    var allCohortlessUsers = document.getElementById('ua_cohortless_user_list');
    var children = allCohortlessUsers.childNodes;
    var assignedUsers = new Array();
    var selectedCohortId
                = ilios.management.user_accounts.programCohortSelectedNode.iliosModel.getCohortId();

    for (var i = 0; i < children.length; i++) {
        if ((children[i].iliosModel != null) && children[i].checkbox.checked) {
            assignedUsers.push(children[i].iliosModel);
        }
    }

    if (assignedUsers.length > 0) {
        var element = new YAHOO.util.Element(document.getElementById('ua_indeterminate_progress'));

        element.setStyle('display', 'block');

        ilios.management.transaction.performCohortAssociations(assignedUsers, selectedCohortId);
    }
};

// @private
ilios.management.user_accounts.getFullNameFromUserModel = function (userModel, lastNameFirst) {
    if (lastNameFirst) {
        return userModel.last_name + ', ' + userModel.first_name
                        + ((userModel.middle_name != '') ? (' ' + userModel.middle_name) : '');
    }
    else {
        return userModel.first_name
                  + ((userModel.middle_name != '') ? (' ' + userModel.middle_name + ' ') : ' ')
                  + userModel.last_name;
    }
};

// @private
ilios.management.user_accounts.showSearchOnlyDOM = function () {
    var Event = YAHOO.util.Event;
    var container = document.getElementById('management_center_content');
    var element = document.createElement('div');
    var subElement = null;
    var searchContainer = document.createElement('div');
    var str;

    ilios.utilities.removeAllChildren(container);

    element.setAttribute('style', 'font-weight: bold; margin: 9px 6px 15px;');
    element.innerHTML = ilios_i18nVendor.getI18NString('management.user_accounts.set_title');
    container.appendChild(element);

    searchContainer.setAttribute('style', 'margin: 9px 36px 42px;');

    element = document.createElement('div');
    element.innerHTML = ilios_i18nVendor.getI18NString('management.user_accounts.find_user') + ':';
    searchContainer.appendChild(element);

    element = document.createElement('div');
    element.setAttribute('style',
                         'text-align: right; font-size: 8pt; margin-bottom: 3px; margin-top: 12px;');
    subElement = document.createElement('a');
    subElement.setAttribute('href', '');
    subElement.setAttribute('onclick', 'return false;');
    Event.addListener(subElement, 'click', function () {
        ilios.management.user_accounts.emptySearchDialog();
        return false;
    });
    subElement.innerHTML = ilios_i18nVendor.getI18NString('general.phrases.search.clear');
    element.appendChild(subElement);
    searchContainer.appendChild(element);

    element = document.createElement('div');
    element.setAttribute('style', 'height: 6px; background-color: #CC6600;');
    searchContainer.appendChild(element);

    element = document.createElement('div');
    element.setAttribute('style',
                         'border: 1px solid #808080; padding-top: 6px; background-color: #EDF5FF; '
                            + 'position: relative;');
    str = ilios_i18nVendor.getI18NString('general.phrases.search.hint');
    subElement = document.createElement('input');
    subElement.setAttribute('id', 'user_search_terms');
    subElement.setAttribute('type', 'text');
    subElement.setAttribute('style', 'width: 606px; margin: 1px 9px 0px; padding-left: 3px;');
    subElement.setAttribute('placeholder', str);
    Event.addListener(subElement, 'keypress', ilios.management.user_accounts.handleSearchFieldInput);
    element.appendChild(subElement);
    subElement = document.createElement('div');
    subElement.setAttribute('class', 'search_icon_button');
    subElement.setAttribute('style', 'margin-right: 62px;');
    Event.addListener(subElement, 'click', function () {
        ilios.management.transaction.performUserSearch(document.getElementById('user_search_terms').value);
        return false;
    });
    element.appendChild(subElement);
    ilios.utilities.appendClearingDivToContainer(element);
    subElement = document.createElement('div');
    subElement.setAttribute('id', 'user_search_results_list');
    subElement.setAttribute('style', 'margin: 6px 3px 0 3px; height: 330px; overflow: auto;');
    element.appendChild(subElement);

    searchContainer.appendChild(element);

    container.appendChild(searchContainer);

    element = document.createElement('div');
    element.setAttribute('id', 'user_search_status');
    element.setAttribute('style',
                         'position: absolute; left: 12px; bottom: 10px; height: 18px; '
                                + 'font-weight: bold; font-size: 9pt; color: #aa3241;');
    container.appendChild(element);

    element = document.getElementById('user_search_terms');
    element.focus();
};

// @private
ilios.management.user_accounts.emptySearchDialog = function () {
    var element = document.getElementById('user_search_terms');

    element.value = '';

    element = document.getElementById('user_search_results_list');
    ilios.utilities.removeAllChildren(element);

    element = document.getElementById('user_search_terms');
    element.focus();
};

ilios.management.user_accounts.handleSearchFieldInput = function (event) {
    var charCode = event.keyCode ? event.keyCode
                                 : event.which
                                        ? event.which
                                        : event.charCode;

    if (charCode == 13) {
        var elem = document.getElementById('user_search_terms');

        ilios.management.transaction.performUserSearch(elem.value);

        event.cancelBubble = true;
        event.returnValue = false;

        return false;
    }

    return true;
};

ilios.management.user_accounts.createNewAccount = function (event, args) {
    ilios.management.user_accounts.buildUserAddAndRolesDOM(null);
    this.hide();
};

ilios.management.user_accounts.buildUserAddAndRolesDOM = function (userModel) {
    var Event = YAHOO.util.Event;
    var addingUser = (userModel == null);
    var userIsDisabled = ((! addingUser) && (! userModel.isEnabled()));
    var syncIgnored = ((! addingUser) && (userModel.isIgnoredFromSync()));
    var container = document.getElementById('management_center_content');
    var subContainer = document.createElement('div');
    var element = document.createElement('div');
    var labelElement = null;
    var inputElement = null;
    var contentContainer = document.createElement('div');
    var i18nStr = null;
    var i = 0;

    if (! addingUser) {
        // KLUDGE
        // store the current user model in a namespace-wide variable,
        // so that it can be accessed by the various popup dialogs
        // on this view.
        // this totally breaks encapsuling.
        // ideally, the current user model would be passed to
        // the the dialogs explicitly
        ilios.management.user_accounts.currentUserModel = userModel;
    }

    ilios.management.user_accounts.userBeingModifiedIsDisabled = userIsDisabled;
    ilios.management.user_accounts.userBeingModifiedIsIgnoredFromSync = syncIgnored;
    ilios.utilities.removeAllChildren(container);

    element.setAttribute('style', 'font-weight: bold; margin: 9px 6px 15px;');
    element.innerHTML = ilios_i18nVendor.getI18NString('management.user_accounts.set_title');
    container.appendChild(element);

    contentContainer.setAttribute('style', 'margin: 9px 36px 42px; position: relative;');

    if (userIsDisabled) {
        element = document.createElement('div');
        element.setAttribute('style', 'font-size: 12pt; color: #FA0000; font-style: italic; font-weight: bold;');
        element.innerHTML = '- ' + ilios_i18nVendor.getI18NString('management.user_accounts.account_disabled').toUpperCase() + ' -';
        contentContainer.appendChild(element);
    }

    if (addingUser) {
        element = document.createElement('div');
        element.innerHTML = '<strong>' + ilios_i18nVendor.getI18NString('management.user_accounts.add_account') + '</strong>';
        contentContainer.appendChild(element);
    }

    // left half content
    subContainer = document.createElement('div');
    subContainer.setAttribute('id', 'ua_role_checkboxes_div');
    subContainer.setAttribute('style',
                              'float: left; margin-top: 10px; width: 37%; position: relative; '
                                + 'padding: 12px; border: 1px solid #8A8A8A;');

    i18nStr = ilios_i18nVendor.getI18NString('management.user_accounts.roles.developer');
    element = ilios.management.user_accounts.generateUserRoleDiv(i18nStr,
                                                                 'ua_developer_role_checkbox',
                                                                 userIsDisabled,
                                                                 addingUser);
    subContainer.appendChild(element);

    i18nStr = ilios_i18nVendor.getI18NString('management.user_accounts.roles.director');
    element = ilios.management.user_accounts.generateUserRoleDiv(i18nStr,
                                                                 'ua_director_role_checkbox',
                                                                 userIsDisabled,
                                                                 false);
    subContainer.appendChild(element);

    i18nStr = ilios_i18nVendor.getI18NString('management.user_accounts.roles.faculty');
    element = ilios.management.user_accounts.generateUserRoleDiv(i18nStr,
                                                                 'ua_faculty_role_checkbox',
                                                                 userIsDisabled,
                                                                 false);
    subContainer.appendChild(element);

    if (! addingUser) {
        element = document.createElement('hr');
        element.setAttribute('style', 'width: 89%;');
        subContainer.appendChild(element);

        i18nStr = ilios_i18nVendor.getI18NString('management.user_accounts.roles.disable');
        element = ilios.management.user_accounts.generateUserRoleDiv(i18nStr,
                                                                     'ua_account_disable_checkbox',
                                                                     false, userIsDisabled);
        subContainer.appendChild(element);

        // user synchronization ignore checkbox
        i18nStr = ilios_i18nVendor.getI18NString('management.user_accounts.exclude_from_sync');
        element = ilios.management.user_accounts.generateUserRoleDiv(i18nStr,
            'ua_account_sync_ignore_checkbox', false, syncIgnored);
        subContainer.appendChild(element);
    }

    contentContainer.appendChild(subContainer);

    // right half content
    subContainer = document.createElement('div');
    subContainer.setAttribute('style',
                              'float: right; margin: 10px 18px; width: 52%; position: relative;');

    if (addingUser) {
        i18nStr = ilios_i18nVendor.getI18NString('general.user.first_name');
        element = ilios.management.user_accounts.generateUserAttributeInputPair(i18nStr, 'ua_first_name_tf');
        subContainer.appendChild(element);

        i18nStr = ilios_i18nVendor.getI18NString('general.user.middle_name');
        element = ilios.management.user_accounts.generateUserAttributeInputPair(i18nStr, 'ua_middle_name_tf');
        subContainer.appendChild(element);

        i18nStr = ilios_i18nVendor.getI18NString('general.user.last_name');
        element = ilios.management.user_accounts.generateUserAttributeInputPair(i18nStr, 'ua_last_name_tf');
        subContainer.appendChild(element);

        i18nStr = ilios_i18nVendor.getI18NString('general.user.uc_id');
        element = ilios.management.user_accounts.generateUserAttributeInputPair(i18nStr, 'ua_uc_id_tf');
        subContainer.appendChild(element);

        i18nStr = ilios_i18nVendor.getI18NString('general.user.email');
        element = ilios.management.user_accounts.generateUserAttributeInputPair(i18nStr, 'ua_email_tf');
        subContainer.appendChild(element);

        if (ilios.management.user_accounts.manageLoginCredentials) {
            i18nStr = ilios_i18nVendor.getI18NString('general.terms.login_name')
            element = ilios.management.user_accounts.generateUserAttributeInputPair(i18nStr, 'ua_login_username_tf');
            subContainer.appendChild(element);

            if (ilios.management.user_accounts.passwordRequired) {
                i18nStr = ilios_i18nVendor.getI18NString('general.terms.password')
                element = ilios.management.user_accounts.generateUserAttributeInputPair(i18nStr, 'ua_login_password_tf', 'password',
                ilios_i18nVendor.getI18NString('management.user_accounts.password_strength_requirements'));
                subContainer.appendChild(element);
            }
        }
    } else {

        element = document.createElement('div');
        element.innerHTML = userModel.getFormattedName(ilios.utilities.UserNameFormatEnum.FIRST_FIRST);
        element.setAttribute('class', 'read_only_data');
        subContainer.appendChild(element);

        element = document.createElement('div');
        element.innerHTML = userModel.getUCUniqueId();
        element.setAttribute('class', 'read_only_data');
        subContainer.appendChild(element);

        element = document.createElement('div');
        element.innerHTML = userModel.getEmailAddress();
        element.setAttribute('class', 'read_only_data');
        subContainer.appendChild(element);

        element = document.createElement('div');
        element.innerHTML = ilios.management.getSchoolForId(userModel.getSchoolId());
        element.setAttribute('class', 'read_only_data');
        subContainer.appendChild(element);

        ilios.management.user_accounts.generateUserCohortsContainerMarkup(subContainer);

        ilios.management.generateLoginCredentialsContainerMarkup(subContainer, userModel);
    }

    contentContainer.appendChild(subContainer);
    ilios.utilities.appendClearingDivToContainer(contentContainer);

    container.appendChild(contentContainer);


    element = document.createElement('button');
    element.innerHTML = ilios_i18nVendor.getI18NString('general.terms.continue');
    element.setAttribute('style', 'position: absolute; bottom: 12px; right: 114px; width: 90px;');
    element.setAttribute('disabled', 'disabled');
    element.setAttribute('id', 'ua_role_continue_button');
    Event.addListener(element, 'click', function () {
        ilios.management.user_accounts.commitUserChanges(userModel, null, null);
    });
    container.appendChild(element);

    element = document.createElement('button');
    element.innerHTML = ilios_i18nVendor.getI18NString('general.terms.cancel');
    element.setAttribute('style', 'position: absolute; bottom: 12px; right: 18px; width: 90px;');
    Event.addListener(element, 'click', function () {
        ilios.management.user_accounts.startUserAccountsWorkflow();
    });
    container.appendChild(element);


    element = document.createElement('div');
    element.setAttribute('class', 'indeterminate_progress');
    element.setAttribute('id', 'ua_indeterminate_progress');
    element.setAttribute('style', 'position: absolute; left: 12px; bottom: 12px;');
    container.appendChild(element);

    if (! addingUser) {
        var xmlHTTPRequest = ilios.utilities.getXMLHttpRequest();
        var url = null;
        var method = null;
        var paramString = null;
        var parsedObject = null;
        var roleModel = null;
        var primaryCohort = null;
        var secondaryCohorts = [];
        var btn;

        // we don't want to AJAX this one lest the user be left with a half-state waiting on a tardy
        //            asynchronous reply
        if (xmlHTTPRequest == null) {
            msg = ilios_i18nVendor.getI18NString('course_management.error.failed_connection');

            // If we throw up our custom YUI alert dialog now, the return which triggers the
            //        collapse of the autocomplete div also has some behind the scenes YUI stupidity
            //        to clear out the dialog.
            setTimeout("ilios.alert.alert('" + msg + "')", 500);

            return;
        }

        url = controllerURL + 'getUserAttributes';
        method = 'GET';
        paramString = 'user_id=' + userModel.getDBId() + '&rnd=' + new Date().getTime();

        xmlHTTPRequest.open(method, url + '?' + paramString, false);
        if ('undefined' !== typeof xmlHTTPRequest.overrideMimeType) {
            xmlHTTPRequest.overrideMimeType("text/plain");
        }
        xmlHTTPRequest.setRequestHeader("X-Requested-With", "XMLHttpRequest");
        xmlHTTPRequest.send(null);

        try {
            parsedObject = YAHOO.lang.JSON.parse(xmlHTTPRequest.responseText);
        }
        catch (e) {
            ilios.global.defaultAJAXFailureHandler(null, e);

            return;
        }

        if (parsedObject.error != null) {
            var msg = ilios_i18nVendor.getI18NString('management.error.loading_attributes');

            // If we throw up our custom YUI alert dialog now, the return which triggers the
            // collapse of the autocomplete div also has some behind the scenes YUI stupidity
            // to clear out the dialog.
            setTimeout("ilios.alert.alert('" + msg + ': ' + parsedObject.error + "')", 500);

            return;
        }

        if (ilios.management.user_accounts.manageLoginCredentials) {
            userModel.setLoginName(parsedObject.attributes.ilios_auth_username);
            ilios.management.user_accounts.updateLoginNameDisplay(parsedObject.attributes.ilios_auth_username);
        }

        if (parsedObject.attributes.primary_cohort !== false) {
            // programName, cohortName, cId, pyId, cohortStartYear, groupModel, activeSchool
            primaryCohort = new ProgramCohortModel(
                parsedObject.attributes.primary_cohort.program_title,
                parsedObject.attributes.primary_cohort.cohort_title,
                parsedObject.attributes.primary_cohort.cohort_id,
                parsedObject.attributes.primary_cohort.py_id,
                parsedObject.attributes.primary_cohort.start_year,
                null, null);
            userModel.setPrimaryCohort(primaryCohort);
        }

        if (parsedObject.attributes.secondary_cohorts.length) {
            for (i = 0; i < parsedObject.attributes.secondary_cohorts.length; i++) {
                secondaryCohorts.push(
                    new SchoolProgramCohortModel(
                        parsedObject.attributes.secondary_cohorts[i].program_title,
                        parsedObject.attributes.secondary_cohorts[i].cohort_title,
                        parsedObject.attributes.secondary_cohorts[i].cohort_id,
                        parsedObject.attributes.secondary_cohorts[i].py_id,
                        parsedObject.attributes.secondary_cohorts[i].start_year,
                        parsedObject.attributes.secondary_cohorts[i].owning_school_id
                    )
                );
            }
            userModel.setSecondaryCohorts(secondaryCohorts);
        }

        ilios.management.user_accounts.populateUserPrimaryCohortDisplay(userModel,
            parsedObject.attributes.is_student, 'ua_account_primary_cohort');
        ilios.management.user_accounts.populateUserSecondaryCohortsDisplay(userModel, 'ua_account_secondary_cohorts');
        // show the edit button for secondary cohorts
        btn = document.getElementById('ua_account_edit_secondary_cohorts_btn');
        btn.setAttribute('style', 'display: inline');

        for (var key in parsedObject.attributes.roles) {
            roleModel = parsedObject.attributes.roles[key];

            element = null;
            switch (parseInt(roleModel.user_role_id)) {
                case 1:
                    element = document.getElementById('ua_director_role_checkbox');
                    break;
                case 2:
                    element = document.getElementById('ua_developer_role_checkbox');
                    break;
                case 3:
                    element = document.getElementById('ua_faculty_role_checkbox');
                    break;
            }

            if (element != null) {
                element.checked = true;
            }
        }
    }

    element = new YAHOO.util.Element(document.getElementById('ua_indeterminate_progress'));
    element.setStyle('display', 'none');

    ilios.management.user_accounts.checkUserData();
};

// @private
ilios.management.user_accounts.commitUserChanges = function (userModel, passbackRoleArray, passbackSyncIgnore) {
    var addingUser = (userModel == null);
    var element = null;
    var disabled = (passbackRoleArray != null);
    var userId = -1;
    var firstName = '';
    var middleName = '';
    var lastName = '';
    var ucId = '';
    var email = '';
    var roleArray = [];
    var secondaryCohorts;
    var secondaryCohortIds = [];
    var loginName = '';
    var password = '';
    var syncIgnore = false;
    var i, n;

    if (passbackSyncIgnore != null) {
        syncIgnore = passbackSyncIgnore;
    } else {
        element = document.getElementById('ua_account_sync_ignore_checkbox');
        if (null != element) {
            syncIgnore = element.checked;
        }
    }

    if (passbackRoleArray != null) {
        roleArray = passbackRoleArray.concat();
    } else {
        if (document.getElementById('ua_developer_role_checkbox').checked) {
            roleArray.push(2);
        }
        if (document.getElementById('ua_director_role_checkbox').checked) {
            roleArray.push(1);
        }
        if (document.getElementById('ua_faculty_role_checkbox').checked) {
            roleArray.push(3);
        }
    }


    element = document.getElementById('ua_account_disable_checkbox');
    if (element != null) {
        disabled = element.checked;

        if (disabled  && (passbackRoleArray == null)
            && (ilios.management.user_accounts.userBeingModifiedIsDisabled != disabled)) {
            ilios.management.user_accounts.displayTheAreYouSuperSuperSureYouWantToDisableView(userModel, roleArray, syncIgnore);
            return;
        }
    }

    element = document.getElementById('ua_account_sync_ignore_checkbox');
    if (null != element) {
        syncIgnore = element.checked;
    }

    element = new YAHOO.util.Element(document.getElementById('ua_indeterminate_progress'));
    element.setStyle('display', 'block');

    if (! addingUser) {
        userId = userModel.getDBId();
        firstName = userModel.getFirstName();
        middleName = userModel.getMiddleName();
        lastName = userModel.getLastName();
        ucId = userModel.getUCUniqueId();
        email = userModel.getEmailAddress();
        secondaryCohorts = userModel.getSecondaryCohorts();
        for (i = 0, n = secondaryCohorts.length; i < n; i++) {
            secondaryCohortIds.push(secondaryCohorts[i].getCohortId());
        }
    } else {
        firstName = document.getElementById('ua_first_name_tf').value;
        middleName = document.getElementById('ua_middle_name_tf').value;
        lastName = document.getElementById('ua_last_name_tf').value;
        ucId = document.getElementById('ua_uc_id_tf').value;
        email = document.getElementById('ua_email_tf').value;

        if (ilios.management.user_accounts.manageLoginCredentials) {
            // get login credentials
            loginName = document.getElementById('ua_login_username_tf').value;
            if (ilios.management.user_accounts.passwordRequired) {
                password = document.getElementById('ua_login_password_tf').value;
            }
        }
    }

    secondaryCohortIds = secondaryCohortIds.concat();

    ilios.management.transaction.addOrUpdateUserAccount(addingUser,
        ilios.management.user_accounts.userBeingModifiedIsDisabled, disabled,
        userId, firstName, middleName, lastName, ucId, email, roleArray, secondaryCohortIds,
        ilios.management.user_accounts.userBeingModifiedIsIgnoredFromSync, syncIgnore,
        loginName, password);
};

// @private
ilios.management.user_accounts.generateUserRoleDiv = function (labelStr, checkboxId, disable,
                                                               checked) {
    var rhett = document.createElement('div');
    var labelElement = document.createElement('div');
    var inputElement = document.createElement('input');

    rhett.setAttribute('style', 'margin-bottom: 3px;');

    labelElement.setAttribute('style', 'float: left; width: 82%;');
    labelElement.innerHTML = labelStr;
    rhett.appendChild(labelElement);

    inputElement.setAttribute('type', 'checkbox');
    if (disable) {
        inputElement.setAttribute('disabled', 'disabled');
    }
    inputElement.checked = checked;
    inputElement.setAttribute('style', 'float: right; margin: 0px 6px;');
    inputElement.setAttribute('id', checkboxId);
    YAHOO.util.Event.addListener(inputElement, 'click', function () {
        ilios.management.user_accounts.checkUserData();
    });
    rhett.appendChild(inputElement);

    ilios.utilities.appendClearingDivToContainer(rhett);

    return rhett;
};

/**
 * Creates the markup for displaying the primary and secondary cohorts
 * associated with the user account being displayed.
 * @method generateUserCohortsContainer
 * @param {HTMLElement} parentEl the parent element in the DOM
 */
ilios.management.user_accounts.generateUserCohortsContainerMarkup = function (parentEl) {
    var containerEl, rowEl, labelEl, dataEl, btnEl;
    var Event = YAHOO.util.Event;

    containerEl = document.createElement('div');
    rowEl = document.createElement('div');
    labelEl = document.createElement('label');
    labelEl.innerHTML = ilios_i18nVendor.getI18NString('general.terms.primary_cohort') + " : ";
    rowEl.appendChild(labelEl);
    dataEl = document.createElement('span');
    dataEl.setAttribute('id', 'ua_account_primary_cohort');
    dataEl.setAttribute('class', 'read_only_data');

    rowEl.appendChild(dataEl);
    containerEl.appendChild(rowEl);

    rowEl = document.createElement('div');
    labelEl = document.createElement('label');
    labelEl.innerHTML = ilios_i18nVendor.getI18NString('general.terms.secondary_cohorts') + " : ";
    rowEl.appendChild(labelEl);

    dataEl = document.createElement('span');
    dataEl.setAttribute('id', 'ua_account_secondary_cohorts');
    dataEl.setAttribute('class', 'read_only_data');
    rowEl.appendChild(dataEl);

    btnEl = document.createElement('button');
    btnEl.setAttribute('id', 'ua_account_edit_secondary_cohorts_btn');
    btnEl.setAttribute('style', 'display: none');
    btnEl.innerHTML = ilios_i18nVendor.getI18NString('general.terms.edit');

    Event.addListener(btnEl, 'click', function (e) {
        ilios.ui.onIliosEvent.fire({
            action: 'gen_dialog_open',
            event: 'find_cohort_and_program'
        });
        return false;
    });
    rowEl.appendChild(btnEl);

    containerEl.appendChild(rowEl);

    parentEl.appendChild(containerEl);
};

/**
 * Creates the markup for displaying the login username
 * associated with the user account being displayed.
 * @method generateLoginCredentialsContainerMarkup
 * @param {HTMLElement} parentEl the parent element in the DOM
 * @param {UserMangementModel} userModel
 */
ilios.management.generateLoginCredentialsContainerMarkup = function (parentEl, userModel) {
    var containerEl, rowEl, labelEl, dataEl, btnEl;
    var Event = YAHOO.util.Event;

    // only show any of this if we are running on ilios internal auth or ldap auth
    if (ilios.management.user_accounts.manageLoginCredentials)  {
        containerEl = document.createElement('div');
        rowEl = document.createElement('div');
        labelEl = document.createElement('label');
        labelEl.innerHTML = ilios_i18nVendor.getI18NString('general.terms.login_name') + ' : ';
        rowEl.appendChild(labelEl);
        dataEl = document.createElement('span');
        dataEl.setAttribute('id', 'ua_login_username_display');
        dataEl.setAttribute('class', 'read_only_data');
        rowEl.appendChild(dataEl);

        btnEl = document.createElement('button');
        btnEl.setAttribute('id', 'ua_account_edit_credentials_btn');
        btnEl.setAttribute('style', 'display: none');
        btnEl.innerHTML = ilios_i18nVendor.getI18NString('management.user_accounts.edit_login_credentials_button');
        Event.addListener(btnEl, 'click', function (e) {
            ilios.ui.onIliosEvent.fire({
                action: 'elc_dialog_open',
                model: userModel
            });
            return false;
        });
        rowEl.appendChild(btnEl);

        btnEl = document.createElement('button');
        btnEl.setAttribute('id', 'ua_account_add_credentials_btn');
        btnEl.setAttribute('style', 'display: none');
        btnEl.innerHTML = ilios_i18nVendor.getI18NString('management.user_accounts.add_login_credentials_button');
        Event.addListener(btnEl, 'click', function (e) {
            ilios.ui.onIliosEvent.fire({
                action: 'alc_dialog_open',
                model: userModel
            });
            return false;
        });
        rowEl.appendChild(btnEl);

        containerEl.appendChild(rowEl);
        parentEl.appendChild(containerEl);
    }
};

/**
 * Populates the user details display with the primary cohort of a given user.
 * @method populateUserPrimaryCohortDisplay
 * @param {UserManagementModel} userModel
 * @param {boolean} isStudent TRUE if the given user is a student, FALSE otherwise
 * @param {String} elementId the element id of the corresponding container in the DOM
 */
ilios.management.user_accounts.populateUserPrimaryCohortDisplay = function (userModel, isStudent, elementId) {
    var primaryCohort = userModel.getPrimaryCohort();
    var el = document.getElementById(elementId);
    var display = [];

    if (isStudent) {
        display.push(ilios_i18nVendor.getI18NString('general.terms.student'));
    }

    if (false !== primaryCohort) {
        display.push(primaryCohort.getDisplayText());
    } else {
        display.push(ilios_i18nVendor.getI18NString('general.phrases.unassigned'));
    }

    if (el) {
        el.innerHTML = display.join(' - ');
    }
};

/**
 * Populates the user details display with the secondary cohorts of a given user.
 * @method populateUserSecondaryCohortsDisplay
 * @param {UserManagementModel} userModel
 * @param {String} elementId the element id of the corresponding container in the DOM
 */
ilios.management.user_accounts.populateUserSecondaryCohortsDisplay = function (userModel, elementId) {
    var secondaryCohorts = userModel.getSecondaryCohorts();
    var i;
    var a = [];
    var el = document.getElementById(elementId);

    if (el) {
        for (i in secondaryCohorts) {
            if (secondaryCohorts.hasOwnProperty(i)) {
                a.push(secondaryCohorts[i].getDisplayText());
            }
        }
    }
    el.innerHTML = a.join(', ');
};

// @private
ilios.management.user_accounts.generateUserAttributeInputPair = function (labelStr, textFieldId, type, description) {
    var rhett = document.createElement('div');
    var labelElement = document.createElement('div');
    var inputWrapper = document.createElement('div');
    var inputElement = document.createElement('input');
    var inputType = type || 'text';
    var el;


    rhett.setAttribute('style', 'margin-bottom: 6px;');

    labelElement.setAttribute('style', 'float: left; width: 33%; text-align: right;');
    labelElement.innerHTML = labelStr + ':';
    rhett.appendChild(labelElement);

    inputWrapper.setAttribute('style', 'float: right; margin: 0px 6px; width: 60%;');
    inputElement.setAttribute('type', inputType);
    inputElement.setAttribute('id', textFieldId);

    YAHOO.util.Event.addListener(inputElement, 'keyup', function () {
        ilios.management.user_accounts.checkUserData();
    });

    inputWrapper.appendChild(inputElement);
    if (description) {
        el = document.createElement('div');
        el.setAttribute('class', 'tiny');
        el.innerHTML = description;
        inputWrapper.appendChild(el);
    }
    rhett.appendChild(inputWrapper);

    ilios.utilities.appendClearingDivToContainer(rhett);
    return rhett;
};

// @private
ilios.management.user_accounts.checkUserData = function () {
    var Element = YAHOO.util.Element;
    var divsToUnstyle = ['ua_first_name_tf', 'ua_last_name_tf', 'ua_uc_id_tf', 'ua_email_tf', 'ua_role_checkboxes_div'];

    if (ilios.management.user_accounts.manageLoginCredentials) {
        divsToUnstyle.push('ua_login_username_tf');
        if (ilios.management.user_accounts.passwordRequired) {
            divsToUnstyle.push('ua_login_password_tf');
        }
    }
    var divsToStyle =  [];
    var divsToHint = [];
    var element = null;
    var i = 0;
    var length = 0;
    var passwordStrength;
    var msg;

    //set the uc_id length to a single value or a range depending on its settings in the config file
    var uc_id_length;
    if(ilios.management.user_accounts.uidOptions.uid_min_length === ilios.management.user_accounts.uidOptions.uid_max_length){
        uc_id_length = ilios.management.user_accounts.uidOptions.uid_max_length;
    } else {
	uc_id_length = ilios.management.user_accounts.uidOptions.uid_min_length + "-" + ilios.management.user_accounts.uidOptions.uid_max_length;
    }


    element = document.getElementById('ua_first_name_tf');
    if ((element != null) && (YAHOO.lang.trim(element.value).length == 0)) {
        divsToStyle.push('ua_first_name_tf');
        divsToHint.push(ilios_i18nVendor.getI18NString('management.error.data.first_name'));
    }

    element = document.getElementById('ua_last_name_tf');
    if ((element != null) && (YAHOO.lang.trim(element.value).length == 0)) {
        divsToStyle.push('ua_last_name_tf');
        divsToHint.push(ilios_i18nVendor.getI18NString('management.error.data.last_name'));
    }

    element = document.getElementById('ua_uc_id_tf');
    if ((element != null) && (YAHOO.lang.trim(element.value).length < ilios.management.user_accounts.uidOptions.uid_min_length)
        || (YAHOO.lang.trim(element.value).length > ilios.management.user_accounts.uidOptions.uid_max_length)) {
	divsToStyle.push('ua_uc_id_tf');
        divsToHint.push(ilios_i18nVendor.getI18NString('management.error.data.uc_id') + " " +  uc_id_length + " " + ilios_i18nVendor.getI18NString('management.error.data.uc_id_chars_in_length'));
    }

    element = document.getElementById('ua_email_tf');
    if ((element != null) && (YAHOO.lang.trim(element.value).length == 0)) {
        divsToStyle.push('ua_email_tf');
        divsToHint.push(ilios_i18nVendor.getI18NString('management.error.data.email'));
    }

    if (ilios.management.user_accounts.userBeingModifiedIsDisabled) {
        if (document.getElementById('ua_account_disable_checkbox').checked) {
            divsToStyle.push('ua_role_checkboxes_div');
            divsToHint.push(ilios_i18nVendor.getI18NString('management.error.data.enable'));
        }
    }

    if (ilios.management.user_accounts.manageLoginCredentials) {
        element = document.getElementById('ua_login_username_tf');
        if ((element != null) && (YAHOO.lang.trim(element.value).length == 0)) {
            divsToStyle.push('ua_login_username_tf');
            divsToHint.push(ilios_i18nVendor.getI18NString('management.error.data.login_username'));
        }
        if (ilios.management.user_accounts.passwordRequired) {
            element = document.getElementById('ua_login_password_tf');
            if (element != null) {
                passwordStrength = ilios.utilities.checkPasswordStrength(element.value);
                if (passwordStrength) {
                    msg = ilios.utilities.getPasswordStrengthCheckWarnings(passwordStrength);
                    divsToStyle.push('ua_login_password_tf');
                    divsToHint.push(msg.join("\n"));
                }
            }
        }
    }

    length = divsToUnstyle.length;
    for (i = 0; i < length; i++) {
        element = document.getElementById(divsToUnstyle[i]);

        if (element == null) {
            continue;
        }
        element = new Element(element);
        if (typeof element.get('element').originalBorder == 'undefined') {
            element.get('element').originalBorder = element.getStyle('border');
        } else {
            element.setStyle('border', (element.get('element').originalBorder == "" ? null : element.get('element').originalBorder));
        }

        element.get('element').removeAttribute('title');
    }

    length = divsToStyle.length;
    for (i = 0; i < length; i++) {
        element = new Element(document.getElementById(divsToStyle[i]));
        element.setStyle('border', '1px solid #F91000');
        element.get('element').setAttribute('title', divsToHint[i]);
    }

    element = document.getElementById('ua_role_continue_button');
    if (divsToStyle.length == 0) {
        element.removeAttribute('disabled');
    }
    else {
        element.setAttribute('disabled', 'disabled');
    }
};

ilios.management.user_accounts.displayAccountUpdateResult = function (addingUser, wasDisabled,
                                                                      isDisabled, userName,
                                                                      ucId) {
    var container = document.getElementById('management_center_content');
    var contentContainer = document.createElement('div');
    var element = document.createElement('div');
    var statusString = null;

    ilios.utilities.removeAllChildren(container);

    element.setAttribute('style', 'font-weight: bold; margin: 9px 6px 15px;');
    element.innerHTML = ilios_i18nVendor.getI18NString('management.user_accounts.set_title');
    container.appendChild(element);

    contentContainer.setAttribute('style',
                                  'margin: 48px 36px 42px; position: relative; height: 225px;');
    contentContainer.setAttribute('class', 'read_only_data');

    statusString = ilios_i18nVendor.getI18NString('management.user_accounts.result_message_prefix')
                    + ' ' + userName
                    + ' (' + ucId + ') '
                    + ilios_i18nVendor.getI18NString('general.phrases.has_been').toLowerCase()
                    + ' ';
    if (addingUser) {
        statusString += ilios_i18nVendor.getI18NString('general.terms.added').toLowerCase();
    }
    else if (wasDisabled != isDisabled) {
        statusString += isDisabled ? ilios_i18nVendor.getI18NString('general.terms.disabled').toLowerCase()
                                   : ilios_i18nVendor.getI18NString('general.terms.enabled').toLowerCase();
    }
    else {
        statusString += ilios_i18nVendor.getI18NString('general.terms.updated').toLowerCase();
    }
    statusString += '.';

    contentContainer.innerHTML = '<center>' + statusString + '</center>';

    container.appendChild(contentContainer);

    element = document.createElement('button');
    element.innerHTML = ilios_i18nVendor.getI18NString('general.terms.finished');
    element.setAttribute('style', 'position: absolute; bottom: 12px; right: 18px; width: 90px;');
    YAHOO.util.Event.addListener(element, 'click', function () {
        ilios.management.user_accounts.startUserAccountsWorkflow();
    });
    container.appendChild(element);
};

// @private
ilios.management.user_accounts.displayTheAreYouSuperSuperSureYouWantToDisableView = function (userModel, roleArray, syncIgnore) {
    var Event = YAHOO.util.Event;
    var container = document.getElementById('management_center_content');
    var contentContainer = document.createElement('div');
    var element = document.createElement('div');
    var subElement = null;
    var i18nStr = null;

    ilios.utilities.removeAllChildren(container);

    element.setAttribute('style', 'font-weight: bold; margin: 9px 6px 15px;');
    element.innerHTML = ilios_i18nVendor.getI18NString('management.user_accounts.disable.title');
    container.appendChild(element);

    contentContainer.setAttribute('style', 'margin: 48px 36px 42px; position: relative;');

    element = document.createElement('div');
    element.setAttribute('style', 'font-weight: bold; margin-bottom: 15px;');
    element.innerHTML
                    = ilios_i18nVendor.getI18NString('management.user_accounts.disable.warning_1');
    contentContainer.appendChild(element);

    element = document.createElement('div');
    element.setAttribute('style', 'font-weight: bold; margin-bottom: 36px;');
    element.innerHTML
                    = ilios_i18nVendor.getI18NString('management.user_accounts.disable.warning_2');
    contentContainer.appendChild(element);

    element = document.createElement('div');
    element.setAttribute('style',
                         'margin: 0px 36px 66px; border: 1px solid #808080; padding: 12px 48px;');
    i18nStr = ilios_i18nVendor.getI18NString('general.user.full_name').toLowerCase();
    subElement
        = ilios.management.user_accounts
                .generateReadOnlyUserAttributePair(i18nStr,
                                                   userModel.getFormattedName(
                                                                    ilios.utilities
                                                                        .UserNameFormatEnum.FIRST_FIRST));
    element.appendChild(subElement);
    i18nStr = ilios_i18nVendor.getI18NString('general.user.uc_id');
    subElement
      = ilios.management.user_accounts.generateReadOnlyUserAttributePair(i18nStr,
                                                                         userModel.getUCUniqueId());
    element.appendChild(subElement);
    i18nStr = ilios_i18nVendor.getI18NString('general.user.email').toLowerCase();
    subElement
      = ilios.management.user_accounts.generateReadOnlyUserAttributePair(i18nStr,
                                                                         userModel.getEmailAddress());
    element.appendChild(subElement);
    i18nStr = ilios_i18nVendor.getI18NString('management.user_accounts.details.primary_school').toLowerCase();
    subElement
      = ilios.management.user_accounts.generateReadOnlyUserAttributePair(i18nStr,
                                                                         ilios.management
                                                                                   .getSchoolForId(
                                                                                        userModel.getSchoolId()));
    element.appendChild(subElement);

    contentContainer.appendChild(element);

    container.appendChild(contentContainer);



    element = document.createElement('div');
    element.setAttribute('class', 'indeterminate_progress');
    element.setAttribute('id', 'ua_indeterminate_progress');
    element.setAttribute('style', 'position: absolute; left: 12px; bottom: 12px; display: none;');
    container.appendChild(element);


    element = document.createElement('button');
    element.innerHTML = ilios_i18nVendor.getI18NString('general.terms.continue');
    element.setAttribute('style', 'position: absolute; bottom: 12px; right: 114px; width: 90px;');
    element.setAttribute('id', 'ua_role_continue_button');
    Event.addListener(element, 'click', function () {
        ilios.management.user_accounts.commitUserChanges(userModel, roleArray, syncIgnore);
    });
    container.appendChild(element);

    element = document.createElement('button');
    element.innerHTML = ilios_i18nVendor.getI18NString('general.terms.cancel');
    element.setAttribute('style', 'position: absolute; bottom: 12px; right: 18px; width: 90px;');
    Event.addListener(element, 'click', function () {
        ilios.management.user_accounts.startUserAccountsWorkflow();
    });
    container.appendChild(element);
};


// @private
ilios.management.user_accounts.generateReadOnlyUserAttributePair = function (labelStr, fieldStr) {
    var rhett = document.createElement('div');
    var labelElement = document.createElement('div');
    var valueElement = document.createElement('div');

    rhett.setAttribute('style', 'margin-bottom: 6px;');

    labelElement.setAttribute('style', 'float: left; width: 33%; text-align: left;');
    labelElement.innerHTML = labelStr + ': ';
    rhett.appendChild(labelElement);

    valueElement.setAttribute('style', 'float: right; margin: 0px 6px; width: 60%;');
    valueElement.setAttribute('class', 'read_only_data');
    valueElement.innerHTML = fieldStr;
    rhett.appendChild(valueElement);

    ilios.utilities.appendClearingDivToContainer(rhett);

    return rhett;
};

ilios.management.user_accounts.updateLoginNameDisplay = function (loginName) {
    if (false !== loginName) {
        document.getElementById('ua_login_username_display').innerHTML = loginName;
        // show the "edit credentials" button
        btn = document.getElementById('ua_account_edit_credentials_btn');
        btn.setAttribute('style', 'display: inline');
        btn = document.getElementById('ua_account_add_credentials_btn');
        btn.setAttribute('style', 'display: none');
    } else {
        document.getElementById('ua_login_username_display').innerHTML = '';
        // no credentials exist
        // show the "add credentials" button
        btn = document.getElementById('ua_account_add_credentials_btn');
        btn.setAttribute('style', 'display: inline');
        btn = document.getElementById('ua_account_edit_credentials_btn');
        btn.setAttribute('style', 'display: none');
    }
};
