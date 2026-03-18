<?php

declare(strict_types=1);

namespace Lexik\Bundle\JWTAuthenticationBundle\Subscriber;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Symfony\Component\Clock\Clock;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final readonly class AdditionalAccessTokenClaimsAndHeaderSubscriber implements EventSubscriberInterface
{
    public function __construct(private ?int $ttl)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            Events::JWT_CREATED => [
                ['addClaims'],
            ],
        ];
    }

    public function addClaims(JWTCreatedEvent $event): void
    {
        $now = Clock::get()->now()->getTimestamp();
        $claims = [
            'jti' => uniqid('', true),
            'iat' => $now,
            'nbf' => $now,
        ];
        $data = $event->getData();
        if (!array_key_exists('exp', $data) && $this->ttl > 0) {
            $claims['exp'] = $now + $this->ttl;
        }
        $event->setData(array_merge($claims, $data));
    }
}
