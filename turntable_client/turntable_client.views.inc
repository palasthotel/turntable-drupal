<?php

function turntable_client_views_data() {
  $table = array(
    'tt_client_node_shared' => array(
      'table' => array(
        'group' => 'tt_client_node',
        'base' => array(
          'field' => 'nid',
          'title' => 'Shared Node Info',
          'help' => 'Shared Node Info'
        ),
        'join' => array(
          'node' => array(
            'left_field' => 'nid',
            'field' => 'nid'
          )
        )
      ),
      'nid' => array(
        'title' => t('Node ID'),
        'help' => t('Node ID'),
        'relationship' => array(
          'base' => 'node',
          'base_field' => 'nid',
          'handler' => 'views_handler_relationship',
          'label' => t('Default label for the relationship'),
          'title' => t('Title shown when adding the relationship'),
          'help' => t('More information on this relationship')
        )
      ),
      'shared_state' => array(
        'title' => t('Shared status'),
        'help' => t('Shared status'),
        'field' => array(
          'handler' => 'views_handler_field',
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
      )
    )
  );
}
