<?php

namespace Drupal\os2web_datalookup\Plugin\os2web\DataLookup;

use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Plugin\PluginBase;
use Drupal\os2web_audit\Service\Logger;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a base class for image effects.
 *
 * @see \Drupal\image\Annotation\ImageEffect
 * @see \Drupal\image\ImageEffectInterface
 * @see \Drupal\image\ConfigurableImageEffectInterface
 * @see \Drupal\image\ConfigurableImageEffectBase
 * @see \Drupal\image\ImageEffectManager
 * @see plugin_api
 */
abstract class DataLookupBase extends PluginBase implements DataLookupInterface, ContainerFactoryPluginInterface {

  /**
   * Plugin readiness flag.
   *
   * @var bool
   */
  protected bool $isReady = TRUE;

  /**
   * Audit logger.
   *
   * @var \Drupal\os2web_audit\Service\Logger
   */
  protected Logger $auditLogger;

  /**
   * {@inheritdoc}
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    Logger $auditLogger,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->auditLogger = $auditLogger;
    $this->setConfiguration($configuration);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('os2web_audit.logger'),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function label(): string {
    return $this->pluginDefinition['label'];
  }

  /**
   * {@inheritdoc}
   */
  public function getConfiguration(): array {
    return $this->configuration;
  }

  /**
   * {@inheritdoc}
   */
  public function setConfiguration(array $configuration): static {
    $this->configuration = $configuration + $this->defaultConfiguration();

    return $this;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration(): array {
    return [];
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
    // Making validate optional.
  }

  /**
   * {@inheritdoc}
   */
  public function getStatus(): string {
    return 'N/A';
  }

  /**
   * {@inheritdoc}
   */
  public function getGroup(): string {
    return $this->pluginDefinition['group'];
  }

  /**
   * {@inheritdoc}
   */
  public function isReady(): bool {
    return $this->isReady;
  }

}
