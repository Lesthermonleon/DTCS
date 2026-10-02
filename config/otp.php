<?php

/*
|--------------------------------------------------------------------------
| OTP (One-Time Password) Configuration — DTCS HIMS
|--------------------------------------------------------------------------
|
| Controls the behaviour of the email OTP two-factor authentication layer.
| All values can be overridden via environment variables.
|
| OTP_LOGIN_POLICY options:
|   every_login  — OTP required on every new login session (current)
|   (future)     — additional policy values may be added without rewriting
|
*/

return [

    /*
    |--------------------------------------------------------------------------
    | Login OTP Policy
    |--------------------------------------------------------------------------
    | Controls when OTP verification is required during the login flow.
    | Set to 'every_login' to require OTP on every new session.
    */
    'policy' => env('OTP_LOGIN_POLICY', 'every_login'),

    /*
    |--------------------------------------------------------------------------
    | OTP Expiration
    |--------------------------------------------------------------------------
    | Number of minutes before an OTP expires. Default: 3 minutes.
    */
    'expires_minutes' => (int) env('OTP_EXPIRES_MINUTES', 3),

    /*
    |--------------------------------------------------------------------------
    | Maximum Verification Attempts
    |--------------------------------------------------------------------------
    | Number of incorrect OTP attempts allowed before the OTP is invalidated
    | and the user must request a new one. Default: 5.
    */
    'max_attempts' => (int) env('OTP_MAX_ATTEMPTS', 5),

    /*
    |--------------------------------------------------------------------------
    | Resend Cooldown
    |--------------------------------------------------------------------------
    | Minimum seconds required between OTP resend requests. Default: 60.
    */
    'resend_cooldown_sec' => (int) env('OTP_RESEND_COOLDOWN_SEC', 60),

    /*
    |--------------------------------------------------------------------------
    | Send Rate Limit
    |--------------------------------------------------------------------------
    | Maximum number of OTP sends allowed within the rate limit window
    | per user account. Prevents automated spam/abuse.
    */
    'rate_limit_max' => (int) env('OTP_RATE_LIMIT_MAX', 5),

    /*
    |--------------------------------------------------------------------------
    | Send Rate Limit Window
    |--------------------------------------------------------------------------
    | Duration in minutes for the OTP send rate limit window. Default: 15.
    */
    'rate_limit_minutes' => (int) env('OTP_RATE_LIMIT_MINUTES', 15),

];
