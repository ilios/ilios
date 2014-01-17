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
            ilios.cm.lm.handleLearningMaterialClick(this, model, showAddIcon);
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
            IEvent.fire({
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
        idString = ilios.cm.lm.generateIdStringForLearningMaterialExpandWidget(containerNumber);

        widgetDiv = document.getElementById(idString);
    }

    yElement = new Element(widgetDiv);
    idString = ilios.cm.lm.generateIdStringForLearningMaterialList(containerNumber);
    div = new Element(document.getElementById(idString).parentNode);
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
    ilios.cm.lm.almLearningMaterialModel.addStateChangeListener(ilios.cm.lm.almDirtyStateListener,
                                                                null);
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
