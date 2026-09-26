<?php

namespace App\Tests\E2E;

final class AuthenticationE2ETest extends AbstractPantherTestCase
{
    public function testAnonymousVisitingProtectedPageIsRedirectedToLogin(): void
    {
        $this->go('/author/new');

        $this->assertOnPath('/login');
        $this->assertPageContains('Please sign in');
    }

    public function testLoginWithInvalidCredentialsShowsError(): void
    {
        $this->go('/login');
        $this->client->submitForm('Sign in', [
            '_username' => self::ADMIN_EMAIL,
            '_password' => 'wrong-password',
        ]);

        $this->assertOnPath('/login');
        $this->assertPageContains('Invalid credentials');
    }

    public function testLoginWithValidCredentialsSucceeds(): void
    {
        $this->loginAsAdmin();

        $this->assertOnPath('/');
        $this->assertPageContains('BookShelf');
        // Only reachable proof of session: the login screen shows the account once logged in.
        $this->go('/login');
        $this->assertPageContains('You are logged in as admin@example.com');
    }

    public function testLogoutClearsSession(): void
    {
        $this->loginAsAdmin();
        $this->logout();

        $this->assertOnPath('/');
        $this->go('/author/new');
        $this->assertOnPath('/login');
    }
}