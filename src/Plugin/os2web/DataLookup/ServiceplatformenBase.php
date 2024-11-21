<?php

namespace Drupal\os2web_datalookup\Plugin\os2web\DataLookup;

use Drupal\Core\Form\FormStateInterface;
use Drupal\os2web_audit\Service\Logger;

/**
 * Defines base plugin class for Serviceplatformen plugins.
 */
abstract class ServiceplatformenBase extends DataLookupBase {

  /**
   * Plugin status string.
   *
   * @var string
   */
  protected string $status;

  /**
   * Service object.
   *
   * @var \SoapClient
   */
  protected \SoapClient $client;

  /**
   * {@inheritdoc}
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    Logger $auditLogger,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $auditLogger);
    $this->init();
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration(): array {
    return [
      'mode_selector' => 0,
      'serviceagreementuuid' => '',
      'serviceuuid' => '',
      'wsdl' => '',
      'location' => '',
      'location_test' => '',
      'usersystemuuid' => '',
      'useruuid' => '',
      'accountinginfo' => '',
      'certfile_passphrase' => '',
      'certfile' => '',
      'certfile_test' => '',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state): array {
    $form['mode_fieldset'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Mode'),
    ];

    $form['mode_fieldset']['mode_selector'] = [
      '#type' => 'radios',
      '#default_value' => $this->configuration['mode_selector'],
      '#options' => [0 => $this->t('Live'), 1 => $this->t('Test')],
    ];

    $form['serviceagreementuuid'] = [
      '#type' => 'textfield',
      '#title' => 'Serviceaftale UUID',
      '#default_value' => $this->configuration['serviceagreementuuid'],
    ];

    $form['serviceuuid'] = [
      '#type' => 'textfield',
      '#title' => 'Service UUID',
      '#default_value' => $this->configuration['serviceuuid'],
      '#description' => $this->t('ex. c0daecde-e278-43b7-84fd-477bfeeea027'),
    ];

    $form['wsdl'] = [
      '#type' => 'textfield',
      '#maxlength' => 500,
      '#title' => 'Service WSDL location',
      '#default_value' => $this->configuration['wsdl'],
      '#description' => $this->t('ex. CVROnline-SF1530/wsdl/token/OnlineService.wsdl. A relative path will be resolved relatively to the location of the OS2Web datalookup module.'),
    ];

    $form['location'] = [
      '#type' => 'textfield',
      '#title' => 'Service location (live)',
      '#default_value' => $this->configuration['location'],
      '#description' => $this->t('ex. https://prod.serviceplatformen.dk/service/CVR/Online/2'),
    ];

    $form['location_test'] = [
      '#type' => 'textfield',
      '#title' => 'Service location (test)',
      '#default_value' => $this->configuration['location_test'],
      '#description' => $this->t('ex. https://exttest.serviceplatformen.dk/service/CVR/Online/2'),
    ];

    $form['usersystemuuid'] = [
      '#type' => 'textfield',
      '#title' => 'System UUID',
      '#default_value' => $this->configuration['usersystemuuid'],
    ];

    $form['useruuid'] = [
      '#type' => 'textfield',
      '#title' => 'Kommune UUID',
      '#default_value' => $this->configuration['useruuid'],
    ];

    $form['accountinginfo'] = [
      '#type' => 'textfield',
      '#title' => 'AccountingInfo',
      '#default_value' => $this->configuration['accountinginfo'],
    ];

    $form['certfile_passphrase'] = [
      '#type' => 'password',
      '#title' => 'Certfile passphrase',
      '#default_value' => $this->configuration['certfile_passphrase'],
    ];

    $form['certfile'] = [
      '#type' => 'textfield',
      '#title' => 'Certfile (live)',
      '#default_value' => $this->configuration['certfile'],
    ];

    $form['certfile_test'] = [
      '#type' => 'textfield',
      '#title' => 'Certfile (test)',
      '#default_value' => $this->configuration['certfile_test'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state): void {
    if ($form_state->getValue('certfile_passphrase') == '') {
      $form_state->unsetValue('certfile_passphrase');
    }

    $keys = array_keys($this->defaultConfiguration());
    $configuration = $this->getConfiguration();
    foreach ($keys as $key) {
      $configuration[$key] = $form_state->getValue($key);
    }
    $this->setConfiguration($configuration);
  }

  /**
   * {@inheritdoc}
   */
  public function getStatus(): string {
    return $this->status;
  }

  /**
   * Plugin init method.
   */
  private function init(): void {
    ini_set('soap.wsdl_cache_enabled', 0);
    ini_set('soap.wsdl_cache_ttl', 0);
    $this->status = $this->t('Plugin is ready to work')->__toString();

    $required_configuration = [
      0 => [
        'serviceagreementuuid',
        'serviceuuid',
        'wsdl',
        'location',
        'usersystemuuid',
        'useruuid',
        'accountinginfo',
        'certfile',
      ],
      1 => [
        'serviceagreementuuid',
        'serviceuuid',
        'wsdl',
        'location_test',
        'usersystemuuid',
        'useruuid',
        'accountinginfo',
        'certfile_test',
      ],
    ];

    $this->isReady = TRUE;
    foreach ($required_configuration[$this->configuration['mode_selector']] as $key) {
      if (empty($this->configuration[$key])) {
        $this->isReady = FALSE;
        $this->status = $this->t('Configuration is not completed.')->__toString();
        return;
      }
    }

    try {
      switch ($this->configuration['mode_selector']) {
        case 0:
          $ws_config = [
            'location' => $this->configuration['location'],
            'local_cert' => $this->configuration['certfile'],
            'passphrase' => $this->configuration['certfile_passphrase'],
            'trace' => TRUE,
          ];
          break;

        case 1:
          $ws_config = [
            'location' => $this->configuration['location_test'],
            'local_cert' => $this->configuration['certfile_test'],
            'trace' => TRUE,
          ];
          break;
      }
      $this->client = new \SoapClient($this->getWsdlUrl(), $ws_config);
    }
    catch (\SoapFault $e) {
      $this->isReady = FALSE;
      $this->status = $e->faultstring;
    }
  }

  /**
   * Get wsdl URL method.
   *
   * @return string
   *   WSDL URL.
   */
  protected function getWsdlUrl(): string {
    $url = $this->configuration['wsdl'];
    // Anything that's not an absolute path or url will be resolved relative to
    // the location of the os2web_datalookup module.
    if (!preg_match('@^([a-z]+:/)?/@', $url)) {
      /** @var \Drupal\Core\Extension\ExtensionPathResolver $extensionPathResolver */
      $extensionPathResolver = \Drupal::service('extension.path.resolver');
      $path = realpath($extensionPathResolver->getPath('module', 'os2web_datalookup'));
      $url = 'file://' . $path . '/' . $url;
    }
    return $url;
  }

  /**
   * Webservice general request array prepare method.
   *
   * @return array
   *   Prepared request with general info.
   */
  protected function prepareRequest(): array {
    $user = \Drupal::currentUser();
    return [
      'InvocationContext' => [
        'ServiceAgreementUUID' => $this->configuration['serviceagreementuuid'],
        'UserSystemUUID' => $this->configuration['usersystemuuid'],
        'UserUUID' => $this->configuration['useruuid'],
        'ServiceUUID' => $this->configuration['serviceuuid'],
        'AccountingInfo' => $this->configuration['accountinginfo'],
        'OnBehalfOfUser' => $user->getAccountName(),
      ],
    ];
  }

  /**
   * Main service request query method.
   *
   * @param string $method
   *   Method name to call.
   * @param array $request
   *   Request array to call method.
   *
   * @return array
   *   Method response or FALSE.
   */
  protected function query(string $method, array $request): array {
    if (!$this->isReady()) {
      return [
        'status' => FALSE,
        'error' => $this->getStatus(),
      ];
    }

    try {
      $msg = sprintf('Method %s called with (%s)', $method, implode(', ', $request));
      $this->auditLogger->info('DataLookup', $msg);
      $response = (array) $this->client->$method($request);
      $response['status'] = TRUE;
    }
    catch (\SoapFault $e) {
      $msg = sprintf('Method %s called with (%s): %s', $method, implode(', ', $request), $e->faultstring);
      $this->auditLogger->error('DataLookup', $msg);
      $response = [
        'status' => FALSE,
        'error' => $e->faultstring,
      ];
    }

    return $response;
  }

}
