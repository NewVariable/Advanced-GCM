<?php

/**
 * Plugin Name: Advanced GCM
 * Description: Advanced GCM notifies registered user through Mobile Devices for the post whenever post is published or edited.
 * Version:     1.0.0
 * Author:      Bhavin Shah
 * Text Domain: advanced-gcm
 */

// Settings Page
include_once __DIR__ . '/includes/Settings_GCM.php';

// User_Profile Page
include_once __DIR__ . '/includes/User_Profile_Token.php';

// Send Push Notifications
include_once __DIR__ . '/includes/Send_Push_Notifications.php';

// Ajax
include_once __DIR__ . '/includes/Ajax.php';

