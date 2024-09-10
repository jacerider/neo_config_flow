<?php

namespace Drupal\neo_config_flow\EventSubscriber;

use Drupal\config_readonly\ReadOnlyFormEvent;
use Drupal\config_readonly\EventSubscriber\ReadOnlyFormSubscriber;

/**
 * Check if the given form should be read-only.
 */
class NeoConfigFlowReadOnlyFormSubscriber extends ReadOnlyFormSubscriber {

  /**
   * {@inheritdoc}
   */
  public function onFormAlter(ReadOnlyFormEvent $event) {
    if (neo_config_flow_lock()) {
      parent::onFormAlter($event);
    }
  }

}
