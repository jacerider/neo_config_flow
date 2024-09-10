<?php

namespace Drupal\neo_config_flow\Config;

use Drupal\config_readonly\Config\ConfigReadonlyStorage;

/**
 * Defines the ConfigReadonly storage controller which will fail on write.
 */
class NeoConfigFlowReadonlyStorage extends ConfigReadonlyStorage {

  /**
   * {@inheritdoc}
   */
  protected function checkLock($name = '') {
    if (neo_config_flow_lock()) {
      parent::checkLock($name);
    }
  }

}
