<?php
require_once './sites/all/libraries/turntable/turntable_client.php';

function turntable_client_content_search($form, &$form_state) {
  $form['turntable_client_content_search'] = array(
    '#type' => 'textfield',
    '#title' => t('Search'),
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

    $shared_nodes = $turntable_client->findSharedNode($query);

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

    $shared_node = $turntable_client->getSharedNode($nid);

    debug($shared_node);

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
