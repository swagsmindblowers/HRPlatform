<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gzip-compress response bodies. The app is served directly by
 * `php artisan serve` (no nginx/Apache in front), so nothing else in the
 * stack compresses responses -- Inertia page payloads and JSON API
 * responses would otherwise go over the wire uncompressed.
 */
class CompressResponse
{
    /**
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! $this->shouldCompress($request, $response)) {
            return $response;
        }

        $content = $response->getContent();
        $compressed = gzencode($content, 6);

        if ($compressed === false) {
            return $response;
        }

        $response->setContent($compressed);
        $response->headers->set('Content-Encoding', 'gzip');
        $response->headers->set('Content-Length', (string) strlen($compressed));
        $response->headers->set('Vary', 'Accept-Encoding');

        return $response;
    }

    private function shouldCompress(Request $request, Response $response): bool
    {
        if (! str_contains($request->headers->get('Accept-Encoding', ''), 'gzip')) {
            return false;
        }

        if ($response->headers->has('Content-Encoding')) {
            return false;
        }

        $contentType = (string) $response->headers->get('Content-Type');
        if ($contentType !== '' && ! preg_match('/text|json|javascript|xml|svg/i', $contentType)) {
            return false;
        }

        $content = $response->getContent();

        return is_string($content) && strlen($content) > 1024;
    }
}
