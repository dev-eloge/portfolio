<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

/** Services initiaux (repris de config/portfolio.php). Ensuite, ils se gèrent dans l'admin. */
class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        if (Service::query()->exists()) {
            return; // ne jamais écraser ce qui a été saisi dans l'admin
        }

        foreach (config('portfolio.services', []) as $order => $service) {
            Service::create([...$service, 'order' => $order + 1]);
        }
    }
}
