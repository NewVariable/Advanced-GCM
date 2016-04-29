<?php

/**
 * Plugin Name: Advanced GCM
 * Description: Advanced GCM notifies registered user on their Mobile Devices for the post whenever the post is published or edited.
 * Version:     1.0.0
 * Author:      NewVariable
 * Author URI: http://newvariable.com/
 * Text Domain: advanced-gcm
 */

// Settings Page
include_once __DIR__ . '/includes/Settings_GCM.php';

// User Page
include_once __DIR__ . '/includes/User_Profile_Token.php';

// Ajax
include_once __DIR__ . '/includes/Ajax.php';

// Send Push Notifications
include_once __DIR__ . '/includes/Send_Push_Notifications.php';



