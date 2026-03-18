<?php

declare(strict_types=1);

namespace Lexik\Bundle\JWTAuthenticationBundle\Event;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * JWTAuthenticatedEvent.
 */
class JWTAuthenticatedEvent extends Event
{
    protected TokenInterface $token;

    public function __construct(protected array $payload, TokenInterface $token)
    {
        $this->token = $token;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function setPayload(array $payload): void
    {
        $this->payload = $payload;
    }

    public function getToken(): TokenInterface
    {
        return $this->token;
    }
}
