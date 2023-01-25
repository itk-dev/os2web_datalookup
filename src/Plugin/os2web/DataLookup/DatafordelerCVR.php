<?php

namespace Drupal\os2web_datalookup\Plugin\os2web\DataLookup;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Render\Markup;
use Drupal\os2web_datalookup\LookupResult\CvrLookupResult;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

/**
 * Defines a plugin for DatafordelerCVR.
 *
 * @DataLookup(
 *   id = "datafordeler_cvr",
 *   label = @Translation("Datafordeler CVR"),
 *   group = "cvr_lookup"
 * )
 */
class DatafordelerCVR extends DataLookupBase implements DataLookupInterfaceCvr {

  /**
   * Plugin readiness flag.
   *
   * @var bool
   */
  protected $httpClient;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->init();
  }

  /**
   * Plugin init method.
   */
  private function init() {
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
  public function getStatus() {
    if ($this->httpClient) {
      return $this->t('Plugin is ready to work');
    }
    else {
      return $this->t('Configuration is not completed');
    }
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'webserviceurl_live' => 'https://s5-certservices.datafordeler.dk/CVR/HentCVRData/1/REST/',
      'cert_path_live' => '',
      'cert_passphrase_live' => '',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
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

    $form['test_cvr'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Test CVR nr.'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    if ($form_state->getValue('cert_passphrase_live') == '') {
      $form_state->unsetValue('cert_passphrase_live');
    }

    $keys = array_keys($this->defaultConfiguration());
    $configuration = $this->getConfiguration();
    foreach ($keys as $key) {
      $configuration[$key] = $form_state->getValue($key);
    }
    $this->setConfiguration($configuration);

    if (!empty($form_state->getValue('test_cvr'))) {
      $cvrResult = $this->lookup($form_state->getValue('test_cvr'));
      $response = (array) $cvrResult;

      \Drupal::messenger()->addMessage(
        Markup::create('<pre>' . print_r($response, 1) . '</pre>'),
        $cvrResult->isSuccessful() ? MessengerInterface::TYPE_STATUS : MessengerInterface::TYPE_WARNING
      );
    }
  }

  /**
   * @inheritDoc
   */
  public function lookup($cvr) {
    try {
      $response = $this->httpClient->get('hentVirksomhedMedCVRNummer', ['query' => ['pCVRNummer' => $cvr]]);
      $result = json_decode((string) $response->getBody());
    }
    catch (ClientException $e) {
      $result = $e->getMessage();
    }

    $cvrResult = new CvrLookupResult();
    if ($result && isset($result->virksomhed) && !empty($result->virksomhed)) {
      $cvrResult->setSuccessful();
      $cvrResult->setCvr($cvr);

      if ($result->virksomhedsnavn) {
        $cvrResult->setName($result->virksomhedsnavn->vaerdi);
      }

      if ($result->beliggenhedsadresse) {
        $address = $result->beliggenhedsadresse;

        $cvrResult->setStreet($address->CVRAdresse_vejnavn ?? '');
        $cvrResult->setHouseNr($address->CVRAdresse_husnummerFra ?? '');
        $cvrResult->setFloor($address->CVRAdresse_etagebetegnelse ?? '');
        $cvrResult->setApartmentNr($address->CVRAdresse_doerbetegnelse ?? '');
        $cvrResult->setPostalCode($address->CVRAdresse_postnummer ?? '');
        $city = $address->CVRAdresse_postdistrikt ?? '' . $cvrResult->getPostalCode();
        $cvrResult->setCity($city);
        $cvrResult->setMunicipalityCode($address->CVRAdresse_kommunekode ?? '');
        $address = $cvrResult->getStreet() . ' ' . $cvrResult->getHouseNr() . ' ' . $cvrResult->getFloor() . $cvrResult->getApartmentNr();
        $cvrResult->setAddress(trim($address));
      }
    }
    else {
      $cvrResult->setSuccessful(FALSE);
      if (is_string($result)) {
        $cvrResult->setErrorMessage($result);
      }
    }

    return $cvrResult;
  }

}
