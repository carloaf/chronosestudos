<?php

namespace App\Providers;

use App\Models\Topic;
use App\Observers\TopicObserver;
use App\Services\MarkdownSanitizer;
use App\Services\SpacedRepetitionService;
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
    }
}
