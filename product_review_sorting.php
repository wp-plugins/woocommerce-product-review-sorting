<?php
/*
Plugin Name: Woocommerce Product Review Sorting
Plugin URI: http://dualcube.com
Description: Woocommerce plugin to sort product reviews by their rating
Author: Aveek Kr Saha, Arim Ghosh, Dualcube
Version: 1.0.3
Author URI: http://dualcube.com
*/

if ( ! class_exists( 'WC_Dependencies_Product_Review_Sorting' ) )
	require_once 'includes/class-dc-dependencies.php';
require_once 'includes/wc-product-review-sorting-core-functions.php';
require_once 'config.php';
if(!defined('ABSPATH')) exit; // Exit if accessed directly
if(!defined('WC_PRODUCT_REVIEW_SORTING_PLUGIN_TOKEN')) exit;
if(!defined('WC_PRODUCT_REVIEW_SORTING_TEXT_DOMAIN')) exit;

if(!WC_Dependencies_Product_Review_Sorting::woocommerce_plugin_active_check()) {
  add_action( 'admin_notices', 'woocommerce_inactive_notice' );
}

if ( version_compare( get_option( 'woocommerce_db_version' ), '2.1', '<' ) ) {
	add_action( 'admin_notices', 'woocommerce_version_compatibility' );
}

if(!class_exists('WC_Product_Review_Sorting')) {
	require_once( 'classes/class-wc-product-review-sorting.php' );
	global $WC_Product_Review_Sorting;
	$WC_Product_Review_Sorting = new WC_Product_Review_Sorting( __FILE__ );
	$GLOBALS['WC_Product_Review_Sorting'] = $WC_Product_Review_Sorting;
}
?>
