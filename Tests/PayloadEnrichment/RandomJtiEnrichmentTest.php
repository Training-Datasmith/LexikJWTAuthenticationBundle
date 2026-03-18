<?php

declare(strict_types=1);

namespace Lexik\Bundle\JWTAuthenticationBundle\Services\PayloadEnrichment;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserInterface;

class RandomJtiEnrichmentTest extends TestCase
{
    public function testEnrich(): void
    {
        $payload = ['foo' => 'bar'];
        $enrichment = new RandomJtiEnrichment();
        $enrichment->enrich($this->createMock(UserInterface::class), $payload);

        $this->assertArrayHasKey('jti', $payload);
        $this->assertIsString($payload['jti']);
        $this->assertArrayHasKey('foo', $payload);
    }
}
