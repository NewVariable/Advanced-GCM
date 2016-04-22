<?php

add_action('wp_ajax_ag_add_device', 'ag_add_device');
add_action('wp_ajax_nopriv_ag_add_device', 'ag_add_device');

// Check if device is added or not , if not then add device.

function ag_add_device()
{

    $device_id = filter_input(INPUT_GET, 'device_id', FILTER_SANITIZE_STRING);

    $user_id = absint(filter_input(INPUT_GET, 'user_id', FILTER_VALIDATE_INT));

    $token = filter_input(INPUT_GET, 'security_key', FILTER_SANITIZE_STRING);

    var_dump($token);

    var_dump("Hello");

    $res = get_option('ag_settings');
    $get_key = $res['ag_uid'];

    $encryption = md5($device_id . $get_key);

    var_dump($encryption);



    if (0 == $user_id) { 
        $result = get_option('ag_settings');
        $user_id = absint((isset($result['default_user']) && intval($result['default_user']) > 0) ? $result['default_user'] : 0);
    }
    $is_exist = get_userdata($user_id);

    if ($is_exist) {
        if (0 < $user_id) {
            $res = get_user_meta($user_id, 'ag_googletoken');

            if (false == in_array($device_id, $res)) {
                add_user_meta($user_id, 'ag_googletoken', $device_id);

                wp_send_json_success();
            }
        }
    }




    wp_send_json_error();



    wp_die(); // this is required to terminate immediately and return a proper response
}