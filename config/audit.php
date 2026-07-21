<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Audit logging
    |--------------------------------------------------------------------------
    */

    'enabled' => (bool) env('AUDIT_LOG_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Request metadata
    |--------------------------------------------------------------------------
    */

    'capture_ip_address' => true,
    'capture_user_agent' => true,
    'capture_url' => true,
    'capture_route' => true,

    /*
    |--------------------------------------------------------------------------
    | Sensitive fields
    |--------------------------------------------------------------------------
    |
    | These values are replaced with "[REDACTED]" before being stored.
    |
    */

    'sensitive_fields' => [
        'password',
        'password_confirmation',
        'current_password',
        'new_password',
        'token',
        'remember_token',
        'api_token',
        'secret',
        'client_secret',
        'authorization',
        'signed_document_path',
        'file_path',
    ],

    /*
    |--------------------------------------------------------------------------
    | Ignored fields
    |--------------------------------------------------------------------------
    |
    | These fields usually add noise rather than useful audit information.
    |
    */

    'ignored_fields' => [
        'updated_at',
        'remember_token',
    ],

    /*
    |--------------------------------------------------------------------------
    | Maximum string length
    |--------------------------------------------------------------------------
    */

    'maximum_value_length' => 10000,
];
