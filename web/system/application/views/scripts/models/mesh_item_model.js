/**
 * This file contains the model repesentation of a MeSH item within the Ilios 2 Javascript land;
 *  a MeSH "item", for us, consists of a descriptor UID (retrievable through getDBId()), the
 *  descriptor name (retrievable through getTitle()), the scope notes for all concepts associated to
 *  the descriptor, and a MeSH tree for the descriptor. Note, that since a descriptor can sit in
 *  many trees within MeSH, the arbitrary tree which we're holding on to here is pretty useless.
 *
 * DEPENDENCY:
 *      scripts/third_party/md5-min.js
 *      scripts/abstract_js_model_form.js
 *      scripts/ilios_utilities.js
 */
function MeSHItemModel (dbObject) {

    AbstractJavaScriptModelForm.apply(this, arguments);

    if (typeof dbObject == 'undefined') {
        this.dbId = null;
        this.keywords = null;

        this.scopeNotes = new Array();

        this.tree = new Array();
    }
    else {
        this.dbId = dbObject.uid;
        this.title = dbObject.name;

        this.scopeNotes = dbObject.scope_notes.concat();

        this.tree = dbObject.tree_path.concat();
    }

    this.dirtyStateListener = {

            modelChanged: function (model, mockedThis) {
                // presently there is no functionality associated to this
            }

    };

}

for (var aMethod in AbstractJavaScriptModelForm.prototype) {
    MeSHItemModel.prototype[aMethod] = AbstractJavaScriptModelForm.prototype[aMethod];
}

MeSHItemModel.prototype.getTree = function () {
    return this.tree;
};

MeSHItemModel.prototype.getScopeNotes = function () {
    return this.scopeNotes;
};

MeSHItemModel.prototype.clone = function () {
    var rhett = new MeSHItemModel();

    rhett.dbId = this.dbId;
    rhett.title = this.title;

    rhett.scopeNotes = this.scopeNotes.concat();
    rhett.tree = this.tree.concat();

    // (should still be clean.. but just in case future coders accidentally add code that dirties
    //      above..)
    rhett.clearDirtyState();

    return rhett;
};

/**
 * Returns a standard compare, the value of which would allow natural ordering. This compares in
 *  the order of:
 *      dbId
 */
MeSHItemModel.prototype.compareTo = function (otherModel) {
    var temp = null;

    if (this.dbId != null) {
        temp = this.dbId.localeCompare(otherModel.dbId);

        return temp;
    }

    return 0;
};
