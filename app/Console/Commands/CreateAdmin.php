<?php

namespace App\Console\Commands;

use App\Support\AdminUser;
use Illuminate\Console\Command;

class CreateAdmin extends Command
{
    protected $signature = 'app:create-admin {--reset : Update the existing user email/password from .env}';

    protected $description = 'Create the single login user from ADMIN_EMAIL / ADMIN_PASSWORD in .env';

    public function handle(): int
    {
        $this->info(AdminUser::syncFromEnv((bool) $this->option('reset')));

        return self::SUCCESS;
    }
}
