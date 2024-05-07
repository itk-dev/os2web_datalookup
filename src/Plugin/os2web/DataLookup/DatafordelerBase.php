<?php

namespace Drupal\os2web_datalookup\Plugin\os2web\DataLookup;

use Drupal\Core\Form\FormStateInterface;
use Drupal\os2web_audit\Service\Logger;
use GuzzleHttp\Client;

/**
 * Defines base plugin class for Datafordeler lookup plugins.
 */
abstract class DatafordelerBase extends DataLookupBase {

  /**
   * Http client.
   *
   * @var \GuzzleHttp\Client
   */
  protected Client $httpClient;

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
   * Plugin init method.
   */
  private function init(): void {
    $this->isReady = FALSE;

    $configuration = $this->getConfiguration();

    if ($webserviceUrl = $configuration['webserviceurl_live']) {
      $options = [
        'base_uri' => $webserviceUrl,
        'headers' => [
          'accept' => 'application/json',
        ],
      ];
      if ($certPath = $configuration['cert_path_live']) {
        $options['cert'] = $certPath;
        $this->httpClient = new Client($options);
        $this->isReady = TRUE;
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getStatus(): string {
    if (isset($this->httpClient)) {
      return $this->t('Plugin is ready to work')->render();
    }
    else {
      return $this->t('Configuration is not completed')->render();
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state): array {
    $form['webserviceurl_live'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Webservice URL (LIVE)'),
      '#description' => $this->t('Live URL against which to make the request, e.g. https://s5-certservices.datafordeler.dk/CVR/HentCVRData/1/REST/'),
      '#default_value' => $this->configuration['webserviceurl_live'],
    ];

    $form['cert_path_live'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Certificate (LIVE)'),
      '#description' => $this->t('Path to the certificate'),
      '#default_value' => $this->configuration['cert_path_live'],
    ];

    $form['cert_passphrase_live'] = [
      '#type' => 'password',
      '#title' => $this->t('Certificate passphrase (LIVE)'),
      '#description' => $this->t('leave empty if not used'),
      '#default_value' => $this->configuration['cert_passphrase_live'],
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state): void {
    if ($form_state->getValue('cert_passphrase_live') == '') {
      $form_state->unsetValue('cert_passphrase_live');
    }

    $keys = array_keys($this->defaultConfiguration());
    $configuration = $this->getConfiguration();
    foreach ($keys as $key) {
      $configuration[$key] = $form_state->getValue($key);
    }
    $this->setConfiguration($configuration);
  }

}
