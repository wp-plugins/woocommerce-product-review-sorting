<?php
/**
 * Dualcube Wp Fields Lib
 *
 * All Standard HTML fields can be include in your frontend or backend pages 
 *
 * @author 		Dualcube
 * @category 	Library
 * @package 	lib/php
 * @version     1.0.2
 */
class DC_WP_Fields {
  
  /**
   * Start up
   */
  public function __construct() {
    
  }
  
  /**
   * Output a checkbox.
   *
   * @access public
   * @param array $field
   * @return void
   */
  public function checkbox_input($field) {
    $field['class'] 		= isset( $field['class'] ) ? $field['class'] : 'checkbox';
    $field['value'] 		= isset( $field['value'] ) ? $field['value'] : '';
    $field['name'] 			= isset( $field['name'] ) ? $field['name'] : $field['id'];
    $field['dfvalue'] 		= isset( $field['dfvalue'] ) ? $field['dfvalue'] : '';
    
    // Custom attribute handling
    $custom_attributes = array();
    
    if ( ! empty( $field['custom_attributes'] ) && is_array( $field['custom_attributes'] ) )
      foreach ( $field['custom_attributes'] as $attribute => $value )
        $custom_attributes[] = 'data-' . esc_attr( $attribute ) . '="' . esc_attr( $value ) . '"';
    
    $field = $this->field_wrapper_start($field);
    
    printf(
        '<input type="checkbox" id="%s" name="%s" class="%s" value="%s" %s %s />',
        esc_attr($field['id']),
        esc_attr($field['name']),
        esc_attr($field['class']),
        esc_attr($field['value']),
        checked( $field['value'], $field['dfvalue'], false ),
        implode( ' ', $custom_attributes )
    );
    
    $this->field_wrapper_end($field);
  }
  
  /**************************************** Help Functions ************************************************/
  
  public function field_wrapper_start($field) {
    $field['wrapper_class'] = isset( $field['wrapper_class'] ) ? ($field['wrapper_class'] . ' ' . $field['id'] . '_wrapper') : ($field['id'] . '_wrapper');
    $field['label_holder_class'] = isset( $field['label_holder_class'] ) ? ($field['label_holder_class']. ' ' . $field['id'] . '_label_holder') : ($field['id'] . '_label_holder');
    $field['label_for'] = isset( $field['label_for'] ) ? ($field['label_for']. ' ' . $field['id']) : $field['id'];
    $field['label_class'] = isset( $field['label_class'] ) ? ($field['label_for']. ' ' . $field['label_class']) : $field['label_for'];
    
    do_action('before_field_wrapper');
    do_action('before_field_wrapper_' . $field['id']);
    
    if(isset($field['in_table'])) {
      printf(
        '<tr class="%s">',
        $field['wrapper_class']
      );
    }
    
    do_action('field_wrapper_start');
    do_action('field_wrapper_start_' . $field['id']);
      
    if(isset($field['label'])) {
      do_action('before_field_label');
      do_action('before_field_label_' . $field['id']);
      
      if(isset($field['in_table'])) {
        printf(
          '<th class="%s">',
          $field['label_holder_class']
        );
      }
      do_action('field_label_start');
      do_action('field_label_start_' . $field['id']);
      printf(
        '<p class="%s"><strong>%s</strong>',
        $field['label_class'],
        $field['label']
      );
      if(isset($field['hints'])) {
        printf(
          '<span class="img_tip" data-desc="%s"></span>',
          wp_kses_post ( $field['hints'] )
        );
      }
      printf(
        '</p><label class="screen-reader-text" for="%s">%s</label>',
        $field['label_for'],
        $field['label']
      );
      do_action('field_label_end_' . $field['id']);
      do_action('field_label_end');
      if(isset($field['in_table'])) printf('</th>');
      
      do_action('after_field_label_' . $field['id']);
      do_action('after_field_label');
    }
    
    do_action('before_field');
    do_action('before_field_' . $field['id']);
    
    if(isset($field['in_table']) && isset($field['label'])) printf('<td>');
    else if(isset($field['in_table']) && !isset($field['label'])) printf('<td colspan="2">');
    
    do_action('field_start');
    do_action('field_start_' . $field['id']);
    
    if(!isset($field['custom_attributes'])) $field['custom_attributes'] = array();
    $field['custom_attributes'] = apply_filters('manupulate_custom_attributes', $field['custom_attributes']);
    $field['custom_attributes'] = apply_filters('manupulate_custom_attributes_' . $field['id'], $field['custom_attributes']);
    
    return $field;
  }
  
  public function field_wrapper_end($field) {
    
    // Help message
    if(!isset($field['label']) && isset($field['hints'])) {
      do_action('before_hints');
      do_action('before_hints_' . $field['id']);
      
      printf(
        '<span class="img_tip" data-desc="%s"></span>',
        wp_kses_post ( $field['hints'] )
      );
      
      do_action('after_hints_' . $field['id']);
      do_action('after_hints');
    }
    
    // Description
    if(isset($field['desc'])) {
      do_action('before_desc');
      do_action('before_desc_' . $field['id']);
      
      printf(
        '<p class="description">%s</p>',
        wp_kses_post ( $field['desc'] )
      );
      
      do_action('after_desc_' . $field['id']);
      do_action('after_desc');
    }
    
    do_action('field_end_' . $field['id']);
    do_action('field_end');
    
    if(isset($field['in_table'])) printf('</td>');
    
    do_action('after_field_' . $field['id']);
    do_action('after_field');
    
    do_action('field_wrapper_end_' . $field['id']);
    do_action('field_wrapper_end');
    
    if(isset($field['in_table'])) printf('</tr>');
    
    do_action('afet_field_wrapper_' . $field['id']);
    do_action('after_field_wrapper');
  }
  
  public function check_field_id_name($fieldID, $field) {
    if(empty($fieldID)) return $field;
    
    if(!isset($field['id']) || empty($field['id'])) $field['id'] = $fieldID;
    if(!isset($field['name']) || empty($field['name'])) $field['name'] = $fieldID;
    
    return $field;
  }
}