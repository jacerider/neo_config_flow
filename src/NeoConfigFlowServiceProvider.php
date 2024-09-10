<?php

namespace Drupal\neo_config_flow;

use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderBase;

/**
 * Modifies the language manager service.
 */
class NeoConfigFlowServiceProvider extends ServiceProviderBase {

  /**
   * {@inheritdoc}
   */
  public function alter(ContainerBuilder $container) {
    $definition = $container->getDefinition('config_readonly_form_subscriber');
    $definition->setClass('Drupal\neo_config_flow\EventSubscriber\NeoConfigFlowReadOnlyFormSubscriber');

    if ($container->getParameter('kernel.environment') !== 'install') {
      $definition = $container->getDefinition('config.storage');
      $definition->setClass('Drupal\neo_config_flow\Config\NeoConfigFlowReadonlyStorage');
    }
  }

}
