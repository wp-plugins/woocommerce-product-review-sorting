<?php
class WC_Product_Review_Sorting_Settings_Gneral {
  /**
   * Holds the values to be used in the fields callbacks
   */
  private $options;
  
  private $tab;

  /**
   * Start up
   */
  public function __construct($tab) {
    $this->tab = $tab;
    $this->options = get_option( "dc_{$this->tab}_settings_name" );
    $this->settings_page_init();
  }
  
  /**
   * Register and add settings
   */
  public function settings_page_init() {
    global $WC_Product_Review_Sorting;
    
    $settings_tab_options = array("tab" => "{$this->tab}",
                                  "ref" => &$this,
                                  "sections" => array(
                                                      "default_settings_section" => array("title" =>  __('', $WC_Product_Review_Sorting->text_domain), // Section one
                                                                                         "fields" => array("is_enable" => array('title' => __('Enable Review Sorting Plugin', $WC_Product_Review_Sorting->text_domain), 'type' => 'checkbox', 'id' => 'is_enable', 'label_for' => 'is_enable', 'name' => 'is_enable', 'value' => 'Enable'), // Checkbox
                                                                                                           )
                                                                                         ), 
                                                      )
                                  );
    
    $WC_Product_Review_Sorting->admin->settings->settings_field_init(apply_filters("settings_{$this->tab}_tab_options", $settings_tab_options));
  }

  /**
   * Sanitize each setting field as needed
   *
   * @param array $input Contains all settings fields as array keys
   */
  public function dc_wc_product_review_sorting_general_settings_sanitize( $input ) {
    global $WC_Product_Review_Sorting;
    $new_input = array();
    
    if( isset( $input['is_enable'] ) )
      $new_input['is_enable'] = sanitize_text_field( $input['is_enable'] );
    
    if(!$hasError) {
      add_settings_error(
        "dc_{$this->tab}_settings_name",
        esc_attr( "dc_{$this->tab}_settings_admin_updated" ),
        __('Settings updated', $WC_Product_Review_Sorting->text_domain),
        'updated'
      );
    }

    return $new_input;
  }

  /** 
   * Print the Section text
   */
  public function default_settings_section_info() {
    global $WC_Product_Review_Sorting;
    _e('', $WC_Product_Review_Sorting->text_domain);
  }
  
}