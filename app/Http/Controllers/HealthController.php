<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Throwable;

class HealthController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $checks = [
            'database' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
            'queue' => $this->checkQueue(),
        ];

        $ok = ! in_array(false, array_column($checks, 'ok'), true);

        return response()->json([
            'status' => $ok ? 'ok' : 'degraded',
            'checks' => $checks,
            'app' => config('app.name'),
            'time' => now()->toIso8601String(),
        ], $ok ? 200 : 503);
    }

    /** @return array{ok: bool, detail: string} */
    private function checkDatabase(): array
    {
        try {
            DB::connection()->getPdo();
            DB::select('select 1');

            return ['ok' => true, 'detail' => 'connected'];
        } catch (Throwable $e) {
            return ['ok' => false, 'detail' => 'unavailable'];
        }
    }

    /** @return array{ok: bool, detail: string} */
    private function checkCache(): array
    {
        try {
            $key = 'health:'.uniqid('', true);
            Cache::put($key, '1', 10);
            $ok = Cache::get($key) === '1';
            Cache::forget($key);

            return ['ok' => $ok, 'detail' => $ok ? 'read_write' : 'mismatch'];
        } catch (Throwable $e) {
            return ['ok' => false, 'detail' => 'unavailable'];
        }
    }

    /** @return array{ok: bool, detail: string} */
    private function checkQueue(): array
    {
        try {
            $connection = config('queue.default', 'sync');
            // sync always "works"; others prove the connection resolves.
            Queue::connection($connection)->size();

            return ['ok' => true, 'detail' => $connection];
        } catch (Throwable $e) {
            return ['ok' => false, 'detail' => 'unavailable'];
        }
    }
}
