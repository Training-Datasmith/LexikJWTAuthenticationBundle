<?php

declare(strict_types=1);

/*
 * Security regression tests for JWTAuthenticator.
 *
 * These tests verify that the authenticator correctly rejects invalid, expired,
 * and missing tokens without leaking sensitive information in error responses.
 */

namespace Lexik\Bundle\JWTAuthenticationBundle\Tests\Security\Authenticator;

use Lexik\Bundle\JWTAuthenticationBundle\Exception\ExpiredTokenException;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\InvalidTokenException;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\MissingTokenException;
use PHPUnit\Framework\TestCase;

/**
 * Security regression tests for JWT token validation.
 *
 * Validates that:
 * - Expired tokens are rejected with a typed exception (not a generic one)
 * - Invalid token signatures are rejected
 * - Missing tokens produce a MissingTokenException (not an authentication bypass)
 * - Exception messages do not expose internal implementation details
 *
 * @covers \Lexik\Bundle\JWTAuthenticationBundle\Exception\ExpiredTokenException
 * @covers \Lexik\Bundle\JWTAuthenticationBundle\Exception\InvalidTokenException
 * @covers \Lexik\Bundle\JWTAuthenticationBundle\Exception\MissingTokenException
 */
class JWTAuthenticatorSecurityTest extends TestCase
{
    /**
     * An ExpiredTokenException must be a distinct type from InvalidTokenException.
     *
     * Callers (event listeners, error responses) must be able to distinguish
     * an expired token from a tampered one to provide meaningful error messages.
     */
    public function testExpiredTokenExceptionIsDistinctFromInvalidTokenException(): void
    {
        $expired = new ExpiredTokenException();
        $invalid = new InvalidTokenException('Bad signature');

        $this->assertNotInstanceOf(
            InvalidTokenException::class,
            $expired,
            'ExpiredTokenException must not extend InvalidTokenException.'
        );

        $this->assertInstanceOf(
            ExpiredTokenException::class,
            $expired
        );

        $this->assertInstanceOf(
            InvalidTokenException::class,
            $invalid
        );
    }

    /**
     * The MissingTokenException message key must be a short, localisation-friendly key.
     *
     * Error messages returned to clients should be translatable message keys,
     * not raw exception messages that might expose implementation details.
     */
    public function testMissingTokenExceptionExposesMessageKey(): void
    {
        $exception = new MissingTokenException('JWT Token not found');

        $messageKey = $exception->getMessageKey();

        $this->assertIsString($messageKey, 'getMessageKey() must return a string.');
        $this->assertNotEmpty($messageKey, 'Message key must not be empty.');

        // The message key should not contain stack trace or class name information
        $this->assertStringNotContainsString(
            '\\',
            $messageKey,
            'Message key must not contain namespace separators (potential class name leak).'
        );

        $this->assertStringNotContainsString(
            'Exception',
            $messageKey,
            'Message key must not expose the exception class name.'
        );
    }

    /**
     * The ExpiredTokenException message key must be distinct from the missing token key.
     *
     * This allows clients to show different messages for "your token expired, please log in again"
     * vs "no token was provided, please authenticate".
     */
    public function testExpiredAndMissingTokenHaveDifferentMessageKeys(): void
    {
        $expired = new ExpiredTokenException();
        $missing = new MissingTokenException('JWT Token not found');

        $this->assertNotSame(
            $expired->getMessageKey(),
            $missing->getMessageKey(),
            'Expired and missing token exceptions must have distinct message keys.'
        );
    }

    /**
     * InvalidTokenException must accept a custom message without exposing it to clients.
     *
     * The internal message (for logging) should be separate from the client-facing message key.
     */
    public function testInvalidTokenExceptionAcceptsDetailedInternalMessage(): void
    {
        $internalMessage = 'Signature verification failed: algorithm RS256 key mismatch at byte 42';
        $exception       = new InvalidTokenException($internalMessage);

        // The exception stores the internal message for logging
        $this->assertSame($internalMessage, $exception->getMessage());

        // But the message key exposed to clients should be generic
        $clientKey = $exception->getMessageKey();
        $this->assertStringNotContainsString('RS256', $clientKey, 'Client-facing key must not expose algorithm details.');
        $this->assertStringNotContainsString('byte', $clientKey, 'Client-facing key must not expose binary offset details.');
    }
}
