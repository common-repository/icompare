<?php
/*
* Plugin Name: iCompare
* Plugin URI: http://icompareplugin.com/
* Description: This plugin allow affiliate marketers to easily add products from eBay and Amazon to their post. The products will be displayed into a table comparing the two prices between eBay and Amazon.
* Version: 1.4
* Author: Social Lead
* Author URI: http://icompareplugin.com/
* 
*/

require_once( plugin_dir_path( __FILE__ ) .'aws_lib/amazon_api_class.php' );
require_once( plugin_dir_path( __FILE__ ) .'options-page.php' );
require_once( plugin_dir_path( __FILE__ ) .'meta-box.php' );
require_once( plugin_dir_path( __FILE__ ) .'ebay_products.php' );
require_once( plugin_dir_path( __FILE__ ) .'amazon_products.php' );


// Register style sheet.
add_action( 'wp_enqueue_scripts', 'icompare_register_plugin_styles' );

/**
 * Register style sheet.
 */
function icompare_register_plugin_styles() {
	wp_register_style( 'icompare-css', plugins_url( 'icompare/css/plugin.css' ) );
	wp_enqueue_style( 'icompare-css' );
	wp_enqueue_script( 'icompare-js', plugins_url( 'icompare/js/plugin.js' ), array(), '1.0.0', true );
}

function icompare_load_custom_product_comparer_wp_admin_style() {
        wp_register_style( 'icompare_admin_css', plugins_url( 'icompare/css/admin_plugin.css' ), false, '1.0.0' );
        wp_enqueue_style( 'icompare_admin_css' );
}
add_action( 'admin_enqueue_scripts', 'icompare_load_custom_product_comparer_wp_admin_style' );


function icompare_search_products(){
	$product_name = sanitize_text_field($_POST['product_name']);
	$category_name = sanitize_text_field($_POST['category_name']);

	$ebay_results = icompare_search_product_from_ebay($product_name);
	$amazon_results = icompare_search_product_from_amazon($product_name, $category_name);

	echo '<div id="ebay-products">
			<h3>Ebay Search Results</h3>
			<hr/>
			'.$ebay_results.'
		  </div>';
	echo '<div id="amazon-products">
			<h3>Amazon Search Results</h3>
			<hr/>
			'.$amazon_results.'
		  </div>';

	wp_die();
}

add_action('wp_ajax_ebay_product', 'icompare_search_products');


add_filter ('the_content', 'icompare_display_selected_products_front');
function icompare_display_selected_products_front($content) {
   if(is_single()) {
   	global $post;
   	$id = $post->ID;
   	$post_title = $post->post_title;
    $new_post_title = str_ireplace("Review", "", $post_title);
    $ebay_good = FALSE;
    $amazon_good = FALSE;

   	$product_id_ebay = get_post_meta( $id, '_icompare_product_id_ebay', true );
	$product_totalPrice_ebay = get_post_meta( $id, '_icompare_product_totalPrice_ebay', true );
	$product_id_amazon = get_post_meta( $id, '_icompare_product_id_amazon', true );
	$affiliate_url_ebay = get_post_meta( $post->ID, '_icompare_affiliate_url_ebay', true );

	$options = get_option('icompare_plugin_options');
	// $selected_include_option = $options['include_powered'];

	// if($selected_include_option == 1){
	// 	$powered_by = '<div id="powered-by"><span><strong><a href="http://icompareplugin.com">Powered by iCompare Plugin</a></strong></span></div>';
	// }else{
	// 	$powered_by = "";
	// }


		if($product_id_ebay != NULL){
			$ebay_selected = icompare_display_selected_ebay_product($product_id_ebay, $product_totalPrice_ebay, $affiliate_url_ebay);
			$ebay_selected_banner = icompare_display_selected_ebay_product_banner($product_id_ebay, $product_totalPrice_ebay, $affiliate_url_ebay);
			$ebay_good = TRUE;
		}
		if($product_id_amazon != NULL){
			$amazon_selected = icompare_display_selected_amazon_product($product_id_amazon);
			$amazon_selected_banner = icompare_display_selected_amazon_product_banner($product_id_amazon);
			$amazon_good = TRUE;
		}
		if($product_id_ebay != NULL || $product_id_amazon != NULL){
			$content .= '<div class="sl-product-row">
						  	<h3>Best prices for '.esc_html($new_post_title).' online</h3>
						  	<hr/>
						  	<div class="icompare-container">
								<div class="product-table-column">'.($ebay_good == TRUE ? $ebay_selected : "").'</div>
								<div class="product-table-column">'.($amazon_good == TRUE ? $amazon_selected : "").'</div>
							</div>
						</div>';
		}
	}
   	return $content;
}

?>