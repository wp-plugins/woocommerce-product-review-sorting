<?php
class WC_Product_Review_Sorting_Settings {
  
  private $tabs = array();
  
  private $options;
  
  /**
   * Start up
   */
  public function __construct() {
    // Admin menu
    add_action( 'admin_menu', array( $this, 'add_settings_page' ), 100 );
    add_action( 'admin_init', array( $this, 'settings_page_init' ) );
    
    // Settings tabs
    add_action('settings_page_wc_product_review_sorting_general_tab_init', array(&$this, 'general_tab_init'), 10, 1);
  }
  
  /**
   * Add options page
   */
  public function add_settings_page() {
    global $WC_Product_Review_Sorting;
    
    add_menu_page(
        __('Product Review Sorting Settings', $WC_Product_Review_Sorting->text_domain), 
        __('Product Review Sorting Settings', $WC_Product_Review_Sorting->text_domain), 
        'manage_options', 
        'wc-product-review-sorting-setting-admin', 
        array( $this, 'create_wc_product_review_sorting_settings' ),
        $WC_Product_Review_Sorting->plugin_url . 'assets/images/dualcube.png'
    );
    
    $this->tabs = $this->get_dc_settings_tabs();
  }
  
  function get_dc_settings_tabs() {
    global $WC_Product_Review_Sorting;
    $tabs = apply_filters('wc_product_review_sorting_tabs', array(
      'wc_product_review_sorting_general' => __('Product Review Sorting', $WC_Product_Review_Sorting->text_domain)
    ));
    return $tabs;
  }
  
  function dc_settings_tabs( $current = 'wc_product_review_sorting_general' ) {
    if ( isset ( $_GET['tab'] ) ) :
      $current = $_GET['tab'];
    else:
      $current = 'wc_product_review_sorting_general';
    endif;
    
    $links = array();
    foreach( $this->tabs as $tab => $name ) :
      if ( $tab == $current ) :
        $links[] = "<a class='nav-tab nav-tab-active' href='?page=wc-product-review-sorting-setting-admin&tab=$tab'>$name</a>";
      else :
        $links[] = "<a class='nav-tab' href='?page=wc-product-review-sorting-setting-admin&tab=$tab'>$name</a>";
      endif;
    endforeach;
    echo '<div class="icon32" id="dualcube_menu_ico"><br></div>';
    echo '<h2 class="nav-tab-wrapper">';
    foreach ( $links as $link )
      echo $link;
    echo '</h2>';
    
    foreach( $this->tabs as $tab => $name ) :
      if ( $tab == $current ) :
        echo "<h2>$name Settings</h2>";
      endif;
    endforeach;
  }

  /**
   * Options page callback
   */
  public function create_wc_product_review_sorting_settings() {
    global $WC_Product_Review_Sorting;
    ?>
    <div class="wrap">
      <?php $this->dc_settings_tabs(); ?>
      <?php
      $tab = ( isset( $_GET['tab'] ) ? $_GET['tab'] : 'wc_product_review_sorting_general' );
      $this->options = get_option( "dc_{$tab}_settings_name" );
      
      // This prints out all hidden setting errors
      settings_errors("dc_{$tab}_settings_name");
      ?>
      <form method="post" action="options.php">
      <?php
        // This prints out all hidden setting fields
        settings_fields( "dc_{$tab}_settings_group" );   
        do_settings_sections( "dc-{$tab}-settings-admin" );
        submit_button(); 
      ?>
      </form>
    </div>
    <?php
    do_action('wc_product_review_sorting_dualcube_admin_footer');
  }

  /**
   * Register and add settings
   */
  public function settings_page_init() {
    do_action('befor_settings_page_init');
    
    // Register each tab settings
    foreach( $this->tabs as $tab => $name ) :
      do_action("settings_page_{$tab}_tab_init", $tab);
    endforeach;
    
    do_action('after_settings_page_init');
  }
  
  /**
   * Register and add settings fields
   */
  public function settings_field_init($tab_options) {
    global $WC_Product_Review_Sorting;
    
    if(!empty($tab_options) && isset($tab_options['tab']) && isset($tab_options['ref']) && isset($tab_options['sections'])) {
      // Register tab options
      register_setting(
        "dc_{$tab_options['tab']}_settings_group", // Option group
        "dc_{$tab_options['tab']}_settings_name", // Option name
        array( $tab_options['ref'], "dc_{$tab_options['tab']}_settings_sanitize" ) // Sanitize
      );
      
      foreach($tab_options['sections'] as $sectionID => $section) {
        // Register section
        add_settings_section(
          $sectionID, // ID
          $section['title'], // Title
          array( $tab_options['ref'], "{$sectionID}_info" ), // Callback
          "dc-{$tab_options['tab']}-settings-admin" // Page
        );
        
        // Register fields
        if(isset($section['fields'])) {
          foreach($section['fields'] as $fieldID => $field) {
            if(isset($field['type'])) {
              $field = $WC_Product_Review_Sorting->dc_wp_fields->check_field_id_name($fieldID, $field);
              $field['tab'] = $tab_options['tab'];
              $callbak = $this->get_field_callback_type($field['type']);
              if(!empty($callbak)) {
                add_settings_field(
                  $fieldID,
                  $field['title'],
                  array( $this, $callbak ),
                  "dc-{$tab_options['tab']}-settings-admin",
                  $sectionID,
                  $field
                );
              }
            }
          }
        }
      }
    }
  }
  
  function general_tab_init($tab) {
    global $WC_Product_Review_Sorting;
    $WC_Product_Review_Sorting->admin->load_class("settings-{$tab}", $WC_Product_Review_Sorting->plugin_path, $WC_Product_Review_Sorting->token);
    new WC_Product_Review_Sorting_Settings_Gneral($tab);
  }
  
  function get_field_callback_type($fieldType) {
    $callBack = '';
    switch($fieldType) {
      case 'checkbox':
        $callBack = 'checkbox_field_callback';
        break;
        
      default:
        $callBack = '';
        break;
    }
    
    return $callBack;
  }
  
  /** 
   * Get the checkbox field display
   */
  public function checkbox_field_callback($field) {
    global $WC_Product_Review_Sorting;
    $field['value'] = isset( $field['value'] ) ? esc_attr( $field['value'] ) : '';
    $field['value'] = isset( $this->options[$field['name']] ) ? esc_attr( $this->options[$field['name']] ) : $field['value'];
    $field['dfvalue'] = isset( $this->options[$field['name']] ) ? esc_attr( $this->options[$field['name']] ) : '';
    $field['name'] = "dc_{$field['tab']}_settings_name[{$field['name']}]";
    $WC_Product_Review_Sorting->dc_wp_fields->checkbox_input($field);
  }
  
}