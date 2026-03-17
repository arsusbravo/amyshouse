<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ShareAuthData
{
    public function handle(Request $request, Closure $next)
    {
        Inertia::share([
            'auth' => fn () => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'group' => $request->user()->group,
                    'is_admin' => $request->user()->is_admin,
                ] : null,
            ],
            'flash' => fn () => [
                'order_number' => $request->session()->get('flash.order_number'),
            ],
            'siteContent' => fn () => [
                'zh-TW' => \App\Models\SiteContent::allForLocale('zh-TW'),
                'en' => \App\Models\SiteContent::allForLocale('en'),
            ],
        ]);

        return $next($request);
    }
}
