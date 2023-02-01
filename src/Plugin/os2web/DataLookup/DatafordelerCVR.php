<?php

namespace Drupal\os2web_datalookup\Plugin\os2web\DataLookup;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Render\Markup;
use Drupal\os2web_datalookup\LookupResult\CompanyLookupResult;
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
class DatafordelerCVR extends DatafordelerBase implements DataLookupInterfaceCompany {

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
    $form = parent::buildConfigurationForm($form, $form_state);

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
    parent::submitConfigurationForm($form, $form_state);

    if (!empty($form_state->getValue('test_cvr'))) {
      $lookupResult = $this->lookup($form_state->getValue('test_cvr'));
      $response = (array) $lookupResult;

      \Drupal::messenger()->addMessage(
        Markup::create('<pre>' . print_r($response, 1) . '</pre>'),
        $lookupResult->isSuccessful() ? MessengerInterface::TYPE_STATUS : MessengerInterface::TYPE_WARNING
      );
    }
  }

  /**
   * {@inheritdoc}
   */
  public function lookup($cvr) {
    try {
      $response = $this->httpClient->get('hentVirksomhedMedCVRNummer', ['query' => ['pCVRNummer' => $cvr]]);
      $result = json_decode((string) $response->getBody());
    }
    catch (ClientException $e) {
      $result = $e->getMessage();
    }

    $cvrResult = new CompanyLookupResult();
    if ($result && isset($result->virksomhed) && !empty((array) $result->virksomhed)) {
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
