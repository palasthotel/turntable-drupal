<?php

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
    debug($form_state['values']['turntable_client_content_search']);
    $form['results'] = array(
      '#type' => 'table',
      '#title' => t('Results'),
      '#tree' => TRUE,
      '#theme' => 'table',
      '#header' => array(
        t('Title'),
        t('Date')
      ),
      '#rows' => array(
        array(
          'a',
          'b'
        )
      )
    );
  }

  return $form;
}

function turntable_client_content_search_submit($form, &$form_state) {
  $form_state['rebuild'] = TRUE;
}
