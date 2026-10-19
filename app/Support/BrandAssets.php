<?php

namespace App\Support;

/**
 * Paths to final Maskanoor SVG brand assets in public/brand/.
 * Do not recolor or transform these files in markup — pick the correct variant instead.
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
                return "/brand/divisions/maskanoor-{$slug}-stacked-{$suffix}.svg";
            }
        }

        return match ($layout) {
            'stacked' => "/brand/logos/maskanoor-group-stacked-{$suffix}.svg",
            'monogram' => "/brand/monogram/maskanoor-monogram-{$suffix}.svg",
            'monogram-nostar' => "/brand/monogram/maskanoor-monogram-nostar-{$suffix}.svg",
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
            : '/brand/og-image.svg';
    }
}
