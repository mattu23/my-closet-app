<?php

namespace App\Http\Middleware;

use Illuminate\Cookie\Middleware\EncryptCookies as Middleware;

class EncryptCookies extends Middleware
{
    /**
     * 暗号化しないクッキー名
     *
     * @var array<int, string>
     */
    protected $except = [
        //
    ];
} 