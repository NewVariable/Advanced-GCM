<?php

add_action( 'wp_ajax_ag_add_device', 'nv_agcm_add_device' );
add_action( 'wp_ajax_nopriv_ag_add_device', 'nv_agcm_add_device' );

/**
 * Check if device is registered or not , if not then will register device.
 */

function nv_agcm_add_device() {

	$device_id = filter_input( INPUT_GET, 'device_id', FILTER_SANITIZE_STRING );

	$user_id = absint( filter_input( INPUT_GET, 'user_id', FILTER_VALIDATE_INT ) );

	$security_key = filter_input( INPUT_GET, 'security_key', FILTER_SANITIZE_STRING );

	$res        = get_option( 'ag_settings' );
	$get_ag_uid = $res['ag_uid'];

	$encrypted_key = sha1( $device_id . $get_ag_uid ); // will encrypt key using device_id and unique_id

	if ( 0 == $user_id ) {
		$result  = get_option( 'ag_settings' );
		$user_id = absint( ( isset( $result['default_user'] ) && intval( $result['default_user'] ) > 0 ) ? $result['default_user'] : 0 );
	}
	$is_exist = get_userdata( $user_id );

	if ( $is_exist ) {
		if ( 0 < $user_id ) {
			$res = get_user_meta( $user_id, 'nv_agcm_googletoken' );

			// compare both the keys , if match then device id will be registered.
			if ( false == in_array( $device_id, $res ) && ( $security_key == $encrypted_key ) ) {
				add_user_meta( $user_id, 'nv_agcm_googletoken', $device_id );

				wp_send_json_success();
			}
		}
	}
	wp_send_json_error();

	wp_die(); // this is required to terminate immediately and return a proper response
}