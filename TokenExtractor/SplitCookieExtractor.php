<?php

namespace Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor;

use Symfony\Component\HttpFoundation\Request;

/**
 * SplitCookieExtractor.
 *
 * @author Adam Lukacovic <adam@adamlukacovic.sk>
 */
class SplitCookieExtractor implements TokenExtractorInterface
{
    public function __construct(private readonly array $cookies)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function extract(Request $request): false|string
    {
        $jwtCookies = [];

        foreach ($this->cookies as $cookie) {
            $jwtCookies[] = $request->cookies->get($cookie, false);
        }

        if (count($this->cookies) !== count(array_filter($jwtCookies))) {
            return false;
        }

        return implode('.', $jwtCookies);
    }
}
