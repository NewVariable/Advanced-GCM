<?php



function ag_send_security_key () {
    $res = get_option('ag_settings');
    $security_key = $res['ag_uid'];

    return $security_key;

}
