<?php

function turntable_client_sync_node_settings() {
  $form['turntable_client_sync_push'] = array(
    '#title' => t('Push'),
    '#type' => 'fieldset',
    '#description' => t('Push local node to the Turntable Master.')
  );

  $form['turntable_client_sync_push']['submit'] = array(
    '#type' => 'button',
    '#value' => t('Push to master')
  );

  $form['turntable_client_sync_pull'] = array(
    '#title' => t('Pull'),
    '#type' => 'fieldset',
    '#description' => t('Pull remote node from the Turntable Master.')
  );

  $form['turntable_client_sync_pull']['sync_type'] = array(
    '#type' => 'value',
    '#value' => array(
      t('Copy'),
      t('Reference')
    )
  );

  $form['turntable_client_sync_pull']['sync_type_select'] = array(
    '#type' => 'select',
    '#title' => t('Synchronization Policy'),
    '#description' => t('Determines if the local node is a copy of the master node or a reference that gets updated on remote changes (local changes will be overwritten).'),
    '#options' => $form['turntable_client_sync_pull']['sync_type']['#value']
  );

  $form['turntable_client_sync_pull']['submit'] = array(
    '#type' => 'button',
    '#value' => t('Update')
  );

  return $form;
}
