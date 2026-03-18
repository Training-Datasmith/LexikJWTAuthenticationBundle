<?php

namespace Lexik\Bundle\JWTAuthenticationBundle\Exception;

/**
 * Base class for exceptions thrown during JWT creation/loading.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class JWTFailureException extends \Exception
{
    public function __construct(private readonly string $reason, string $message, ?\Throwable $previous = null, private readonly ?array $payload = null)
    {
        parent::__construct($message, 0, $previous);
    }

    public function getReason(): string
    {
        return $this->reason;
    }

    public function getPayload(): ?array
    {
        return $this->payload;
    }
}
