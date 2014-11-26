<?php
function turntable_client_views_default_views() {
  $is_master = FALSE;
  $view = _get_admin_view($is_master);
  $views[$view->name] = $view;
  // return views
  return $views;
}
