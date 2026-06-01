<?php

namespace Afterburner\Playbook\Console\Commands;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'afterburner:playbook:install';

    protected $description = 'Install the Afterburner Playbook package';

    public function handle(): int
    {
        $this->info('Installing Afterburner Playbook package...');

        $this->call('vendor:publish', [
            '--tag' => 'afterburner-playbook-config',
            '--force' => true,
        ]);

        $this->call('vendor:publish', [
            '--tag' => 'afterburner-playbook-migrations',
            '--force' => true,
        ]);

        $this->info('Installation complete!');
        $this->newLine();
        $this->comment('Run php artisan migrate to create FAQ tables.');
        $this->comment('Visit /help to browse Help & Support.');

        return Command::SUCCESS;
    }
}
