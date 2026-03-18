<?php

declare(strict_types=1);

namespace Lexik\Bundle\JWTAuthenticationBundle\Services\PayloadEnrichment;

use Lexik\Bundle\JWTAuthenticationBundle\Services\PayloadEnrichmentInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\User\UserInterface;

class ChainEnrichmentTest extends TestCase
{
    public function testEnrich(): void
    {
        $payload = ['foo' => 'bar'];

        $enrichmentFoo = new class () implements PayloadEnrichmentInterface {
            public function enrich(UserInterface $user, array &$payload): void
            {
                $payload['foo'] = 'baz';
            }
        };

        $enrichmentBar = new class () implements PayloadEnrichmentInterface {
            public function enrich(UserInterface $user, array &$payload): void
            {
                $payload['bar'] = 'qux';
            }
        };

        $chainEnrichment = new ChainEnrichment([$enrichmentFoo, $enrichmentBar]);
        $chainEnrichment->enrich($this->createMock(UserInterface::class), $payload);

        $this->assertEquals(['foo' => 'baz', 'bar' => 'qux'], $payload);
    }
}
