//---------------------------------------- global variables

const ckeInstances = [];       // instances processed

//---------------------------------------- main

document.addEventListener('DOMContentLoaded', function () {
  // customise CKE default config
  if ((typeof CKEDITOR !== 'undefined') && (typeof CKEConfig === 'object')) {
    console.log(CKEConfig);
    // fire only once an instance is ready the first time it is created to avoid infinite loop
    CKEDITOR.on('instanceReady', function(event) {
      // get the name of the instance
      var editor = event.editor.name;
      // ensure the instance has been created by Omeka
      if ((editor.match(/^o:.+/)) === null) return;
      // check if the instances has already been processed
      if (ckeInstances.includes(editor)) return;
      ckeInstances.push(editor);
      // destroy the instance to avoid error "editor-element-conflict"
      CKEDITOR.instances[editor].destroy(true);
      // customise the config
      CKEDITOR.replace(editor, CKEConfig);
    });
  }
});
