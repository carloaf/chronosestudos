<?php

namespace App\Providers;

use App\Models\Topic;
use App\Models\User;
use App\Observers\TopicObserver;
use App\Services\MarkdownSanitizer;
use App\Services\SpacedRepetitionService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(SpacedRepetitionService::class);
        $this->app->singleton(MarkdownSanitizer::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Topic::observe(TopicObserver::class);

        Gate::before(function (User $user, string $ability, array $arguments = []) {
            // Deixa a UserPolicy decidir quando o alvo é outro usuário
            // (protege admins fixos, impede auto-bloqueio/rebaixamento, etc.).
            if (! empty($arguments) && $arguments[0] instanceof User) {
                return null;
            }

            return $user->isAdmin() ? true : null;
        });
    }
}
