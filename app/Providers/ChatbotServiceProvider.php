<?php
// app/Providers/ChatbotServiceProvider.php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\ChatbotService;

class ChatbotServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ChatbotService::class, function ($app) {
            return new ChatbotService();
        });
    }
}
