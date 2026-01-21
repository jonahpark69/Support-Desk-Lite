<?php

namespace App\Providers;

use App\Models\Comment;
use App\Models\Ticket;
use App\Policies\CommentPolicy;
use App\Policies\TicketPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Comment::class, CommentPolicy::class);
        Gate::policy(Ticket::class, TicketPolicy::class);

        RateLimiter::for('ticket-create', function (Request $request) {
            $key = $request->user()
                ? 'user:' . $request->user()->id
                : 'ip:' . $request->ip();

            return Limit::perMinute(5)->by($key);
        });
    }
}
