<?php

namespace Drupal\os2web_datalookup\Plugin\os2web\DataLookup;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Messenger\MessengerInterface;
use Drupal\Core\Render\Markup;
use Drupal\os2web_datalookup\LookupResult\CprLookupResult;

/**
 * Defines a plugin for ServiceplatformenCPRExtended.
 *
 * @DataLookup(
 *   id = "serviceplatformen_cpr_extended",
 *   label = @Translation("Serviceplatformen CPR - extended (SF1520)"),
 *   group = "cpr_lookup"
 * )
 */
class ServiceplatformenCPRExtended extends ServiceplatformenBase implements DataLookupCPRInterface {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return array_merge(parent::defaultConfiguration(), [
      'test_mode_fixed_cpr' => '',
    ]);
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildConfigurationForm($form, $form_state);
    $form['mode_fieldset']['test_mode_fixed_cpr'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Fixed test CPR'),
      '#default_value' => $this->configuration['test_mode_fixed_cpr'],
      '#description' => $this->t('Fixed CPR that will be used for all requests to the serviceplatformen instead of the provided CPR.'),
      '#states' => [
        // Hide the settings when the cancel notify checkbox is disabled.
        'visible' => [
          'input[name="mode_selector"]' => ['value' => 1],
        ],
      ],
    ];
    $form['test_cpr'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Test CPR nr.'),
    ];
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);

    if (!empty($form_state->getValue('test_cpr'))) {
      $cpr = $form_state->getValue('test_cpr');
    }

    if (!empty($cpr)) {
      $cprResult = $this->lookup($cpr);
      $response = (array) $cprResult;

      \Drupal::messenger()->addMessage(
        Markup::create('<pre>' . print_r($response, 1) . '</pre>'),
        $cprResult->isSuccessful() ? MessengerInterface::TYPE_STATUS : MessengerInterface::TYPE_WARNING
      );
    }
  }

  /**
   * @inheritDoc
   */
  public function lookup($cpr, $allowCprTestModeReplace = TRUE) {
    if ($this->configuration['mode_selector'] == 1 && $this->configuration['test_mode_fixed_cpr']) {
      if ($allowCprTestModeReplace) {
        $cpr = $this->configuration['test_mode_fixed_cpr'];
        \Drupal::messenger()->addMessage(
          $this->t("Test mode enabled, all CPR lookup requests are made against CPR: %cpr", ['%cpr' => $cpr]),
          MessengerInterface::TYPE_STATUS
        );
      }
    }

    $request = $this->prepareRequest();
    $request['PNR'] = str_replace('-', '', $cpr);

    $result = $this->query('PersonLookup', $request);

    $cprResult = new CprLookupResult();
    // If all goes well we return address array.
    if ($result['status']) {
      $cprResult->setSuccessful();
      $cprResult->setCpr($cpr);
      $persondata = $result['persondata'];

      if ($persondata->navn) {
        $cprResult->setName($persondata->navn->personadresseringsnavn);
      }

      $address = $result['adresse'];
      if ($address->aktuelAdresse) {
        $cprResult->setStreet($address->aktuelAdresse->vejnavn ?? '');
        $cprResult->setHouseNr(isset($address->aktuelAdresse->husnummer) ? ltrim($address->aktuelAdresse->husnummer, '0') : '');
        $cprResult->setFloor($address->aktuelAdresse->etage ?? '');
        $cprResult->setApartmentNr(isset($address->aktuelAdresse->sidedoer) ? ltrim($address->aktuelAdresse->sidedoer, '0') : '');
        $cprResult->setPostalCode($address->aktuelAdresse->postnummer ?? '');
        $cprResult->setCity($address->aktuelAdresse->postdistrikt ?? '');
        $cprResult->setMunicipalityCode($address->aktuelAdresse->kommunekode ?? '');
        $cprResult->setAddress($address->aktuelAdresse->standardadresse ?? '');
      }

      $relationship = $result['relationer'];
      if ($relationship->barn && is_array($relationship->barn)) {
        $children = [];
        foreach ($relationship->barn as $child) {
          $childCprResult = $this->lookup($child->personnummer, FALSE);

          $children[] = [
            'cpr' => $childCprResult->getCpr(),
            'name' => $childCprResult->getName()
          ];
        }
        $cprResult->setChildren($children);
      }

      // Leaving empty, no information in webservice.
      $cprResult->setCoName('');
      $cprResult->setNameAddressProtected(FALSE);
    }
    else {
      $cprResult->setSuccessful(FALSE);
      $cprResult->setErrorMessage($result['error']);
    }

    return $cprResult;
  }

}
