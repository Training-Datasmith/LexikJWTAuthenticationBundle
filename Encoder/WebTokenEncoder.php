<?php

namespace Lexik\Bundle\JWTAuthenticationBundle\Encoder;

use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTDecodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTEncodeFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Exception\JWTFailureException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\WebToken\AccessTokenBuilder;
use Lexik\Bundle\JWTAuthenticationBundle\Services\WebToken\AccessTokenLoader;

/**
 * Json Web Token encoder/decoder based on the web-token framework.
 *
 * @author Florent Morsellis <florent.morselli@spomky-labs.com>
 */
final readonly class WebTokenEncoder implements HeaderAwareJWTEncoderInterface
{
    public function __construct(private ?AccessTokenBuilder $accessTokenBuilder, private ?AccessTokenLoader $accessTokenLoader)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function encode(array $payload, array $header = []): string
    {
        if (!$this->accessTokenBuilder) {
            throw new \LogicException('The access token issuance features are not enabled.');
        }

        try {
            return $this->accessTokenBuilder->build($header, $payload);
        } catch (\InvalidArgumentException $e) {
            throw new JWTEncodeFailureException(JWTEncodeFailureException::INVALID_CONFIG, 'An error occurred while trying to encode the JWT token. Please verify your configuration (private key/passphrase)', $e, $payload);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function decode($token): array
    {
        if (!$this->accessTokenLoader) {
            throw new \LogicException('The access token verification features are not enabled.');
        }

        return $this->accessTokenLoader->load($token);
    }
}
