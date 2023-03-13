<?php

namespace Drupal\the_tribe_notification_ms\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

class FormConfigTheTribeNotificaciones extends ConfigFormBase {

  public function getFormId() {
    return 'the_tribe_notifications_ms.config';
  }

  public function getEditableConfigNames() {
    return ['the_tribe_notifications_ms.config'];
  }

  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('the_tribe_notifications_ms.config');
    $form['status'] = [
      '#type' => 'select',
      '#title' => t('Seleccione el estado del módulo'),
      '#options' => [
        'test' => 'Pruebas',
        'prod' => 'Producción',
      ],
      '#required' => TRUE,
      '#default_value' => $config->get('status'),
    ];
    $form['sharp_field_name'] = [
      '#type' => 'text',
      '#title' => t('Escriba el nombre del campo para el sharp id'),
      '#required' => TRUE,
      '#default_value' => $config->get('sharp_field_name'),
    ];
    return parent::buildForm($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('the_tribe_notifications_ms.config')
      ->set('status',$form_state->getValue('status'))
      ->set("sharp_field_name", $form_state->getValue("sharp_field_name"))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
