<?php
require_once './sites/all/libraries/turntable/turntable_client.php';

function turntable_client_sync_node_settings() {
  $form['turntable_client_sync_push'] = array(
    '#title' => t('Push'),
    '#type' => 'fieldset'
  );

  $form['turntable_client_sync_push']['push_enabled'] = array(
    '#title' => t('Push to master'),
    '#type' => 'checkbox',
    '#default' => FALSE,
    '#description' => t(
        'If this box is checked, changes to the node will be pushed to the Turntable Master.')
  );

  $form['turntable_client_sync_pull'] = array(
    '#title' => t('Pull'),
    '#type' => 'fieldset'
  );

  $form['turntable_client_sync_pull']['sync_types'] = array(
    '#type' => 'value',
    '#value' => array(
      turntable_client::SYNC_NONE => t('Not a remote content'),
      turntable_client::SYNC_COPY => t('Copy'),
      turntable_client::SYNC_REF => t('Reference')
    )
  );

  $form['turntable_client_sync_pull']['sync_type'] = array(
    '#type' => 'select',
    '#title' => t('Synchronization Policy'),
    '#description' => t(
        'Determines if the local node is a copy of the master node or a reference that gets updated on remote changes (local changes will be overwritten).'),
    '#options' => $form['turntable_client_sync_pull']['sync_types']['#value']
  );

  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Submit')
  );

  return $form;
}

function turntable_client_sync_node_settings_submit(&$form, &$form_state) {
  if (!is_numeric(arg(1))) {
    return;
  }

  // get the node's id
  $nid = (int) arg(1);

  // form settings
  $push_enabled = (boolean) $form_state['values']['push_enabled'];
  $sync_type = $form_state['values']['sync_type'];

  $client = turntable_client::getInstance();
  $db = $client->getDB();

  $db->setSynchronizationSettings($nid, $push_enabled, $sync_type);
}

function turntable_client_node_presave($node) {
}
