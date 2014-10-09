<?php
require_once './sites/all/libraries/turntable/turntable_client.php';

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
  $tt_client = turntable_client::getInstance();
  $db = $tt_client->getDB();
  $enabled_clients = $db->getEnabledClients();
  $enabled_clients_str = implode(',', $enabled_clients);

  $form['turntable_master_enabled_clients'] = array(
    '#type' => 'textfield',
    '#title' => t('Enabled client IDs'),
    '#default_value' => $enabled_clients_str,
    '#description' => t('Comma separated list of IDs for enabled clients.')
  );

  $form['#submit'][] = 'turntable_master_admin_settings_submit';

  return system_settings_form($form);
}

function turntable_master_admin_settings_submit(&$form, &$form_state) {
  $tt_client = turntable_client::getInstance();
  $db = $tt_client->getDB();

  // set master url
  $enabled_clients = explode(',',
      $form_state['values']['turntable_master_enabled_clients']);
  foreach ($enabled_clients as &$value) {
    $value = trim($value);
  }

  if (!$db->setEnabledClients($enabled_clients)) {
    drupal_message('error', t('Could not set the enabled client IDs.'));
  }
}
