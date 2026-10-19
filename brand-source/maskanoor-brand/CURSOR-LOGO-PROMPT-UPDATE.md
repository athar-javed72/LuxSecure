# Cursor prompt — Logo section (replaces the old "Logo" block in the master prompt)

## Logo and brand assets (final clean SVG set)

All logo files are in `public/brand/`. Use ONLY these files. Do not redraw, recolor, stretch, rotate, or add shadows/effects to them. Do not use any PNG/AI-generated logo board on the website. There must be no "concept logo" text anywhere.

Folders:
- `logos/` — primary stacked + horizontal lockups
- `monogram/` — M monogram (with star, and `nostar` versions)
- `divisions/` — Hotels, Properties, Living lockups
- `favicon/` — favicon.svg

Variant rules (backgrounds are transparent):
- On dark surfaces (Deep Forest `#142A25`, Forest Shadow `#0D1D19`, dark hero overlays): use `*-for-dark-bg.svg` (ivory M + champagne gold arch).
- On light surfaces (Warm Ivory `#F5F0E6`, Stone `#E5DED1`, white): use `*-for-light-bg.svg` (forest M + dark gold `#876A2F`). NEVER use `#D6B779` for text or thin lines on light backgrounds (low contrast).
- Header: use `maskanoor-group-horizontal-*` (transparent header over hero = dark-bg variant; solid ivory header = light-bg variant; solid forest header = dark-bg variant). Swap variant when the header state changes on scroll, with no layout shift (fixed height, same aspect ratio).
- Mobile header and any size under 120px wide: use `maskanoor-monogram-nostar-*` (star removed because it disappears at small sizes).
- Favicon: `favicon/favicon.svg`; also export 32x32, 180x180 (apple-touch-icon), 192 and 512 PNGs from it. Social/OG image: stacked dark-bg logo centered on `#142A25`, 1200x630.
- Footer: stacked or horizontal dark-bg logo on Deep Forest.
- Division pages (/hospitality, /properties, /living): use the matching file from `divisions/` in the page hero or section header. All divisions share the same monogram and wordmark; only the descriptor changes.
- Tagline "Elevating Spaces. Enriching Stays." is NOT part of the logo lockup. Set it as live text in Cormorant Garamond italic next to or below the logo where needed.
- Clear space: at least the height of the "M" stem width x 2 (about half the monogram's arch height) on all sides. Minimum width: stacked logo 140px, horizontal logo 120px, monogram 24px.
- Inline the SVG or use `<img>` with meaningful alt text ("Maskanoor Group"). Keep the logo easy to replace if the owner later commissions a final trademark-cleared logo.
