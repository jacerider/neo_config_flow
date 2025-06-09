<?php

namespace Drupal\neo_config_flow\EventSubscriber;

use Drupal\config_readonly\ReadOnlyFormEvent;
use Drupal\config_readonly\EventSubscriber\ReadOnlyFormSubscriber;
use Drupal\Core\Entity\EntityFormInterface;
use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Url;

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
      if ($event->isFormReadOnly() && \Drupal::currentUser()->hasPermission('import configuration')) {
        // Check if the form is a ConfigFormBase or a ConfigEntityListBuilder.
        $form_object = $event->getFormState()->getFormObject();
        if ($form_object instanceof EntityFormInterface) {
          $entity = $form_object->getEntity();
          \Drupal::messenger()->addStatus(t('<strong class="text-sm">CONFIG LOCK:</strong><br> The submission of this form is prevented as changes are stored in config and need to move through the configuration workflow. You can allow submitting of this form by <a href=":url">whitelisting</a> the config used on this page: %config.', [
            ':url' => Url::fromRoute('config_ignore.settings')->toString(),
            '%config' => $entity->getConfigDependencyName(),
          ]));
        }
        if ($form_object instanceof ConfigFormBase) {
          $editable_config = $event->getEditableConfigNames();
          \Drupal::messenger()->addStatus(t('<strong class="text-sm">CONFIG LOCK:</strong><br> The submission of this form is prevented as changes are stored in config and need to move through the configuration workflow. You can allow submitting of this form by <a href=":url">whitelisting</a> the configs used on this page: %config/', [
            ':url' => Url::fromRoute('config_ignore.settings')->toString(),
            '%config' => implode(', ', $editable_config),
          ]));
        }
      }
    }
  }

}
