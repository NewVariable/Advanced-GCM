<?php

add_action( 'show_user_profile', 'nv_agcm_extra_user_profile_fields' );
add_action( 'edit_user_profile', 'nv_agcm_extra_user_profile_fields' );


/**
 * Get GoogleToken of Device
 *
 * @param $user
 */
function nv_agcm_extra_user_profile_fields( $user ) {
	?>
	<h3><?php _e( "Google Token", "blank" ); ?></h3>

	<?php

	$user_metas = (array) get_user_meta( $user->ID, 'ag_googletoken' );

	?>

	<table class="form-table">
		<th><label for="ag_googletoken"><?php _e( "Your Google Token" ); ?></label></th>

		<?php
		foreach ( $user_metas as $user_meta ) {
			$user_meta_len = strlen( $user_meta );

			if ( $user_meta_len > 100 ) {
				$user_meta = substr( $user_meta, 0, 5 ) . '**********' . substr( $user_meta, $user_meta_len - 5 );
			} ?>

			<tr>
				<td>
					<label name="ag_googletoken" id="ag_googletoken"
					       class="regular-text"><?php echo esc_html( $user_meta ); ?>
					</label>
				</td>
			</tr>

			<?php
		} // foreach close
		?>
	</table>

<?php }

add_action( 'personal_options_update', 'nv_agcm_save_extra_user_profile_fields' );
add_action( 'edit_user_profile_update', 'nv_agcm_save_extra_user_profile_fields' );

/**
 * Save User Data
 *
 * @param $user_id
 *
 * @return bool
 */
function nv_agcm_save_extra_user_profile_fields( $user_id ) {

	if ( ! current_user_can( 'edit_user', $user_id ) ) {
		return false;
	}

	$google_token = filter_input( INPUT_POST, 'nv_agcm_googletoken', FILTER_SANITIZE_STRING );

	if ( isset( $google_token ) ) {
		return false;
	}

	update_user_meta( $user_id, 'nv_agcm_googletoken', $google_token );

	return true;
}
