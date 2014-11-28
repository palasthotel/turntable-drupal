<?php
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
