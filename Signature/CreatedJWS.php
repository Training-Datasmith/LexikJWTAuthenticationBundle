<?php

declare(strict_types=1);

namespace Lexik\Bundle\JWTAuthenticationBundle\Signature;

/**
 * Object representation of a newly created JSON Web Signature.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
final readonly class CreatedJWS
{
    public function __construct(private string $token, private bool $signed)
    {
    }

    public function isSigned(): bool
    {
        return $this->signed;
    }

    public function getToken(): string
    {
        return $this->token;
    }
}
