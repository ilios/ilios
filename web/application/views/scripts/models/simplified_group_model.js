/**
 * This serves as a simplified representation of a group - since this is only for the most minimal
 *  of puposes of offering a skeleton of a group tree, it finds lightweight use in both CM and OM.
 */

/**
 * @param groupName the display title of the group
 * @param parentGroupId
 * @param id the database row id associated to this object
 */
function SimplifiedGroupModel (groupName, parentGroupId, id) {

    this.groupTitle = groupName;

    this.parentId = parentGroupId;

    this.dbId = id;

    this.subgroups = new Array();

}

SimplifiedGroupModel.prototype.getGroupTitle = function () {
    return this.groupTitle;
};

SimplifiedGroupModel.prototype.getParentGroupId = function () {
    return this.parentId;
};

SimplifiedGroupModel.prototype.getDBId = function () {
    return this.dbId;
};

/**
 * This does not verify whether the group already exists.
 */
SimplifiedGroupModel.prototype.addSubgroup = function (group) {
    this.subgroups.push(group);

    group.parentId = this.dbId;
};

SimplifiedGroupModel.prototype.getSubgroups = function () {
    return this.subgroups;
};

/**
 * Returns a standard compare, the value of which would allow natural ordering. This compares in
 *  the order of:
 *      groupTitle
 *      parentId
 *      db id
 */
SimplifiedGroupModel.prototype.compareTo = function (otherModel) {
    var temp = this.groupTitle.localeCompare(otherModel.groupTitle);

    if (temp != 0) {
        return temp;
    }

    if (this.parentId != otherModel.parentId) {
        return this.parentId - otherModel.parentId;
    }

    return this.dbId - otherModel.dbId;
};

SimplifiedGroupModel.prototype.clone = function () {
    return new SimplifiedGroupModel(this.groupTitle, this.parentId, this.dbId);
};
