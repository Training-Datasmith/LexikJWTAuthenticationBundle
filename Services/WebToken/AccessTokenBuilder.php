<?php

declare(strict_types=1);

namespace Lexik\Bundle\JWTAuthenticationBundle\Services\WebToken;

use Jose\Bundle\JoseFramework\Services\JWEBuilder;
use Jose\Bundle\JoseFramework\Services\JWEBuilderFactory;
use Jose\Bundle\JoseFramework\Services\JWSBuilder;
use Jose\Bundle\JoseFramework\Services\JWSBuilderFactory;
use Jose\Component\Core\JWK;
use Jose\Component\Encryption\Serializer\CompactSerializer as JweCompactSerializer;
use Jose\Component\Signature\Serializer\CompactSerializer as JwsCompactSerializer;
use Lexik\Bundle\JWTAuthenticationBundle\Event\BeforeJWEComputationEvent;
use Lexik\Bundle\JWTAuthenticationBundle\Events;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

final class AccessTokenBuilder
{
    /**
     * @var JWSBuilder
     */
    private $jwsBuilder;

    /**
     * @var null|JWEBuilder
     */
    private $jweBuilder;

    /**
     * @var JWK
     */
    private $signatureKey;

    /**
     * @var JWK|null
     */
    private $encryptionKey;

    private readonly string $signatureAlgorithm;

    private readonly ?string $keyEncryptionAlgorithm;

    private readonly ?string $contentEncryptionAlgorithm;

    public function __construct(
        private readonly EventDispatcherInterface $dispatcher,
        JWSBuilderFactory $jwsBuilderFactory,
        ?JWEBuilderFactory $jweBuilderFactory,
        string $signatureAlgorithm,
        string $signatureKey,
        ?string $keyEncryptionAlgorithm,
        ?string $contentEncryptionAlgorithm,
        ?string $encryptionKey
    ) {
        $this->jwsBuilder = $jwsBuilderFactory->create([$signatureAlgorithm]);
        if ($jweBuilderFactory !== null && $keyEncryptionAlgorithm !== null && $contentEncryptionAlgorithm !== null) {
            $this->jweBuilder = $jweBuilderFactory->create([$keyEncryptionAlgorithm, $contentEncryptionAlgorithm]);
        }
        $this->signatureKey = JWK::createFromJson($signatureKey);
        $this->encryptionKey = $encryptionKey ? JWK::createFromJson($encryptionKey) : null;
        $this->signatureAlgorithm = $signatureAlgorithm;
        $this->keyEncryptionAlgorithm = $keyEncryptionAlgorithm;
        $this->contentEncryptionAlgorithm = $contentEncryptionAlgorithm;
    }

    public function build(array $header, array $claims): string
    {
        $token = $this->buildJWS($header, $claims);

        if ($this->jweBuilder !== null) {
            return $this->buildJWE($claims, $token);
        }

        return $token;
    }

    /**
     * @param array<string, mixed> $header
     * @param array<string, mixed> $claims
     */
    private function buildJWS(array $header, array $claims): string
    {
        $header['alg'] = $this->signatureAlgorithm;
        if ($this->signatureKey->has('kid')) {
            $header['kid'] = $this->signatureKey->get('kid');
        }
        $jws = $this->jwsBuilder
            ->create()
            ->withPayload(json_encode($claims, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE))
            ->addSignature($this->signatureKey, $header)
            ->build()
        ;

        return (new JwsCompactSerializer())->serialize($jws);
    }

    /**
     * @param array<string, mixed> $header
     */
    private function buildJWE(array $claims, string $payload): string
    {
        $header = [
            'alg' => $this->keyEncryptionAlgorithm,
            'enc' => $this->contentEncryptionAlgorithm,
            'cty' => 'JWT',
            'typ' => 'JWT',
        ];
        if ($this->encryptionKey->has('kid')) {
            $header['kid'] = $this->encryptionKey->get('kid');
        }
        foreach (['exp', 'iat', 'nbf'] as $claim) {
            if (array_key_exists($claim, $claims)) {
                $header[$claim] = $claims[$claim];
            }
        }
        $event = $this->dispatcher->dispatch(new BeforeJWEComputationEvent($header), Events::BEFORE_JWE_COMPUTATION);
        $jwe = $this->jweBuilder
            ->create()
            ->withPayload($payload)
            ->withSharedProtectedHeader($event->getHeader())
            ->addRecipient($this->encryptionKey)
            ->build()
        ;
        return (new JweCompactSerializer())->serialize($jwe);
    }
}
