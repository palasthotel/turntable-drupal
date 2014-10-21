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
  global $base_url;

  $form['turntable_client_master_url'] = array(
    '#type' => 'textfield',
    '#title' => t('Turntable Master endpoint URL'),
    '#default_value' => variable_get('turntable_client_master_url'),
    '#description' => t('The URL of the linked Turntable Master instance.')
  );

  $form['turntable_client_update_interval'] = array(
    '#type' => 'textfield',
    '#title' => t('Turntable update interval'),
    '#default_value' => variable_get('turntable_client_update_interval'),
    '#description' => t(
        'Number of hours between automatic updates of referenced nodes.')
  );

  $form['turntable_client_upload_limit'] = array(
      '#type' => 'textfield',
      '#title' => t('Turntable upload limit'),
      '#default_value' => variable_get('turntable_client_upload_limit'),
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

  // set update interval
  variable_set('turntable_client_update_interval',
      $form_state['values']['turntable_client_update_interval']);

  // set client id
  variable_set('turntable_client_id',
      $form_state['values']['turntable_client_id']);

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
}
