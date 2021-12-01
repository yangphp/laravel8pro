<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/admin/swiper/upload',
        '/admin/course/upload',
        '/admin/course/upIntroImg',
		'/admin/oss/callback',
    ];
}
