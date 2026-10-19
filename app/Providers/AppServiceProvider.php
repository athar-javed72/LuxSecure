<?php

namespace App\Providers;

use App\Contracts\MessageChannel;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Language;
use App\Models\Page;
use App\Services\Messaging\HttpMessageChannel;
use App\Services\Messaging\LogMessageChannel;
use App\Support\BrandAssets;
use App\Support\CountryContext;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

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
        $this->configureRateLimiting();

        Paginator::useTailwind();

        Blade::directive('nonce', function () {
            return '<?php echo \'nonce="\'.e(\\Illuminate\\Support\\Facades\\Vite::cspNonce()).\'"\'; ?>';
        });

        View::composer(['include.footer', 'include.navbar', 'include.master', 'admin.layout', 'components.auth-card'], function ($view) {
            try {
                $ctx = CountryContext::class;
                $locale = app()->getLocale();
                $tagline = $ctx::setting('site.tagline.'.$locale)
                    ?: $ctx::setting('site.tagline', '');
                $view->with('siteName', $ctx::setting('site.name', config('app.name', 'Maskanoor')));
                $view->with('siteTagline', $tagline);
                $view->with('siteLogo', $ctx::setting('site.logo', BrandAssets::logo('horizontal', 'light')));
                $view->with('siteFavicon', $ctx::setting('site.favicon', BrandAssets::favicon()));
                $view->with('siteLegalName', $ctx::setting('site.legal_name', $ctx::setting('site.name', config('app.name', 'Maskanoor'))));
                $view->with('siteEmail', $ctx::contact('site.email') ?? $ctx::contact('site.support_email'));
                $view->with('sitePhone', $ctx::contact('site.phone'));
                $view->with('siteHours', $ctx::contact('site.hours'));
                $view->with('siteAddress', $ctx::contact('site.address'));
                $view->with('countries', Country::query()->where('is_active', true)->orderBy('sort_order')->get());
                $view->with('currencies', Currency::query()->where('is_active', true)->orderBy('code')->get());
                $view->with('languages', Language::query()->where('is_active', true)->orderBy('sort_order')->get());
                $view->with('legalPrivacy', Page::resolveLegal('privacy'));
                $view->with('legalTerms', Page::resolveLegal('terms'));
                $view->with('legalRefund', Page::resolveLegal('refund'));
            } catch (\Throwable) {
                $view->with('siteName', config('app.name', 'Maskanoor'));
                $view->with('siteTagline', '');
                $view->with('siteLogo', BrandAssets::logo('horizontal', 'light'));
                $view->with('siteFavicon', BrandAssets::favicon());
                $view->with('siteLegalName', config('app.name', 'Maskanoor'));
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

    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('api-auth', function (Request $request) {
            $email = Str::lower((string) $request->input('email'));

            return [
                Limit::perMinute(10)->by($request->ip()),
                Limit::perMinute(5)->by(($email !== '' ? $email : 'guest').'|'.$request->ip()),
            ];
        });

        RateLimiter::for('login', function (Request $request) {
            $email = Str::lower((string) $request->input('email'));

            return Limit::perMinute(5)->by($email.'|'.$request->ip());
        });

        RateLimiter::for('two-factor', function (Request $request) {
            $userKey = (string) ($request->session()->get('login.two_factor_user_id') ?: 'guest');

            return Limit::perMinute(6)->by($userKey.'|'.$request->ip());
        });

        RateLimiter::for('contact', function (Request $request) {
            return Limit::perMinute(6)->by($request->ip());
        });
    }
}
