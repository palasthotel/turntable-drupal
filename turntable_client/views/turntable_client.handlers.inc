<?php
class turntable_shared_state_handler extends views_handler_field {

  /**
   * Render callback handler.
   *
   * Return the markup that will appear in the rendered field.
   */
  public function render($values) {
    $value = $this->get_value($values);
    switch ($value) {
      case '0' :
        return t('Not shared');
      case '1' :
        return t('Copy');
      case '2' :
        return t('Reference');
      case '3' :
        return t('Original node');
      default :
        return '';
    }
  }
}

//
class turntable_date_handler extends views_handler_field {

  /**
   * Render callback handler.
   *
   * Return the markup that will appear in the rendered field.
   */
  public function render($values) {
    $value = $this->get_value($values);
    return $value;
  }
}

//
class turntable_origin_handler extends views_handler_field {

  public function render($values) {
    $value = $this->get_value($values);

    if ($value === NULL) {
      return t('This site');
    }

    return $value;
  }
}

//
class turntable_edit_tt_settings_handler extends views_handler_field_node_link {

  /**
   * Renders the link.
   */
  function render_link($node, $values) {
    // Ensure user has access to edit this node.
    if (!node_access('update', $node)) {
      return;
    }

    $this->options['alter']['make_link'] = TRUE;
    $this->options['alter']['path'] = "node/$node->nid/turntable";
    $this->options['alter']['query'] = drupal_get_destination();

    $text = !empty($this->options['text']) ? $this->options['text'] : t(
        'edit settings');
    return $text;
  }
}
