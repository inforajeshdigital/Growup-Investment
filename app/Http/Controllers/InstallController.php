<?php

namespace App\Http\Controllers;

use App\Support\AdminUser;
use Illuminate\Support\Facades\Artisan;

/**
 * One-time setup for shared hosting without SSH:
 * open https://your-domain.com/install/{INSTALL_KEY} once after uploading .env.
 * It runs migrations and creates the login user. Disabled when INSTALL_KEY is empty.
 */
class InstallController extends Controller
{
    public function __invoke(string $key)
    {
        $expected = (string) config('growup.install_key');
        abort_if($expected === '' || ! hash_equals($expected, $key), 404);

        Artisan::call('migrate', ['--force' => true]);
        $output = Artisan::output();
        $output .= "\n".AdminUser::syncFromEnv()."\n";
        $output .= "\nDone. Now set INSTALL_KEY= (empty) in .env to disable this page.\n";

        return response($output, 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }
}
