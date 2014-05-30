<?php
require_once './sites/all/libraries/turntable/turntable_master.php';

function turntable_master_save_shared_node($shared_node) {
  $turntable_master = turntable_master::getInstance();
  $db = $turntable_master->getDB();

  // get the id of a possibly existing node
  $nid = $db->getSharedNodeID($shared_node);
  if ($nid === FALSE) {
    // load the node
    $local_node = node_load($nid);

    // update and store the node
    $local_node->title = $shared_node['title'];

    $shared_node['nid'] = $nid;

    $result = $db->saveSharedNode($shared_node);
  } else {
    global $user; // use the current (anonymous) user

    $values = array(
      'type' => 'YOUR_NODE_TYPE',
      'uid' => $user->uid,
      'status' => 0,
      'comment' => 0,
      'promote' => 0
    );

    // create entity and wrapper
    $entity = entity_create('node', $values);
    $ewrapper = entity_metadata_wrapper('node', $entity);

    // set title,
    $ewrapper->title->set($shared_node['title']);
    // body
    $ewrapper->body->set(array(
      'value' => $shared_node['body']
    ));
  }
  return $result;
}

function turntable_master_index_shared_nodes() {
  // TODO to be implemented
  return array();
}
