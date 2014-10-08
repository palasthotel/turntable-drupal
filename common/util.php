<?php
/**
 * Returns an array of all available node content types.
 *
 * @return array
 */
function get_available_node_content_types() {
  $cts = db_select('node_type', 'nt')->fields('nt', array(
    'type'
  ))->execute();

  $available_content_types = array();
  while ($ct = $cts->fetchAssoc()) {
    $available_content_types[] = $ct['type'];
  }
  return $available_content_types;
}
