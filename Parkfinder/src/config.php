<?php
/**
 * OCPMS - Configuration File
 * ---------------------------
 * Contains database connection settings and system constants.
 */

return [
    'db' => [
        'host' => 'localhost',
        'name' => 'ocpms',
        'user' => 'root',
        'pass' => '',
        'charset' => 'utf8mb4'
    ],

    'app' => [
        'base_url' => 'http://localhost/ocpms/public/',
        'name' => 'Online Car Parking Management System',
        'timezone' => 'Africa/Nairobi',
        'version' => '1.0.0'
    ],

    'security' => [
        'csrf_token_key' => 'ocpms_secure_key_2025',
        'session_name'   => 'ocpms_session'
    ],

    'email' => [
        'from_name'  => 'OCPMS Notifications',
        'from_email' => 'no-reply@ocpms.com'
    ]
];
