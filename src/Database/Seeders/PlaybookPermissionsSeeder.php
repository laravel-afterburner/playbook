<?php

namespace Afterburner\Playbook\Database\Seeders;

use Afterburner\Playbook\Support\PlaybookPermissionDefinitions;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlaybookPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        if (! DB::getSchemaBuilder()->hasTable('permissions')) {
            if (isset($this->command)) {
                $this->command->error('Permissions table does not exist. Please ensure your database migrations are up to date.');
            }

            return;
        }

        $now = Carbon::now();

        foreach (PlaybookPermissionDefinitions::all() as $permission) {
            DB::table('permissions')->insertOrIgnore($permission + [
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        if (isset($this->command)) {
            $this->command->info('✓ Playbook permissions seeded successfully!');
        }
    }
}
