<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

/**
 * Shared hosting (Hostinger) has no deploy hook for `php artisan migrate`.
 * After a deploy adds new migration files, the first web request runs them once.
 * A small signature file remembers which migration files have already been applied,
 * so normal requests only do a cheap file-list check.
 */
class RunPendingMigrations
{
    public function handle(Request $request, Closure $next): Response
    {
        $this->migrateIfNeeded();

        return $next($request);
    }

    private function migrateIfNeeded(): void
    {
        $files = glob(database_path('migrations/*.php')) ?: [];
        $signature = md5(implode('|', array_map('basename', $files)));
        $marker = storage_path('framework/cache/migrations.sig');

        if (@file_get_contents($marker) === $signature) {
            return;
        }

        $lock = @fopen(storage_path('framework/cache/migrations.lock'), 'c');
        if (! $lock || ! flock($lock, LOCK_EX)) {
            return;
        }

        try {
            // Another request may have finished while we waited for the lock
            if (@file_get_contents($marker) !== $signature) {
                Artisan::call('migrate', ['--force' => true]);
                file_put_contents($marker, $signature);
            }
        } catch (\Throwable $e) {
            // Do not block the site; the error is logged and it retries on the next request.
            Log::error('Automatic migration failed: '.$e->getMessage());
        } finally {
            flock($lock, LOCK_UN);
            fclose($lock);
        }
    }
}
