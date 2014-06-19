<?php

function turntable_client_content_search() {
  $form['turntable_client_content_search'] = array(
    '#type' => 'textfield',
    '#title' => t('Query'),
    '#description' => t(
        'Search terms. Searches all fields that are shared within the node.')
  );
  $form['#submit'][] = 'turntable_client_content_search_submit';
  $form['submit'] = array('#type' => 'submit', '#value' => t('Search'));

  return $form;
}

function turntable_client_content_search_submit(&$form, &$form_state) {
  debug($form_state);
}
