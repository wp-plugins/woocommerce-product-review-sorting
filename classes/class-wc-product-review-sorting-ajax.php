<?php
class WC_Product_Review_Sorting_Ajax {

	public function __construct() {
		
		//Sort product rating using Ajax
		add_action( 'wp_ajax_sort_product_rating_ajax', array(&$this, 'sort_product_rating') );
		add_action( 'wp_ajax_nopriv_sort_product_rating_ajax', array(&$this, 'sort_product_rating') );
	}

	function sort_product_rating() {
		$customer_review = $ratings = array();
		$product_id = $selected_option = '';
		$product_id = $_POST['product_id'];
		$selected_option = $_POST['selected_option'];
		$no_of_comments = 0;
		
		$customer_review = get_comments( array( 'post_id' => $product_id ) );
		
		foreach( $customer_review as $key => $each_review ) {
			$ratings = intval( get_comment_meta( $each_review->comment_ID, 'rating', true ) );
			if( !empty($ratings) && $selected_option == $ratings ) {
				$GLOBALS['comment'] = $each_review;
				wc_get_template( 'single-product/review.php', array( 'comment' => $each_review ) );
				$no_of_comments++;
			}
		}
		
		echo '[${#(%18_concatenate-string%18)#}$]'.$no_of_comments;
		
		die();
	}

}
