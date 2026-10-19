# Geo data (countries / states / cities)

## Source

Primary dataset: **[dr5hn/countries-states-cities-database](https://github.com/dr5hn/countries-states-cities-database)**  
Licence: **ODbL** — attribute the Open Database Licence and upstream project when redistributing derived data.

Optional alternative: [GeoNames](https://www.geonames.org/) (CC BY 4.0) — not wired by default.

## Local files (not committed)

Place JSON exports under:

```
storage/app/geo/countries.json
storage/app/geo/states.json
storage/app/geo/cities.json
```

Download (example — Windows PowerShell / curl.exe):

```bash
# countries + states still live under master/json/
curl.exe -L -o storage/app/geo/countries.json https://raw.githubusercontent.com/dr5hn/countries-states-cities-database/master/json/countries.json
curl.exe -L -o storage/app/geo/states.json https://raw.githubusercontent.com/dr5hn/countries-states-cities-database/master/json/states.json

# cities.json was removed from master/json — use the release gzip export, then decompress
curl.exe -L -o storage/app/geo/json-cities.json.gz https://github.com/dr5hn/countries-states-cities-database/releases/download/v5.0.0-export.1/json-cities.json.gz
# decompress to storage/app/geo/cities.json (gzip -d / 7zip / PHP), then delete the .gz
```

Verify sizes are non-trivial (`cities.json` is typically ~150–200 MB uncompressed). Record a SHA-256 checksum for your pinned revision in ops notes.

Pinned release used by ops: **v5.0.0-export.1** (`json-cities.json.gz`).

`storage/app/geo/` is gitignored for the large city dump — keep only docs here in the repo.

## Import

```bash
php artisan maskanoor:geo:import --dry-run
php artisan maskanoor:geo:import
php artisan maskanoor:geo:import --country=PK,AE
php artisan maskanoor:geo:import --chunk=1000
# Full-world cities.json needs ~1GB PHP memory (command raises the limit automatically)
```

Behaviour:

- Idempotent upsert by `source_id` (and ISO2 for countries).
- Existing seeded cities (Islamabad, Lahore, Dubai, …) are matched by country + name; **IDs are preserved** so listings stay valid.
- Imported countries start **`is_active = false`**. Already-activated countries keep their operational settings.
- Cities referenced by listings are never deleted by this command.

## Known limits

- Duplicate / near-duplicate city names across states (handled via `state_code`).
- Transliterated / English-only names; local-language names are incomplete.
- No sub-city “areas” (DHA, Bahria, etc.) — those remain in the admin-managed `areas` table.
- Population / coordinates quality varies by source revision.

## Admin enablement

After import, enable countries in **Admin → Countries → Visibility** only when the readiness checklist passes (currency, timezone, locale, tax or no-tax, KYC type, payment method, cancellation policy, privacy/terms).
