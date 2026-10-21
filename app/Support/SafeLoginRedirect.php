<?php

namespace App\Support;

/**
 * Whitelist same-site relative paths for post-login redirects.
 */
class SafeLoginRedirect
{
    /**
     * Return a safe relative path (path + optional query/fragment) or null.
     */
    public static function path(?string $redirect): ?string
    {
        if (! is_string($redirect) || $redirect === '') {
            return null;
        }

        // No whitespace / control characters / null bytes.
        if ($redirect !== trim($redirect) || preg_match('/[\x00-\x1f\x7f]/', $redirect)) {
            return null;
        }

        // Backslashes enable /\evil.com-style tricks in some browsers.
        if (str_contains($redirect, '\\')) {
            return null;
        }

        // Must be a single-slash relative path (not protocol-relative //host).
        if (! str_starts_with($redirect, '/') || str_starts_with($redirect, '//')) {
            return null;
        }

        // Reject embedded schemes / credentials (defense in depth).
        if (str_contains($redirect, '://') || str_contains($redirect, '@')) {
            return null;
        }

        if (preg_match('/^[a-z][a-z0-9+.-]*:/i', $redirect)) {
            return null;
        }

        $parts = parse_url($redirect);
        if ($parts === false) {
            return null;
        }

        if (isset($parts['scheme']) || isset($parts['host']) || isset($parts['user']) || isset($parts['pass'])) {
            return null;
        }

        $path = $parts['path'] ?? '';
        if ($path === '' || ! str_starts_with($path, '/') || str_starts_with($path, '//')) {
            return null;
        }

        $safe = $path;
        if (isset($parts['query']) && $parts['query'] !== '') {
            $safe .= '?'.$parts['query'];
        }
        if (isset($parts['fragment']) && $parts['fragment'] !== '') {
            $safe .= '#'.$parts['fragment'];
        }

        return $safe;
    }
}
