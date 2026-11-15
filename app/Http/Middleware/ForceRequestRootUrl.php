<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class ForceRequestRootUrl
{
    public function handle(Request $request, Closure $next): Response
    {
        // استخدم host الحالي دائماً حتى لا تُولَّد روابط على APP_URL مختلف
        URL::forceRootUrl($request->getSchemeAndHttpHost());

        return $next($request);
    }
}
