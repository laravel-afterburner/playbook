<?php

namespace Afterburner\Playbook\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

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

        $this->addEnvironmentVariables();

        $this->info('Installation complete!');
        $this->newLine();
        $this->comment('Visit /help to browse Help & Support.');

        return Command::SUCCESS;
    }

    protected function addEnvironmentVariables(): void
    {
        $envVars = [
            '',
            '# Afterburner Playbook Configuration',
            'AFTERBURNER_PLAYBOOK_ENABLED=true',
            'AFTERBURNER_PLAYBOOK_NAVIGATION_ENABLED=true',
        ];

        foreach ([base_path('.env'), base_path('.env.example')] as $envPath) {
            if (! File::exists($envPath)) {
                continue;
            }

            $envContent = File::get($envPath);

            foreach ($envVars as $var) {
                if ($var && ! str_contains($envContent, explode('=', $var)[0])) {
                    File::append($envPath, "\n".$var);
                }
            }
        }
    }
}
