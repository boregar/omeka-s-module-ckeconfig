# CKEConfig

A simple module that allows to configure the CKEditor instance used in some forms of the administration interface.

Current version: v0.1.4

This module is still under active development. It is recommended to wait for the v1.0 release before using it on a production environment.

## Installation

See general end user documentation for [Installing a module](http://omeka.org/s/docs/user-manual/modules/#installing-modules)

## How To Use

Once installed and activated, click the **Configure** button to set the following settings:

- **Enabled**: enables or disables CKEConfig.
- **Allowed roles**: only users who have one of these roles can use the custom configuration. Leave unchecked for no restriction.
- **CKEditor config**: the custom configuration, formatted as a JS object. Make sure the syntax is valid.

  Example:

```js
{
	uiColor: '#ccdcec',
	bodyClass: 'my-background-color',
	allowedContent: true
}
```

- **CKEditor styles**: the styles to be added to the default styleset avalailable under the Styles dropdown, formatted as a JS array. Make sure the syntax is valid.

  Example:

```js
[
  {
    name: 'Hero',
    element: 'div',
    attributes: {
      'class': 'my-hero'
    }
  },
  {
    name: 'Hero body',
    element: 'div',
    attributes: {
      'class': 'my-hero-body'
    }
  },
  {
    name: 'Subtitle',
    element: 'div',
    attributes: {
      'class': 'my-subtitle'
    }
  },
  {
    name: 'Title',
    element: 'div',
    attributes: {
      'class': 'my-title'
    }
  },
  {
    name: 'Title 4',
    element: 'h4',
    attributes: {
      'class': 'my-title is-size-4'
    }
  },
  {
    name: 'Chapô',
    element: 'div',
    attributes: {
      'class': 'my-text-content is-size-5'
    }
  },
 {
    name: 'Body',
    element: 'p',
    attributes: {
      'class': 'my-text-content'
    }
  }
]
```

- **Additional stylesheets**: the absolute or relative URLs of the stylesheets to be added to CKEditor to make it WYSIWYG. Enter one URL by line.

  Example:

```
https://my.distribution.server/my-library/css/my-library.css
/themes/my-theme/asset/css/my-style.css
```

The above configuration is then applied to the CKEditor instance of each **HTML block** added to a Page.

More information will be delivered as soon as the module becomes ready for testing.

## Resources

Explore the [CKEditor documentation](https://ckeditor.com/docs/ckeditor4/latest/api/CKEDITOR_config.html)

## Copyright

Copyright 2026 Christian Morel

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

<http://www.apache.org/licenses/LICENSE-2.0>

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
