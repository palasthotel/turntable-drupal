<?php
require_once './sites/all/libraries/turntable/turntable_master.php';
require_once './sites/all/libraries/turntable/core/http.php';
require_once './sites/all/libraries/turntable/core/util.php';
require_once './sites/all/libraries/turntable/drupal/images.php';

function turntable_master_save_shared_node($shared_node) {
  if (!is_client_enabled()) {
    header("HTTP/1.1 403 Forbidden");
    return array('error' => 'Unknown Client');
  }

  $db = turntable_master::getInstance()->getDB();

  // get the id of a possibly existing node
  $nid = $db->getSharedNodeID($shared_node);

  if ($nid === FALSE) {
    // new node

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
      'value' => $shared_node['all']
    ));

    // save node
    $ewrapper->save();

    // get and store its node id
    $nid = $local_node->nid;
    $shared_node['nid'] = $nid;

    // add the shared node to the db table
    return $db->addSharedNode($shared_node);
  } else {
    // update node

    // load the node
    $local_node = node_load($nid);

    $ewrapper = entity_metadata_wrapper('node', $local_node);

    // update and store the node
    $ewrapper->title->set($shared_node['title']);

    // body
    $ewrapper->body->set(array(
      'value' => $shared_node['all']
    ));

    // save node
    $ewrapper->save();

    $shared_node['nid'] = $nid;

    // update the node rather than inserting a new one
    return $db->updateSharedNode($shared_node);
  }
}

function turntable_master_get_shared_node($nid) {
  if (!is_client_enabled()) {
    header("HTTP/1.1 403 Forbidden");
    return array('error' => 'Unknown Client');
  }

  $nid = (int) $nid;

  $turntable_master = turntable_master::getInstance();
  $db = $turntable_master->getDB();

  $shared = $db->getSharedNode($nid);
  $node = node_load($nid);

  $result = array();
  $result['nid'] = $nid;

  $result['title'] = $node->title;
  $result['language'] = $node->language;

  $result['client_id'] = $shared['client_id'];
  $result['client_nid'] = $shared['client_nid'];
  $result['revision_uid'] = $shared['client_vid'];
  $result['content_type'] = $shared['client_type'];
  $result['user_name'] = $shared['client_user_name'];
  $result['author_name'] = $shared['client_author_name'];
  $result['last_sync'] = date('c', strtotime($shared['last_sync']));

  $result['all'] = $node->body[$node->language][0]['value'];
  $result['images'] = $shared['client_images'];

  return $result;
}

function turntable_master_find_shared_node($query) {
  $db = turntable_master::getInstance()->getDB();

  // don't check the client id when searching for nodes
  // helps with debugging

  return $db->findSharedNode($query);
}

function turntable_master_get_image($url) {
  // Don't check for valid client id's in image service
  // unable to set the correct http header in request
  if (!is_client_enabled()) {
    header("HTTP/1.1 403 Forbidden");
    return array('error' => 'Unknown Client');
  }

  $dir = 'public://turntable/';
  $fname = url_to_filename($url);
  $uri = $dir . $fname;

  $info = ensure_image_is_available($dir, $fname, $url, FALSE);

  if (!$info || isset($info['error'])) {
    header("HTTP/1.1 404 Not Found");
    return $info;
  }

  // transfer the image to the client
  file_transfer($uri,
      array(
        'Content-Type' => $info['mime_type'],
        'Content-Length' => $info['file_size']
      ));
}

function is_client_enabled() {
  $enabled_clients = explode(',',
      variable_get('turntable_master_enabled_clients', ''));

  // get the client id from the http headers
  if (isset($_SERVER['HTTP_TURNTABLE_CLIENT_ID'])) {
    $client_id = $_SERVER['HTTP_TURNTABLE_CLIENT_ID'];
  } else if(isset($_GET['client_id'])) {
    $client_id = $_GET['client_id'];
  } else {
    return FALSE;
  }

  return in_array($client_id, $enabled_clients);
}
