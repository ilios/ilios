/**
 * This file contains the model repesentation of a Learning Material Asset in javascript land; this
 *  is used to encapsulate, and so therefore better track the dirty state of, our model.
 *
 * DEPENDENCY:
 *      scripts/abstract_js_model_form.js
 *      scripts/ilios_utilities.js
 */
function LearningMaterialModel (dbObject) {

    AbstractJavaScriptModelForm.apply(this, arguments);

    this.meshTerms = new Array();
    this.notes = '';
    this.notesArePubliclyViewable = true;

    if (typeof dbObject == 'undefined') {
        this.mimeType = null;
        this.description = null;

        this.filename = null;
        this.filesize = -1;
        this.hasCopyrightOwnership = true;
        this.copyrightRationale = null;

        this.link = null;
        this.citation = null;

        this.uploadDate = null;

        this.owningUserId = -1;
        this.owningUserDisplayName = null;

        this.statusId = -1;
        this.ownerRoleId = -1;

        this.creator = null;

        this.required = true;
    }
    else {
        this.dbId = dbObject.learning_material_id;
        this.title = dbObject.title;

        this.mimeType = dbObject.mime_type;
        this.description = dbObject.description;

        this.filename = dbObject.filename;
        this.filesize = dbObject.filesize;

        if ((typeof dbObject.copyright_rationale != 'undefined')
                                    && (dbObject.copyright_rationale != null)
                                    && (dbObject.copyright_rationale.length > 0)) {
            this.copyrightRationale = dbObject.copyright_rationale;
            this.hasCopyrightOwnership = false;
        }
        else {
            this.copyrightRationale = null;
            this.hasCopyrightOwnership = true;
        }

        this.link = dbObject.web_link;
        this.citation = dbObject.citation;

        this.uploadDate = dbObject.upload_date;

        this.owningUserId = dbObject.owning_user_id;
        this.owningUserDisplayName = dbObject.owning_user_name;

        this.statusId = dbObject.status_id;
        this.ownerRoleId = dbObject.owner_role_id;

        this.creator = dbObject.asset_creator;

        for (var key in dbObject.mesh_terms) {
            this.meshTerms.push(new MeSHItemModel(dbObject.mesh_terms[key]));
        }

        if ((typeof dbObject.notes != 'undefined')
                && (dbObject.notes != null)
                && (dbObject.notes.length > 0)) {
            this.notes = dbObject.notes;
        }

        this.required = (dbObject.required == '1');

        this.notesArePubliclyViewable = (dbObject.notes_are_public == '1');
    }

    this.dirtyStateListener = {

            modelChanged: function (model, mockedThis) {
                // presently there is no functionality associated to this
            }

    };

}

for (var aMethod in AbstractJavaScriptModelForm.prototype) {
    LearningMaterialModel.prototype[aMethod] = AbstractJavaScriptModelForm.prototype[aMethod];
}

LearningMaterialModel.prototype.addMeSHItem = function (meshItem) {
    this.meshTerms.push(meshItem);
};

LearningMaterialModel.prototype.removeMeSHItem = function (meshItem) {
    for (var key in this.meshTerms) {
        if (meshItem.compareTo(this.meshTerms[key]) == 0) {
            delete this.meshTerms[key];

            return;
        }
    }
};

//after decoupling the LM's from course and session, we need to set the model dirty without notifying
LearningMaterialModel.prototype.setDirty = function () {
    this.isDirty = true;
};

LearningMaterialModel.prototype.containsMeSHItem = function (meshItem) {
    for (var key in this.meshTerms) {
        if (meshItem.compareTo(this.meshTerms[key]) == 0) {
            return true;
        }
    }

    return false;
};

/**
 * @return a non-associative array of MeSHItemModel instances
 */
LearningMaterialModel.prototype.getMeSHItems = function () {
    return this.meshTerms;
};

LearningMaterialModel.prototype.getMeSHItemsAsFormattedText = function () {
    return ilios.utilities.delimitedStringOfTitledObjects(this.meshTerms, ';');
};

LearningMaterialModel.prototype.setRequired = function (flag) {
    if (this.required != flag) {
        this.required = flag;

        //because we're decoupling, set dirty but don't notify
        this.setDirty();

    }
};

LearningMaterialModel.prototype.isRequired = function () {
    return this.required;
};

LearningMaterialModel.prototype.setMimeType = function (mt) {
    if (this.mimeType != mt) {
        this.mimeType = mt;

        this.setDirtyAndNotify();
    }
};

LearningMaterialModel.prototype.getMimeType = function () {
    return this.mimeType;
};

LearningMaterialModel.prototype.setOwningUserName = function (userName) {
    this.owningUserDisplayName = userName;
};

LearningMaterialModel.prototype.getOwningUserName = function () {
    return this.owningUserDisplayName;
};

LearningMaterialModel.prototype.setOwningUserId = function (uid) {
    this.owningUserId = uid;
};

LearningMaterialModel.prototype.getOwningUserId = function () {
    return this.owningUserId;
};

LearningMaterialModel.prototype.setFilename = function (lmFilename) {
    if (this.filename != lmFilename) {
        this.filename = lmFilename;

        this.setDirtyAndNotify();
    }
};

LearningMaterialModel.prototype.getFilename = function () {
    return this.filename;
};

LearningMaterialModel.prototype.setFileSize = function (lmFileSize) {
    if (this.filesize != lmFileSize) {
        this.filesize = lmFileSize;

        this.setDirtyAndNotify();
    }
};
LearningMaterialModel.prototype.getFileSize = function () {
    return this.filesize;
};

LearningMaterialModel.prototype.setCopyrightRationale = function (rationale) {
    if (this.copyrightRationale != rationale) {
        this.copyrightRationale = rationale;

        this.setDirtyAndNotify();
    }
};

LearningMaterialModel.prototype.setCopyrightOwnership = function (flag) {
    if (this.hasCopyrightOwnership != flag) {
        this.hasCopyrightOwnership = flag;

        this.setDirtyAndNotify();
    }
};

LearningMaterialModel.prototype.setDescription = function (lmDescription) {
    if (this.description != lmDescription) {
        this.description = lmDescription;

        this.setDirtyAndNotify();
    }
};

LearningMaterialModel.prototype.getDescription = function () {
    return this.description;
};

LearningMaterialModel.prototype.setCreator = function (name) {
    if (this.creator != name) {
        this.creator = name;

        this.setDirtyAndNotify();
    }
};

LearningMaterialModel.prototype.getCreator = function () {
    return this.creator;
};

LearningMaterialModel.prototype.setCitation = function (cite) {
    if (this.citation != cite) {
        this.citation = cite;

        this.setDirtyAndNotify();
    }
};

LearningMaterialModel.prototype.getCitation = function () {
    return this.citation;
};

LearningMaterialModel.prototype.setLink = function (webLink) {
    if (this.link != webLink) {
        this.link = webLink;

        this.setDirtyAndNotify();
    }
};

LearningMaterialModel.prototype.getLink = function () {
    return this.link;
};

LearningMaterialModel.prototype.setNotes = function (noteText) {
    if (this.notes != noteText) {
        this.notes = noteText;

        this.setDirtyAndNotify();
    }
};

LearningMaterialModel.prototype.getNotes = function () {
    return this.notes;
};

LearningMaterialModel.prototype.setNotesArePubliclyViewable = function (viewable) {
    if (this.notesArePubliclyViewable != viewable) {
        this.notesArePubliclyViewable = viewable;

        this.setDirtyAndNotify();
    }
};

LearningMaterialModel.prototype.isNotesPubliclyViewable = function () {
    return this.notesArePubliclyViewable;
};

LearningMaterialModel.prototype.setOwnerRoleId = function (id) {
    if (this.ownerRoleId != id) {
        this.ownerRoleId = id;

        this.setDirtyAndNotify();
    }
};

LearningMaterialModel.prototype.getOwnerRoleId = function () {
    return this.ownerRoleId;
};

LearningMaterialModel.prototype.setStatusId = function (id) {
    if (this.statusId != id) {
        this.statusId = id;

        this.setDirtyAndNotify();
    }
};

LearningMaterialModel.prototype.getStatusId = function () {
    return this.statusId;
};

LearningMaterialModel.prototype.getUploadDate = function () {
    return this.uploadDate;
};

LearningMaterialModel.prototype.clone = function () {
    var rhett = new LearningMaterialModel();

    rhett.replaceContentWithModel(this, true);

    rhett.clearDirtyState();

    return rhett;
};

/**
 * Returns a standard compare, the value of which would allow natural ordering. This compares in
 *  the order of:
 *      title
 *      mesh term cardinality
 *      dbId
 */
LearningMaterialModel.prototype.compareTo = function (otherModel) {
    var temp = null;

    if (this.title != null) {
        temp = this.title.localeCompare(otherModel.title);

        if (temp != 0) {
            return temp;
        }
    }
    else if (otherModel.title != null) {
        return 1;           // arbitrary but consistent
    }

    if (ilios.utilities.objectPropertyCount(this.meshTerms)
                                            != ilios.utilities.objectPropertyCount(otherModel.meshTerms)) {
        return 1;           // arbitrary but consistent
    }

    return (this.dbId - otherModel.dbId);
};

/**
 * If the newModel differs from this model, then it is replaced by the newModel and this is set
 *      dirty. Comparisons are done by values and not indentical instance.
 *
 * NOTES / REMINDERS:
 *  . Any modifications to the users held by the parameter after this method is called will also
 *          change those instances as held by this instance.
 */
LearningMaterialModel.prototype.replaceContentWithModel = function (newModel, forceReplacement) {
    if (forceReplacement || (this.compareTo(newModel) != 0)) {
        this.title = newModel.title;
        this.dbId = newModel.dbId;

        this.mimeType = newModel.mimeType;
        this.description = newModel.description;

        this.filename = newModel.filename;
        this.filesize = newModel.filesize;

        this.copyrightRationale = newModel.copyrightRationale;
        this.hasCopyrightOwnership = newModel.hasCopyrightOwnership;

        this.link = newModel.link;
        this.citation = newModel.citation;

        this.uploadDate = newModel.uploadDate;

        this.owningUserId = newModel.owningUserId;
        this.owningUserDisplayName = newModel.owningUserDisplayName;

        this.statusId = newModel.statusId;
        this.ownerRoleId = newModel.ownerRoleId;

        this.creator = newModel.creator;

        this.meshTerms = newModel.meshTerms.concat();

        this.setDirtyAndNotify();
    }
};
