<?php
require_once './sites/all/libraries/turntable/turntable_master.php';

/**
 * @file
 * Administration page callbacks for the turntable master module.
 */

/**
 * Form builder.
 * Configure turntable master.
 *
 * @ingroup forms
 *
 * @see system_settings_form().
 *
 */
function turntable_master_admin_settings() {
  $enabled_clients_str = implode(',', $enabled_clients);

  $form['turntable_master_enabled_clients'] = array(
    '#type' => 'textfield',
    '#title' => t('Enabled client IDs'),
    '#default_value' => implode(', ',
        variable_get('turntable_master_enabled_clients', array())),
    '#description' => t('Comma separated list of IDs of enabled clients.')
  );

  $form['#submit'][] = 'turntable_master_admin_settings_submit';

  return system_settings_form($form);
}

function turntable_master_admin_settings_submit(&$form, &$form_state) {
  // set master url
  $enabled_clients = explode(',',
      $form_state['values']['turntable_master_enabled_clients']);
  foreach ($enabled_clients as &$value) {
    $value = trim($value);
  }

  variable_set('turntable_master_enabled_clients', $enabled_clients);
}
