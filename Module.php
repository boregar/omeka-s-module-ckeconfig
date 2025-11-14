<?php
namespace CKEConfig;

use CKEConfig\Form\ConfigForm;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Laminas\View\Renderer\PhpRenderer;
use Laminas\Mvc\Controller\AbstractController;
use Laminas\EventManager\Event;
use Laminas\EventManager\SharedEventManagerInterface;
use Omeka\Module\AbstractModule;
use Omeka\Stdlib\Message;
use Omeka\Entity\User;

class Module extends AbstractModule {

  public function getConfig() {
    return include __DIR__ . '/config/module.config.php';
  }

  public function getConfigForm(PhpRenderer $renderer) {
    $services = $this->getServiceLocator();
    $settings = $services->get('Omeka\Settings');
    // give the form a reference to the ACL because the Form object has no access to ServiceLocator
    $acl = $services->get('Omeka\Acl');
    $form = new ConfigForm;
    $form->setAcl($acl);
    $form->init();
    $form->setData([
      'ckeconfig_enabled_mode' => $settings->get('ckeconfig_enabled_mode', 'no'),
      'ckeconfig_roles' => $settings->get('ckeconfig_roles'),
      'ckeconfig_js' => $settings->get('ckeconfig_js'),
      'ckeconfig_styles' => $settings->get('ckeconfig_styles'),
      'ckeconfig_stylesheets' => $settings->get('ckeconfig_stylesheets'),
    ]);
    return $renderer->formCollection($form, false);
  }

  public function handleConfigForm(AbstractController $controller) {
    $services = $this->getServiceLocator();
    $settings = $services->get('Omeka\Settings');
    // give the form a reference to the ACL because the Form object has no access to ServiceLocator
    $acl = $services->get('Omeka\Acl');
    $form = new ConfigForm;
    $form->setAcl($acl);
    $form->init();
    $form->setData($controller->params()->fromPost());
    if (!$form->isValid()) {
      $controller->messenger()->addErrors($form->getMessages());
      return false;
    }
    $formData = $form->getData();
    $settings->set('ckeconfig_enabled_mode', $formData['ckeconfig_enabled_mode']);
    $settings->set('ckeconfig_roles', $formData['ckeconfig_roles']);
    $settings->set('ckeconfig_js', trim($formData['ckeconfig_js']));
    $settings->set('ckeconfig_styles', trim($formData['ckeconfig_styles']));
    $settings->set('ckeconfig_stylesheets', trim($formData['ckeconfig_stylesheets']));
    return true;
  }

  public function attachListeners(SharedEventManagerInterface $sharedEventManager) {
    $sharedEventManager->attach('*', 'view.layout', [$this, 'hookViewLayout']);
  }

  public function hookViewLayout(Event $event) {
    $services = $this->getServiceLocator();
    // limit custom config to admin interface
    if ($services->get('Omeka\Status')->isSiteRequest()) {
      return;
    }
    // get the role of current user
    $view = $event->getTarget();
    $user = $view->identity();
    $userRole = $user ? $user->getRole() : '';
    // get the CKEConfig settings
    $settings = $services->get('Omeka\Settings');
    if ($settings->get('ckeconfig_enabled_mode', 'no') === 'no') {
      return;
    }
    // limit custom config to allowed roles only
    $allowedRoles = $settings->get('kintme_roles');
    if ((gettype($allowedRoles) !== 'array') || in_array($userRole, $allowedRoles)) {
      // get the JS of the custom config
      $configJS = $settings->get('ckeconfig_js');
      // check that the value represents a JS object
      $configJS = preg_match('/^\{.+\}$/s', $configJS) ? $configJS : 'null';
      // inject the config as a script in the head of the layout
      $view->headScript()->appendScript(sprintf('const CKEConfig = %s;', $configJS));
      // get the styles of the custom config
      $configStyles = $settings->get('ckeconfig_styles');
      // check that the value represents a JS array
      $configStyles = preg_match('/^\[.+\]$/s', $configStyles) ? $configStyles : 'null';
      // inject the styles as a script in the head of the layout
      $view->headScript()->appendScript(sprintf('const CKEStyles = %s;', $configStyles));
      // add the JS code that will apply the custom config and styles to the CKEditor instances
      $view->headScript()->appendFile($view->assetUrl('js/ckeconfig.js', 'CKEConfig'));
      // get the additional CSS stylesheet
      $configStyleSheets = $settings->get('ckeconfig_stylesheets');
      // TODO: check that the value contains a list of .css files
      $configStyleSheets = preg_replace('/\r?\n/', '|', $configStyleSheets);
      // inject the stylesheet as a script in the head of the layout
      $view->headScript()->appendScript(sprintf('const CKEStyleSheets = "%s";', $configStyleSheets));
    }
  }

  public function install(ServiceLocatorInterface $serviceLocator) {
    $settings = $serviceLocator->get('Omeka\Settings');
    $settings->set('ckeconfig_enabled_mode', 'no');
    $settings->set('ckeconfig_roles', ['global_admin']);
    $settings->set('ckeconfig_js', "{\r\n  uiColor: '#ccdcec',\r\n  allowedContent: true\r\n}");
    $settings->set('ckeconfig_styles', "[\r\n  {\r\n    name: 'CKEConfig div',\r\n    element: 'div',\r\n    attributes: {\r\n      'class': 'ckeconfig-div'\r\n    }\r\n  }\r\n]");
    $settings->set('ckeconfig_stylesheets', '');
  }

  public function upgrade($oldVersion, $newVersion, ServiceLocatorInterface $serviceLocator) {
    if (version_compare($oldVersion, '0.1.1', '<')) {
      $settings = $serviceLocator->get('Omeka\Settings');
      $settings->delete('ckeconfig_json');
    }
    if (version_compare($oldVersion, '0.1.3', '<')) {
      $settings = $serviceLocator->get('Omeka\Settings');
      $settings->set('ckeconfig_stylesheets', '');
    }
  }

  public function uninstall(ServiceLocatorInterface $serviceLocator) {
    $settings = $serviceLocator->get('Omeka\Settings');
    $settings->delete('ckeconfig_enabled_mode');
    $settings->delete('ckeconfig_roles');
    $settings->delete('ckeconfig_js');
    $settings->delete('ckeconfig_styles');
    $settings->delete('ckeconfig_stylesheets');
  }
}
