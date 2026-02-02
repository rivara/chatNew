<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Broadcast::routes([
        //     'middleware' => ['web'], // 🔥 SIN auth
        // ]);Broadcast::routes();
        Broadcast::routes();
        require base_path('routes/channels.php');
    }
}
