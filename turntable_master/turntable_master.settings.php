<?php

/**
 * @file
 * Administration page callbacks for the turntable master module.
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
function turntable_master_admin_settings() {
  $form['turntable_master_enabled_clients'] = array(
    '#type' => 'textfield',
    '#title' => t('Turntable Master endpoint URL'),
    '#default_value' => variable_get('turntable_master_allowed_clients'),
    '#description' => t('The URL of the linked Turntable Master instance.')
  );

  $form['turntable_client_update_interval'] = array(
    '#type' => 'textfield',
    '#title' => t('Turntable update interval'),
    '#default_value' => variable_get('turntable_client_update_interval'),
    '#description' => t(
        'Number of hours between automatic updates of referenced nodes.')
  );

  $form['#submit'][] = 'turntable_client_admin_settings_submit';

  return system_settings_form($form);
}

function turntable_client_admin_settings_submit(&$form, &$form_state) {
  // set master url
  variable_set('turntable_client_master_url',
      $form_state['values']['turntable_client_master_url']);

  // set update interval
  variable_set('turntable_client_update_interval',
      $form_state['values']['turntable_client_update_interval']);
}
