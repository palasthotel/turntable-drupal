<?php
require_once './sites/all/libraries/turntable/turntable_client.php';

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
    foreach ($shared_nodes as $node) {
      if ($node->client_id !== $base_url) {
        $filtered[] = $node;
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

function turntable_client_content_search_create(&$form_state, $asReference) {
  $nid = $form_state['values']['results'];

  if ($nid === '') {
    drupal_set_message(t('Empty selection.'), 'warning');
    $form_state['rebuild'] = TRUE;
  } else {
    $nid = (int) $nid;

    $turntable_client = turntable_client::getInstance();
    $turntable_client->setMasterURL(variable_get('turntable_client_master_url'));
    $db = $turntable_client->getDB();

    $shared_node = $turntable_client->getSharedNode($nid);

    global $user; // use the current user

    $values = array(
      'type' => 'article',
      'uid' => $user->uid,
      'status' => 0,
      'comment' => 0,
      'promote' => 0
    );

    // create entity and wrapper
    $local_node = entity_create('node', $values);
    $ewrapper = entity_metadata_wrapper('node', $local_node);

    // set title,
    $ewrapper->title->set($shared_node->title);

    // body
    $ewrapper->body->set(array(
      'value' => $shared_node->body
    ));

    // save node
    $ewrapper->save();

    $nid = $local_node->nid;

    $shared_node->nid = $nid;

    // set shared state
    if ($asReference) {
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
    if ($asReference) {
      drupal_set_message(
          t('Successfully imported selected node as a reference.'), 'status');
    } else {
      drupal_set_message(t('Successfully imported selected node as a copy.'),
          'status');
    }
  }
}

function turntable_client_content_search_create_copy($form, &$form_state) {
  turntable_client_content_search_create($form_state, FALSE); // delegate
}

function turntable_client_content_search_create_ref($form, &$form_state) {
  turntable_client_content_search_create($form_state, TRUE); // delegate
}
