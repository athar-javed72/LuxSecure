<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        if (app()->environment('production') && ! $request->secure() && ! $request->is('webhooks/*')) {
            return redirect()->secure($request->getRequestUri());
        }

        /** @var Response $response */
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        if (app()->environment('production')) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        $response->headers->set('Content-Security-Policy', $this->contentSecurityPolicy());

        return $response;
    }

    private function contentSecurityPolicy(): string
    {
        $scriptSrc = ["'self'", "'unsafe-inline'", "'unsafe-eval'", 'https://unpkg.com'];
        $styleSrc = ["'self'", "'unsafe-inline'", 'https://fonts.googleapis.com'];
        $fontSrc = ["'self'", 'https://fonts.gstatic.com', 'data:'];
        $imgSrc = ["'self'", 'data:', 'blob:', 'https:'];
        $connectSrc = ["'self'", 'https://assets2.lottiefiles.com', 'https://unpkg.com'];
        $mediaSrc = ["'self'", 'https://assets2.lottiefiles.com'];
        $workerSrc = ["'self'", 'blob:'];

        // Vite HMR only in local — matches public/hot when `npm run dev` is active.
        if (app()->environment('local')) {
            $viteOrigins = [
                'http://localhost:5173',
                'http://127.0.0.1:5173',
                'http://[::1]:5173',
                'ws://localhost:5173',
                'ws://127.0.0.1:5173',
                'ws://[::1]:5173',
            ];
            $scriptSrc = array_merge($scriptSrc, $viteOrigins);
            $styleSrc = array_merge($styleSrc, $viteOrigins);
            $connectSrc = array_merge($connectSrc, $viteOrigins);
            $fontSrc = array_merge($fontSrc, $viteOrigins);
            $workerSrc = array_merge($workerSrc, $viteOrigins);
        } else {
            $connectSrc = array_merge($connectSrc, ['ws:', 'wss:']);
        }

        return implode('; ', [
            "default-src 'self'",
            'script-src '.implode(' ', $scriptSrc),
            'style-src '.implode(' ', $styleSrc),
            'font-src '.implode(' ', $fontSrc),
            'img-src '.implode(' ', $imgSrc),
            'connect-src '.implode(' ', $connectSrc),
            'media-src '.implode(' ', $mediaSrc),
            'worker-src '.implode(' ', $workerSrc),
            "frame-ancestors 'self'",
            "base-uri 'self'",
            "form-action 'self'",
        ]);
    }
}
