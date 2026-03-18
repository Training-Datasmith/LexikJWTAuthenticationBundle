<?php

namespace Lexik\Bundle\JWTAuthenticationBundle\Event;

use Symfony\Contracts\EventDispatcher\Event;

class JWTEncodedEvent extends Event
{
    public function __construct(private readonly string $jwtString)
    {
    }

    public function getJWTString(): string
    {
        return $this->jwtString;
    }
}
