<?php

declare(strict_types=1);

namespace Lexik\Bundle\JWTAuthenticationBundle\Exception;

use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * User not found during authentication.
 *
 * @author Robin Chalas <robin.chalas@gmail.com>
 */
class UserNotFoundException extends AuthenticationException
{
    public function __construct(private readonly string $userIdentityField, private readonly string $identity)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getMessageKey(): string
    {
        return sprintf('Unable to load an user with property "%s" = "%s". If the user identity has changed, you must renew the token. Otherwise, verify that the "lexik_jwt_authentication.user_identity_field" config option is correctly set.', $this->userIdentityField, $this->identity);
    }
}
