<?php

namespace App\Providers;

use App\Contracts\MessageChannel;
use App\Services\Messaging\HttpMessageChannel;
use App\Services\Messaging\LogMessageChannel;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
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
            $driver = (string) config('luxsecure.messaging.default', 'log');

            if ($driver === 'http') {
                return new HttpMessageChannel(
                    (string) config('luxsecure.messaging.http.url', ''),
                    config('luxsecure.messaging.http.key'),
                    config('luxsecure.messaging.http.sender'),
                );
            }

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

        Blade::directive('nonce', function () {
            return '<?php echo \'nonce="\'.e(\\Illuminate\\Support\\Facades\\Vite::cspNonce()).\'"\'; ?>';
        });

        View::composer(['include.footer', 'include.navbar', 'include.master'], function ($view) {
            try {
                $view->with('siteName', \App\Models\Setting::getValue('site.name', config('app.name', 'LuxSecure')));
                $view->with('siteEmail', \App\Models\Setting::getValue('site.email', '[PLACEHOLDER: CONTACT_EMAIL]'));
                $view->with('sitePhone', \App\Models\Setting::getValue('site.phone', '[PLACEHOLDER: CONTACT_PHONE]'));
                $view->with('siteHours', \App\Models\Setting::getValue('site.hours', '[PLACEHOLDER: BUSINESS_HOURS]'));
            } catch (\Throwable) {
                $view->with('siteName', config('app.name', 'LuxSecure'));
                $view->with('siteEmail', '[PLACEHOLDER: CONTACT_EMAIL]');
                $view->with('sitePhone', '[PLACEHOLDER: CONTACT_PHONE]');
                $view->with('siteHours', '[PLACEHOLDER: BUSINESS_HOURS]');
            }
        });
    }
}
