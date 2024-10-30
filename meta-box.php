<?php

/**
 * Adds a box to the main column on the Post and Page edit screens.
 */
function icompare_add_meta_box() {

	$screens = array( 'post' );

	foreach ( $screens as $screen ) {

		add_meta_box(
			'icompare_metabox',
			__( 'Select Products To Display', 'product_comparer_textdomain' ),
			'icompare_meta_box_callback',
			$screen,
			'normal',
			'high'
		);
	}
}
add_action( 'add_meta_boxes', 'icompare_add_meta_box' );

/**
 * Prints the box content.
 * 
 * @param WP_Post $post The object for the current post/page.
 */
function icompare_meta_box_callback( $post ) {

	// Add a nonce field so we can check for it later.
	wp_nonce_field( 'icompare_save_meta_box_data', 'icompare_meta_box_nonce' );

	/*
	 * Use get_post_meta() to retrieve an existing value
	 * from the database and use the value for the form.
	 */
	$product_searched_name = get_post_meta( $post->ID, '_icompare_product_searched_name', true );
	$product_id_ebay = get_post_meta( $post->ID, '_icompare_product_id_ebay', true );
	$affiliate_url_ebay = get_post_meta( $post->ID, '_icompare_affiliate_url_ebay', true );
	$product_totalPrice_ebay = get_post_meta( $post->ID, '_icompare_product_totalPrice_ebay', true );
	$product_id_amazon = get_post_meta( $post->ID, '_icompare_product_id_amazon', true );
	$selected_category_name = get_post_meta( $post->ID, '_icompare_selected_category_name', true );

	$options = get_option('icompare_plugin_options');

	$app_id = $options['app_id'];
	$camp_id = $options['camp_id'];
	$access_key = $options['access_key'];
	$secrect_access_key = $options['secrect_access_key'];
	$affiliate_id = $options['affiliate_id'];

	if($app_id == NULL || $camp_id == NULL || $access_key == NULL || $secrect_access_key == NULL || $affiliate_id == NULL){
		?>
		<form id="product-comparer-admin-search">
			<div class="product-comprer-container">
				<p style="font-weight:bold;color:#A00;">Please fill-up the <a href="admin.php?page=icompare_config">iCompare</a> config settings with the necessary datas for plugin to work properly.</p>
			</div>
		</form>
		<?php
	}else{
		?>
		<form id="product-comparer-admin-search">
			<div class="product-comprer-container">
				<div id="search-inputs" <?php echo ($product_id_ebay != NULL || $product_id_amazon != NULL ? 'style="display:none;"' : ""); ?>>
					<div class="input-block">
						<label for="item-name-search">
							Product Name
						</label>
						<input type="text" id="item-name-search" name="item-name-search" placeholder="Enter Product Name to Search" value="<?php echo esc_attr($product_searched_name); ?>" size="25" />
						<input type="hidden" id="product-id-ebay" name="product-id-ebay" value="<?php echo esc_attr($product_id_ebay); ?>" />
						<input type="hidden" id="product-totalPrice-ebay" name="product-totalPrice-ebay" value="<?php echo esc_attr($product_totalPrice_ebay); ?>" />
						<input type="hidden" id="product-affiliateURL-ebay" name="product-affiliateURL-ebay" value="<?php echo esc_attr($affiliate_url_ebay); ?>" />
						<input type="hidden" id="product-id-amazon" name="product-id-amazon" value="<?php echo esc_attr($product_id_amazon); ?>" />
					</div>
					<div class="input-block">
						<label for="category-name">
							Amazon Category Name
						</label>
						<select id="category-name" name="category-name">
							<option value="<?php echo $selectValue=''; ?>" <?php echo ($selectValue == $selected_category_name ? "selected" : ""); ?>>Select Category</option>
							<option value="<?php echo $selectValue='ArtsAndCrafts'; ?>" <?php echo ($selectValue == $selected_category_name ? "selected" : ""); ?>>ArtsAndCrafts</option>
							<option value="<?php echo $selectValue='Appliances'; ?>" <?php echo ($selectValue == $selected_category_name ? "selected" : ""); ?>>Appliances</option>
							<option value="<?php echo $selectValue='Automotive'; ?>" <?php echo ($selectValue == $selected_category_name ? "selected" : ""); ?>>Automotive</option>
							<option value="<?php echo $selectValue='Baby'; ?>" <?php echo ($selectValue == $selected_category_name ? "selected" : ""); ?>>Baby</option>
							<option value="<?php echo $selectValue='Beauty'; ?>" <?php echo ($selectValue == $selected_category_name ? "selected" : ""); ?>>Beauty</option>
							<option value="<?php echo $selectValue='Books'; ?>" <?php echo ($selectValue == $selected_category_name ? "selected" : ""); ?>>Books</option>
							<option value="<?php echo $selectValue='Collectibles'; ?>" <?php echo ($selectValue == $selected_category_name ? "selected" : ""); ?>>Collectibles</option>
							<option value="<?php echo $selectValue='Electronics'; ?>" <?php echo ($selectValue == $selected_category_name ? "selected" : ""); ?>>Electronics</option>
							<option value="<?php echo $selectValue='Fashion'; ?>" <?php echo ($selectValue == $selected_category_name ? "selected" : ""); ?>>Fashion</option>
							<option value="<?php echo $selectValue='FashionBaby'; ?>" <?php echo ($selectValue == $selected_category_name ? "selected" : ""); ?>>FashionBaby</option>
							<option value="<?php echo $selectValue='FashionBoys'; ?>" <?php echo ($selectValue == $selected_category_name ? "selected" : ""); ?>>FashionBoys</option>
							<option value="<?php echo $selectValue='FashionGirls'; ?>" <?php echo ($selectValue == $selected_category_name ? "selected" : ""); ?>>FashionGirls</option>
							<option value="<?php echo $selectValue='FashionMen'; ?>" <?php echo ($selectValue == $selected_category_name ? "selected" : ""); ?>>FashionMen</option>
							<option value="<?php echo $selectValue='FashionWomen'; ?>" <?php echo ($selectValue == $selected_category_name ? "selected" : ""); ?>>FashionWomen</option>
							<option value="<?php echo $selectValue='GiftCards'; ?>" <?php echo ($selectValue == $selected_category_name ? "selected" : ""); ?>>GiftCards</option>
							<option value="<?php echo $selectValue='Grocery'; ?>" <?php echo ($selectValue == $selected_category_name ? "selected" : ""); ?>>Grocery</option>
							<option value="<?php echo $selectValue='HealthPersonalCare'; ?>" <?php echo ($selectValue == $selected_category_name ? "selected" : ""); ?>>HealthPersonalCare</option>
							<option value="<?php echo $selectValue='HomeGarden'; ?>" <?php echo ($selectValue == $selected_category_name ? "selected" : ""); ?>>HomeGarden</option>
							<option value="<?php echo $selectValue='Industrial'; ?>" <?php echo ($selectValue == $selected_category_name ? "selected" : ""); ?>>Industrial</option>
							<option value="<?php echo $selectValue='KindleStore'; ?>" <?php echo ($selectValue == $selected_category_name ? "selected" : ""); ?>>KindleStore</option>
							<option value="<?php echo $selectValue='LawnAndGarden'; ?>" <?php echo ($selectValue == $selected_category_name ? "selected" : ""); ?>>LawnAndGarden</option>
							<option value="<?php echo $selectValue='Luggage'; ?>" <?php echo ($selectValue == $selected_category_name ? "selected" : ""); ?>>Luggage</option>
							<option value="<?php echo $selectValue='Magazines'; ?>" <?php echo ($selectValue == $selected_category_name ? "selected" : ""); ?>>Magazines</option>
							<option value="<?php echo $selectValue='MobileApps'; ?>" <?php echo ($selectValue == $selected_category_name ? "selected" : ""); ?>>MobileApps</option>
							<option value="<?php echo $selectValue='MP3Downloads'; ?>" <?php echo ($selectValue == $selected_category_name ? "selected" : ""); ?>>MP3Downloads</option>
							<option value="<?php echo $selectValue='Music'; ?>" <?php echo ($selectValue == $selected_category_name ? "selected" : ""); ?>>Music</option>
							<option value="<?php echo $selectValue='MusicalInstruments'; ?>" <?php echo ($selectValue == $selected_category_name ? "selected" : ""); ?>>MusicalInstruments</option>
							<option value="<?php echo $selectValue='OfficeProducts'; ?>" <?php echo ($selectValue == $selected_category_name ? "selected" : ""); ?>>OfficeProducts</option>
							<option value="<?php echo $selectValue='PCHardware'; ?>" <?php echo ($selectValue == $selected_category_name ? "selected" : ""); ?>>PCHardware</option>
							<option value="<?php echo $selectValue='PetSupplies'; ?>" <?php echo ($selectValue == $selected_category_name ? "selected" : ""); ?>>PetSupplies</option>
							<option value="<?php echo $selectValue='Software'; ?>" <?php echo ($selectValue == $selected_category_name ? "selected" : ""); ?>>Software</option>
							<option value="<?php echo $selectValue='SportingGoods'; ?>" <?php echo ($selectValue == $selected_category_name ? "selected" : ""); ?>>SportingGoods</option>
							<option value="<?php echo $selectValue='Tools'; ?>" <?php echo ($selectValue == $selected_category_name ? "selected" : ""); ?>>Tools</option>
							<option value="<?php echo $selectValue='Toys'; ?>" <?php echo ($selectValue == $selected_category_name ? "selected" : ""); ?>>Toys</option>
							<option value="<?php echo $selectValue='UnboxVideo'; ?>" <?php echo ($selectValue == $selected_category_name ? "selected" : ""); ?>>UnboxVideo</option>
							<option value="<?php echo $selectValue='VideoGames'; ?>" <?php echo ($selectValue == $selected_category_name ? "selected" : ""); ?>>VideoGames</option>
							<option value="<?php echo $selectValue='Wine'; ?>" <?php echo ($selectValue == $selected_category_name ? "selected" : ""); ?>>Wine</option>
							<option value="<?php echo $selectValue='Wireless'; ?>" <?php echo ($selectValue == $selected_category_name ? "selected" : ""); ?>>Wireless</option>
						</select>
					</div>
					<hr/>
				</div>
				<div class="product-details" id="saved-products">
					<?php
						if($product_id_ebay != NULL){
							$ebay_results = icompare_pull_single_product_ebay($product_id_ebay,$product_totalPrice_ebay);
							echo '<div class="saved-products ebay">';
							echo "<h3>Saved Product from Ebay</h3>";
							echo $ebay_results;
							echo '</div>';
						}
						if($product_id_amazon != NULL){
							$amazon_results = icompare_pull_single_product_amazon($product_id_amazon);
							echo '<div class="saved-products amazon">';
							echo "<h3>Saved Product from Amazon</h3>";
							echo $amazon_results;
							echo '</div>';
						}
					?>
				</div>
				<div class="product-details" id="search-results">
				</div>
				<hr/>
				<?php
					if($product_id_ebay != NULL || $product_id_amazon != NULL ){
						?>
						<button class="button button-primary button-large" id="reselect-products">Reselect products?</button>
						<button class="button button-primary button-large" id="search-product" style="display:none;">Search</button>
						<button class="button button-primary button-large" id="update-post" style="display:none;">Update</button>

						<?php

					}else{
						?>
						<button class="button button-primary button-large" id="search-product">Search</button>
						<button class="button button-primary button-large" id="update-post">Update</button>
						<button class="button button-primary button-large" id="reselect-products" style="display:none;">Reselect products?</button>
						<?php
					}
				?>
				<img src="<?php echo plugins_url('icompare/img/ajax-loader.gif'); ?>" id="zsSetOptionLoader" style="display: none;" />
			</div>
		</form>
		<?php
	}
}

/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved.
 */
function icompare_save_meta_box_data( $post_id ) {

	/*
	 * We need to verify this came from our screen and with proper authorization,
	 * because the save_post action can be triggered at other times.
	 */

	// Check if our nonce is set.
	if ( ! isset( $_POST['icompare_meta_box_nonce'] ) ) {
		return;
	}

	// Verify that the nonce is valid.
	if ( ! wp_verify_nonce( $_POST['icompare_meta_box_nonce'], 'icompare_save_meta_box_data' ) ) {
		return;
	}

	// If this is an autosave, our form has not been submitted, so we don't want to do anything.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Check the user's permissions.
	if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}

	} else {

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	}

	/* OK, it's safe for us to save the data now. */
	
	// Make sure that it is set.
	if ( ! isset( $_POST['product-id-ebay'] ) ) {
		return;
	}
	if ( ! isset( $_POST['product-totalPrice-ebay'] ) ) {
		return;
	}
	if ( ! isset( $_POST['product-id-amazon'] ) ) {
		return;
	}
	if ( ! isset( $_POST['product-affiliateURL-ebay'] ) ) {
		return;
	}
	if ( ! isset( $_POST['item-name-search'] ) ) {
		return;
	}
	if ( ! isset( $_POST['category-name'] ) ) {
		return;
	}

	// Sanitize user input.
	$icompare_product_id_ebay = sanitize_text_field( $_POST['product-id-ebay'] );
	$icompare_product_totalPrice_ebay = sanitize_text_field( $_POST['product-totalPrice-ebay'] );
	$icompare_product_id_amazon = sanitize_text_field( $_POST['product-id-amazon'] );
	$icompare_affiliate_url_ebay = sanitize_text_field( $_POST['product-affiliateURL-ebay'] );
	$icompare_searched_product_name = sanitize_text_field( $_POST['item-name-search'] );
	$icompare_selected_category_name = sanitize_text_field( $_POST['category-name'] );

	// Update the meta field in the database.
	update_post_meta( $post_id, '_icompare_product_id_ebay', $icompare_product_id_ebay );
	update_post_meta( $post_id, '_icompare_product_totalPrice_ebay', $icompare_product_totalPrice_ebay );
	update_post_meta( $post_id, '_icompare_product_id_amazon', $icompare_product_id_amazon );
	update_post_meta( $post_id, '_icompare_affiliate_url_ebay', $icompare_affiliate_url_ebay );
	update_post_meta( $post_id, '_icompare_product_searched_name', $icompare_searched_product_name );
	update_post_meta( $post_id, '_icompare_selected_category_name', $icompare_selected_category_name );
}
add_action( 'save_post', 'icompare_save_meta_box_data' );