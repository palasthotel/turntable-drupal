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
  $client_ids = (array) variable_get('turntable_master_enabled_clients',
      array());

  $form['turntable_master_enabled_clients'] = array(
    '#type' => 'textfield',
    '#title' => t('Enabled client IDs'),
    '#default_value' => implode(', ', $client_ids),
    '#description' => t('Comma separated list of IDs of enabled clients.')
  );

  $form['#submit'][] = 'turntable_master_admin_settings_submit';

  return system_settings_form($form);
}

function turntable_master_admin_settings_submit(&$form, &$form_state) {
  // set master url
  $str = $form_state['values']['turntable_master_enabled_clients'];
  $enabled_clients = explode(',', $str);

  debug($enabled_clients);

  variable_set('turntable_master_enabled_clients', $enabled_clients);
}
