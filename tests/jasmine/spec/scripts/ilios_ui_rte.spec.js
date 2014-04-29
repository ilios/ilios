describe("ilios_ui_rte", function () {
  it("should create a ui namespace on the ilios global object", function () {
    expect(typeof ilios.ui).toBe("object");
  });
});

describe("RichTextEditor()", function () {
  it("should use defaults", function(){
    var el = document.createElement('textarea');
    var editor = new ilios.ui.RichTextEditor(el);
    expect(editor.get('toolbar').buttons.length).toBe(5);
    var expected = [
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
    ];
    expect(editor.get('toolbar').buttons).toEqual(expected);
  });
  it("and it should allow overrides to those defaults", function(){
    var el = document.createElement('textarea');
    var attrs =  {toolbar: {buttons: []}}
    var editor = new ilios.ui.RichTextEditor(el, attrs);

    expect(editor.get('toolbar').buttons.length).toBe(0);
  });
  it("and it should allow overrides to the YUI class", function(){
    var el = document.createElement('textarea');
    var attrs =  {height: '200px'}
    var editor = new ilios.ui.RichTextEditor(el, attrs);

    expect(editor.get('height')).toEqual('200px');
  });
});
