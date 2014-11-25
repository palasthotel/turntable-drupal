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
        break;
      case '1' :
        return t('Copy');
        break;
      case '2' :
        return t('Reference');
        break;
      case '3' :
        return t('Original node');
      default :
        return '';
    }
  }
}

class turntable_shared_state_date_handler extends views_handler_field_date {

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
