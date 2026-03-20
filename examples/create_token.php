<?php

declare(strict_types=1);

/**
 * LexikJWTAuthenticationBundle — token creation and usage example.
 *
 * This bundle requires a Symfony kernel. The code below shows the typical
 * patterns used in controllers and custom authenticators.
 *
 * --- config/packages/lexik_jwt_authentication.yaml ---
 *
 * lexik_jwt_authentication:
 *     secret_key: '%env(resolve:JWT_SECRET_KEY)%'
 *     public_key: '%env(resolve:JWT_PUBLIC_KEY)%'
 *     pass_phrase: '%env(JWT_PASSPHRASE)%'
 *     token_ttl: 3600
 *
 * --- Controller: manual token creation ---
 *
 * use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
 * use Symfony\Component\Security\Core\User\UserInterface;
 *
 * class AuthController
 * {
 *     public function token(
 *         UserInterface $user,
 *         JWTTokenManagerInterface $jwtManager
 *     ): JsonResponse {
 *         return new JsonResponse(['token' => $jwtManager->create($user)]);
 *     }
 * }
 *
 * --- Customising the JWT payload via an event listener ---
 *
 * use Lexik\Bundle\JWTAuthenticationBundle\Events;
 * use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
 * use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
 *
 * #[AsEventListener(event: Events::JWT_CREATED)]
 * class JWTCreatedListener
 * {
 *     public function onJWTCreated(JWTCreatedEvent $event): void
 *     {
 *         $payload = $event->getData();
 *         $payload['ip'] = $_SERVER['REMOTE_ADDR'] ?? null;
 *         $event->setData($payload);
 *     }
 * }
 *
 * --- API request with JWT ---
 *
 * GET /api/protected
 * Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...
 */

echo 'LexikJWTAuthenticationBundle requires a Symfony kernel.' . PHP_EOL;
echo 'See the docblock above for usage patterns.' . PHP_EOL;
