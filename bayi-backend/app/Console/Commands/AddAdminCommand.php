<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AddAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add:admin {name} {email} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add Admin User';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $name = $this->argument('name');
        $email = $this->argument('email');
        $password = $this->argument('password');

        $admin = new \App\Models\Admin();
        $admin->email = $email;
        $admin->fullname = $name;
        $admin->password = \Hash::make($password);
        $admin->save();

        $this->info('Admin user created successfully');
    }
}
