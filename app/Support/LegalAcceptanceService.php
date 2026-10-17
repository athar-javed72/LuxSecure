<?php

namespace App\Support;

use App\Models\LegalAcceptance;
use App\Models\Page;
use App\Models\User;

class LegalAcceptanceService
{
    /** @return array{terms: ?Page, privacy: ?Page} */
    public static function currentPublished(): array
    {
        $terms = Page::resolveLegal('terms');
        $privacy = Page::resolveLegal('privacy');

        return [
            'terms' => self::isPublished($terms) ? $terms : null,
            'privacy' => self::isPublished($privacy) ? $privacy : null,
        ];
    }

    public static function isPublished(?Page $page): bool
    {
        return $page && ($page->is_published || $page->status === 'published');
    }

    /** @return list<Page> */
    public static function pendingFor(?User $user): array
    {
        if (! $user) {
            return [];
        }

        $pending = [];
        foreach (self::currentPublished() as $page) {
            if ($page && ! LegalAcceptance::hasAccepted($user, $page)) {
                $pending[] = $page;
            }
        }

        return $pending;
    }

    public static function requiresAcceptance(?User $user): bool
    {
        return self::pendingFor($user) !== [];
    }

    /** @param  array<string, bool>  $flags accept_terms / accept_privacy */
    public static function recordFromRequest(User $user, array $flags, ?string $ip = null): void
    {
        $pages = self::currentPublished();
        if (($flags['accept_terms'] ?? false) && $pages['terms']) {
            LegalAcceptance::record($user, $pages['terms'], $ip);
        }
        if (($flags['accept_privacy'] ?? false) && $pages['privacy']) {
            LegalAcceptance::record($user, $pages['privacy'], $ip);
        }
    }

    /** @return array<string, string> validation rules */
    public static function validationRules(): array
    {
        $rules = [];
        $pages = self::currentPublished();
        if ($pages['terms']) {
            $rules['accept_terms'] = ['accepted'];
        }
        if ($pages['privacy']) {
            $rules['accept_privacy'] = ['accepted'];
        }

        return $rules;
    }
}
