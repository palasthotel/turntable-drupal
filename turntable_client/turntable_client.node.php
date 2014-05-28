<?php
require_once './sites/all/libraries/turntable/turntable_client.php';

function turntable_client_shared_state_settings() {
  $form['turntable_client_node_shared_states'] = array(
    '#type' => 'value',
    '#value' => array(
      turntable_client::SHARED_NONE => t('Not shared (normal node)'),
      turntable_client::SHARED_COPY => t('Copy'),
      turntable_client::SHARED_REF => t('Reference'),
      turntable_client::SHARED_ORIG => t('Original')
    )
  );

  $form['turntable_client_shared_state'] = array(
    '#type' => 'select',
    '#title' => t('Shared state'),
    '#description' => t(
        'Determines if the local node is a copy of the master node or a reference that gets updated on remote changes (local changes will be overwritten).'),
    '#options' => $form['turntable_client_shared_states']['#value']
  );

  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Submit')
  );

  return $form;
}

function turntable_client_shared_state_settings_submit(&$form, &$form_state) {
  if (!is_numeric(arg(1))) {
    return;
  }

  // get the node's id
  $nid = (int) arg(1);

  // form settings
  $shared_state = $form_state['values']['shared_state'];

  $client = turntable_client::getInstance();
  $db = $client->getDB();

  $db->setSynchronizationSettings($nid, $shared_state);
}

function turntable_client_node_presave($node) {
}
