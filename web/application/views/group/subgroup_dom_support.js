/**
 * DEPENDENCIES:
 * 		YUI lib (at least YAHOO.widget and its dependencies)
 * 		scripts/ilios_utilities.js
 * 		group_manager_dom.js
 */

ilios.namespace('gm.subgroup');


ilios.gm.subgroup.subContainerPixelIndentIncrement = 1; //using em for measurement

ilios.gm.subgroup.isPresentlyCollapsed = false;
ilios.gm.subgroup.indexPathsNeedRebuilding = true;

ilios.gm.subgroup.collapseGroupForSubgrouping = function (manageGroupLinkElement, clickEvent) {
	var parentFormElement = manageGroupLinkElement.parentNode.parentNode.parentNode;
	var containerNumber = parentFormElement.getAttribute('cnumber');
	var groupModel = ilios.gm.currentRootViewGroup.getSubgroupForContainerNumber(containerNumber);
	var element;

	if (ilios.gm.subgroup.indexPathsNeedRebuilding) {
		ilios.gm.subgroup.recursivelyBuildIndexPaths(ilios.gm.currentModel.getRootGroup().getSubgroups(),
													 null);

		ilios.gm.subgroup.indexPathsNeedRebuilding = false;
	}

	ilios.gm.subgroup.collapseGroupWithGroupModel(groupModel);

	ilios.gm.collapseOrExpandGroups(false, true);

    // enable the "open cohort" button to provide a means to bail out of this subgroup view
    element = new YAHOO.util.Element(document.getElementById('open_cohort'));
    element.setStyle('display', 'inline');

	// If we don't stop the cascade here, the new DOM elements will pick up the click
	//		event [for whatever dumb and broken reason] and expand the container under this
	//		mouse click in the new layout.
	clickEvent.cancelBubble = true;
	if (clickEvent.returnValue) {
		clickEvent.returnValue = false;
	}
	if (clickEvent.stopPropagation) {
		clickEvent.stopPropagation();
	}
};

// @private
ilios.gm.subgroup.recursivelyBuildIndexPaths = function (subgroups, currentIndexPath) {
	var indexPath = ((currentIndexPath != null) ? currentIndexPath.concat() : new Array());
	var pathLength = indexPath.length;
	var counter = 1;
	var groupModel = null;

	for (var key in subgroups) {
		groupModel = subgroups[key];

		indexPath[pathLength] = counter++;
		groupModel.indexPath = indexPath.concat();

		ilios.gm.subgroup.recursivelyBuildIndexPaths(groupModel.getSubgroups(), indexPath);
	}
};

// @private
ilios.gm.subgroup.collapseGroupWithGroupModel = function (groupModel) {
	var container = null;
	var containerIsCollapsed = ilios.gm.subgroup.isPresentlyCollapsed;
	var indexPath = groupModel.indexPath;


	if (! containerIsCollapsed) {
		var i18nStr = ilios_i18nVendor.getI18NString('groups.subgroups.page_header');
		/*
		 * x change verbiage mentioning group -> sub-group
		 * x collapse all cohort into a div for the breadcrumb trail div -- 'breadcrumb_group_trail'
		 */

		//container = document.getElementById('cohort_form');
		//(new YAHOO.util.Element(container)).setStyle('display', 'none');

		//this is dumb - just leave the cohort alone

		ilios.gm.setPageTitle(i18nStr);

		i18nStr = ilios_i18nVendor.getI18NString('groups.subgroups.add_new_group');
		ilios.gm.setAddNewGroupText(i18nStr);

		//ilios.gm.subgroup.createCohortBreadCrumbContainer();

		// if index path @ 0 isn't 1 we need to slot in the above, not in focus, breadcrumbed groups
		if (indexPath[0] != 1) {
			var counter = 1;
			var subgroupModel = null;

			while (counter < indexPath[0]) {
				subgroupModel = ilios.gm.currentModel.getRootGroup().getNthSubgroup(counter);

				ilios.gm.subgroup.addGroupBreadCrumbForGroup(subgroupModel,
															 'breadcrumb_group_trail', false);
				counter++;
			}
		}

		// if not top level, we need to insert the whole chain ahead of us in the breadcrumb trail
		//		TODO this still doesn't do mid-chain peers
		if (indexPath.length > 1) {
			var parentGroupModel = ilios.gm.currentModel.getRootGroup();
			var subgroupModel = null;
			var subgroupCount = 0;
			var peerGroupModel = null;
			var subIndexPath = null;
			var lastPath = 0;
			var counter = 0;

			for (var i = 0; i < indexPath.length; i++) {
				subgroupModel = parentGroupModel.getNthSubgroup(indexPath[i]);
				subgroupCount = parentGroupModel.getSubgroupCount();

				if (subgroupCount > 1) {
					subIndexPath = subgroupModel.indexPath;
					lastPath = subIndexPath.length - 1;

					counter = 1;
					while (counter < subIndexPath[lastPath]) {
						peerGroupModel = parentGroupModel.getNthSubgroup(counter++);

						ilios.gm.subgroup.addGroupBreadCrumbForGroup(peerGroupModel,
																	 'breadcrumb_group_trail',
																	 false);
					}

					counter = subgroupCount;
					while (counter > subIndexPath[lastPath]) {
						peerGroupModel = parentGroupModel.getNthSubgroup(counter--);

						ilios.gm.subgroup.addGroupBreadCrumbForGroup(peerGroupModel,
												   					 'breadcrumbed_suffixed_group_trail',
												   					 true);
					}
				}

				ilios.gm.subgroup.addGroupBreadCrumbForGroup(subgroupModel,
															 'breadcrumb_group_trail', false);
				parentGroupModel = subgroupModel;
			}
		}

		ilios.gm.subgroup.isPresentlyCollapsed = true;
	}
	else if (indexPath.length > 1) {
		var lastPath = indexPath.length - 1;
		var counter = 1;
		var parentGroupModel = groupModel.getParentGroup();
		var peerCount = parentGroupModel.getSubgroupCount();
		var peerGroupModel = null;

		while (counter < indexPath[lastPath]) {
			peerGroupModel = parentGroupModel.getNthSubgroup(counter);

			ilios.gm.subgroup.addGroupBreadCrumbForGroup(peerGroupModel, 'breadcrumb_group_trail',
														 false);

			counter++;
		}

		counter = peerCount;
		while (counter > indexPath[lastPath]) {
			peerGroupModel = parentGroupModel.getNthSubgroup(counter--);

			ilios.gm.subgroup.addGroupBreadCrumbForGroup(peerGroupModel,
														 'breadcrumbed_suffixed_group_trail', true);
		}
	}

	if (containerIsCollapsed || (indexPath.length == 1)) {
		ilios.gm.subgroup.addGroupBreadCrumbForGroup(groupModel, 'breadcrumb_group_trail', false);
	}

	ilios.gm.currentRootViewGroup = groupModel;
	ilios.gm.subgroup.emptyGroupContainerAndPopulateWithModel(groupModel);

	/*
	 * We need to fill in the breadcrumb trailer - div id = 'breadcrumbed_suffixed_group_trail'
	 *  if we ever get here, it's because one of the master groups is the div/group which
	 *  began the action. To that extent, we should fill in the other master groups and their
	 *  subgroups there.
	 */
	if ((! containerIsCollapsed) && (indexPath.length == 1)) {
		var subgroupCount = ilios.gm.currentModel.getRootGroup().getSubgroupCount();
		var counter = indexPath[0] + 1;
		var subgroupModel = null;

		while (counter <= subgroupCount) {
			subgroupModel = ilios.gm.currentModel.getRootGroup().getNthSubgroup(counter);

			ilios.gm.subgroup.addGroupBreadCrumbForGroup(subgroupModel,
														 'breadcrumbed_suffixed_group_trail',
														 false);

			counter++;
		}
	}
};

ilios.gm.subgroup.indexPathCanEdit = function (indexPath) {
	var parentGroupModel = null;

	if (indexPath.length == 1) {
		parentGroupModel = ilios.gm.currentModel.getRootGroup();
	}
	else {
		var parentIndexPath = indexPath.slice(0, (indexPath.length - 1));

		parentGroupModel = ilios.gm.currentModel.getRootGroup().getSubgroupForIndexPath(parentIndexPath);
	}

	return (parentGroupModel == ilios.gm.currentRootViewGroup);
};

// display chain must be representational prior to calling this
// @private
ilios.gm.subgroup.emptyGroupContainerAndPopulateWithModel = function (groupModel) {
	var subgroups = groupModel.getSubgroups();
	var container = document.getElementById('group_container');
	var subgroupModel = null;
	var containerNumber = -1;
	var element = null;
	var i18nStr = ilios_i18nVendor.getI18NString('groups.subgroups.group_name') + ' ';

	ilios.utilities.removeAllChildren(container);

	for (var key in subgroups) {
		subgroupModel = subgroups[key];
		containerNumber = groupModel.getContainerNumberForSubgroup(subgroupModel);

		ilios.gm.createGroupUI(container, containerNumber, subgroupModel);
		ilios.dom.collapseChildForContainerNumber(containerNumber, 1, ilios.gm.handleGroupDivCollapse);

		element = document.getElementById(ilios.gm.generateIdForGroupNameLabel(containerNumber));
		element.innerHTML = i18nStr;
	}
};

// @private
ilios.gm.subgroup.getFlattenedRepresentationOfIndexPath = function (indexPath) {
	var rhett = '';
	var len = indexPath.length;

	for (var i = 0; i < len; i++) {
		if (i > 0) {
			rhett += ',';
		}

		rhett += '' + indexPath[i];
	}

	return rhett;
};

// @private
ilios.gm.subgroup.getIndexPathFromFlattenedRepresentation = function (representation) {
	return representation.split(',');
};

// @private
ilios.gm.subgroup.changeBreadcrumbViewLevelOrSidestep = function (path) {
	if (path == '-1') {
		ilios.gm.subgroup.restoreTopLevelView();
		ilios.gm.collapseOrExpandGroups(false, true);
        // hide the "open cohort" button
        element = new YAHOO.util.Element(document.getElementById('open_cohort'));
        element.setStyle('display', 'none');
		return;
	} else {
		var indexPath = ilios.gm.subgroup.getIndexPathFromFlattenedRepresentation(path);
		var groupModel = ilios.gm.currentModel.getRootGroup().getSubgroupForIndexPath(indexPath);

		// pretty expensive tear-down and reconstruct here.. TODO
		ilios.gm.subgroup.restoreTopLevelView();
		ilios.gm.subgroup.collapseGroupWithGroupModel(groupModel);
	}
};

// @private
ilios.gm.subgroup.restoreTopLevelView = function () {
    var Element = YAHOO.util.Element;
    var container = null;
	var groupModel = null;
	var subgroups = null;
	var subgroupModel = null;
	var containerNumber = 0;
	var i18nStr = null;

	// MAY RETURN THIS BLOCK
	if (! ilios.gm.subgroup.isPresentlyCollapsed) {
		return;
	}

	container = document.getElementById('cohort_form');
	(new Element(container)).setStyle('display', 'inline');

	container = document.getElementById('breadcrumb_group_trail');
	ilios.utilities.removeAllChildren(container);

	container = document.getElementById('breadcrumbed_suffixed_group_trail');
	ilios.utilities.removeAllChildren(container);

	container = document.getElementById('group_container');
	ilios.utilities.removeAllChildren(container);

	ilios.gm.currentRootViewGroup = ilios.gm.currentModel.getRootGroup();

	// this must occur prior to group UI div creation
	ilios.gm.subgroup.isPresentlyCollapsed = false;

	groupModel = ilios.gm.currentModel.getRootGroup();
	subgroups = groupModel.getSubgroups();
	for (var key in subgroups) {
		subgroupModel = subgroups[key];
		containerNumber = groupModel.getContainerNumberForSubgroup(subgroupModel);

		ilios.gm.createGroupUI(container, containerNumber, subgroupModel);
	}

	i18nStr = ilios_i18nVendor.getI18NString('groups.page_header');
	ilios.gm.setPageTitle(i18nStr);

	i18nStr = ilios_i18nVendor.getI18NString('groups.add_new_group');
	ilios.gm.setAddNewGroupText(i18nStr);
};

// can probably delete this
// @private
ilios.gm.subgroup.createCohortBreadCrumbContainer = function () {
	var parentContainer = document.getElementById('breadcrumb_group_trail');
	var cohortDiv = document.createElement('div');
	var scratchElement = document.createElement('span');
	var textNode = document.createTextNode(ilios.gm.getCohortDisplayText());
	var i18nStr = ilios_i18nVendor.getI18NString('general.phrases.program_title_short');

	scratchElement.setAttribute('class', 'sub_group_title');
	scratchElement.setAttribute('style', 'color: #800000');
	scratchElement.appendChild(textNode);
	cohortDiv.appendChild(scratchElement);


	scratchElement = document.createElement('span');
	scratchElement.setAttribute('style', 'padding-left: 38px;');
	textNode = document.createTextNode(i18nStr + ' ');
	scratchElement.appendChild(textNode);
	cohortDiv.appendChild(scratchElement);

	scratchElement = document.createElement('span');
	scratchElement.setAttribute('class', 'read_only_data');
	scratchElement.setAttribute('style', 'padding-left: 12px;');
	textNode = document.createTextNode(ilios.gm.currentModel.getProgramShortTitle());
	scratchElement.appendChild(textNode);
	cohortDiv.appendChild(scratchElement);


	i18nStr = ilios_i18nVendor.getI18NString('general.phrases.current_enrollment');
	scratchElement = document.createElement('span');
	scratchElement.setAttribute('style', 'padding-left: 68px;');
	textNode = document.createTextNode(i18nStr + ' ');
	scratchElement.appendChild(textNode);
	cohortDiv.appendChild(scratchElement);

	scratchElement = document.createElement('span');
	scratchElement.setAttribute('class', 'read_only_data');
	scratchElement.setAttribute('style', 'padding-left: 12px;');
	textNode = document.createTextNode(ilios.gm.currentModel.getCurrentEnrollment());
	scratchElement.appendChild(textNode);
	cohortDiv.appendChild(scratchElement);


	scratchElement = document.createElement('div');
	scratchElement.setAttribute('style', 'position: absolute; top: 12px; right: 30px;');
	ilios.gm.subgroup.addReturnToViewDivToContainer(scratchElement, -1, null);

	cohortDiv.appendChild(scratchElement);


	cohortDiv.setAttribute('style', 'padding-left: 12px;');
	cohortDiv.setAttribute('class', 'entity_container');

	parentContainer.appendChild(cohortDiv);
};

// @private
ilios.gm.subgroup.addReturnToViewDivToContainer = function (container, path, groupModel) {
	var scratchElement = document.createElement('a');
	var textNode = null;
	var i18nStr = ilios_i18nVendor.getI18NString('groups.subgroups.return_to_view');

	scratchElement.setAttribute('href', '');
	scratchElement.setAttribute('onclick',
								"ilios.gm.subgroup.changeBreadcrumbViewLevelOrSidestep('" + path
																		+ "'); return false;");
	textNode = document.createTextNode(i18nStr + ((groupModel != null) ? ' ' : ''));
	scratchElement.appendChild(textNode);

	if (groupModel != null) {
		var subgroupCountSpan = document.createElement('span');

		subgroupCountSpan.setAttribute('id',
									   ilios.gm.subgroup.subgroupIdStringForSubgroupCountSpan(groupModel));
		textNode = document.createTextNode('(' + groupModel.getSubgroupCount() + ')');
		subgroupCountSpan.appendChild(textNode);
		scratchElement.appendChild(subgroupCountSpan);
	}

	container.appendChild(scratchElement);
};

ilios.gm.subgroup.updateSubgroupCount = function (groupModel) {
	var element
	  = document.getElementById(ilios.gm.subgroup.subgroupIdStringForSubgroupCountSpan(groupModel));
	var textNode = document.createTextNode('(' + groupModel.getSubgroupCount() + ')');

	ilios.utilities.removeAllChildren(element);
	element.appendChild(textNode);
};

ilios.gm.subgroup.subgroupIdStringForSubgroupCountSpan = function (groupModel) {
	return groupModel.getUniqueKey() + "_subgroup_count";
};

ilios.gm.subgroup.subgroupIdStringForMembersAndReturnDiv = function (groupModel) {
	return groupModel.getUniqueKey() + "_members_and_return";
};

ilios.gm.subgroup.subgroupIdStringForTotalMembers = function (groupModel) {
	return groupModel.getUniqueKey() + "_total_members";
};

ilios.gm.subgroup.subgroupIdStringForUnassignedMembers = function (groupModel) {
	return groupModel.getUniqueKey() + "_total_orphans";
};

// @private
ilios.gm.subgroup.addGroupBreadCrumbForGroup = function (groupModel, parentContainerId,
														 shouldPrependToContainer) {
	var parentContainer = document.getElementById(parentContainerId);
	var groupDiv = document.createElement('div');
	var scratchDiv = document.createElement('div');
	var scratchElement = document.createElement('span');
	var i18nStr = ilios_i18nVendor.getI18NString('general.terms.group');
	var textNode = document.createTextNode(i18nStr + ': ' + groupModel.getTitle());
	var totalIndent
				= (groupModel.indexPath.length * ilios.gm.subgroup.subContainerPixelIndentIncrement) - ilios.gm.subgroup.subContainerPixelIndentIncrement;
	var path = ilios.gm.subgroup.getFlattenedRepresentationOfIndexPath(groupModel.indexPath);

	scratchDiv.setAttribute('class', 'sub_group_title');
	scratchDiv.setAttribute('style', 'margin-bottom: 12px;');
	scratchDiv.appendChild(textNode);
	groupDiv.appendChild(scratchDiv);


	i18nStr = ilios_i18nVendor.getI18NString('groups.default_instructor');
	scratchDiv = document.createElement('div');
	scratchDiv.setAttribute('style', 'padding-left: 24px; margin-bottom: 6px; width: 80%;');
	scratchElement = document.createElement('span');
	textNode = document.createTextNode(i18nStr + ' ');
	scratchElement.appendChild(textNode);
	scratchDiv.appendChild(scratchElement);
	scratchElement = document.createElement('span');
	scratchElement.setAttribute('class', 'read_only_data');
	scratchElement.setAttribute('style', 'padding-left: 12px;');
	textNode = document.createTextNode(groupModel.getInstructorsAsFormattedText());
	scratchElement.appendChild(textNode);
	scratchDiv.appendChild(scratchElement);
	groupDiv.appendChild(scratchDiv);


	i18nStr = ilios_i18nVendor.getI18NString('groups.default_location');
	scratchDiv = document.createElement('div');
	scratchDiv.setAttribute('style', 'padding-left: 24px;');
	scratchElement = document.createElement('span');
	textNode = document.createTextNode(i18nStr + ' ');
	scratchElement.appendChild(textNode);
	scratchDiv.appendChild(scratchElement);
	scratchElement = document.createElement('span');
	scratchElement.setAttribute('class', 'read_only_data');
	scratchElement.setAttribute('style', 'padding-left: 12px;');
	textNode = document.createTextNode(groupModel.getLocation());
	scratchElement.appendChild(textNode);
	scratchDiv.appendChild(scratchElement);
	groupDiv.appendChild(scratchDiv);


	scratchDiv = document.createElement('div');
	scratchDiv.setAttribute('style', 'position: absolute; top: 12px; right: 30px;');
	scratchDiv.setAttribute('id',
							ilios.gm.subgroup.subgroupIdStringForMembersAndReturnDiv(groupModel));

	i18nStr = ilios_i18nVendor.getI18NString('general.phrases.total_members');
	scratchElement = document.createElement('span');
	textNode = document.createTextNode(i18nStr + ': ');
	scratchElement.appendChild(textNode);
	scratchDiv.appendChild(scratchElement);
	scratchElement = document.createElement('span');
	scratchElement.setAttribute('class', 'read_only_data');
	scratchElement.setAttribute('style', 'padding-left: 12px;');
	scratchElement.setAttribute('id',
								ilios.gm.subgroup.subgroupIdStringForTotalMembers(groupModel));
	textNode = document.createTextNode(groupModel.getEnrollment());
	scratchElement.appendChild(textNode);
	scratchDiv.appendChild(scratchElement);

	scratchDiv.appendChild(document.createElement('br'));

	i18nStr = ilios_i18nVendor.getI18NString('general.phrases.unassigned');
	scratchElement = document.createElement('span');
	textNode = document.createTextNode(i18nStr + ': ');
	scratchElement.appendChild(textNode);
	scratchDiv.appendChild(scratchElement);
	scratchElement = document.createElement('span');
	scratchElement.setAttribute('class', 'read_only_data');
	scratchElement.setAttribute('style', 'padding-left: 12px;');
	scratchElement.setAttribute('id',
								ilios.gm.subgroup.subgroupIdStringForUnassignedMembers(groupModel));
	textNode = document.createTextNode(groupModel.getOrphanCount());
	scratchElement.appendChild(textNode);
	scratchDiv.appendChild(scratchElement);

	scratchDiv.appendChild(document.createElement('br'));

	ilios.gm.subgroup.addReturnToViewDivToContainer(scratchDiv, path, groupModel);

	groupDiv.appendChild(scratchDiv);

	groupDiv.setAttribute('style', 'padding-left: 36px; margin-left: ' + totalIndent + 'em;');
	groupDiv.setAttribute('class', 'entity_container');

	groupDiv.iliosModel = groupModel;

	if ((! shouldPrependToContainer) || (parentContainer.children.length == 0)) {
		parentContainer.appendChild(groupDiv);
	}
	else {
		parentContainer.insertBefore(groupDiv, parentContainer.firstElementChild);
	}
};
