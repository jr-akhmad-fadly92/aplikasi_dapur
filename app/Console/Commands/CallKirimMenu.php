<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class CallKirimMenu extends Command
{
    protected $signature = 'route:kirim-menu';
    protected $description = 'Panggil route /kirim-menu tiap menit';

    public function handle()
    {
        $url = config('app.url') . '/kirim-menu';

        $response = Http::get($url);

        $this->info('Kirim-menu dipanggil. Response: ' . $response->body());
    }
}
