<?php

namespace Tests\Feature;

use App\Support\SafeLoginRedirect;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Support\CreatesUsers;
use Tests\Support\SeedsRbac;
use Tests\TestCase;

class SafeLoginRedirectTest extends TestCase
{
    use CreatesUsers;
    use RefreshDatabase;
    use SeedsRbac;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedRbac();
    }

    public function test_valid_relative_path_is_stored_as_intended(): void
    {
        $this->get(route('login.form', [
            'redirect' => '/hospitality?destination=Islamabad&guests=2',
        ]))->assertOk();

        $this->assertSame(
            url('/hospitality?destination=Islamabad&guests=2'),
            session('url.intended')
        );
    }

    #[DataProvider('dangerousRedirectProvider')]
    public function test_dangerous_redirects_are_rejected(string $redirect): void
    {
        $this->assertNull(SafeLoginRedirect::path($redirect));

        $this->withSession([])->get(route('login.form', [
            'redirect' => $redirect,
        ]))->assertOk();

        $this->assertNull(session('url.intended'));
    }

    public static function dangerousRedirectProvider(): array
    {
        return [
            'https absolute' => ['https://evil.com'],
            'http absolute' => ['http://evil.com/phish'],
            'protocol relative' => ['//evil.com'],
            'backslash trick' => ['/\\evil.com'],
            'javascript scheme' => ['javascript:alert(1)'],
            'data scheme' => ['data:text/html,hi'],
        ];
    }

    public function test_login_honors_safe_intended_and_ignores_evil(): void
    {
        $user = $this->makeClient(['email' => 'redir@example.com']);

        $this->get(route('login.form', ['redirect' => '/hospitality?guests=2']))->assertOk();

        $this->post(route('login'), [
            'email' => 'redir@example.com',
            'password' => 'password',
        ])->assertRedirect(url('/hospitality?guests=2'));

        $this->assertAuthenticatedAs($user);

        auth()->logout();

        $this->get(route('login.form', ['redirect' => 'https://evil.com']))->assertOk();

        $this->post(route('login'), [
            'email' => 'redir@example.com',
            'password' => 'password',
        ])->assertRedirect(route('profile'));
    }
}
