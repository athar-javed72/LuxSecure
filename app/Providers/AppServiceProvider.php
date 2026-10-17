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
                $ctx = \App\Support\CountryContext::class;
                $view->with('siteName', $ctx::setting('site.name', config('app.name', 'LuxSecure')));
                $view->with('siteEmail', $ctx::contact('site.email') ?? $ctx::contact('site.support_email'));
                $view->with('sitePhone', $ctx::contact('site.phone'));
                $view->with('siteHours', $ctx::contact('site.hours'));
                $view->with('siteAddress', $ctx::contact('site.address'));
                $view->with('countries', \App\Models\Country::query()->where('is_active', true)->orderBy('sort_order')->get());
                $view->with('currencies', \App\Models\Currency::query()->where('is_active', true)->orderBy('code')->get());
                $view->with('languages', \App\Models\Language::query()->where('is_active', true)->orderBy('sort_order')->get());
                $view->with('legalPrivacy', \App\Models\Page::resolveLegal('privacy'));
                $view->with('legalTerms', \App\Models\Page::resolveLegal('terms'));
                $view->with('legalRefund', \App\Models\Page::resolveLegal('refund'));
            } catch (\Throwable) {
                $view->with('siteName', config('app.name', 'LuxSecure'));
                $view->with('siteEmail', null);
                $view->with('sitePhone', null);
                $view->with('siteHours', null);
                $view->with('siteAddress', null);
                $view->with('countries', collect());
                $view->with('currencies', collect());
                $view->with('languages', collect());
                $view->with('legalPrivacy', null);
                $view->with('legalTerms', null);
                $view->with('legalRefund', null);
            }
        });
    }
}
