<?php

function turntable_master_create_shared_node($data) {
  print_r($data);
}

function turntable_master_update_shared_node($nid, $data) {
  print_r($nid);

  print_r($data);
}

function turntable_master_index_shared_nodes() {
  return array();
}
