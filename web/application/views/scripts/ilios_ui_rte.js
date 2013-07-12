/**
 * Rich Text Editor implementation, based on YUI2's RTE.
 *
 * Defines the following namespaces:
 *
 * ilios.ui
 *
 * Dependencies:
 *
 * YAHOO.widget.SimpleEditor and its dependencies
 *
 */
ilios.namespace('ui');

/**
 * RichTextEditor inherits YUI Simple Editor.
 *
 * This is used in places like 'descriptions', 'objectives', 'notes', etc.  We want
 * to provide the same and consistent features across all textarea elements.
 *
 * @param el
 * @param attrs
 *
 */
ilios.ui.RichTextEditor = function(el, attrs) {
    // Set up the default attributes for Ilios Editor
    var defAttrs = {
        toolbar: {
            collapse: true,
            titlebar: false,
            draggable: false,
            buttons: [
                {
                    group: 'fontstyle', label: 'Font Size',
                    buttons: [{
                        type: 'spin',
                        label: '13',
                        value: 'fontsize',
                        range: [9, 45 ],
                        disabled: true
                    }]
                },
                {
                    type: 'separator'
                },
                {
                    group: 'textstyle',
                    label: 'Font Style',
                    buttons: [
                        {
                            type: 'push',
                            label: 'Bold CTRL + SHIFT + B',
                            value: 'bold'
                        },
                        {
                            type: 'push',
                            label: 'Italic CTRL + SHIFT + I',
                            value: 'italic'
                        },
                        {
                            type: 'push',
                            label: 'Underline CTRL + SHIFT + U',
                            value: 'underline'
                        },
                        {
                            type: 'push',
                            label: 'Strike Through',
                            value: 'strikethrough'
                        },
                        {
                            type: 'separator'
                        },
                        {   type: 'color',
                            label: 'Font Color',
                            value: 'forecolor',
                            disabled: true
                        },
                        {
                            type: 'color',
                            label: 'Background Color',
                            value: 'backcolor',
                            disabled: true
                        }
                    ]
                },
                {
                    type: 'separator'
                },
                {
                    group: 'indentlist',
                    label: 'Lists',
                    buttons: [
                        {
                            type: 'push',
                            label: 'Create an Unordered List',
                            value: 'insertunorderedlist'
                        },
                        {
                            type: 'push',
                            label: 'Create an Ordered List',
                            value: 'insertorderedlist'
                        }
                    ]
                }
            ]
        }
    };

    if (attrs) {
        YAHOO.lang.augmentObject(defAttrs, attrs, true);
    }

    ilios.ui.RichTextEditor.superclass.constructor.call(this, el, defAttrs);

    this.on('editorKeyUp', this.saveHTML);
    this.on('afterNodeChange', this.saveHTML);
};

YAHOO.lang.extend( ilios.ui.RichTextEditor, YAHOO.widget.SimpleEditor );
