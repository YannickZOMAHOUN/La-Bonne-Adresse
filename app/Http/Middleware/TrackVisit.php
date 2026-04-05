<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class TrackVisit
{
    public function handle(Request $request, Closure $next): Response
    {
        $userAgent = $request->userAgent() ?? '';

        $isBot = str_contains(strtolower($userAgent), 'bot')
              || str_contains(strtolower($userAgent), 'crawl')
              || str_contains(strtolower($userAgent), 'spider');

        $isAsset = $request->is('*.css', '*.js', '*.png', '*.jpg', '*.ico', '*.svg');
        $isAdmin = $request->is('admin/*');

        if (!$isBot && !$isAsset && !$isAdmin) {
            DB::table('page_views')->insert([
                'url'        => $request->path(),
                'ip'         => $request->ip(),
                'user_agent' => $userAgent,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return $next($request);
    }
}
