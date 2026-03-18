<?php

namespace Lexik\Bundle\JWTAuthenticationBundle\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\MissingClaimException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\BlockedTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\CacheItemPoolBlockedTokenManager;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\TokenExtractor\TokenExtractorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\DisabledException;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class BlockJWTListener
{
    public function __construct(private readonly BlockedTokenManagerInterface $blockedTokenManager, private readonly TokenExtractorInterface      $tokenExtractor, private readonly JWTTokenManagerInterface     $jwtManager)
    {
    }

    public function onLoginFailure(LoginFailureEvent $event): void
    {
        $exception = $event->getException();
        if (($exception instanceof DisabledException) || ($exception->getPrevious() instanceof DisabledException)) {
            $this->blockTokenFromRequest($event->getRequest());
        }
    }

    public function onLogout(LogoutEvent $event): void
    {
        $this->blockTokenFromRequest($event->getRequest());
    }

    private function blockTokenFromRequest(Request $request): void
    {
        $token = $this->tokenExtractor->extract($request);

        if ($token === false) {
            // There's nothing to block if the token isn't in the request
            return;
        }

        try {
            $payload = $this->jwtManager->parse($token);
        } catch (JWTDecodeFailureException $e) {
            // Ignore decode failures, this would mean the token is invalid anyway
            return;
        }

        try {
            $this->blockedTokenManager->add($payload);
        } catch (MissingClaimException) {
            // We can't block a token missing the claims our system requires, so silently ignore this one
        }
    }
}
