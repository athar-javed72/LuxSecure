<?php

namespace App\Support;

/**
 * Paths to approved SVG brand assets in public/brand/.
 * Files are copied byte-for-byte from brand-source/.
 * Do not recolor, regenerate, or transform these files in markup.
 * Display names and taglines come from Settings — not from this helper.
 */
final class BrandAssets
{
    public const TAGLINE = 'Elevating Spaces. Enriching Stays.';

    public static function logo(string $layout = 'horizontal', string $surface = 'dark', ?string $division = null): string
    {
        $surface = $surface === 'light' ? 'light' : 'dark';
        $suffix = $surface === 'light' ? 'for-light-bg' : 'for-dark-bg';

        if ($division) {
            $slug = match (strtolower($division)) {
                'hotel', 'hotels', 'hospitality' => 'hotels',
                'property', 'properties' => 'properties',
                'living' => 'living',
                default => null,
            };
            if ($slug) {
                return "/brand/divisions/maskanoor-{$slug}-{$suffix}.svg";
            }
        }

        return match ($layout) {
            'stacked', 'primary' => "/brand/logos/maskanoor-group-primary-{$suffix}.svg",
            'compact' => "/brand/logos/maskanoor-group-horizontal-compact-{$suffix}.svg",
            'monogram' => "/brand/monogram/maskanoor-monogram-two-tone-{$suffix}.svg",
            'monogram-nostar' => "/brand/monogram/maskanoor-monogram-nostar-two-tone-{$suffix}.svg",
            default => "/brand/logos/maskanoor-group-horizontal-{$suffix}.svg",
        };
    }

    public static function favicon(): string
    {
        return '/brand/favicon/favicon.svg';
    }

    public static function ogImage(): string
    {
        return file_exists(public_path('brand/og-image.png'))
            ? '/brand/og-image.png'
            : '/brand/logos/maskanoor-group-primary-for-dark-bg.svg';
    }
}
