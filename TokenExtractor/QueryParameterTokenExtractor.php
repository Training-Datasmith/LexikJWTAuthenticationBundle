<?php

declare(strict_types=1);

namespace Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor;

use Symfony\Component\HttpFoundation\Request;

/**
 * QueryParameterTokenExtractor.
 *
 * @author Nicolas Cabot <n.cabot@lexik.fr>
 */
class QueryParameterTokenExtractor implements TokenExtractorInterface
{
    public function __construct(protected string $parameterName)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function extract(Request $request)
    {
        return $request->query->get($this->parameterName, false);
    }
}
