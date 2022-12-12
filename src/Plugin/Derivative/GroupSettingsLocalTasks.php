<?php

namespace Drupal\os2web_datalookup\Plugin\Derivative;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines dynamic group settings local tasks.
 */
class GroupSettingsLocalTasks extends DeriverBase implements ContainerDeriverInterface {

  /** @var \Drupal\os2web_datalookup\Plugin\DataLookupManager */
  protected $dataLookupManager;

  /**
   * Constructs a new GroupSettingsLocalTasks object.
   *
   * @param \Drupal\os2web_datalookup\Plugin\DataLookupManager
   *   Datalookup manager.
   */
  public function __construct(PluginManagerInterface $dataLookupManager) {
    $this->dataLookupManager = $dataLookupManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static(
      $container->get('plugin.manager.os2web_datalookup')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    // Implement dynamic logic to provide values for the same keys as in example.links.task.yml.
    $groups = $this->dataLookupManager->getDatalookupGroups();

    foreach ($groups as $group) {
      $this->derivatives[$group] = $base_plugin_definition;
      $this->derivatives[$group]['title'] = $group;
      $this->derivatives[$group]['route_name'] = "os2web_datalookup.groups.$group";
      $this->derivatives[$group]['base_route'] = "os2web_datalookup.status_list";
    }

    return $this->derivatives;
  }

}
