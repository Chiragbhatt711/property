<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'coingate-callback',
        'check_user_login',
        'check_user_otp',
        'check_admin_user_login',
        'check_admin_user_otp',
        'ordergatway/admin-login',
        'login',
        'reset-password/*',
        'change-password',
        'safex-failure',
        'safex-success',
        'payeer-success',
        'payeer-fail',
        'payeer-status',
    ];
}
