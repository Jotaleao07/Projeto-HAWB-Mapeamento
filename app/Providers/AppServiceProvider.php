<?php

namespace App\Providers;

use App\Models\Conversation;
use App\Models\Publication;
use App\Policies\ConversationPolicy;
use App\Policies\PublicationPolicy;
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
        Gate::policy(Publication::class, PublicationPolicy::class);
    }
}
