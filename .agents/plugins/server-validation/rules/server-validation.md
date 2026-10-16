# Server-Side Validation Rules (LuxSecure / Laravel)

Whenever writing, modifying, or reviewing backend code in this Laravel project:

1. **Zero Unvalidated Input**: Never pass `$request->all()`, `$request->input()` or raw query/route values into business logic or Eloquent writes. Use `$request->validated()` from a Form Request (or an explicit validated array).
2. **Form Request First**: Prefer `app/Http/Requests/...` before expanding controller actions that accept user input.
3. **Strict Rules**: Declare types, `max` lengths, `exists` / `in` rules where relevant.
4. **Mass Assignment**: Prefer explicit `$fillable` on models that receive user input; never widen `$guarded = []` casually.
5. **Auth & roles**: Admin routes stay behind admin middleware; never weaken role checks for convenience.
6. **Clean Status Codes**: Keep Laravel 422 validation responses; use 403 for authorization failures and 404 when a resource is missing.
7. **Files / images**: Validate mime, size, and store via the public disk; do not trust client filenames alone.
