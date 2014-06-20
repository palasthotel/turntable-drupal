<?php
require_once './sites/all/libraries/turntable/turntable_master.php';

function turntable_master_save_shared_node($shared_node) {
  $turntable_master = turntable_master::getInstance();
  $db = $turntable_master->getDB();

  // get the id of a possibly existing node
  $nid = $db->getSharedNodeID($shared_node);

  $old_nid = $nid;

  if ($nid === FALSE) {
    global $user; // use the current (anonymous) user

    $values = array(
      'type' => 'shared',
      'uid' => $user->uid,
      'status' => 0,
      'comment' => 0,
      'promote' => 0
    );

    // create entity and wrapper
    $local_node = entity_create('node', $values);
    $ewrapper = entity_metadata_wrapper('node', $local_node);

    // set title,
    $ewrapper->title->set($shared_node['title']);

    // body
    $ewrapper->body->set(array(
      'value' => $shared_node['body']
    ));

    // save node
    $ewrapper->save();

    $nid = $local_node->nid;

    $shared_node['nid'] = $nid;

    return $db->addSharedNode($shared_node);
  } else {
    // load the node
    $local_node = node_load($nid);

    $ewrapper = entity_metadata_wrapper('node', $local_node);

    // update and store the node
    $ewrapper->title->set($shared_node['title']);

    // body
    $ewrapper->body->set(array(
      'value' => $shared_node['body']
    ));

    // save node
    $ewrapper->save();

    $shared_node['nid'] = $nid;

    return $db->updateSharedNode($shared_node);
  }
}

function turntable_master_find_shared_node($query) {
  $turntable_master = turntable_master::getInstance();
  $db = $turntable_master->getDB();

  return $db->findSharedNode($query);
}
