<?php
function turntable_master_views_default_views() {
  $is_master = TRUE;
  $view = _get_admin_view($is_master);
  $views[$view->name] = $view;
  return $views;
}
