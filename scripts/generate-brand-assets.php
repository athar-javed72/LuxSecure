<?php

/**
 * Generate Maskanoor brand SVG kit under public/brand/.
 * Run: php scripts/generate-brand-assets.php
 */
$root = dirname(__DIR__).DIRECTORY_SEPARATOR.'public'.DIRECTORY_SEPARATOR.'brand';

$colors = [
    'dark' => ['ink' => '#F5F0E6', 'gold' => '#D6B779'],
    'light' => ['ink' => '#142A25', 'gold' => '#876A2F'],
];

function starPath(): string
{
    return 'M60 6 L63.2 16.8 L74.5 16.8 L65.4 23.4 L68.6 34.2 L60 27.6 L51.4 34.2 L54.6 23.4 L45.5 16.8 L56.8 16.8 Z';
}

function mPath(): string
{
    return 'M22 122 L22 38 L41 38 L60 80 L79 38 L98 38 L98 122 L81 122 L81 68 L66 100 L54 100 L39 68 L39 122 Z';
}

function archPath(): string
{
    return 'M47 122 L47 98 C47 90 52 84 60 80 C68 84 73 90 73 98 L73 122 L65 122 L65 100 C65 95 63 92 60 90 C57 92 55 95 55 100 L55 122 Z';
}

function monogramParts(string $ink, string $gold, bool $withStar): string
{
    $html = '';
    if ($withStar) {
        $html .= '<path fill="'.$gold.'" d="'.starPath().'"/>';
    }
    $html .= '<path fill="'.$ink.'" d="'.mPath().'"/>';
    $html .= '<path fill="'.$gold.'" d="'.archPath().'"/>';

    return $html;
}

function monogram(string $ink, string $gold, bool $withStar): string
{
    return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 132" fill="none" role="img" aria-label="Maskanoor Group">'
        .monogramParts($ink, $gold, $withStar)
        .'</svg>'."\n";
}

function stacked(string $ink, string $gold, string $descriptor): string
{
    $desc = htmlspecialchars(strtoupper($descriptor), ENT_XML1);

    return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 360 220" fill="none" role="img" aria-label="Maskanoor Group">'
        .'<g transform="translate(120,8)">'.monogramParts($ink, $gold, true).'</g>'
        .'<text x="180" y="168" text-anchor="middle" font-family="Cormorant Garamond, Georgia, serif" font-size="36" font-weight="600" letter-spacing="0.18em" fill="'.$ink.'">MASKANOOR</text>'
        .'<line x1="90" y1="192" x2="140" y2="192" stroke="'.$gold.'" stroke-width="1.2"/>'
        .'<text x="180" y="198" text-anchor="middle" font-family="Cormorant Garamond, Georgia, serif" font-size="16" font-weight="500" letter-spacing="0.35em" fill="'.$gold.'">'.$desc.'</text>'
        .'<line x1="220" y1="192" x2="270" y2="192" stroke="'.$gold.'" stroke-width="1.2"/>'
        .'</svg>'."\n";
}

function horizontal(string $ink, string $gold): string
{
    return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 420 72" fill="none" role="img" aria-label="Maskanoor Group">'
        .'<g transform="translate(4,2) scale(0.52)">'.monogramParts($ink, $gold, true).'</g>'
        .'<text x="84" y="34" font-family="Cormorant Garamond, Georgia, serif" font-size="28" font-weight="600" letter-spacing="0.16em" fill="'.$ink.'">MASKANOOR</text>'
        .'<text x="84" y="56" font-family="Cormorant Garamond, Georgia, serif" font-size="12" font-weight="500" letter-spacing="0.32em" fill="'.$gold.'">GROUP</text>'
        .'</svg>'."\n";
}

function faviconSvg(): string
{
    return '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" fill="none" role="img" aria-label="Maskanoor">'
        .'<rect width="32" height="32" rx="6" fill="#142A25"/>'
        .'<g transform="translate(2.5,1.5) scale(0.225)">'.monogramParts('#F5F0E6', '#D6B779', false).'</g>'
        .'</svg>'."\n";
}

function ogSvg(): string
{
    return '<svg xmlns="http://www.w3.org/2000/svg" width="1200" height="630" viewBox="0 0 1200 630" role="img" aria-label="Maskanoor Group">'
        .'<rect width="1200" height="630" fill="#142A25"/>'
        .'<g transform="translate(420,70) scale(1.8)">'.monogramParts('#F5F0E6', '#D6B779', true).'</g>'
        .'<text x="600" y="420" text-anchor="middle" font-family="Cormorant Garamond, Georgia, serif" font-size="64" font-weight="600" letter-spacing="0.18em" fill="#F5F0E6">MASKANOOR</text>'
        .'<line x1="430" y1="458" x2="520" y2="458" stroke="#D6B779" stroke-width="2"/>'
        .'<text x="600" y="470" text-anchor="middle" font-family="Cormorant Garamond, Georgia, serif" font-size="28" font-weight="500" letter-spacing="0.35em" fill="#D6B779">GROUP</text>'
        .'<line x1="680" y1="458" x2="770" y2="458" stroke="#D6B779" stroke-width="2"/>'
        .'</svg>'."\n";
}

function writeFile(string $path, string $contents): void
{
    $dir = dirname($path);
    if (! is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
    file_put_contents($path, $contents);
    echo "Wrote {$path}\n";
}

foreach ($colors as $variant => $c) {
    $suffix = $variant === 'dark' ? 'for-dark-bg' : 'for-light-bg';
    writeFile("{$root}/monogram/maskanoor-monogram-{$suffix}.svg", monogram($c['ink'], $c['gold'], true));
    writeFile("{$root}/monogram/maskanoor-monogram-nostar-{$suffix}.svg", monogram($c['ink'], $c['gold'], false));
    writeFile("{$root}/logos/maskanoor-group-stacked-{$suffix}.svg", stacked($c['ink'], $c['gold'], 'GROUP'));
    writeFile("{$root}/logos/maskanoor-group-horizontal-{$suffix}.svg", horizontal($c['ink'], $c['gold']));
    foreach (['Hotels', 'Properties', 'Living'] as $div) {
        $slug = strtolower($div);
        writeFile("{$root}/divisions/maskanoor-{$slug}-stacked-{$suffix}.svg", stacked($c['ink'], $c['gold'], $div));
    }
}

writeFile("{$root}/favicon/favicon.svg", faviconSvg());
writeFile("{$root}/og-image.svg", ogSvg());
copy("{$root}/logos/maskanoor-group-horizontal-for-light-bg.svg", "{$root}/maskanoor-logo.svg");
copy("{$root}/favicon/favicon.svg", dirname($root).DIRECTORY_SEPARATOR.'favicon.svg');

echo "Brand SVG kit generated.\n";
