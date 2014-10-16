<?php
require_once './sites/all/libraries/turntable/turntable_client.php';
require_once './sites/all/libraries/turntable/core/util.php';
require_once './sites/all/libraries/turntable/drupal/images.php';
require_once './sites/all/libraries/turntable/drupal/util.php';

function turntable_client_content_search($form, &$form_state) {
  global $base_url;

  $form['turntable_client_content_search'] = array(
    '#type' => 'textfield',
    '#title' => t('Turntable Search'),
    '#description' => t(
        'Searches all fields that are shared within the node. Terms are space separated.')
  );

  $form['turntable_client_content_search_submit'] = array(
    '#type' => 'submit',
    '#value' => t('Search'),
    '#submit' => array(
      'turntable_client_content_search_submit'
    )
  );

  if (!empty($form_state['values']['turntable_client_content_search'])) {
    $turntable_client = turntable_client::getInstance();
    $turntable_client->setMasterURL(variable_get('turntable_client_master_url'));

    $query = $form_state['values']['turntable_client_content_search'];

    $shared_nodes = $turntable_client->findSharedNodes($query);

    // filter shared nodes
    $filtered = array();
    // filter out remote nodes with local client id
    if (is_array($shared_nodes)) {
      foreach ($shared_nodes as $node) {
        if ($node->client_id !== $base_url) {
          $filtered[] = $node;
        }
      }
    }
    $shared_nodes = $filtered;

    $rows = array();

    // preload labels
    $label_none = t('None');
    $label_copy = t('Copy');
    $label_ref = t('Reference');

    foreach ($shared_nodes as $shared_node) {
      $rows[$shared_node->nid] = array(
        $shared_node->title,
        $shared_node->author,
        $shared_node->last_sync
      );
    }

    $form['results'] = array(
      '#type' => 'tableselect',
      '#title' => t('Results'),
      '#tree' => TRUE,
      '#header' => array(
        t('Title'),
        t('Author'),
        t('Last sync')
      ),
      '#options' => $rows,
      '#multiple' => FALSE
    );

    $form['copy'] = array(
      '#type' => 'submit',
      '#value' => t('Import as copy'),
      '#submit' => array(
        'turntable_client_content_search_create_copy'
      )
    );

    $form['reference'] = array(
      '#type' => 'submit',
      '#value' => t('Import as reference'),
      '#submit' => array(
        'turntable_client_content_search_create_ref'
      )
    );
  }

  return $form;
}

function turntable_client_content_search_submit($form, &$form_state) {
  $form_state['rebuild'] = TRUE;

  if (empty($form_state['values']['turntable_client_content_search'])) {
    drupal_set_message(t('Empty search query.'), 'warning');
  }
}

/**
 * Create content (copy or reference).
 *
 * This means a remote node will be downloaded.
 *
 * @param array $form_state
 * @param boolean $as_reference
 */
function turntable_client_content_search_create(&$form_state, $as_reference) {
  global $base_url;

  $form_state['rebuild'] = TRUE;
  $nid = $form_state['values']['results'];

  if ($nid === '') {
    drupal_set_message(t('Empty selection.'), 'warning');
  } else {
    $nid = (int) $nid;

    $turntable_client = turntable_client::getInstance();
    $turntable_client->setMasterURL(variable_get('turntable_client_master_url'));
    $turntable_client->setClientID(
        variable_get('turntable_client_id', $base_url));
    $db = $turntable_client->getDB();

    // get the shared node from master
    $shared_node = $turntable_client->getSharedNode($nid);
    $shared_node->master_node_id = $nid;

    $existing_node_id = $db->getSharedNodeID($nid);

    // check if the node has been imported yet
    if ($existing_node_id !== FALSE) {
      drupal_set_message(
          t(
              'The selected node has already been imported before. You might want to change its settings.'),
          'warning');
      return;
    }

    // if the node has not been imported yet, create it
    global $user; // use the current user

    // set up the values of this node
    $values = std_to_array(json_decode($shared_node->all));

    // check if the content type is available
    if (!in_array($values['type'], get_available_node_content_types())) {
      drupal_set_message(
          t(
              'The selected node could not be imported due to incompatible content type.'),
          'warning');
      return;
    }

    $images = std_to_array(json_decode($shared_node->images));

    $values['uid'] = $user->uid; // current user id
    $values['status'] = 0; // not published
    $values['comment'] = 0; // disallow commends
    $values['promote'] = 0; // not promoted
    $values['is_new'] = TRUE; // new node

    foreach ($images as $i => &$img) {
      // download the image
      $downloaded = download_image($img);

      if ($downloaded) {
        // replace the remote fid with the local fid
        foreach ($values['field_image'] as $lang => &$img_array) {
          foreach ($img_array as $i => &$node_img) {
            if ($node_img['fid'] === $img['fid']) {
              $node_img['fid'] = $img['local_fid'];
            }
          }
        }
      } else {
        drupal_set_message(t('Could not download an image.'), 'warning');
        return;
      }
    }

    // remove unneeded attributes
    unset($values['nid']);
    unset($values['vid']);
    unset($values['path']);

    // create entity
    $local_node = entity_create('node', $values);

    // save node
    if (entity_save('node', $local_node) === FALSE) {
      drupal_set_message(t('Could not import the selected node.'), 'warning');
      return;
    }

    $nid = $local_node->nid;

    $shared_node->nid = $nid;

    // set shared state
    if ($as_reference) {
      $shared_node->shared_state = turntable_client::SHARED_REF;
    } else {
      $shared_node->shared_state = turntable_client::SHARED_COPY;
    }

    // parse ISO 8601 date
    $shared_node->last_sync = DateTime::createFromFormat(DateTime::ISO8601,
        $shared_node->last_sync);

    // add shared node to db
    $res = $db->addSharedNode($shared_node);

    // show error
    if ($res === FALSE) {
      drupal_set_message(t('Could not import the selected node.'), 'warning');
      return;
    }

    // messages according to state
    if ($as_reference) {
      drupal_set_message(
          t('Successfully imported selected node as a reference.'), 'status');
    } else {
      drupal_set_message(t('Successfully imported selected node as a copy.'),
          'status');
    }

    // redirect the user to the newly created node
    drupal_goto("node/$nid/edit");
  }
}

/**
 * Copy a shared node from the master.
 *
 * @param array $form
 * @param boolean $form_state
 */
function turntable_client_content_search_create_copy($form, &$form_state) {
  turntable_client_content_search_create($form_state, FALSE); // delegate
}

/**
 * Reference a shared node from the master.
 *
 * @param array $form
 * @param boolean $form_state
 */
function turntable_client_content_search_create_ref($form, &$form_state) {
  turntable_client_content_search_create($form_state, TRUE); // delegate
}
