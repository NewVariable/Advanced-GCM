<?php

add_action( 'post_submitbox_misc_actions', 'nv_agcm_add_checkbox' );
add_action( 'save_post', 'nc_agcm_check_valid' );
add_action( 'ag_call_event', 'nv_agcm_send_push_notification' );


/**
 *  Add Checkbox to Publish Block
 */
function nv_agcm_add_checkbox() {
	?>
	<div class="misc-pub-section">
		<label><input type="checkbox" name="ag_ignore_send" value="1">Don't Send Notification</label>
	</div>
	<?php
}

/**
 * Checks required validation
 *
 * @param $post_id
 */

function nc_agcm_check_valid( $post_id ) {
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return $post_id;
	}

	// check if current post is a revision or not
	if ( wp_is_post_revision( $post_id ) ) {
		return;
	}

	// User can edit post
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( 'publish' !== get_post_status( $post_id ) ) {
		return;
	}

	$ignore_flag = filter_input( INPUT_POST, 'ag_ignore_send', FILTER_VALIDATE_BOOLEAN );
	if ( $ignore_flag ) {
		return;
	}

	if ( nv_get_agcm_settings() ) {
		wp_schedule_single_event( time(), 'ag_call_event', array( $post_id ) );
	}
}


/**
 *  Sends a notification to registered mobile devices.
 *
 * @param $post_id
 */
function nv_agcm_send_push_notification( $post_id ) {

	$limit            = 15;
	$offset           = 0;
	$registration_ids = nv_agcm_get_registered_id( $offset ); // call of ag_get_registered_id()

	$content_post = get_post( $post_id ); // all post details
	$contents     = $content_post->post_content; // only post content

	if ( is_array( $registration_ids ) && false === empty( $registration_ids ) ) {

		while ( true ) {
			$url = 'https://android.googleapis.com/gcm/send';

			$fields = array(
				'registration_ids' => $registration_ids,
				'data'             => array(
					'title'   => get_the_title( $post_id ),
					'content' => $contents,
					'post_id' => (int) $post_id,
				)
			);

			$header = array(
				'Authorization' => 'key=' . esc_html( nv_get_agcm_settings() ), // Settings GCM Token
				'Content-Type'  => 'application/json',
			);

			wp_remote_post( $url, array(
				'headers' => $header,
				'body'    => wp_json_encode( $fields ),
			) );

			if ( count( $registration_ids ) == $limit ) {
				$offset ++;
				$registration_ids = nv_agcm_get_registered_id( $offset );

				if ( is_array( $registration_ids ) && count( $registration_ids ) > 0 ) {
					continue;
				} else {
					break;
				}
			} else {
				break;
			}
		} // while close

	} // outer if close
}

/**
 *  Get all Google Tokens of registered Users
 *
 * @param $offset , $limit
 *
 * @return array
 */
function nv_agcm_get_registered_id( $offset, $limit = 50 ) {
	global $wpdb;

	$query  = $wpdb->prepare( "SELECT meta_value FROM {$wpdb->usermeta} WHERE meta_key = 'nv_agcm_googletoken' LIMIT %d, %d", $offset * $limit, $limit );
	$result = $wpdb->get_col( $query );

	return $result;
}
