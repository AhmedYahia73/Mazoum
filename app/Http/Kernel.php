<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application.
     *
     * @var array
     */
    protected $middleware = [
        // \App\Http\Middleware\TrustHosts::class,
        \App\Http\Middleware\TrustProxies::class,
        \Fruitcake\Cors\HandleCors::class,
        \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
    ];

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class, 
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
        // ===== Custom API inside web group =====
        'api_in_web' => [
            'throttle:api', // حماية ضد spamming
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            // مفيش StartSession, CSRF, ShareErrorsFromSession
        ],
    ];

    /**
     * The application's route middleware.
     *
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

        'AuthAdmin' => \App\Http\Middleware\Admin::class,
        'AuthMember' => \App\Http\Middleware\Member::class,
        'AuthAssistant' => \App\Http\Middleware\Assistant::class,
        'AuthParking' => \App\Http\Middleware\Parking::class,

        'AuthUser' => \App\Http\Middleware\User::class,

        'Admin_Language' => \App\Http\Middleware\Admin_Language::class,

        'checkPassword' => \App\Http\Middleware\CheckPassword::class,
        'CheckLang' => \App\Http\Middleware\CheckLang::class,

        'changeLange' => \App\Http\Middleware\ChangeLange::class,
        'CheckUserToken' => \App\Http\Middleware\CheckUserToken::class,

        'Localization' => \App\Http\Middleware\Localization::class,

    ];
}
