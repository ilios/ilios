ilios.namespace('cm.lm');

ilios.cm.lm.learningMaterialLightboxIsDirty = false;

ilios.cm.lm.currentlyShowingTab = -1;

ilios.cm.lm.searchTabPane = null;
ilios.cm.lm.assetTabPane = null;
ilios.cm.lm.linkTabPane = null;
ilios.cm.lm.citationTabPane = null;

ilios.cm.lm.generateIdStringForLearningMaterialCount = function (containerNumber) {
    return '' + containerNumber + '_learning_material_count';
};

ilios.cm.lm.generateIdStringForLearningMaterialExpandWidget = function (containerNumber) {
    return '' + containerNumber + '_learning_material_expand_widget';

};

ilios.cm.lm.generateIdStringForLearningMaterialList = function (containerNumber) {
    return '' + containerNumber + '_learning_material_list';
};

ilios.cm.lm.generateIdStringForLearningMaterialsContainer = function (containerNumber) {
    return '' + containerNumber + '_learning_materials_container';
};

ilios.cm.lm.generateIdStringForLearningMaterialsContainerLabel = function (containerNumber) {
    return '' + containerNumber + '_learning_materials_container_label';
};

/**
 * TODO: JH - need to add comments/document
 *
 * generate the id for the respective learning material container
 *
 */
ilios.cm.lm.generateIdStringForLearningMaterialTextArea = function (containerNumber, lmNumber) {
    return '' + containerNumber + '_' + lmNumber + '_learning_materials_container';
};

ilios.cm.lm.generateIdStringForLearningMaterialsContainerExpandWidget = function (containerNumber) {
    return '' + containerNumber + '_learning_materials_container_expand_widget';
};

/**
 * TODO: JH - need to add comments/document
 *
 * generate the id for the respective learning materials 'Add Mesh Link'
 *
 */
ilios.cm.lm.generateIdStringForLearningMaterialMeSHLink = function (containerNumber, lmNumber) {
    return '' + containerNumber + '_' + lmNumber + '_learning_materials_mesh_link';
};

/**
 * Generates a unique element id for learning material search links, based on the given container element id.
 * @method generateIdStringForLearningMaterialSearchLink
 * @param {Number} containerNumber the container element id
 * @return {String}
 */
ilios.cm.lm.generateIdStringForLearningMaterialSearchLink = function (containerNumber) {
    return '' + containerNumber + '_session_learning_material_search_link';
};

// @private
ilios.cm.lm.setLearningMaterialLightboxDirty = function () {
    if (! ilios.cm.lm.learningMaterialLightboxIsDirty) {
        var element = document.getElementById('alm_transaction_status');

        element.innerHTML = '';
        ilios.cm.lm.learningMaterialLightboxIsDirty = true;
    }
};

ilios.cm.lm.populateLearningMaterialList = function (containerNumber) {
    var isCourse = (containerNumber == -1);
    var model = isCourse ? ilios.cm.currentCourseModel
                         : ilios.cm.currentCourseModel.getSessionForContainer(containerNumber);
    var listElement = document.getElementById(
                              ilios.cm.lm.generateIdStringForLearningMaterialList(containerNumber));
    var learningMaterials = model.getLearningMaterials();
    var learningMaterialModel = null;
    //var fixDivSize = (learningMaterials.length > 4);
    var divElement = new YAHOO.util.Element(listElement.parentNode);

    ilios.utilities.removeAllChildren(listElement);

    for (var key in learningMaterials) {
        learningMaterialModel = learningMaterials[key];

        listElement.appendChild(ilios.cm.lm.createListElementForLearningMaterial(learningMaterialModel,
                                                                                 false,
                                                                                 containerNumber));
    }

    //if (fixDivSize) {
    //    divElement.setStyle('height', '120px');
    //    divElement.setStyle('overflow', 'auto');
    //}
    //else {
    //    divElement.setStyle('height', null);
    //    divElement.setStyle('overflow', null);
    //}

    ilios.cm.lm.updateLearningMaterialCount(containerNumber, model);
};

ilios.cm.lm.createListElementForLearningMaterial = function (model, showAddIcon, containerNumber) {
    var Event = YAHOO.util.Event;
    var rhett = document.createElement('li');
    var titleElement = document.createElement(showAddIcon ? 'span' : 'a');
    var fileSizeElement = null;
    var downloadURL = learningMaterialsControllerURL
                            + "getLearningMaterialWithId?learning_material_id=" + model.getDBId();
    var affectingWidget = document.createElement('span');
    var affectingWidgetClass = null;
    var downloadWidget = document.createElement('span');
    var buttonWidgetDiv = document.createElement('div');
    var mouseOverBGColor = null;
    var isLink = (model.getMimeType() == 'link');
    var isCitation = (model.getMimeType() == 'citation');

    rhett.setAttribute('class', ilios.utilities.convertMimeTypeToCSSClassName(model.getMimeType()));

    if (showAddIcon) {
        var innerHTML = model.getTitle();
        var descriptionI18NStr = ilios_i18nVendor.getI18NString('general.terms.description');
        var noneI18NStr = ilios_i18nVendor.getI18NString('general.terms.none');
        var statusString = ilios.cm.lm.learningMaterialStatuses[model.getStatusId()];
        var colorCoding = null;

        if (model.getOwningUserId() == currentUserId) {
            colorCoding = '#00a400';
        }
        else {
            colorCoding = '#cc0000';
        }

        innerHTML += ' &nbsp; <span style="font-size: 8pt; color: ' + colorCoding + ';">('
                        + statusString + ')</span>';

        innerHTML += '<br/><span style="font-size: 8pt; font-weight: bold;">' + descriptionI18NStr;
        innerHTML += ': </span> <span style="font-size: 8pt;">';
        innerHTML += (((model.getDescription() == null) || (model.getDescription().length == 0))
                            ? ('(' + noneI18NStr + ')')
                            : model.getDescription())
                        + ' </span>';

        titleElement.innerHTML = innerHTML;
    }
    else {
        var textNode = document.createTextNode(model.getTitle());
        titleElement.setAttribute('href', '');
        titleElement.setAttribute('onclick', 'return false;');
        titleElement.appendChild(textNode);
    }

    titleElement.setAttribute('class', 'title');
    rhett.appendChild(titleElement);

    if (showAddIcon) {
        affectingWidgetClass = 'add_widget';
    }
    else {
        affectingWidgetClass = 'remove_widget';
        rhett.container_number = containerNumber;
    }

    buttonWidgetDiv.setAttribute('class', 'buttonset');

    downloadWidget.setAttribute('class', 'download_widget');
    affectingWidget.setAttribute('class', affectingWidgetClass);

    if ((! isLink) && (! isCitation)) {
        buttonWidgetDiv.appendChild(downloadWidget);

        fileSizeElement = document.createElement('span');
        fileSizeElement.setAttribute('class', 'filesize');
        fileSizeElement.innerHTML = ' &nbsp;(' + model.getFileSize() + ' KB)';
        rhett.appendChild(fileSizeElement);
    }

    if (! ilios.cm.currentCourseModel.isLocked()) {
        buttonWidgetDiv.appendChild(affectingWidget);

        Event.addListener(affectingWidget, 'click', function (e) {
            //ilios.cm.lm.newHandleLearningMaterialClick(this, model, showAddIcon);
            //TODO: JH - rename the following method to something that isn't a placeholder...
            ilios.cm.lm.newHandleLearningMaterialClick(model);
        });
    } else {
        ilios.cm.uiElementsToHideOnLockedView.push(new YAHOO.util.Element(affectingWidget));
    }

    Event.addListener(downloadWidget, 'click', function (e) {
        window.location.href = downloadURL;
    });
    if (! showAddIcon) {
        Event.addListener(titleElement, 'click', function (e) {
            ilios.common.lm.learningMaterialsDetailsModel = model;
            ilios.ui.onIliosEvent.fire({
                action: 'lm_metadata_dialog_open',
                container_number: containerNumber
            });
            return false;
        });
    }
    //Event.addListener(rhett, 'mouseover',
    //                   function (e) { (new YAHOO.util.Element(this)).setStyle('background-color',
    //                                                                mouseOverBGColor); });
    //Event.addListener(rhett, 'mouseout',
    //                   function (e) { (new YAHOO.util.Element(this)).setStyle('background-color', null); });

    rhett.appendChild(buttonWidgetDiv);

    return rhett;
};

// @private
ilios.cm.lm.handleLearningMaterialClick = function (widgetElement, learningMaterialModel, add) {
    var liElement = widgetElement.parentNode.parentNode;
    var containerNumber = null;
    var isCourse = null;
    var model = null;

    if (add) {
        containerNumber = ilios.cm.lm.learningMaterialDialog.cnumber;
        isCourse = (containerNumber == -1);
        model = isCourse ? ilios.cm.currentCourseModel
                         : ilios.cm.currentCourseModel.getSessionForContainer(containerNumber);

        liElement.parentNode.removeChild(liElement);

        model.addLearningMaterial(learningMaterialModel);

        ilios.cm.transaction.associateLearningMaterial(learningMaterialModel.getDBId(),
                                                       model.getDBId(), isCourse);

        ilios.cm.lm.setLearningMaterialDivVisibility(containerNumber, null, false);

        ilios.cm.lm.clearLearningMaterialsDialogFields(true);
    }
    else {
        var args = {};
        var dirtyStr = ilios_i18nVendor.getI18NString('general.warning.delete_prefix');
        var yesStr = ilios_i18nVendor.getI18NString('general.terms.yes');

        containerNumber = liElement.container_number;
        isCourse = (containerNumber == -1);
        model = isCourse ? ilios.cm.currentCourseModel
                         : ilios.cm.currentCourseModel.getSessionForContainer(containerNumber);

        args.lmId = learningMaterialModel.getDBId();
        args.assocId = model.getDBId();
        args.liElement = liElement;

        // TODO consider using the container number as a roundtrip to the server during
        //          the disassociation transaction
        ilios.cm.lm.learningMaterialDialog.cnumber = containerNumber;

        ilios.alert.inform(('<p style="margin-bottom:9px; text-align:justify;">' + dirtyStr + '?</p>'), yesStr,
            ilios.cm.lm.continueLearningMaterialDelete, args);
    }
};

ilios.common.lm.populateLearningMaterialMetadataDialog = function (lmModel) {
    var element = document.getElementById('ilios_lm_display_name');
    var isLink = (lmModel.getLink() != null);
    var isCitation = (lmModel.getCitation() != null);
    var lightboxContainer = document.getElementById('ilios_learning_material_lightbox_wrap');
    var dateObject = null;

    element.innerHTML = lmModel.getTitle();

    element = document.getElementById('ilios_lm_owner_name');
    element.innerHTML = lmModel.getOwningUserName();

    element = document.getElementById('ilios_lm_owner_role');
    element.innerHTML = ilios.cm.lm.learningMaterialOwnerRoles[lmModel.getOwnerRoleId()];

    element = document.getElementById('ilios_lm_creator');
    element.innerHTML = lmModel.getCreator();

    element = document.getElementById('ilios_lm_upload_date');
    dateObject = ilios.utilities.mySQLDateToDateObject(lmModel.getUploadDate(), true);
    element.innerHTML = dateObject.format('mm/dd/yyyy h:MM:ss tt');

    element = document.getElementById('ilios_lm_description');
    element.innerHTML = lmModel.getDescription();

    element = document.getElementById('ilios_lm_mesh');
    element.innerHTML = lmModel.getMeSHItemsAsFormattedText();

    element = document.getElementById('ilios_lm_notes');
    element.innerHTML = (lmModel.getNotes() != null) ? lmModel.getNotes() : "";

    element = document.getElementById('ilios_lm_required_checkbox');
    element.checked = lmModel.isRequired();
    ilios.dom.setElementEnabled(element, (! ilios.cm.currentCourseModel.isLocked()));

    element = document.getElementById('ilios_lm_mesh_link');
    ilios.dom.setElementEnabled(element, (! ilios.cm.currentCourseModel.isLocked()));

    element = document.getElementById('ilios_lm_notes_link');
    ilios.dom.setElementEnabled(element, (! ilios.cm.currentCourseModel.isLocked()));

    if (isLink) {//getElementsByClassName
        ilios.dom.setDisplayForAllChildrenOfClass(lightboxContainer, 'alm_file_div', 'none');
        ilios.dom.setDisplayForAllChildrenOfClass(lightboxContainer, 'alm_link_div', 'block');
        ilios.dom.setDisplayForAllChildrenOfClass(lightboxContainer, 'alm_citation_div', 'none');

        element = document.getElementById('ilios_lm_link_value');
        element.href = (lmModel.getLink().indexOf('://') == -1) ? ('http://' + lmModel.getLink())
                                                                : lmModel.getLink();
        element.title = lmModel.getLink();
        element.innerHTML = ilios.utilities.getDomainFromURL(lmModel.getLink());
    }
    else if (isCitation) {
        ilios.dom.setDisplayForAllChildrenOfClass(lightboxContainer, 'alm_file_div', 'none');
        ilios.dom.setDisplayForAllChildrenOfClass(lightboxContainer, 'alm_link_div', 'none');
        ilios.dom.setDisplayForAllChildrenOfClass(lightboxContainer, 'alm_citation_div', 'block');

        element = document.getElementById('ilios_lm_citation_value');
        element.innerHTML = lmModel.getCitation();
    }
    else {
        ilios.dom.setDisplayForAllChildrenOfClass(lightboxContainer, 'alm_file_div', 'block');
        ilios.dom.setDisplayForAllChildrenOfClass(lightboxContainer, 'alm_link_div', 'none');
        ilios.dom.setDisplayForAllChildrenOfClass(lightboxContainer, 'alm_citation_div', 'none');

        element = document.getElementById('ilios_lm_file_name');
        element.innerHTML = lmModel.getFilename();

        element = document.getElementById('ilios_lm_file_size');
        element.innerHTML = lmModel.getFileSize() + ' KB';

        element = document.getElementById('ilios_lm_file_type');
        element.innerHTML = lmModel.getMimeType();
    }

    element = document.getElementById('lm_meta_statuses_selector');
    ilios.utilities.selectOptionWithValue(element, lmModel.getStatusId());
    if ((lmModel.getOwningUserId() == currentUserId)
                                                    && (! ilios.cm.currentCourseModel.isLocked())) {
        ilios.dom.setElementEnabled(element, true);
    }
    else {
        ilios.dom.setElementEnabled(element, false);
    }
};

ilios.cm.lm.setLearningMaterialDivVisibility = function (containerNumber, widgetDiv, shouldToggle) {
    var Element = YAHOO.util.Element;
    var yElement = null;
    var idString = null;
    var div = null;

    if (ilios.cm.currentCourseModel == null) {
        return;
    }

    if (widgetDiv == null) {
        idString = ilios.cm.lm.generateIdStringForLearningMaterialsContainerExpandWidget(containerNumber);
        widgetDiv = document.getElementById(idString);
    }

    yElement = new Element(widgetDiv);
    idString = ilios.cm.lm.generateIdStringForLearningMaterialsContainer(containerNumber);
    div = new Element(document.getElementById(idString));
    if ((div.getStyle('display') != 'none') && shouldToggle) {
        yElement.removeClass('expanded_widget');
        yElement.addClass('collapsed_widget');
        div.setStyle('display', 'none');
    } else {
        yElement.removeClass('collapsed_widget');
        yElement.addClass('expanded_widget');
        div.setStyle('display', 'block');
    }
};

ilios.cm.lm.updateLearningMaterialCount = function (containerNumber, model) {
    var idString = ilios.cm.lm.generateIdStringForLearningMaterialCount(containerNumber);
    var element = document.getElementById(idString);

    element.innerHTML = ' (' + model.getLearningMaterials().length + ')';
};

// @private
ilios.cm.lm.clearLearningMaterialsDialogFields = function (clearTransactionStatus) {
    var element = document.getElementById('alm_title');

    if (element != null) {
        element.value = '';
    }

    element = document.getElementById('alm_content_creator');
    if (element != null) {
        element.value = '';
    }

    element = document.getElementById('alm_filepath_field');
    if (element != null) {
        element.value = '';
    }

    element = document.getElementById('alm_copyright_checkbox');
    if (element != null) {
        element.checked = false;
    }

    if (clearTransactionStatus) {
        element = document.getElementById('alm_transaction_status');
        element.innerHTML = '';
    }

    element = document.getElementById('alm_copyright_rationale');
    if (element != null) {
        element.value
            = ilios_i18nVendor.getI18NString('learning_material.asset.default_copyright_rationale');
        element.removeAttribute('disabled');
        element.hasCleared = false;
    }

    element = document.getElementById('alm_search_results_ul');
    if (element != null) {
        ilios.utilities.removeAllChildren(element);
    }

    element = document.getElementById('alm_search_textfield');
    if (element != null) {
        element.value = '';
    }

    element = document.getElementById('alm_description');
    if (element != null) {
        element.value = '';
    }

    element = document.getElementById('alm_roles_selector');
    if (element != null) {
        element.selectedIndex = 0;
    }

    element = document.getElementById('alm_statuses_selector');
    if (element != null) {
        element.selectedIndex = 0;
    }

    ilios.cm.lm.learningMaterialLightboxIsDirty = false;

    ilios.cm.lm.almLearningMaterialModel = new LearningMaterialModel();
    //ilios.cm.lm.almLearningMaterialModel.addStateChangeListener(ilios.cm.lm.almDirtyStateListener,
    //                                                            null);
};

ilios.cm.lm.resetAddLearningMaterialsDialog = function (dialog, clearTransactionStatus) {
    var model = (dialog.cnumber == -1) ? null
                                       : ilios.cm.currentCourseModel.getSessionForContainer(dialog.cnumber);
    var element = null;

    if (typeof clearTransactionStatus == 'undefined') {
        clearTransactionStatus = true;
    }

    ilios.cm.lm.clearCachedTabPanes();
    ilios.cm.lm.handleLearningMaterialAddTypeClick(0);

    element = document.getElementById('alm_sid_value');
    element.value = (model == null) ? 0 : model.getDBId();

    element = document.getElementById('alm_cid_value');
    element.value = ilios.cm.currentCourseModel.getDBId();

    ilios.cm.lm.clearLearningMaterialsDialogFields(clearTransactionStatus);
};

// @private
ilios.cm.lm.almDirtyStateListener = {

        modelChanged: function (model, mockedThis) {
            ilios.cm.lm.learningMaterialLightboxIsDirty = true;
        }

};

/**
 * @method continueLearningMaterialDelete
 * @private
 * @param {Event} clickTarget
 * @param {Object} args
 */
ilios.cm.lm.continueLearningMaterialDelete = function (clickTarget, args) {
    var liElement = args.liElement;
    var isCourse = (ilios.cm.lm.learningMaterialDialog.cnumber == -1);

    liElement.parentNode.removeChild(liElement);

    ilios.cm.transaction.disassociateLearningMaterial(args.lmId, args.assocId, isCourse);

    this.hide();
};

// @private
ilios.cm.lm.makeCitationUploadDiv = function () {
    var rhett = document.createElement('div');
    var i18nStr = ilios_i18nVendor.getI18NString('general.terms.citation');
    var element = document.createElement('textarea');
    var label = document.createElement('label');

    rhett.setAttribute('style', 'margin-bottom: 12px;  margin-top: 24px;');

    label.setAttribute('style', 'vertical-align: top; float: left');
    label.appendChild(document.createTextNode(i18nStr + ': '));
    rhett.appendChild(label);

    element.setAttribute('id', 'alm_citation');
    element.setAttribute('name', 'citation');
    element.setAttribute('rows', '2');
    element.setAttribute('cols', '90');
    element.setAttribute("style", "float: right");

    YAHOO.util.Event.addListener(element, 'keyup', ilios.cm.lm.setLearningMaterialLightboxDirty);

    rhett.appendChild(element);

    ilios.utilities.appendClearingDivToContainer(rhett);

    return rhett;
};

// @private
ilios.cm.lm.makeLinkUploadDiv = function () {
    var rhett = document.createElement('div');
    var i18nStr = ilios_i18nVendor.getI18NString('general.terms.link');
    var label = document.createElement("label");
    var element = document.createElement("input");

    rhett.setAttribute('style', 'margin-bottom: 12px; margin-top: 24px;');

    label.setAttribute('style', 'vertical-align: top; float: left;');
    label.appendChild(document.createTextNode(i18nStr + ': '));
    rhett.appendChild(label);

    element.setAttribute('id', 'alm_web_link');
    element.setAttribute('type', 'text');
    element.setAttribute('name', 'web_link');
    element.setAttribute("size", "90");
    element.setAttribute("style", "float: right");
    YAHOO.util.Event.addListener(element, 'keyup', ilios.cm.lm.setLearningMaterialLightboxDirty);
    rhett.appendChild(element);

    ilios.utilities.appendClearingDivToContainer(rhett);

    return rhett;
};

// @private
ilios.cm.lm.makeAssetUploadDiv = function () {
    var rhett = document.createElement('div');
    var i18nStr = ilios_i18nVendor.getI18NString('general.terms.filename');
    var element = document.createElement('input');
    var label = document.createElement("label");

    rhett.setAttribute('style', 'margin-bottom: 6px;');

    label.setAttribute('style', 'vertical-align: top; float: left;');
    label.appendChild(document.createTextNode(i18nStr + ': '));
    rhett.appendChild(label);

    element.setAttribute('id', 'alm_filepath_field');
    element.setAttribute('type', 'file');
    element.setAttribute('name', 'userfile');
    element.setAttribute('size', '78');
    element.setAttribute("style", "float: right");
    YAHOO.util.Event.addListener(element, 'change', ilios.cm.lm.setLearningMaterialLightboxDirty);
    rhett.appendChild(element);

    ilios.utilities.appendClearingDivToContainer(rhett);

    return rhett;
};

//@private
ilios.cm.lm.makeSearchTabPaneDiv = function () {
    var rhett = document.createElement('div');
    var i18nStr = null;
    var container = null;
    var subContainer = null;

    i18nStr = ilios_i18nVendor.getI18NString('learning_material.search.title');
    container = document.createElement('input');
    container.setAttribute('id', 'alm_search_textfield');
    container.setAttribute('name', 'alm_search');
    container.setAttribute('type', 'text');
    container.setAttribute("size", "108");

    YAHOO.util.Event.addListener(container, 'keyup', ilios.cm.transaction.searchLearningMaterials);

    rhett.appendChild(document.createTextNode(i18nStr + ": "));
    rhett.appendChild(container);

    container = document.createElement('div');
    container.setAttribute('id', 'alm_search_results_div');
    subContainer = document.createElement('ul');
    subContainer.setAttribute('id', 'alm_search_results_ul');
    subContainer.setAttribute('class', 'learning_material_list');
    container.appendChild(subContainer);
    rhett.appendChild(container);

    return rhett;
};

//@private
ilios.cm.lm.makeTabPaneDiv = function (tabPaneContentFunction, showCopyrightInfo) {
    var Event = YAHOO.util.Event;
    var rhett = document.createElement('div');
    var i18nStr = null;
    var container = null;
    var subContainer = null;
    var element = null;
    var option = null;
    var key = null;

    container = document.createElement('div');
    container.setAttribute('style', 'margin-bottom: 12px;');
    i18nStr = ilios_i18nVendor.getI18NString('learning_material.asset.title');
    container.appendChild(document.createTextNode(i18nStr + ': '));
    element = document.createElement('input');
    element.setAttribute('id', 'alm_title');
    element.setAttribute('type', 'text');
    element.setAttribute('name', 'title');
    element.setAttribute("width", "30");
    element.setAttribute('style', 'margin-right: 18px;');
    Event.addListener(element, 'keyup', ilios.cm.lm.setLearningMaterialLightboxDirty);
    container.appendChild(element);
    i18nStr = ilios_i18nVendor.getI18NString('general.terms.status');
    container.appendChild(document.createTextNode(i18nStr + ': '));
    element = document.createElement('select');
    element.setAttribute('id', 'alm_statuses_selector');
    element.setAttribute('name', 'status');
    element.setAttribute('style', 'width: 85px;');
    for (key in ilios.cm.lm.learningMaterialStatuses) {
        option = document.createElement('option');
        option.setAttribute('value', key);
        option.appendChild(document.createTextNode(ilios.cm.lm.learningMaterialStatuses[key]));
        element.appendChild(option);
    }
    Event.addListener(element, 'change', ilios.cm.lm.setLearningMaterialLightboxDirty);
    container.appendChild(element);
    subContainer = document.createElement('div');
    subContainer.setAttribute('style', 'float: right; margin-right: 3px;');
    element = document.createElement('span');
    element.setAttribute('class', 'read_only_data');
    element.appendChild(document.createTextNode(adminUserDisplayName));
    subContainer.appendChild(element);
    i18nStr = ilios_i18nVendor.getI18NString('general.terms.role').toLowerCase();
    subContainer.appendChild(document.createTextNode("'s " + i18nStr + ": "));
    element = document.createElement('select');
    element.setAttribute('id', 'alm_roles_selector');
    element.setAttribute('name', 'owner_role');
    element.setAttribute('style', 'width: 150px;');
    for (key in ilios.cm.lm.learningMaterialOwnerRoles) {
        option = document.createElement('option');
        option.setAttribute('value', key);
        option.appendChild(document.createTextNode(ilios.cm.lm.learningMaterialOwnerRoles[key]));
        element.appendChild(option);
    }
    Event.addListener(element, 'change', ilios.cm.lm.setLearningMaterialLightboxDirty);
    subContainer.appendChild(element);
    container.appendChild(subContainer);
    ilios.utilities.appendClearingDivToContainer(container);
    rhett.appendChild(container);

    if (showCopyrightInfo) {
        container = document.createElement('div');
        container.setAttribute('style', 'float: left;');
        i18nStr = ilios_i18nVendor.getI18NString('learning_material.asset.copyright_ownership');
        container.appendChild(document.createTextNode(i18nStr + ': '));
        rhett.appendChild(container);
    }

    container = document.createElement('div');
    container.setAttribute('style', 'float: right; margin-bottom: 6px;');
    i18nStr = ilios_i18nVendor.getI18NString('learning_material.asset.creator');
    container.appendChild(document.createTextNode(i18nStr + ': '));
    element = document.createElement('input');
    element.setAttribute('id', 'alm_content_creator');
    element.setAttribute('type', 'text');
    element.setAttribute('name', 'content_creator');
    element.setAttribute('style', 'width: 273px;');
    Event.addListener(element, 'keyup', ilios.cm.lm.setLearningMaterialLightboxDirty);
    container.appendChild(element);
    rhett.appendChild(container);
    ilios.utilities.appendClearingDivToContainer(rhett);

    if (showCopyrightInfo) {
        container = document.createElement('div');
        container.setAttribute('style', 'float: left; width: 3%; margin-left: 2%;');
        element = document.createElement('input');
        element.setAttribute('id', 'alm_copyright_checkbox');
        element.setAttribute('type', 'checkbox');
        element.setAttribute('value', 'own_copyright');
        element.setAttribute('name', 'own_copyright');
        container.appendChild(element);
        Event.addListener(element, 'click', function () {
            var anElement = document.getElementById('alm_copyright_rationale');

            ilios.dom.setElementEnabled(anElement, (! this.checked));

            ilios.cm.lm.setLearningMaterialLightboxDirty();

            if (this.checked) {
                anElement.value = '';
            }

            anElement = new YAHOO.util.Element(document.getElementById('alm_copyright_disclaimer'));

            if (this.checked) {
                anElement.setStyle('color', '#000000');
            } else {
                anElement.setStyle('color', '#808080');
            }
        });

        rhett.appendChild(container);

        container = document.createElement('div');
        container.setAttribute('style', 'float: right; margin-bottom: 6px; color: #808080; font-size: 8pt; width: 94%;');
        container.setAttribute('id', 'alm_copyright_disclaimer');
        i18nStr = ilios_i18nVendor.getI18NString('learning_material.asset.copyright_disclaimer');
        container.appendChild(document.createTextNode(i18nStr));
        rhett.appendChild(container);
        ilios.utilities.appendClearingDivToContainer(rhett);

        container = document.createElement('div');
        container.setAttribute('style', 'margin-bottom: 12px;');
        i18nStr = ilios_i18nVendor.getI18NString('learning_material.asset.copyright_rationale');
        container.appendChild(document.createTextNode(i18nStr + ': '));
        element = document.createElement('input');
        element.setAttribute('id', 'alm_copyright_rationale');
        element.setAttribute('type', 'text');
        element.setAttribute('name', 'copyright_rationale');
        element.setAttribute('style', 'width: 628px;');
        container.appendChild(element);
        rhett.appendChild(container);
        Event.addListener(element, 'keyup', ilios.cm.lm.setLearningMaterialLightboxDirty);
        Event.addListener(element, 'focus', function () {
            if (! this.hasCleared) {
                this.value = '';
                this.hasCleared = true;
            }
        });
    }


    container = document.createElement('div');
    container.setAttribute("style", "margin-bottom: 6px;");
    i18nStr = ilios_i18nVendor.getI18NString('general.terms.description');
    subContainer = document.createElement('label');
    subContainer.setAttribute('style', 'vertical-align: top;');
    subContainer.appendChild(document.createTextNode(i18nStr + ': '));
    container.appendChild(subContainer);
    element = document.createElement('textarea');
    element.setAttribute('id', 'alm_description');
    element.setAttribute('name', 'description');
    element.setAttribute('rows', '5');
    element.setAttribute("cols", "98");

    //Event.addListener(element, 'keyup', ilios.cm.lm.setLearningMaterialLightboxDirty);
    container.appendChild(element);


    var descriptionEditor = new ilios.ui.RichTextEditor( element, {
        height: "180px" // must give it a height, otherwise Chrome will break the layout of the dialog.
    });

    descriptionEditor.on('editorKeyUp', ilios.cm.lm.setLearningMaterialLightboxDirty);
    descriptionEditor.on('afterNodeChange', ilios.cm.lm.setLearningMaterialLightboxDirty);

    descriptionEditor.render();

    rhett.appendChild(container);
    ilios.utilities.appendClearingDivToContainer(rhett);

    rhett.appendChild(tabPaneContentFunction());

    container = document.createElement("div");
    container.setAttribute("style", "float: right;");
    i18nStr = ilios_i18nVendor.getI18NString("general.phrases.add_learning_material");
    element = document.createElement("button");
    element.setAttribute("id", "alm_upload_button");
    element.setAttribute("onclick",
        "ilios.cm.transaction.handleAddLearningMaterialUploadClick(this); return false;");
    element.appendChild(document.createTextNode(i18nStr));
    container.appendChild(element);
    rhett.appendChild(container);
    ilios.utilities.appendClearingDivToContainer(rhett);

    return rhett;
};

ilios.cm.lm.handleLearningMaterialAddTypeClick = function (tabNumber) {
    var Element = YAHOO.util.Element;
    if (('undefined' != typeof event) && ('undefined' != typeof event.preventDefault)) {
        event.preventDefault();
    }

    var element = null;
    var tabPaneContent = null;

    // MAY RETURN THIS BLOCK
    if (tabNumber == ilios.cm.lm.currentlyShowingTab) {
        return false;
    }

    for (var i = 0; i < 4; i++) {
        if (i != tabNumber) {
            element = new Element(document.getElementById('add_type_tab_' + i));

            if (i == 0) {
                element.setStyle('border-left', '1px #a2a2a2 solid');
            }
            else {
                element.setStyle('border-left', 'none');
            }

            element.setStyle('border-top', '1px #a2a2a2 solid');
            element.setStyle('border-right', '1px #a2a2a2 solid');
        }
    }

    element = new Element(document.getElementById('add_type_tab_' + tabNumber));
    element.setStyle('border-left', '2px #000000 solid');
    element.setStyle('border-top', '2px #000000 solid');
    element.setStyle('border-right', '2px #000000 solid');

    element = document.getElementById('alm_tab_pane');
    ilios.utilities.removeAllChildren(element);

    // For some reason the rich text editor does not work inside the tab. It will be disabled
    // when going back and forth different tabs.  Forcing redraw on all the tab panes fixes
    // the problem, but of course, does not retain user's input.
    ilios.cm.lm.assetTabPane = null;
    ilios.cm.lm.linkTabPane = null;
    ilios.cm.lm.citationTabPane = null;

    switch (tabNumber) {
        case 0:
            if (ilios.cm.lm.searchTabPane == null) {
                ilios.cm.lm.searchTabPane = ilios.cm.lm.makeSearchTabPaneDiv();
            }

            tabPaneContent = ilios.cm.lm.searchTabPane;

            break;
        case 1:
            if (ilios.cm.lm.assetTabPane == null) {
                ilios.cm.lm.assetTabPane = ilios.cm.lm.makeTabPaneDiv(ilios.cm.lm.makeAssetUploadDiv,
                                                                      true);
            }

            tabPaneContent = ilios.cm.lm.assetTabPane;

            break;
        case 2:
            if (ilios.cm.lm.linkTabPane == null) {
                ilios.cm.lm.linkTabPane = ilios.cm.lm.makeTabPaneDiv(ilios.cm.lm.makeLinkUploadDiv,
                                                                     false);
            }

            tabPaneContent = ilios.cm.lm.linkTabPane;

            break;
        case 3:
            if (ilios.cm.lm.citationTabPane == null) {
                ilios.cm.lm.citationTabPane
                            = ilios.cm.lm.makeTabPaneDiv(ilios.cm.lm.makeCitationUploadDiv, false);
            }

            tabPaneContent = ilios.cm.lm.citationTabPane;

            break;
    }
    element.appendChild(tabPaneContent);

    ilios.cm.lm.currentlyShowingTab = tabNumber;

    return false;
};

// @private
ilios.cm.lm.clearCachedTabPanes = function () {
    ilios.cm.lm.searchTabPane = null;
    ilios.cm.lm.assetTabPane = null;
    ilios.cm.lm.linkTabPane = null;
    ilios.cm.lm.citationTabPane = null;
};

/**
 * TODO: JH - need to add comments/document
 *
 * This populates the the top-level container on the courses/sessions page with LM's
 * pre-built as divs...
 *
 */

ilios.cm.lm.populateLearningMaterialsContainer = function (containerNumber) {
    //only the course should have -1 containerNumber
    var isCourse = (containerNumber == -1);
    //get the course model or the session model
    var model = isCourse ? ilios.cm.currentCourseModel
        : ilios.cm.currentCourseModel.getSessionForContainer(containerNumber);
    //get the learning materials from the course/session model
    var learningMaterials = model.getLearningMaterials();
    //create each of the learning material items to populate the container
    var learningMaterialItems = ilios.cm.lm.buildLearningMaterialItemsForContainer (learningMaterials, containerNumber);
    //if we're in a session, build the new lmContainer
    if(!isCourse) {
        var lmContainerElement = document.createElement('div');
        lmContainerElement.setAttribute('id', ilios.cm.lm.generateIdStringForLearningMaterialsContainer(containerNumber));
    }
    //the lmContainer is now built or it already exists (for a course), so target it for adding the learning materials
    lmContainer = document.getElementById(ilios.cm.lm.generateIdStringForLearningMaterialsContainer(containerNumber));
    //loop through all the learning materials to create and append each row to the lmContainer
    for (var key in learningMaterialItems) {
        lmContainer.appendChild(learningMaterialItems[key]);
    }
    //and finally, update the learning material count
    ilios.cm.lm.updateLearningMaterialCountText(containerNumber);
};

/**
 * TODO: JH - need to add comments/document
 *
 * This builds all the LM divs for population in the top-level container on the courses/sessions page.
 *
 */

ilios.cm.lm.buildLearningMaterialItemsForContainer = function (learningMaterials, containerNumber) {
    //instantiate an array to hold all the learning materials
    var learningMaterialItems = [];
    var key = null;
    //now create each learning material from the learningMaterials model
    for (key = 0; key < learningMaterials.length; key += 1) {
        //set the lmNumber off the zero-indexed key
        lmNumber = ((Number (key)) + 1);
        //load the individual learning material as its own respective model
        learningMaterialModel = learningMaterials[key];
        //now add the individual learning material items
        learningMaterialItems.push(
            ilios.cm.lm.buildLearningMaterialItem(learningMaterialModel,containerNumber,lmNumber));
    }
    return learningMaterialItems;
};


/**
 * TODO: JH - need to add comments/document
 *
 * Builds the individual learning material div for displaying in courses/sessions...
 */

ilios.cm.lm.buildLearningMaterialItem = function (learningMaterialModel, containerNumber, lmNumber, isLocked) {

    var learningMaterialItemModel = learningMaterialModel;
    var learningMaterialItemNumber = Number(lmNumber);
    var learningMaterialItemTitle = learningMaterialItemModel.getTitle();


    //set up the title text as a link, so we can view the learniing materials details
    var linkedTitleElement = document.createElement('a');
    linkedTitleElement.setAttribute('title', learningMaterialItemTitle);
    linkedTitleElement.setAttribute('href','');
    linkedTitleElement.setAttribute('onclick','return false;');
    linkedTitleElement.setAttribute('lmnumber',learningMaterialItemNumber);
    linkedTitleElement.innerText = learningMaterialItemTitle;
    var mimeTypeClass = null;


    var scratchElement = null;
    var scratchString = null;
    var fileSizeElement = null;
    var downloadURL = learningMaterialsControllerURL
        + "getLearningMaterialWithId?learning_material_id=" + learningMaterialItemModel.getDBId();
    var affectingWidget = document.createElement('span');

    var isLink = (learningMaterialItemModel.getMimeType() == 'link');
    var isCitation = (learningMaterialItemModel.getMimeType() == 'citation');
    var Event = YAHOO.util.Event;
    var Element = YAHOO.util.Element;
    var isLocked = isLocked || false;

    //declare the parent container which will hold all the learning material items
    var parentContainer = document.getElementById(containerNumber+"_learning_materials_container");

    //set up the container that will hold the entire row
    var learningMaterialItem = document.createElement('div');
    learningMaterialItem.setAttribute('class', 'learning_material_container');
    learningMaterialItem.setAttribute('cnumber', containerNumber);
    learningMaterialItem.setAttribute('lmnumber', learningMaterialItemNumber);

    //get the mimeType of the learning material to set the styling
    mimeTypeClass = ilios.utilities.convertMimeTypeToCSSClassName(learningMaterialModel.getMimeType());

    // Delete widget
    scratchElement = new Element(document.createElement('div'));
    scratchElement.addClass('delete_widget icon-cancel');
    scratchElement.get('element').setAttribute('title', ilios_i18nVendor.getI18NString("general.phrases.delete_learning_material"));
    scratchElement.get('element').setAttribute('cnumber', containerNumber);
    scratchElement.get('element').setAttribute('lmnumber', learningMaterialItemNumber);
    if (! isLocked) {
        scratchElement.addListener('click', ilios.cm.lm.deleteLearningMaterial, null, this);
    }
    learningMaterialItem.appendChild(scratchElement.get('element'));
    ilios.cm.uiElementsToHideOnLockedView.push(scratchElement);

    // learning material description container
    scratchElement = document.createElement('div');
    scratchString = ilios.cm.lm.generateIdStringForLearningMaterialTextArea(containerNumber, lmNumber);

    scratchElement.setAttribute('class', 'learning_material_description_container ' + mimeTypeClass);
    scratchElement.setAttribute('id', scratchString);
    scratchElement.appendChild(linkedTitleElement);

    //the button/download widgets
    //the buttonWidget is a div that will contain the button widgets (eg, downloadWidget...)
    var buttonWidgetDiv = document.createElement('span');
    buttonWidgetDiv.setAttribute('class', 'buttonset');

    //the downloadWidget will display/handle the download of a lm resoource
    var downloadWidget = document.createElement('span');
    downloadWidget.setAttribute('class', 'download_widget');

    //if it's not a link or a citation...
    if ((! isLink) && (! isCitation)) {
        fileSizeElement = document.createElement('span');
        fileSizeElement.setAttribute('class', 'filesize');
        fileSizeElement.innerHTML = ' &nbsp;(' + learningMaterialModel.getFileSize() + ' KB)';
        //add the filesize to lm description container
        scratchElement.appendChild(fileSizeElement);

        //it's a file, so attach the download widget to the buttonWidget container
        buttonWidgetDiv.appendChild(downloadWidget);
    }

    if (! ilios.cm.currentCourseModel.isLocked()) {
        //set up the addLearningMaterialButton
        var alm_button = document.getElementById(containerNumber + '_add_learning_material_link');
        Event.addListener(alm_button, 'click', function (e) {
            ilios.cm.lm.addNewLearningMaterial(containerNumber);
        });
    }

    /*
     * TODO: JH - re-enable the lock checks
     */

    /*if (ilios.cm.currentCourseModel.isLocked()) {
        /*buttonWidgetDiv.appendChild(affectingWidget);

        Event.addListener(affectingWidget, 'click', function (e) {
            ilios.cm.lm.newHandleLearningMaterialClick(this, model, showAddIcon);
        });
    } else {
        ilios.cm.uiElementsToHideOnLockedView.push(new YAHOO.util.Element(affectingWidget));
    }*/

    Event.addListener(downloadWidget, 'click', function (e) {
        window.location.href = downloadURL;
    });


    //attach the listener to the learning materials link to show the details
    //of the learning material...
    var showAddIcon = false;
    if (! showAddIcon) {
        Event.addListener(linkedTitleElement, 'click', function (e) {
            ilios.common.lm.learningMaterialsDetailsModel = learningMaterialModel;
            ilios.ui.onIliosEvent.fire({
                action: 'lm_metadata_dialog_open',
                container_number: containerNumber
            });
            return false;
        });
    }

    //add the button widget container to the description container
    scratchElement.appendChild(buttonWidgetDiv);

    //add the description div to the parent container
    learningMaterialItem.appendChild(scratchElement);

    //TODO: JH - renable the lock-checking...
    /*if (! isLocked) {
        if (-1 === containerNumber) {  // course learning material
            // register click event handler on learning material description container
            Event.addListener(scratchElement, "click", function (e) { // pop up the "Search/Add" dialog
                //ilios.cm.inEditObjectiveModel = objectiveModel;
                ilios.ui.onIliosEvent.fire({
                    action: 'alm_dialog_open',
                    cnumber: containerNumber,
                    lmnumber: lmNumber
                });
            });
        } else { // session learning material
            // register click event handler on learning material description container
            Event.addListener(scratchElement, "click", function (e) { // pop up the "Search/Add" dialog
                //ilios.cm.inEditObjectiveModel = objectiveModel;
                ilios.ui.onIliosEvent.fire({
                    action: 'alm_dialog_open',
                    cnumber: containerNumber,
                    lmnumber: lmNumber
                });
            });
        }

        learningMaterialItemModel.addStateChangeListener(ilios.cm.learningMaterialDirtyStateListener, {containerId : scratchString});
    }*/
    scratchString = ilios.cm.lm.generateIdStringForLearningMaterialMeSHLink(containerNumber, learningMaterialItemNumber);
    scratchInput = document.createElement('a');
    scratchInput.setAttribute('id', scratchString);
    scratchInput.setAttribute('class', 'mesh_btn tiny secondary radius button');
    scratchInput.setAttribute('href', '');
    scratchInput.setAttribute('onclick', 'return false;');
    if (! isLocked) {
        Event.addListener(scratchInput, 'click', function (e) {
            ilios.ui.onIliosEvent.fire({
                action: 'mesh_picker_dialog_open',
                model_in_edit: learningMaterialItemModel
            });
            return false;
        });
    }

    scratchInput.innerHTML = ilios.cm.meshLinkText(learningMaterialItemModel);
    ilios.cm.uiElementsToHideOnLockedView.push(new Element(scratchInput));
    learningMaterialItem.appendChild(scratchInput);

    //append the clearing div to the newly-created learning material container
    ilios.utilities.appendClearingDivToContainer(learningMaterialItem);

    //add everything to the parent container
    return learningMaterialItem;

};

/**
 * TODO: JH - need to add comments/document
 *
 * The mimics (replaces?) the list-based functionality of ilios.cm.lm.handleLearningMaterialClick
 * and applies it to the div-based LM list in courses and sessions....
 */

ilios.cm.lm.newHandleLearningMaterialClick = function (learningMaterialModel, add) {
    var lmElement = null
    var containerNumber = null;
    var isCourse = null;
    var model = null;


        containerNumber = ilios.cm.lm.learningMaterialDialog.cnumber;
        isCourse = (containerNumber == -1);
        model = isCourse ? ilios.cm.currentCourseModel
            : ilios.cm.currentCourseModel.getSessionForContainer(containerNumber);

        //liElement.parentNode.removeChild(liElement);

        model.addLearningMaterial(learningMaterialModel);

        ilios.cm.transaction.associateLearningMaterial(learningMaterialModel.getDBId(),
            model.getDBId(), isCourse);
    //ilios.cm.lm.setLearningMaterialDivVisibility(containerNumber, null, false);

        ilios.cm.lm.clearLearningMaterialsDialogFields(true);

        ilios.cm.lm.setLearningMaterialDivVisibility(containerNumber, null, false);

        ilios.cm.lm.clearLearningMaterialsDialogFields(true);

};


/**
 * TODO: JH - need to add comments/document
 *
 * Initiates the addition of a new learning material to a course/session by firing up the "add learning materials"
 * search popup window. Disallows the addition of a learning material to a session and alerts if the session has not
 * been saved yet (isDirty).
 *
 * @method addNewLearningMaterial
 * @param {String} containerNumber the learning materials container id
 */

ilios.cm.lm.addNewLearningMaterial = function (containerNumber) {

    //check if it is a course..
    var isCourse = (containerNumber == -1);
    //a course is saved upon creation, so if it is not a course, check to make sure that the session
    //is not dirty...
    model = isCourse ? ilios.cm.currentCourseModel
        : ilios.cm.currentCourseModel.getSessionForContainer(containerNumber);

    //if the model already exists and is not dirty, open the Add LM dialog...
    if((model) && (!model.isDirty)){
        ilios.ui.onIliosEvent.fire({
            action: 'alm_dialog_open',
            container_number: containerNumber
        });
        return false;
    } else {
        //if not, warn the user to save the session first...
        var i18nString = ilios_i18nVendor.getI18NString('learning_material.error.save_session_first');
        ilios.alert.alert(i18nString);
    }
};

/**
 * TODO: JH - need to add comments/document
 *
 * initiates disassociation process of lm's from course or session
 */
ilios.cm.lm.deleteLearningMaterial = function (event) {
    var target = ilios.utilities.getEventTarget(event);
    var deleteLearningMaterialStr = ilios_i18nVendor.getI18NString("general.warning.delete_learning_material");
    var yesStr = ilios_i18nVendor.getI18NString('general.terms.yes');
    var args = {
        "cnumber": target.getAttribute("cnumber"),
        "lmnumber": target.getAttribute("lmnumber")
    };
    ilios.alert.inform(deleteLearningMaterialStr, yesStr, ilios.cm.lm.continueDeletingLearningMaterial, args);
};

/**
 * TODO: JH - need to add comments/document
 *
 * "Click" event handler function for the "delete learning material" confirmation dialog's "OK" button.
 * @method ilios.cm.lm.continueDeletingLearningMaterial
 * @param {Event} event
 * @param {Object} obj handler arguments, expected attributes are the corresponding container id ("cnumber")
 *    and the id of the learning material to delete ("lmNumber").
 */
ilios.cm.lm.continueDeletingLearningMaterial = function(event, obj) {
    var containerNumber = obj.cnumber;
    var lmNumber = obj.lmnumber;
    var isCourse = (containerNumber == -1);
    var lmDescriptionContainerId = ilios.cm.lm.generateIdStringForLearningMaterialTextArea(containerNumber, lmNumber);
    var model = isCourse ?
        ilios.cm.currentCourseModel : ilios.cm.currentCourseModel.getSessionForContainer(containerNumber);
    var assocId = model.getDBId();
    var element = document.getElementById(lmDescriptionContainerId).parentNode;

    var learningMaterials = model.getLearningMaterials();
    var lmIndex = (lmNumber - 1);
    var learningMaterialModel = learningMaterials[lmIndex];
    var lmId = learningMaterialModel.getDBId();

    //this should disassociate the learning material
    ilios.cm.transaction.disassociateLearningMaterial(lmId, assocId, isCourse);
    element.parentNode.removeChild(element);
    model.removeLearningMaterialFromContainer(lmIndex);
    ilios.cm.lm.updateLearningMaterialCountText(containerNumber);
    this.hide();
};

/*
 * TODO: JH - need to add comments/document
 *
 * updates the learning material count between the parentheses in the course/session...
 */

ilios.cm.lm.updateLearningMaterialCountText = function (containerNumber) {
    var isCourse = (containerNumber == -1);
    var model = isCourse ? ilios.cm.currentCourseModel
        : ilios.cm.currentCourseModel.getSessionForContainer(containerNumber);
    var idString = ilios.cm.lm.generateIdStringForLearningMaterialsContainerLabel(containerNumber);
    var i18nStr = ilios_i18nVendor.getI18NString('general.phrases.learning_materials');
    var element = document.getElementById(idString);

    element.innerHTML = i18nStr + ' (' + ilios.utilities.objectPropertyCount(model.getLearningMaterials()) + ')';
};

/*
 * TODO: JH - need to add comments/document
 *
 * this adds the lm to the learning materials div list after it has been selected from the
 * search box.
 */

ilios.cm.lm.addNewLearningMaterialToDom = function (containerNumber, learningMaterialId) {
    if (ilios.cm.currentCourseModel != null) {
        var isCourse = null;
        var model = null;
        isCourse = (containerNumber == -1);
        model = isCourse ? ilios.cm.currentCourseModel
            : ilios.cm.currentCourseModel.getSessionForContainer(containerNumber);
        var nextLearningMaterialItemNumber = model.getNextLearningMaterialNumber();
        //var nextLearningMaterialItemNumber = (learningMaterialItemNumber + 1);
        //ilios.cm.lm.buildAndPopulateLearningMaterial(containerNumber, nextLearningMaterialItemNumber, model, learningMaterialModel, container);
        var learningMaterialModel = ilios.cm.lm.getLearningMaterialModelFromId(model, learningMaterialId);
        var lmItem = ilios.cm.lm.buildLearningMaterialItem (learningMaterialModel, containerNumber, nextLearningMaterialItemNumber, false);
        var containerId = ilios.cm.lm.generateIdStringForLearningMaterialsContainer(containerNumber);
        var container = document.getElementById(containerId);
        container.appendChild(lmItem);
        ilios.cm.lm.updateLearningMaterialCountText(containerNumber);
        ilios.cm.lm.setLearningMaterialDivVisibility(containerNumber, null, false);
    }
};

/*
 * TODO: JH - need to add comments/document
 *
 * get the learning material model from the lm's dbId - for whatever reason
 * the same type of function defined in the course_model was not being found
 */

ilios.cm.lm.getLearningMaterialModelFromId = function (courseOrSessionModel, learningMaterialId) {
    var learningMaterials = courseOrSessionModel.learningMaterials;
    var key = null;
    for (key = 0; key < learningMaterials.length; key += 1) {
        if (learningMaterialId == learningMaterials[key].dbId) {
            return learningMaterials[key];
        }
    }
}

/*
 * @param shouldToggle if true, the current visibility will be toggled; if false, the div will be
 *                          made visible
 */
ilios.cm.lm.setlearningMaterialDivVisibility = function (containerNumber, widgetDiv, shouldToggle) {
    var Element = YAHOO.util.Element;
    var element = null;
    var idString = null;
    var div = null;

    if (ilios.cm.currentCourseModel == null) {
        return;
    }

    if (widgetDiv == null) {
        idString = ilios.cm.lm.generateIdStringForLearningMaterialsContainerExpandWidget(containerNumber);
        widgetDiv = document.getElementById(idString);
    }

    element = new Element(widgetDiv);
    idString = ilios.cm.lm.generateIdStringForLearningMaterialsContainer(containerNumber);
    div = new Element(document.getElementById(idString));
    if ((div.getStyle('display') != 'none') && shouldToggle) {
        element.removeClass('expanded_widget');
        element.addClass('collapsed_widget');
        div.setStyle('display', 'none');
    }
    else {
        element.removeClass('collapsed_widget');
        element.addClass('expanded_widget');
        div.setStyle('display', 'block');
    }
};