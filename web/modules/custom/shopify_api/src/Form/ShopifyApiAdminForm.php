<?php

namespace Drupal\shopify_api\Form;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Shopify\PrivateApp;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Shopify Api Admin Form.
 */
class ShopifyApiAdminForm extends ConfigFormBase {

  /**
   * Module Handler variable.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * {@inheritdoc}
   */
  public function __construct(ConfigFactoryInterface $config_factory, ModuleHandlerInterface $moduleHandler) {
    $this->moduleHandler = $moduleHandler;
    parent::__construct($config_factory);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
      $container->get('module_handler')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'shopify_api_admin_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'shopify_api.settings',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $this->moduleHandler->loadInclude('shopify_api', 'install');

    $config = $this->config('shopify_api.settings');

    // Connection.
    $form['connection'] = [
      '#type' => 'details',
      '#title' => $this->t('Connection'),
      '#open' => TRUE,
    ];
    $form['connection']['help'] = [
      '#type' => 'details',
      '#title' => $this->t('Help'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
    ];
    $form['connection']['help']['list'] = [
      '#theme' => 'item_list',
      '#type' => 'ol',
      '#items' => [
        $this->t('Log in to your Shopify store in order to access the administration section.'),
        $this->t('Click on "Apps" on the left-side menu.'),
        $this->t('Click "Private Apps" on the top-right of the page.'),
        $this->t('Enter a name for the application. This is private and the name does not matter.'),
        $this->t('Click "Save App".'),
        $this->t('Copy the API Key, Password, and Shared Secret values into the connection form.'),
        $this->t('Enter your Shopify store URL as the "Domain". It should be in the format of [STORE_NAME].myshopify.com.'),
        $this->t('Click "Save configuration".'),
      ],
    ];
    $form['connection']['domain'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Domain'),
      '#required' => TRUE,
      '#default_value' => $config->get('domain'),
      '#description' => $this->t('Do not include http:// or https://.'),
    ];
    $form['connection']['api_key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('API key'),
      '#required' => TRUE,
      '#default_value' => $config->get('api_key'),
    ];
    $form['connection']['password'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Password'),
      '#required' => TRUE,
      '#default_value' => $config->get('password'),
    ];
    $form['connection']['shared_secret'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Shared Secret'),
      '#required' => TRUE,
      '#default_value' => $config->get('shared_secret'),
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    try {
      $client = new PrivateApp($form_state->getValue('domain'), $form_state->getValue('api_key'), $form_state->getValue('password'), $form_state->getValue('shared_secret'));
      $shop_info = $client->getShopInfo();
      $this->messenger()->addMessage($this->t('Successfully connected to %store.', ['%store' => $shop_info->name]));
    }
    catch (\Exception $e) {
      $form_state->setErrorByName(NULL, 'API Error: ' . $e->getMessage());
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('shopify_api.settings')
      ->set('domain', $form_state->getValue('domain'))
      ->set('api_key', $form_state->getValue('api_key'))
      ->set('password', $form_state->getValue('password'))
      ->set('shared_secret', $form_state->getValue('shared_secret'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
