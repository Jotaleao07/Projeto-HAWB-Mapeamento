<?php

namespace App\Providers;

use App\Models\Conversation;
use App\Policies\ConversationPolicy;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Usa a view de paginação do projeto em vez do preset Tailwind.
        Paginator::defaultView('pagination.hawb');

        Gate::policy(Conversation::class, ConversationPolicy::class);
    }
}
