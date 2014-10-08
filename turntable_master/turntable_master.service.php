<?php
require_once './sites/all/libraries/turntable/turntable_master.php';
require_once './sites/all/libraries/turntable/core/http.php';

function turntable_master_save_shared_node($shared_node) {
  $turntable_master = turntable_master::getInstance();
  $db = $turntable_master->getDB();

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
  $turntable_master = turntable_master::getInstance();
  $db = $turntable_master->getDB();

  return $db->findSharedNode($query);
}

function turntable_master_get_image($url) {
  $dir = 'public://field/image/';
  $uri = $dir . url_to_filename($url);

  // check if image already exists
  $info = image_get_info($uri);
  if ($info === FALSE) {
    // prepare the directory
    if (!file_prepare_directory($dir, FILE_CREATE_DIRECTORY)) {
      return array(
        'error' => 'Could not create the directory "' . $dir . '".'
      );
    }

    // download the file
    $finfo = system_retrieve_file($url, $uri, TRUE, FILE_EXISTS_REPLACE);
    if ($finfo === FALSE) {
      return array(
        'error' => 'Could not retrieve the requested file "' . $url . '".'
      );
    }

    $img_info = getimagesize($uri);

    $entity_type = 'image';
    $entity = entity_create($entity_type, array(
      ''
    ));

    // set meta data
    $ewrapper = entity_metadata_wrapper($entity_type, $entity);
    $ewrapper->field_image_fid->set($finfo->fid);
    $ewrapper->field_image_width->set($img_info[0]);
    $ewrapper->field_image_height->set($img_info[1]);
    $ewrapper->save();

    $info = array(
      'width' => $img_info[0],
      'height' => $img_info[1],
      'extension' => pathinfo($finfo['filename'], PATHINFO_EXTENSION),
      'mime_type' => $finfo['filemime'],
      'file_size' => $finfo['filesize']
    );
  }

  // transfer the image
  file_transfer($uri,
      array(
        'Content-Type' => $info['mime_type'],
        'Content-Length' => $info['file_size']
      ));
}

function url_to_filename($url) {
  return str_replace(array(
    ':',
    '/'
  ), array(
    '_',
    '_'
  ), $url);
}
