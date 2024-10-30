<?php
add_action('admin_menu', 'icompare_options');
function icompare_options() {
	add_menu_page('iComparer Configuration', 'iCompare', 'manage_options', 'icompare_config', 'icompare_plugin_options_page' , 'dashicons-format-gallery', 3);
	// add_options_page('iComparer Configuration', 'iComparer', 'manage_options', 'product_comparer_config', 'icompare_plugin_options_page');
}

function icompare_action_javascript() {
	wp_enqueue_script('icompare-action-javascript', plugins_url('icompare/js/product-comparer-admin.js') );
}
add_action('admin_footer', 'icompare_action_javascript');

// display the admin options page
function icompare_plugin_options_page() {
?>
	<div class="icompare-pro-form">
		<h2>iCompare Configs</h2>
		<form action="options.php" method="post">
			<?php settings_fields('icompare_plugin_options'); ?>
			<?php do_settings_sections('icompare_plugin'); ?>
			<?php
			echo '<div id="icompare-pro-message" class="notice notice-warning">';
			echo '<p>To Enable <strong>other features of the plugin</strong>, please purchase the <a href="http://icompareplugin.com" target="_blank">premium version</a> of the plugin.</p>';
			echo '</div>';
			?>
			<input name="Submit" type="submit" class="button button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
		</form>
	</div>
<?php
}

add_action('admin_init', 'icompare_plugin_admin_init');
function icompare_plugin_admin_init(){
	register_setting( 'icompare_plugin_options', 'icompare_plugin_options', 'icompare_plugin_options_validate' );
	add_settings_section('icompare_plugin_main', 'Main Settings', 'icompare_plugin_header_text', 'icompare_plugin');
	// add_settings_field('plugin_license_key', 'License Key', 'plugin_setting_license_key', 'plugin', 'plugin_main');
	// add_settings_field('plugin_app_id', 'eBay App ID', 'icompare_plugin_setting_app_id', 'icompare_plugin', 'icompare_plugin_main');
	add_settings_field('plugin_camp_id', 'eBay Campaign ID', 'icompare_plugin_setting_camp_id', 'icompare_plugin', 'icompare_plugin_main');
	add_settings_field('plugin_tracking_id', 'eBay Tracking ID<br/><span id="icomparer-optional">(Optional: An alphanumeric identifier of up to 256 characters)</span>', 'icompare_plugin_setting_tracking_id', 'icompare_plugin', 'icompare_plugin_main');
	// add_settings_field('plugin_access_key', 'Amazon Access Key', 'icompare_plugin_setting_access_key', 'icompare_plugin', 'icompare_plugin_main');
	// add_settings_field('plugin_secrect_access_key', 'Amazon Secret Access Key', 'icompare_plugin_setting_secrect_access_key', 'icompare_plugin', 'icompare_plugin_main');
	add_settings_field('plugin_affiliate_id', 'Amazon Tracking ID', 'icompare_plugin_setting_affiliate_id', 'icompare_plugin', 'icompare_plugin_main');
	// add_settings_field('plugin_include_powered', 'Include powered by link?', 'icompare_plugin_setting_include_powered', 'icompare_plugin', 'icompare_plugin_main');
	add_settings_field('plugin_color_scheme', '<span class="disabled-function">Front-end Color Scheme</span>', 'icompare_plugin_setting_color_scheme', 'icompare_plugin', 'icompare_plugin_main');
	add_settings_field('plugin_e_commerce', '<span class="disabled-function">Enable other ecommerce sites</span>', 'icompare_plugin_setting_e_commerce', 'icompare_plugin', 'icompare_plugin_main');
	add_settings_field('plugin_floating_banner', '<span class="disabled-function">Enable/Disable Floating Banner</span>', 'icompare_plugin_setting_floating_banner', 'icompare_plugin', 'icompare_plugin_main');
	add_settings_field('plugin_table_placement', '<span class="disabled-function">Select Table Placement</span>', 'icompare_plugin_setting_table_placement', 'icompare_plugin', 'icompare_plugin_main');
}

function icompare_plugin_header_text() {
	echo '<p>Place eBay Campaign ID, eBay Tracking ID. For Amazon enter your Tracking ID. If you have suggestions please submit it here or contact us at <a href="http://icompareplugin.com/" target="_blank">icompareplugin.com</a></p>';
}


function icompare_plugin_setting_app_id() {
	$options = get_option('icompare_plugin_options');
	
	echo "<input id='plugin_app_id' name='icompare_plugin_options[app_id]' size='40' type='hidden' value='SocialLe-60d0-4d5f-9e79-d77cf765c3ab' />";
}
function icompare_plugin_setting_camp_id() {
	$options = get_option('icompare_plugin_options');
	echo "<input id='plugin_camp_id' name='icompare_plugin_options[camp_id]' size='40' type='text' value='".esc_attr($options['camp_id'])."' />";
}
function icompare_plugin_setting_tracking_id() {
	$options = get_option('icompare_plugin_options');
	echo "<input id='plugin_tracking_id' name='icompare_plugin_options[tracking_id]' size='40' type='text' value='".esc_attr($options['tracking_id'])."' />";
}
function icompare_plugin_setting_access_key() {
	$options = get_option('icompare_plugin_options');
	
	echo "<input id='plugin_access_key' name='icompare_plugin_options[access_key]' size='40' type='hidden' value='AKIAJZ6DMBOFPAYCFEBA' />";
}
function icompare_plugin_setting_secrect_access_key() {
	$options = get_option('icompare_plugin_options');
	echo "<input id='plugin_secrect_access_key' name='icompare_plugin_options[secrect_access_key]' size='40' type='hidden' value='RFoIB9FbnWX7TItSArbeUW98Dn7bon4LI8dAdO84' />";
}
function icompare_plugin_setting_affiliate_id() {
	$options = get_option('icompare_plugin_options');
	echo "<input id='plugin_affiliate_id' name='icompare_plugin_options[affiliate_id]' size='40' type='text' value='".esc_attr($options['affiliate_id'])."' />";
}
function icompare_plugin_setting_color_scheme() {
	$options = get_option('icompare_plugin_options');
	echo "<input id='plugin_color_scheme' class='color-picker' name='icompare_plugin_options[color_scheme]' size='40' disabled type='text' value='".esc_attr($options['color_scheme'])."' />";
}
function icompare_plugin_setting_include_powered() {
	$options = get_option('icompare_plugin_options');
	$selected_include_option = $options['include_powered'];
	echo '<select id="plugin_include_powered" name="icompare_plugin_options[include_powered]" class="include_powered">';
	echo '<option value="0" '.($selected_include_option == 0 ? "selected" : "").' >Disable</option>';
	echo '<option value="1" '.($selected_include_option == 1 ? "selected" : "").' >Enable</option>';
	echo '</select>';
}
function icompare_plugin_setting_floating_banner() {
	$options = get_option('icompare_plugin_options');
	echo '<select id="plugin_floating_banner" disabled name="icompare_plugin_options[floating_banner]" class="floating_banner">';
	echo '<option value="0" >Disable</option>';
	echo '<option value="1" >Enable</option>';
	echo '</select>';
}
function icompare_plugin_setting_table_placement() {
	$options = get_option('icompare_plugin_options');
	echo '<select id="plugin_table_placement" disabled name="icompare_plugin_options[table_placement]" class="table_placement">';
	echo '<option value="top">Before Content</option>';
	echo '<option value="middle">Middle of Content</option>';
	echo '<option value="bottom">Bottom of Content</option>';
	echo '</select>';
}
function icompare_plugin_setting_e_commerce() {
	$options = get_option('icompare_plugin_options');
	$ali_express = $options['e_commerce_ali_express'];
	$walmart = $options['e_commerce_walmart'];
	$imart = $options['e_commerce_imart'];
	echo '<div class="ecommerce-checkboxes">';
	echo "<span><input type='checkbox' name='icompare_plugin_options[e_commerce_ali_express]' disabled value='1' ".($ali_express == 1 ? 'checked' : '')." />Ali Express</span>";
	echo '</div>';
}

function icompare_plugin_options_validate($input) {
	$options = get_option('icompare_plugin_options');
	$options['app_id'] = trim('SocialLe-60d0-4d5f-9e79-d77cf765c3ab');
	$options['camp_id'] = trim($input['camp_id']);
	$options['tracking_id'] = trim($input['tracking_id']);
	$options['access_key'] = trim('AKIAJZ6DMBOFPAYCFEBA');
	$options['secrect_access_key'] = trim('RFoIB9FbnWX7TItSArbeUW98Dn7bon4LI8dAdO84');
	$options['affiliate_id'] = trim($input['affiliate_id']);
	// $options['include_powered'] = trim($input['include_powered']);

	return $options;
}

?>