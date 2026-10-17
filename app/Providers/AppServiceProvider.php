<?php

namespace App\Providers;

use App\Contracts\MessageChannel;
use App\Services\Messaging\LogMessageChannel;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(MessageChannel::class, function () {
            return new LogMessageChannel('sms');
        });

        $this->app->bind('messaging.whatsapp', function () {
            return new LogMessageChannel('whatsapp');
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useTailwind();

        View::composer(['include.footer', 'include.navbar', 'include.master'], function ($view) {
            try {
                $view->with('siteName', \App\Models\Setting::getValue('site.name', config('app.name', 'LuxSecure')));
                $view->with('siteEmail', \App\Models\Setting::getValue('site.email', 'hello@luxsecure.example'));
                $view->with('sitePhone', \App\Models\Setting::getValue('site.phone', '+92 300 0000000'));
            } catch (\Throwable) {
                $view->with('siteName', config('app.name', 'LuxSecure'));
                $view->with('siteEmail', 'hello@luxsecure.example');
                $view->with('sitePhone', '+92 300 0000000');
            }
        });
    }
}
