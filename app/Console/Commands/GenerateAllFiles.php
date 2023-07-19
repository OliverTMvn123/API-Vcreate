<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class GenerateAllFiles extends Command
{
    protected $signature = 'generate:all {name}';
    protected $description = 'Generate migration, model, and controller';

    public function handle()
    {
        $name = $this->argument('name');

        // Tạo file migration
        Artisan::call('make:migration', [
            'name' => 'create_'.$name.'_table',
        ]);

        // Tạo file model
        Artisan::call('make:model', [
            'name' => $name,
        ]);

        // Tạo file controller
        Artisan::call('make:controller', [
            'name' => $name.'Controller',
            '--resource' => true,
        ]);

        $this->info('All files generated successfully.');
    }
}