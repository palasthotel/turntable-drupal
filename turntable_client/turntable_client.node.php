<?php
require_once './sites/all/libraries/turntable/turntable_client.php';
require_once './sites/all/libraries/turntable/core/http.php';

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
  $shared_state = (int) $form_state['values']['turntable_client_shared_state'];
  $turntable_client = turntable_client::getInstance();
  $turntable_client->getDB()->setSharedState($nid, $shared_state);

  // post to master
  if ($shared_state === turntable_client::SHARED_ORIG) {
    $node = node_load($nid);
    send_shared_node($turntable_client, $node);
  }
}

function turntable_client_node_presave($node) {
  $turntable_client = turntable_client::getInstance();
  $shared_state = $turntable_client->getDB()->getSharedState($node);

  if ($shared_state === turntable_client::SHARED_ORIG) {
    send_shared_node($turntable_client, $node);
  }
}

function send_shared_node($turntable_client, $node) {
  global $base_url;
  global $user;

  $turntable_client = turntable_client::getInstance();
  $turntable_client->setMasterURL(variable_get('turntable_client_master_url'));
  $turntable_client->setClientID($base_url);

  $turntable_client->sendSharedNode($node, $user);
}
