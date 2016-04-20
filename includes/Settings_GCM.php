<?php

add_action( 'admin_menu', 'ag_add_admin_menu' );
add_action( 'admin_init', 'ag_settings_init' );

// Shows message if Google API Key is not set
function ag_no_setting_notice() {
	?>
	<div class="notice notice-error is-dismissible">
		<p><?php printf( esc_html__( 'Push Notifications: API key is not set, %s Please update the setting %s', 'ag' ), '<a href="' . esc_url( admin_url( 'options-general.php?page=advanced-gcm' ) ) . '">', "</a>" ); ?></p>
	</div>
	<?php
}

function ag_add_admin_menu() {
	add_submenu_page( 'options-general.php', 'Advanced-GCM', 'Advanced-GCM', 'manage_options', 'advanced-gcm', 'ag_options_page' );
}

// Get Google API Key of Settings page
/**
 * @return bool
 */
function get_ag_settings() {
	$setting = get_option( 'ag_settings', false );
	if ( is_array( $setting ) && isset( $setting['api_key'] ) && false === empty( $setting['api_key'] ) ) {
		return $setting['api_key'];
	}

	return false;
}

function ag_settings_init() {
	if ( false === get_ag_settings() ) {
		add_action( 'admin_notices', 'ag_no_setting_notice' );
	}

	register_setting( 'pluginPage', 'ag_settings' ); // register a new setting to Settings page


// Add a new Section to a Settings page
	add_settings_section(
		'ag_pluginPage_section',
		__( '', 'advanced-GCM' ), // Your Section Description of advanced-gcm
		'ag_settings_section_callback',
		'pluginPage' // Call of do_settings_sections();
	);


// Add a Google API Key field to a Settings page
	add_settings_field(
		'api_key',
		__( 'Google API Key', 'advanced-GCM' ), // Your TextBox Label Name
		'ag_text_field_api_key',
		'pluginPage', // Call of settings_fields()
		'ag_pluginPage_section'
	);

// Add a Security UID field to a Settings page
	add_settings_field(
		'ag_uid',
		__( 'Security UID', 'advanced-GCM' ), // Your TextBox Label Name
		'ag_text_field_unique_id',
		'pluginPage', // Call of settings_fields()
		'ag_pluginPage_section'
	);

// Add a Default User field to a Settings page
	add_settings_field(
		'default_user',
		__( 'Default Users', 'advanced-GCM' ), // Your Dropdown Box Label Name
		'ag_text_field_default_users',
		'pluginPage', // Call of settings_fields()
		'ag_pluginPage_section'
	);
}


function ag_text_field_api_key() {

	$options = get_option( 'ag_settings' );
	?>
	<input type='text' name='ag_settings[api_key]' value='<?php echo esc_attr( $options['api_key'] ); ?>'/><br>
	<span class="description"><?php _e( "You can enter Google API Key from your Google API Console" ); ?></span><br>

	<?php
}

function ag_text_field_unique_id() {
	$option = get_option('ag_settings');
	?>

	<script type="application/javascript">

	// will generate random string 
		function ag_random_generator() {
			var string = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
			var ret_val = "";

			for(var i = 0 ; i < string.length ; i++){
				ret_val+= string.charAt(Math.floor(Math.random() * string.length));
			}
			 document.getElementById('ag_uid').value = ret_val;
		}
	</script>

	<input type='text' id="ag_uid" name='ag_settings[ag_uid]' value="<?php echo $option['ag_uid'] ?>" /><br>
	<span class="description"><?php _e( "Here Unique ID will be generated" ); ?></span> <br><br>

	<input type="button" class="button button-primary" value="Generate Key" onclick="ag_random_generator(this)"> </input>

	<?php
}

function ag_text_field_default_users() {

	$result = get_option( 'ag_settings' );
	$value  = (isset($result['default_user']) && intval($result['default_user']) > 0) ? $result['default_user']:false;

	?>
		<form action="<?php bloginfo( 'url' ); ?>" method="get">
			<?php

			wp_dropdown_users(
				array(
					'name' => 'ag_settings[default_user]',
					'selected' => $value,
				)
			); ?>
	<?php
}

function ag_settings_section_callback() {
	echo __( '', 'advanced-GCM' ); // Your section description about advanced-gcm
}

function ag_options_page() {

	?>
	<form action='options.php' method='post'>

		<h2> <?php esc_html_e( 'Advanced - GCM', 'ag' ); ?></h2>

		<?php
		settings_fields( 'pluginPage' );
		do_settings_sections( 'pluginPage' );
		submit_button();
		?>
	</form>
	<?php
}
