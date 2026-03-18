<?php

declare(strict_types=1);

namespace Lexik\Bundle\JWTAuthenticationBundle\Services\PayloadEnrichment;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserInterface;

class NullEnrichmentTest extends TestCase
{
    public function testEnrich(): void
    {
        $payload = ['foo' => 'bar'];
        $enrichment = new NullEnrichment();
        $enrichment->enrich($this->createMock(UserInterface::class), $payload);

        $this->assertEquals(['foo' => 'bar'], $payload);
    }
}
