<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Http\Middleware\TrustProxies as Middleware;
use Symfony\Component\HttpFoundation\Request as RequestAlias;

class TrustProxies extends Middleware
{
    /**
     * The trusted proxies for this application.
     *
     * @var array<int, string>|string|null
     */
    protected $proxies;

    /**
     * The headers that should be used to detect proxies.
     *
     * @var int
     */
    protected $headers = RequestAlias::HEADER_X_FORWARDED_ALL;

    /**
     * TrustProxies constructor.
     */
    public function __construct()
    {
        // Ако сме в локална среда, не доверявай проксита
        if (app()->environment('local')) {
            $this->proxies = null;
        } else {
            $this->proxies = '*';
        }
    }
}

