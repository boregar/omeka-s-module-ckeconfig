<?php
namespace CKEConfig\Form;

use Laminas\Form\Form;
use Laminas\Validator\Callback;
use Omeka\Permissions\Acl;
use Omeka\Entity\User;

class ConfigForm extends Form {

  protected $acl;   // we need the ACL provided by Module.php because the Form object has no access to ServiceLocator

  public function init() {

    // get the role labels
    $roles = $this->getAcl()->getRoleLabels();

    $this->add([
      'type' => 'checkbox',
      'name' => 'ckeconfig_enabled_mode',
      'options' => [
        'label' => 'Enabled', // @translate
        'info' => 'Enables or disables CKEConfig.', // @translate
        'use_hidden_element' => true,
        'checked_value' => 'yes',
        'unchecked_value' => 'no',
      ],
      'attributes' => [
        'id' => 'ckeconfig-enabled-mode',
      ],
    ]);

    $this->add([
      'type' => 'multicheckbox',
      'name' => 'ckeconfig_roles',
      'options' => [
        'label' => 'Allowed roles', // @translate
        'info' => 'Only users who have one of these roles can use the custom configuration. Leave unchecked for no restriction.', // @translate
        'use_hidden_element' => true,
        'checked_value' => 'yes',
        'unchecked_value' => 'no',
        'value_options' => $roles,
      ],
      'attributes' => [
        'id' => 'ckeconfig-roles',
      ],
    ]);

    $this->add([
      'type' => 'textarea',
      'name' => 'ckeconfig_js',
      'options' => [
        'label' => 'CKEditor config', // @translate
        'info' => 'The custom configuration, formatted as a JS object. Make sure the syntax is valid.', // @translate
        'use_hidden_element' => true,
      ],
      'attributes' => [
        'id' => 'ckeconfig-js',
        'rows' => '10',
      ],
    ]);

    $this->add([
      'type' => 'textarea',
      'name' => 'ckeconfig_styles',
      'options' => [
        'label' => 'CKEditor styles', // @translate
        'info' => 'The styles to be added to the default styleset avalailable under the Styles dropdown, formatted as a JS array. Make sure the syntax is valid.', // @translate
        'use_hidden_element' => true,
      ],
      'attributes' => [
        'id' => 'ckeconfig-js',
        'rows' => '10',
      ],
    ]);

    $this->add([
      'type' => 'textarea',
      'name' => 'ckeconfig_stylesheets',
      'options' => [
        'label' => 'Additional stylesheets', // @translate
        'info' => 'The URL of the stylesheets to be added to CKEditor to make it WYSIWYG. Enter one URL by line.', // @translate
        'use_hidden_element' => true,
      ],
      'attributes' => [
        'id' => 'ckeconfig-stylesheets',
        'rows' => '5',
      ],
    ]);

  }

  public function setAcl(Acl $acl) {
    $this->acl = $acl;
  }

  public function getAcl() {
    return $this->acl;
  }
}
