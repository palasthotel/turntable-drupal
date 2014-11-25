<?php
/*
 * function turntable_client_views_default_views() { $is_master = FALSE; $view = _get_admin_view($is_master); $views[$view->name] = $view; // return views return $views; }
 */
function turntable_client_views_data() {
  $data['tt_client_node_shared']['table']['group'] = t('Shared Content');

  $data['tt_client_node_shared']['table']['base'] = array(
    'field' => 'nid', // This is the identifier field for the view.
    'title' => t('Nodes'),
    'help' => t('Nodes table.'),
    'weight' => -10
  );

  $data['tt_client_node_shared']['table']['join'] = array(
    'node' => array(
      'handler' => 'views_join',
      'left_field' => 'nid',
      'field' => 'nid',
      'type' => 'INNER'
    )
  );

  $data['tt_client_node_shared']['nid'] = array(
    'title' => t('Example content'),
    'help' => t('Some example content that references a node.'),

    // Define a relationship to the {node} table, so example_table views can
    // add a relationship to nodes. If you want to define a relationship the
    // other direction, use hook_views_data_alter(), or use the 'implicit' join
    // method described above.
    'relationship' => array(
      'base' => 'node', // The name of the table to join with.
      'base field' => 'nid', // The name of the field on the joined table.
                             // 'field' => 'nid' -- see hook_views_data_alter(); not needed here.
      'handler' => 'views_handler_relationship',
      'label' => t('Default label for the relationship'),
      'title' => t('Title shown when adding the relationship'),
      'help' => t('More information on this relationship')
    )
  );

  // Example plain text field.
  $data['tt_client_node_shared']['shared_state'] = array(
      'title' => t('Shared state'),
      'help' => t('Sharing state of the node.'),
      'field' => array(
          'handler' => 'turntable_shared_state_handler',
          'click sortable' => TRUE, // This is used by the table display plugin.
      ),
      'sort' => array(
          'handler' => 'views_handler_sort',
      ),
      'filter' => array(
          'handler' => 'views_handler_filter_string',
      ),
      'argument' => array(
          'handler' => 'views_handler_argument_string',
      ),
  );

  // Example timestamp field.
  $data['tt_client_node_shared']['last_sync'] = array(
      'title' => t('Last sync'),
      'help' => t('Date of the last synchronization.'),
      'field' => array(
          'handler' => 'turntable_shared_state_date_handler',
          'click sortable' => TRUE,
      ),
      'sort' => array(
          'handler' => 'views_handler_sort_date',
      ),
      'filter' => array(
          'handler' => 'views_handler_filter_date',
      ),
  );

  return $data;
}
