<?php
require_once './sites/all/libraries/turntable/turntable_client.php';

function get_node_id() {
  if (!is_numeric(arg(1))) {
    return -1;
  }

  // return the node's id
  return (int) arg(1);
}

function turntable_client_shared_state_settings() {
  $nid = get_node_id();

  $shared_state = turntable_client::getInstance()->getDB()->getSharedState($nid);

  $form['turntable_client_shared_states'] = array(
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
        'Determines if the local node is not shared, a copy of a node on the master, a reference that gets updated on remote changes (local changes will be overwritten), or the original (remote nodes will get updated).'),
    '#options' => $form['turntable_client_shared_states']['#value'],
    '#default_value' => $shared_state
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
  $shared_state = $form_state['values']['turntable_client_shared_state'];

  $client = turntable_client::getInstance();
  $db = $client->getDB();

  $db->setSharedState($nid, $shared_state);
}

function turntable_client_node_presave($node) {
}
