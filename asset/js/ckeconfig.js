//---------------------------------------- global variables

const ckeInstances = [];       // instances processed

//---------------------------------------- main

document.addEventListener('DOMContentLoaded', function () {
  // customise CKE default config
  if ((typeof CKEDITOR !== 'undefined') && ((typeof CKEConfig === 'object') || (typeof CKEStyles === 'array'))) {
    if (CKEStyles) {
      CKEDITOR.stylesSet.add('default', CKEStyles);
    }
    // fire only once an instance is ready the first time it is created to avoid infinite loop
    CKEDITOR.on('instanceReady', function(event) {
      // get the name of the instance
      var editor = event.editor.name;
      // ensure the instance has been created by Omeka
      if ((editor.match(/^o:.+/)) === null) return;
      // if this instance is already present in the ckeInstances array, this means that the current event is sent by the CKEDITOR.replace(...) below
      if (ckeInstances.includes(editor)) {
        // inject the stylesheet into the iframe containing the HTML
        if (CKEStyleSheets) {
          if (ckeFrame = CKEDITOR.instances[editor].container.$.querySelector('iframe.cke_wysiwyg_frame')) {
            var styleSheets = CKEStyleSheets.split('|');
            styleSheets.forEach((item, i) => {
              var styleSheet = ckeFrame.contentDocument.createElement('link');
              styleSheet.rel = 'stylesheet';
              styleSheet.type = 'text/css';
              styleSheet.media = 'screen';
              styleSheet.href = item;
              ckeFrame.contentDocument.getElementsByTagName('head')[0].appendChild(styleSheet);
            });
          }
        }
        // no further processing is needed
        return;
      }
      // store the instance in the ckeInstances array to avoid infinite loop
      ckeInstances.push(editor);
      // to apply the config to the instance, we need to destroy it before recreating it with CKEDITOR.replace(...)
      if (CKEConfig) {
        // destroy the instance to avoid error "editor-element-conflict"
        CKEDITOR.instances[editor].destroy(true);
        // customise the config
        CKEDITOR.replace(editor, CKEConfig);
      }
    });
  }
});
