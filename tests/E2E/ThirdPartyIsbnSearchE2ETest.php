<?php

namespace App\Tests\E2E;

/**
 * Covers the /search/proxy/jp_ndl guard rails. The successful upstream fetch
 * is deliberately not covered here: it calls the real NDL SRU API over the
 * network and is unsuitable for a deterministic suite. The 400 paths that
 * short-circuit before any HTTP call, plus the auth gate, are covered E2E.
 */
final class ThirdPartyIsbnSearchE2ETest extends AbstractPantherTestCase
{
    public function testAnonymousIsRedirectedToLogin(): void
    {
        $this->go('/search/proxy/jp_ndl?isbn=9784167105860');

        $this->assertOnPath('/login');
    }

    public function testMissingIsbnReturnsBadRequest(): void
    {
        $this->loginAsAdmin();

        $this->go('/search/proxy/jp_ndl');

        $this->assertStatusCode(400);
        $this->assertJsonBodyContains('ISBN parameter is required');
    }

    public function testInvalidIsbnFormatReturnsBadRequest(): void
    {
        $this->loginAsAdmin();

        $this->go('/search/proxy/jp_ndl?isbn=123');

        $this->assertStatusCode(400);
        $this->assertJsonBodyContains('Invalid ISBN format');
    }

    public function testHyphenatedIsbnIsRejected(): void
    {
        $this->loginAsAdmin();

        $this->go('/search/proxy/jp_ndl?isbn=978-4-16-710586-0');

        $this->assertStatusCode(400);
        $this->assertJsonBodyContains('Invalid ISBN format');
    }
}