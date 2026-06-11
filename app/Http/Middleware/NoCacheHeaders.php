<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NoCacheHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Usamos $response->headers->set() (HeaderBag) en lugar de withHeaders():
        // withHeaders() solo existe en Illuminate\Http\Response, no en
        // StreamedResponse/BinaryFileResponse (descargas CSV/PDF), que también
        // pasan por este middleware del grupo 'auth'.
        $response->headers->set('Cache-Control', 'no-store, no-cache, must-revalidate, private');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }
}
