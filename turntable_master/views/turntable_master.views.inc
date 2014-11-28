<?php
function turntable_client_views_data() {
  $data['tt_master_node_shared']['table']['group'] = t('Shared Content');

  $data['tt_master_node_shared']['table']['base'] = array(
    'field' => 'nid', // This is the identifier field for the view.
    'title' => t('Nodes'),
    'help' => t('Nodes table.'),
    'weight' => -10
  );

  $data['tt_master_node_shared']['table']['join'] = array(
    'node' => array(
      'handler' => 'views_join',
      'left_field' => 'nid',
      'field' => 'nid',
      'type' => 'INNER'
    )
  );

  $data['tt_master_node_shared']['nid'] = array(
    'title' => t('nid'),
    'help' => t('Node ID'),

    'relationship' => array(
      'base' => 'node',
      'base field' => 'nid',
      'handler' => 'views_handler_relationship',
      'label' => t('Default label for the relationship'),
      'title' => t('Title shown when adding the relationship'),
      'help' => t('More information on this relationship')
    )
  );

  $data['tt_master_node_shared']['client_id'] = array(
    'title' => t('Origin'),
    'help' => t('Original site.'),
    'field' => array(
      'handler' => 'turntable_origin_handler',
      'click sortable' => TRUE
    ),
    'sort' => array(
      'handler' => 'views_handler_sort'
    ),
    'filter' => array(
      'handler' => 'views_handler_filter_string'
    ),
    'argument' => array(
      'handler' => 'views_handler_argument_string'
    )
  );

  $data['tt_master_node_shared']['last_sync'] = array(
    'title' => t('Last sync'),
    'help' => t('Date of the last synchronization.'),
    'field' => array(
      'handler' => 'turntable_date_handler',
      'click sortable' => TRUE
    ),
    'sort' => array(
      'handler' => 'views_handler_sort_date'
    ),
    'filter' => array(
      'handler' => 'views_handler_filter_date'
    )
  );

  return $data;
}
