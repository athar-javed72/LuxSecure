# Maskanoor Group API v1

Base URL: `/api/v1`  
Auth: Sanctum bearer tokens (`Authorization: Bearer {token}`)  
Interactive docs: run `php artisan scribe:generate` then open `/docs`

## Endpoints

| Method | Path | Auth |
|--------|------|------|
| POST | `/auth/register` | No |
| POST | `/auth/login` | No |
| POST | `/auth/two-factor-challenge` | No |
| POST | `/auth/logout` | Yes |
| GET/PUT | `/profile` | Yes |
| GET | `/listings` | No |
| GET | `/listings/{id\|slug}` | No |
| GET | `/favorites` | Yes |
| POST | `/favorites/{listing}` | Yes (toggle) |
| GET/POST | `/bookings` | Yes |
| GET | `/bookings/{booking}` | Yes |
| POST | `/bookings/{booking}/cancel` | Yes |
| POST | `/two-factor/setup\|confirm\|disable` | Yes (admin roles) |

Errors are JSON: `{ "success": false, "message": "...", "code": "...", "errors": {} }`.

Rate limits: `api-auth` (login/register) 5/min per email+IP; authenticated `api` 60/min.
