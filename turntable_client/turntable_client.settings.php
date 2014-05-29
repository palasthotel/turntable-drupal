<?php

/**
 * @file
 * Administration page callbacks for the turntable client module.
 */

/**
 * Form builder.
 * Configure turntable client.
 *
 * @ingroup forms
 *
 * @see system_settings_form().
 *
 */
function turntable_client_admin_settings() {
  $form['turntable_client_master_url'] = array(
    '#type' => 'textfield',
    '#title' => t('Turntable Master endpoint URL'),
    '#default_value' => variable_get('turntable_client_master_url'),
    '#description' => t('The URL of the linked Turntable Master instance.')
  );
  $form['#submit'][] = 'turntable_client_admin_settings_submit';
  return system_settings_form($form);
}

function turntable_client_admin_settings_submit(&$form, &$form_state) {
  variable_set('turntable_client_master_url',
      $form_state['values']['turntable_client_master_url']);
}
