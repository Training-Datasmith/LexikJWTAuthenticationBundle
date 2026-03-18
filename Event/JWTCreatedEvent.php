<?php

declare(strict_types=1);

namespace Lexik\Bundle\JWTAuthenticationBundle\Event;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * JWTCreatedEvent.
 */
class JWTCreatedEvent extends Event
{
    protected UserInterface $user;

    public function __construct(protected array $data, UserInterface $user, protected array $header = [])
    {
        $this->user = $user;
    }

    public function getHeader(): array
    {
        return $this->header;
    }

    public function setHeader(array $header): void
    {
        $this->header = $header;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }
}
