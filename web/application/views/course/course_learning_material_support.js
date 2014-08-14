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
 * Creates an element id for the div that holds all of a course/session's learning materials
 * Formatted as [containerNubmer]_[lm dbId]_learning_materials_container
 *
 * @param {int} containerNumber the numeric id of the lm's container
 * @param {int} the dbId of the learning material
 * @return {String} the id string for the element
 */
ilios.cm.lm.generateIdStringForLearningMaterialTextArea = function (containerNumber, lmNumber) {
    return '' + containerNumber + '_' + lmNumber + '_learning_materials_container';
};

ilios.cm.lm.generateIdStringForLearningMaterialsContainerExpandWidget = function (containerNumber) {
    return '' + containerNumber + '_learning_materials_container_expand_widget';
};

/**
 * Creates an element id for the 'Add MeSH (x)' button for a learning material
 * Formatted as [containerNubmer]_[lm dbId]_learning_materials_mesh_link
 *
 * @param {int} containerNumber the numeric id of the lm's container
 * @param {int} the dbId of the learning material
 * @return {String} the id string for the element
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

/**
 * Populates the learning material (ul/li) list of the LM search dialog results
 *
 * @param containerNumber
 */

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
            ilios.cm.lm.handleLearningMaterialClick(model);
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
 * This populates the the top-level course/session containers on the course management page with the entire
 * "list" of LM divs associated to the course/session.
 *
 * @method populateLearningMaterialsContainer
 * @param {Int} ContainerNumber the numeric id of the course/session container within which the lm's will be added
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
    //if we're in a session, build the new lmContainer entirely
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

/***
 * This builds all the LM divs for population in the respective top-level course/session container
 * on the course page
 *
 * @method buildLearningMaterialItemsForContainer
 * @param {Array} learningMaterials the array of all learning material objects in a course or session
 * @param {int} containerNumber the numeric id of the course/session container
 * @return {Array} learningMaterialItems an array of built-out learning material item divs
 *
 */

ilios.cm.lm.buildLearningMaterialItemsForContainer = function (learningMaterials, containerNumber) {
    //instantiate an array to hold all the built learning materials
    var learningMaterialItems = [];
    var key = null;
    //now create the array of pre-built learning materials from the learningMaterials' models
    for (key = 0; key < learningMaterials.length; key += 1) {
        //load the individual learning material as its own respective model
        learningMaterialModel = learningMaterials[key];
        //build out the learning material items for display and them to the new array
        learningMaterialItems.push(
            ilios.cm.lm.buildLearningMaterialItem(learningMaterialModel, containerNumber));
    }
    //return the array of ALL of the newly-built lm items to the parent container
    return learningMaterialItems;
};


/**
 * builds and returns one fully-built learning material item with its deletion, download and
 * 'Add Learning Material' buttons
 *
 * @method buildLearningMaterialItem
 * @param {Object} learningMaterialItemModel the learning material object
 * @param {Int} containerNumber the numeric id of the container the lm is in, used for determing course/session
 * @return {String} the fully-built learning material item, ready for placement in the DOM
 */

ilios.cm.lm.buildLearningMaterialItem = function (learningMaterialItemModel, containerNumber) {

    //initialize some vars
    var Event = YAHOO.util.Event;
    var Element = YAHOO.util.Element;
    var scratchElement = null;
    var fileSizeElement = null;

    //get the title of the lm
    var learningMaterialItemTitle = learningMaterialItemModel.getTitle();
    //get the dbId of the lm
    var learningMaterialItemId = learningMaterialItemModel.getDBId();

    //set up the title text as a link, so we can view the learning material's detail dialog upon clicking
    var linkedTitleElement = document.createElement('a');
    linkedTitleElement.setAttribute('title', learningMaterialItemTitle);
    linkedTitleElement.setAttribute('href','');
    linkedTitleElement.setAttribute('onclick','return false;');
    linkedTitleElement.setAttribute('lmnumber',learningMaterialItemId);
    linkedTitleElement.innerHTML = learningMaterialItemTitle;

    //build/set the download link
    var downloadURL = learningMaterialsControllerURL
        + "getLearningMaterialWithId?learning_material_id=" + learningMaterialItemId;

    //check the mimetype for the lm for special handling
    var isLink = (learningMaterialItemModel.getMimeType() == 'link');
    var isCitation = (learningMaterialItemModel.getMimeType() == 'citation');

    //set up the container that will hold the entire row for the single learning material
    var learningMaterialItem = document.createElement('div');
    learningMaterialItem.setAttribute('class', 'learning_material_container');
    learningMaterialItem.setAttribute('cnumber', containerNumber);
    learningMaterialItem.setAttribute('lmnumber', learningMaterialItemId);

    //set the class name of the row, based on the mime-type
    mimeTypeClass = ilios.utilities.convertMimeTypeToCSSClassName(learningMaterialItemModel.getMimeType());

    //Set up the 'delete' widget which will disassociate the lm with its course/session
    //do not display it if the course is locked
    if (! ilios.cm.currentCourseModel.isLocked()) {
        scratchElement = new Element(document.createElement('div'));
        scratchElement.addClass('delete_widget icon-cancel');
        scratchElement.get('element').setAttribute('title', ilios_i18nVendor.getI18NString("general.phrases.delete_learning_material"));
        scratchElement.get('element').setAttribute('cnumber', containerNumber);
        scratchElement.get('element').setAttribute('lmnumber', learningMaterialItemId);
        scratchElement.addListener('click', ilios.cm.lm.deleteLearningMaterial, null, this);
        learningMaterialItem.appendChild(scratchElement.get('element'));
        ilios.cm.uiElementsToHideOnLockedView.push(scratchElement);
    }

    //set up learning material description container to hold the linked title of the LM
    scratchElement = document.createElement('div');
    //set the id of the div to include the lm's dbID and the container number
    scratchString = ilios.cm.lm.generateIdStringForLearningMaterialTextArea(containerNumber, learningMaterialItemId);
    scratchElement.setAttribute('class', 'learning_material_description_container ' + mimeTypeClass);
    scratchElement.setAttribute('id', scratchString);
    //insert the linked title into the div
    scratchElement.appendChild(linkedTitleElement);

    //the buttonWidget is a span container that will hold the download widget
    var buttonWidgetDiv = document.createElement('span');
    buttonWidgetDiv.setAttribute('class', 'buttonset');

    //initialize the downloadWidget will handle the download of a lm file resource
    var downloadWidget = document.createElement('span');
    downloadWidget.setAttribute('class', 'download_widget');

    //if the lm is not a link or a citation, add its filesize
    if ((! isLink) && (! isCitation)) {
        fileSizeElement = document.createElement('span');
        fileSizeElement.setAttribute('class', 'filesize');
        fileSizeElement.innerHTML = ' &nbsp;(' + learningMaterialItemModel.getFileSize() + ' KB)';
        //add the filesize to lm description container
        scratchElement.appendChild(fileSizeElement);

        //it's a file, so attach the download widget to the buttonWidget container
        buttonWidgetDiv.appendChild(downloadWidget);
    }

    //add the download behavior to the download widget arrow
    Event.addListener(downloadWidget, 'click', function (e) {
        window.location.href = downloadURL;
    });

    //if the course is not locked, add the 'Add Learning Material' button
    if (! ilios.cm.currentCourseModel.isLocked()) {
        //set up the 'Add Learning Material' button behavior
        var alm_button = document.getElementById(containerNumber + '_add_learning_material_link');
        Event.addListener(alm_button, 'click', function (e) {
            ilios.cm.lm.addNewLearningMaterial(containerNumber);
        });
    }

    //attach the on-click behavior to the learning materials title link to show the detail dialog
    //of the learning material that lets you change properties of the lm itself
    Event.addListener(linkedTitleElement, 'click', function (e) {
        ilios.common.lm.learningMaterialsDetailsModel = learningMaterialItemModel;
        ilios.ui.onIliosEvent.fire({
            action: 'lm_metadata_dialog_open',
            cnumber: containerNumber,
            lmnumber: learningMaterialItemId
        });
        return false;
    });

    //add the button widget container to the description container
    scratchElement.appendChild(buttonWidgetDiv);

    //add the description div to the parent container
    learningMaterialItem.appendChild(scratchElement);

    //set up for the 'Add MeSH (x)' button
    scratchString = ilios.cm.lm.generateIdStringForLearningMaterialMeSHLink(containerNumber, learningMaterialItemId);
    scratchInput = document.createElement('a');
    scratchInput.setAttribute('id', scratchString);
    scratchInput.setAttribute('class', 'mesh_btn tiny secondary radius button');
    scratchInput.setAttribute('href', '');
    scratchInput.setAttribute('onclick', 'return false;');
    //if the course or session is not locked, add the MeSH button and its behavior
    if (! ilios.cm.currentCourseModel.isLocked()) {
        Event.addListener(scratchInput, 'click', function (e) {
            ilios.common.lm.learningMaterialsDetailsModel = learningMaterialItemModel;
            ilios.ui.onIliosEvent.fire({
                action: 'mesh_picker_dialog_open',
                cnumber: containerNumber,
                lmnumber: learningMaterialItemId,
                model_in_edit: ilios.common.lm.learningMaterialsDetailsModel,
                //because lm mesh terms can be updated from within their Details dialog
                //OR the mesh-only mesh picker dialog, we need to check for the latter
                //so we know how to handle the save/cancel buttons
                dialog_type: 'learning_material_mesh_only'
            });
            return false;
        });
    }

    //add the text for the mesh link button
    scratchInput.innerHTML = ilios.cm.meshLinkText(learningMaterialItemModel);
    ilios.cm.uiElementsToHideOnLockedView.push(new Element(scratchInput));
    learningMaterialItem.appendChild(scratchInput);

    //append the clearing div to the newly-created learning material container
    ilios.utilities.appendClearingDivToContainer(learningMaterialItem);

    //return the now-built lm item to the parent container
    return learningMaterialItem;

};

/**
 * Adds a selected learning material to the respective course/session model
 *
 * @method handleLearningMaterialClick
 * @param {object} learningMaterialModel the learning material model
 *
 */

ilios.cm.lm.handleLearningMaterialClick = function (learningMaterialModel) {
    var lmElement = null
    var containerNumber = null;
    var isCourse = null;
    var model = null;
    var learningMaterialItemId = learningMaterialModel.getDBId();

    containerNumber = ilios.cm.lm.learningMaterialDialog.cnumber;
    isCourse = (containerNumber == -1);
    model = isCourse ? ilios.cm.currentCourseModel
            : ilios.cm.currentCourseModel.getSessionForContainer(containerNumber);

    model.addLearningMaterial(learningMaterialModel);

    ilios.cm.transaction.associateLearningMaterial(learningMaterialItemId,
        model.getDBId(), isCourse);

    ilios.cm.lm.clearLearningMaterialsDialogFields(true);

    ilios.cm.lm.setLearningMaterialDivVisibility(containerNumber, null, false);

    ilios.cm.lm.clearLearningMaterialsDialogFields(true);

};


/**
 *
 * Fires up the "add learning materials" dialog to initiate the addition of a new learning material
 * to a course/session. Disallows the addition of a learning material to a session and warns the user if
 * the session has not yet been saved (dbId == -1)
 *
 * @method addNewLearningMaterial
 * @param {Int} containerNumber the learning material's associated container id
 */

ilios.cm.lm.addNewLearningMaterial = function (containerNumber) {

    //check if it is a course..
    var isCourse = (containerNumber == -1);
    //a course is saved upon creation, so if it is not a course, check to make sure that the session
    //is not dirty...
    model = isCourse ? ilios.cm.currentCourseModel
        : ilios.cm.currentCourseModel.getSessionForContainer(containerNumber);

    //If the session has not been saved, do not permit addition of Learning Materials
    //the model.dbId will be -1 if the session has not yet been saved
    if(model.dbId !== -1){
        ilios.ui.onIliosEvent.fire({
            action: 'alm_dialog_open',
            container_number: containerNumber
        });
        return false;
    } else {
        //if not, warn the user that the session should be saved first...
        var i18nString = ilios_i18nVendor.getI18NString('learning_material.error.save_session_first');
        ilios.alert.alert(i18nString);
    }
};

/**
 * Initiates the disassociation (deletion) of a learning material from a course or session and displays
 * verification pop-up to user to confirm the deletion
 *
 * @event the click event
 *
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
 * "Click" event handler function for the "delete learning material" confirmation dialog's "OK" button.
 * @method ilios.cm.lm.continueDeletingLearningMaterial
 * @event
 * @param {Object} obj handler arguments, expected attributes are the corresponding container id ("cnumber")
 *    and the id of the dbId learning material to delete ("lmnumber").
 */
ilios.cm.lm.continueDeletingLearningMaterial = function(event, obj) {
    var containerNumber = obj.cnumber;
    var lmNumber = obj.lmnumber;
    //whether or not it is dealing with a course or session
    var isCourse = (containerNumber == -1);
    var lmDescriptionContainerId = ilios.cm.lm.generateIdStringForLearningMaterialTextArea(containerNumber, lmNumber);
    var model = isCourse ?
        ilios.cm.currentCourseModel : ilios.cm.currentCourseModel.getSessionForContainer(containerNumber);
    //get the associated course or session id
    var assocId = model.getDBId();
    //get the wrapping div that holds the learning material info
    var element = document.getElementById(lmDescriptionContainerId).parentNode;
    //disassociate the learning material in the db via XHR request
    ilios.cm.transaction.disassociateLearningMaterial(lmNumber, assocId, isCourse);
    //remove the dissassociated item from the course/session model
    model.removeLearningMaterialWithId(lmNumber);
    //remove the learning material listing from the parent container that holds ALL the lm's
    element.parentNode.removeChild(element);
    //update the 'Learning Materials (x)' value of x for the container
    ilios.cm.lm.updateLearningMaterialCountText(containerNumber);
    //hide the confirmation window
    this.hide();
};

/*
 * updates the learning material count between the parentheses in the course/session container
 *
 * @method updateLearningMaterialCountText
 * @param {Int} containerNumber the id of the container within which the learning material resides
 */

ilios.cm.lm.updateLearningMaterialCountText = function (containerNumber) {
    //is it a course or a session
    var isCourse = (containerNumber == -1);
    //get the course or session model
    var model = isCourse ? ilios.cm.currentCourseModel
        : ilios.cm.currentCourseModel.getSessionForContainer(containerNumber);
    var idString = ilios.cm.lm.generateIdStringForLearningMaterialsContainerLabel(containerNumber);
    //get the text for the button
    var i18nStr = ilios_i18nVendor.getI18NString('general.phrases.learning_materials');
    //get the button element
    var element = document.getElementById(idString);
    //and update the text of the button to reflect the total
    element.innerHTML = i18nStr + ' (' + ilios.utilities.objectPropertyCount(model.getLearningMaterials()) + ')';
};

/*
 * Adds the fully built learning material element to the the proper course/session container
 *
 * @method addNewLearningMaterialToDom
 * @param {int} containerNumber the id of the container number within which the lm reside
 * @param {int} the dbId of the Learning Material
 */

ilios.cm.lm.addNewLearningMaterialToDom = function (containerNumber, learningMaterialId) {

    //if there is no current course model, none of this matters
    if (ilios.cm.currentCourseModel != null) {
        var isCourse = null;
        var model = null;
        var lmItem = null;

        //is it a course or session
        isCourse = (containerNumber == -1);
        //get the course or session model
        model = isCourse ? ilios.cm.currentCourseModel
            : ilios.cm.currentCourseModel.getSessionForContainer(containerNumber);
        //get the next learning material number
        var nextLearningMaterialItemNumber = model.getNextLearningMaterialNumber();
        //get the learning material model to build it out for placement in the lm list
        var learningMaterialModel = ilios.cm.lm.getLearningMaterialModelFromId(model, learningMaterialId);
        //build the learning material item
        var lmItem = ilios.cm.lm.buildLearningMaterialItem (learningMaterialModel, containerNumber, nextLearningMaterialItemNumber, false);
        //get the container to which to will be adding the the lm
        var containerId = ilios.cm.lm.generateIdStringForLearningMaterialsContainer(containerNumber);
        var container = document.getElementById(containerId);
        //add the lm to the container
        container.appendChild(lmItem);
        //update the learning material total for the respective course/session container
        ilios.cm.lm.updateLearningMaterialCountText(containerNumber);
        //toggle the learning material expander for the respective course/session container
        ilios.cm.lm.setLearningMaterialDivVisibility(containerNumber, null, false);
    }
};

/*
 * Returns the learning material model from a course or session based on the lm's dbId
 *
 * @param {Object} courseOrSessionModel the course or session object containing the Learning Materials
 * @param {int} learningMaterialId the dbId of the learning material
 *
 * @return {Object} learningMaterial the entire learning material object
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
 * Toggles the visibility of the learning materials container which wraps all the learning material items
 *
 * @method setlearningMaterialDivVisibility
 * @param {Int} containerNumber the id of the course or session container within which the lms should appear
 * @param {Object} widgetDiv the lm-containing div element that will be expanded/collapse
 * @param {Boolean} shouldToggle if true, the current visibility will be toggled; if false, the div will be
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

/*
 * Sets the text for a learning materials 'Add MeSH (x)' button based on the meshTotal returned
 * from an update
 *
 * @param {int} containerNumber the id of the course or session container within which the associated lm resides
 * @param {int} lmNumber the dbId of the learningMaterial that correspond to the 'Add MeSH (x)' button
 * @meshTotal {int} the total number of mesh terms as returned by a mesh term update
 */

ilios.cm.lm.updateLearningMaterialMeSHCount = function (containerNumber, lmNumber, meshTotal) {

    var idString = null;
    var lmMeshCountButton = null;

    //set the 'Add MeSH (x)' button text, including the meshTotal
    var idString = ilios.cm.lm.generateIdStringForLearningMaterialMeSHLink(containerNumber, lmNumber);
    lmMeshCountButton = document.getElementById(idString);
    lmMeshCountButton.innerHTML = 'Add MeSH (' + meshTotal + ')';
};