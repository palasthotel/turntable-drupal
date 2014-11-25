<?php

function turntable_client_views_default_views() {
  $is_master = FALSE;
  $view = _get_admin_view($is_master);

  $views[$view->name] = $view;

  // return views
  return $views;
}

function my_module_views_data_alter(&$data) {
  $data['node']['shared_content']['title'] = 'Shared content';
  $data['node']['shared_content']['help'] = 'Only shows shared content.';
  $data['node']['shared_content']['filter']['handler'] = 'turntable_client_views_content_filter';
}

function turntable_client_views_data() {
  $table['node']['table']['group'] = t('Content');

  $table['node']['table']['base'] = array(
    'field' => 'nid',
    'title' => t('Content'),
    'weight' => -10,
    'access query tag' => 'node_access', // TODO find correct access tag
    'defaults' => array(
      'field' => 'title'
    )
  );

  $table['node']['table']['join'] = array(
    'tt_client_node_shared' => array(
      'left_table' => 'tt_client_node_shared',
      'left_field' => 'nid',
      'field' => 'nid'
    )
  );

  $table['node']['nid'] = array(
    'title' => t('Nid'),
    'help' => t('The node ID.'), // The help that appears on the UI,
                                 // Information for displaying the nid
    'field' => array(
      'handler' => 'views_handler_field_node',
      'click sortable' => TRUE
    ),
    // Information for accepting a nid as an argument
    'argument' => array(
      'handler' => 'views_handler_argument_node_nid',
      'name field' => 'title', // the field to display in the summary.
      'numeric' => TRUE,
      'validate type' => 'nid'
    ),
    // Information for accepting a nid as a filter
    'filter' => array(
      'handler' => 'views_handler_filter_numeric'
    ),
    // Information for sorting on a nid.
    'sort' => array(
      'handler' => 'views_handler_sort'
    )
  );
}
