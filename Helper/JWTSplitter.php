<?php

namespace Lexik\Bundle\JWTAuthenticationBundle\Helper;

/**
 * JWTSplitter.
 *
 * @author Adam Lukacovic <adam@adamlukacovic.sk>
 *
 * @final
 */
class JWTSplitter
{
    public function __construct(private readonly string $jwt)
    {
        [$this->header, $this->payload, $this->signature] = explode('.', $this->jwt);
    }

    public function getParts(array $parts = []): string
    {
        if (!$parts) {
            return $this->jwt;
        }

        return implode('.', array_intersect_key(get_object_vars($this), array_flip($parts)));
    }
}
