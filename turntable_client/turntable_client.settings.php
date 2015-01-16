<?php
require_once './sites/all/libraries/turntable/turntable_client.php';

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
  global $base_url;

  $form['turntable_client_master_url'] = array(
    '#type' => 'textfield',
    '#title' => t('Turntable Master endpoint URL'),
    '#default_value' => variable_get('turntable_client_master_url'),
    '#description' => t('The URL of the linked Turntable Master instance.')
  );

  $form['turntable_client_upload_limit'] = array(
    '#type' => 'textfield',
    '#title' => t('Turntable upload limit'),
    '#default_value' => variable_get('turntable_client_upload_limit', 100),
    '#description' => t(
        'Number of nodes that are shared with the master during a single cron run.')
  );

  $form['turntable_client_id'] = array(
    '#type' => 'textfield',
    '#title' => t('Turntable Client ID'),
    '#default_value' => variable_get('turntable_client_id', $base_url),
    '#description' => t('Unique string that is used to identify a Client.')
  );

  $node_types = get_node_types();
  $node_types_selection = variable_get('turntable_client_share_node_types',
      array(
        'article',
        'page'
      ));

  $form['turntable_client_share_node_types'] = array(
    '#type' => 'checkboxes',
    '#title' => t('Shared node types'),
    '#options' => $node_types,
    '#default_value' => $node_types_selection,
    '#description' => t(
        'Node types that are shared by default when they are saved.')
  );

  $form['turntable_term'] = array(
    '#type' => 'textfield',
    '#title' => t('Turntable alternative term'),
    '#default_value' => variable_get('turntable_term', 'Turntable'),
    '#description' => t('Term that is used instead of "Turntable".')
  );

  $form['turntable_reset_sharing'] = array(
    '#type' => 'checkboxes',
    '#title' => t('Reset sharing index'),
    '#options' => array('Reset'),
    '#description' => t('All nodes that have a shared node type will be shared again.')
  );

  $form['#submit'][] = 'turntable_client_admin_settings_submit';

  return system_settings_form($form);
}

/**
 * Returns a list of node types.
 *
 * @return array
 */
function get_node_types() {
  $node_types = array();

  foreach (node_type_get_types() as $type => $obj) {
    $node_types[$type] = $obj->name;
  }

  return $node_types;
}

function turntable_client_admin_settings_submit(&$form, &$form_state) {
  // set master url
  variable_set('turntable_client_master_url',
      $form_state['values']['turntable_client_master_url']);

  // set upload limit
  variable_set('turntable_client_upload_limit',
      (int) $form_state['values']['turntable_client_upload_limit']);

  // set client id
  variable_set('turntable_client_id',
      $form_state['values']['turntable_client_id']);

  // turntable term
  variable_set('turntable_term',
      $form_state['values']['turntable_term']);

  // get the string we need to store
  $selection = $form_state['values']['turntable_client_share_node_types'];
  $selected_node_types = array();
  foreach ($selection as $key => $value) {
    if ($value !== 0) {
      $selected_node_types[] = $value;
    }
  }

  // set selected node types
  variable_set('turntable_client_share_node_types', $selected_node_types);

  // reset sharing
  $reset = $form_state['values']['turntable_reset_sharing'];
  if (count($reset) > 0) {
    $turntable_client = turntable_client::getInstance();
    $db = $turntable_client->getDB();
    $db->resetRemainingSharedNodes();
    drupal_set_message(t('Reset sharing index.'), 'status');
  }
}
