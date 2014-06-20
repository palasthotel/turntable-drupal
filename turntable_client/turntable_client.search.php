<?php
require_once './sites/all/libraries/turntable/turntable_client.php';

function turntable_client_content_search($form, &$form_state) {
  $form['turntable_client_content_search'] = array(
    '#type' => 'textfield',
    '#title' => t('Query'),
    '#description' => t(
        'Search terms. Searches all fields that are shared within the node.')
  );
  $form['#submit'][] = 'turntable_client_content_search_submit';
  $form['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Search')
  );

  if (!empty($form_state['values']['turntable_client_content_search'])) {
    $turntable_client = turntable_client::getInstance();
    $turntable_client->setMasterURL(variable_get('turntable_client_master_url'));

    $query = $form_state['values']['turntable_client_content_search'];

    $results = $turntable_client->findRemoteContent($query);

    debug($results);

    $form['results'] = array(
      '#type' => 'table',
      '#title' => t('Results'),
      '#tree' => TRUE,
      '#theme' => 'table',
      '#header' => array(
        t('Title'),
        t('Author'),
        t('Date')
      ),
      '#rows' => array(
        array()
      )
    );
  }

  return $form;
}

function turntable_client_content_search_submit($form, &$form_state) {
  $form_state['rebuild'] = TRUE;
}
