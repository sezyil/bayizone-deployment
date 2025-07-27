<?php

namespace App\Console\Commands;

use App\Libraries\Permissions\PermissionGenerator;
use Illuminate\Console\Command;

class RebuildPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:rebuild-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rebuild permissions for all users';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        PermissionGenerator::rebuildForSystemUpdate();
    }
}
