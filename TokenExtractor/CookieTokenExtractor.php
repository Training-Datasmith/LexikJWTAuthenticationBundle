<?php

declare(strict_types=1);

namespace Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor;

use Symfony\Component\HttpFoundation\Request;

/**
 * CookieTokenExtractor.
 *
 * @author Nicolas Cabot <n.cabot@lexik.fr>
 */
class CookieTokenExtractor implements TokenExtractorInterface
{
    public function __construct(protected string $name)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function extract(Request $request)
    {
        return $request->cookies->get($this->name, false);
    }
}
