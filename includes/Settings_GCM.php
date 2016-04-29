<?php

add_action( 'admin_menu', 'nv_agcm_add_admin_menu' );
add_action( 'admin_init', 'nv_agcm_settings_init' );

/**
 * Shows error message if Google API Key is not set
 */

function nv_agcm_no_setting_notice() {
	?>
	<div class="notice notice-error is-dismissible">
		<p><?php printf( esc_html__( 'Push Notifications: API key is not set, %s Please update the setting %s', 'ag' ), '<a href="' . esc_url( admin_url( 'options-general.php?page=advanced-gcm' ) ) . '">', "</a>" ); ?></p>
	</div>
	<?php
}

function nv_agcm_add_admin_menu() {
	add_submenu_page( 'options-general.php', 'Advanced-GCM', 'Advanced-GCM', 'manage_options', 'advanced-gcm', 'ag_options_page' );
}

/**
 * Get Google API Key of Settings page
 *
 * @return bool
 */

function nv_get_agcm_settings() {
	$setting = get_option( 'ag_settings', false );
	if ( is_array( $setting ) && isset( $setting['api_key'] ) && false === empty( $setting['api_key'] ) ) {
		return $setting['api_key'];
	}
	return false;
}

/**
 * Initialize settings sections & settings fields
 */
function nv_agcm_settings_init() {
	if ( false === nv_get_agcm_settings() ) {
		add_action( 'admin_notices', 'nv_agcm_no_setting_notice' );
	}

	register_setting( 'nv_ag_add_settings_page', 'ag_settings' ); // register a new setting to Settings page


// Add a new Section to a Settings page
	add_settings_section(
		'nv_ag_add_settings_page_section',
		__( '', 'advanced-gcm' ), // Your Section Description of advanced-gcm
		'ag_settings_section_callback',
		'nv_ag_add_settings_page' // Call of do_settings_sections();
	);


// Add a Google API Key field to a Settings page
	add_settings_field(
		'api_key',
		__( 'Google API Key', 'advanced-gcm' ), // Your API Key TextBox Label Name
		'nv_agcm_text_field_api_key',
		'nv_ag_add_settings_page', // Call of settings_fields()
		'nv_ag_add_settings_page_section'
	);

// Add a Security UID field to a Settings page
	add_settings_field(
		'ag_uid',
		__( 'Security UID', 'advanced-gcm' ), // Your Security UID TextBox Label Name
		'nv_agcm_text_field_unique_id',
		'nv_ag_add_settings_page', // Call of settings_fields()
		'nv_ag_add_settings_page_section'
	);

// Add a Default User Checkbox field to a Settings page
	add_settings_field(
		'default_user',
		__( 'Default Users', 'advanced-gcm' ), // Your Dropdown Box Label Name
		'nv_agcm_text_field_default_users',
		'nv_ag_add_settings_page', // Call of settings_fields()
		'nv_ag_add_settings_page_section'
	);
}

/**
 *  Declare API Key Text Field
 */
function nv_agcm_text_field_api_key() {

	$options = get_option( 'ag_settings' );
	?>
	<input type='text' name='ag_settings[api_key]' value='<?php echo esc_attr( $options['api_key'] ); ?>'/><br>
	<span class="description"><?php _e( "You can enter Google API Key from your Google API Console" ); ?></span><br>

	<?php
}

/**
 * Declare Unique ID Text Field
 */
function nv_agcm_text_field_unique_id() {

$option = get_option('ag_settings');

    if(isset($option['ag_uid']) && !empty($option['ag_uid'])){

        ?>
            <label name='ag_settings[ag_uid]' id='ag_uid'> <?php echo esc_html ($option['ag_uid']); ?></label>
            <input type='hidden' id="ag_uid" name='ag_settings[ag_uid]' value="<?php esc_html_e ($option['ag_uid']); ?>" /><br>
        <?php
    } // if close

    else{
?>
    <script type="application/javascript">

        // will generate random string
         function ag_random_generator() {
              var string = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
              var random_string = "";

              for(var i = 0 ; i < string.length ; i++) {
                  random_string += string.charAt(Math.floor(Math.random() * string.length));
              }
               document.getElementById('ag_uid').value = random_string;

         } // ag_random_generator() close

        </script>

        	<input type='text' id="ag_uid" name='ag_settings[ag_uid]' value="<?php echo $option['ag_uid'] ?>" /><br>
            <span class="description"><?php esc_html( "Here Unique ID will be generated" ); ?></span> <br><br>

            <input type="button" class="button button-primary" value="Generate Key" onclick="ag_random_generator(this)"/>

    <?php
    } // else close

}

/**
 * Declare Default Users Checkbox
 */

function nv_agcm_text_field_default_users() {

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
}


/**
 * Set all the fields to options-general.php page
 */
function ag_options_page() {

	?>
	<form action='options.php' method='post'>

		<h2> <?php esc_html_e( 'Advanced - GCM', 'advanced-gcm' ); ?></h2>

		<?php
			settings_fields( 'nv_ag_add_settings_page' );
			do_settings_sections( 'nv_ag_add_settings_page' );
			submit_button();
		?>
	</form>
	<?php
}

