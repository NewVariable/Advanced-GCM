<?php



add_action('post_submitbox_misc_actions', 'ag_add_checkbox_to_publish_box');
add_action('save_post', 'ag_push_notification_to_gcm');
//add_action('ag_call_event', 'ag_push_notification_to_gcm');


// Add Checkbox to Publish Box
function ag_add_checkbox_to_publish_box()
{
    ?>
    <div class="misc-pub-section">
        <label><input type="checkbox" name="ag_ignore_send" value="1">Don't Send Notification</label>
    </div>
    <?php
}
/*
*
 * @param $post_id
 */

// checks about required validation
function ag_check_valid($post_id)
{
    if (defined('DOING_AJAX') && DOING_AJAX) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return $post_id;
    }

    // User can edit post
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $ignore_flag = filter_input(INPUT_POST, 'ag_ignore_send', FILTER_VALIDATE_BOOLEAN);


    if (get_ag_settings() && !($ignore_flag)) {
        wp_schedule_single_event(time(), 'ag_call_event', array($post_id));
    }

    // Post status should be publish
    $post_status = get_post_status($post_id);
    if ($post_status != 'publish') {
        return;
    }
}

/**
 * @param $post_id
 */

function ag_push_notification_to_gcm($post_id) {

 if (wp_is_post_revision( $post_id )) {
        return ;
    }

    $postID = get_the_ID();
    $limit =15;
    $offset = 0;
    $registration_ids = ag_get_registered_id($offset); // call of ag_get_registered_id()

    $content_post = get_post($post_id); // all post details
    $contents = $content_post->post_content; // only post content

    if (is_array($registration_ids) && false === empty($registration_ids)) {

        while (true) {
            $url = 'https://android.googleapis.com/gcm/send';

            $fields = array(
                'registration_ids' => $registration_ids,
                'data' => array(
                    'title' => get_the_title($post_id),
                    'content' => $contents,
                    'post_id' => $post_id ->ID,
                )
            );

            $header = array(
                'Authorization' => 'key=' . esc_html(get_ag_settings()), // Settings GCM Token
                'Content-Type' => 'application/json',
            );

          wp_remote_post($url, array(
                'headers' => $header,
                'body' => wp_json_encode($fields),
            ));


            if (count($registration_ids) == $limit) {
                $offset++;
                $registration_ids = ag_get_registered_id($offset);

                if (is_array($registration_ids) && count($registration_ids) > 0) {
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
 * @param $offset , $limit
 *
 * @return array
 */

// Get all the IDs of registered Users
function ag_get_registered_id($offset, $limit = 50)
{
    global $wpdb;

    $query = $wpdb->prepare("SELECT meta_value FROM {$wpdb->usermeta} WHERE meta_key = 'ag_googletoken' LIMIT %d, %d", $offset * $limit, $limit);
    $result = $wpdb->get_col($query);

    return $result;
}
