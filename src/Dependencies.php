<?php

declare(strict_types=1);

namespace GAState\Web\LTI\Slim;

use Firebase\JWT\CachedKeySet                     as FirebaseKeySet;
use GAState\Web\Slim\App                          as SlimApp;
use GAState\Web\LTI\Slim\Env                      as LTIEnv;
use GAState\Web\LTI\Slim\App                      as LTIApp;
use GAState\Web\LTI\Model\MessageFactory          as DefaultMessageFactory;
use GAState\Web\LTI\Model\MessageFactoryInterface as MessageFactory;
use GAState\Web\LTI\Util\JWKS                     as JWKS;
use GAState\Web\LTI\Util\JWKSFactory              as DefaultJWKSFactory;
use GAState\Web\LTI\Util\JWKSFactoryInterface     as JWKSFactory;
use GAState\Web\LTI\Util\JWTDecoder               as DefaultJWTDecoder;
use GAState\Web\LTI\Util\JWTDecoderInterface      as JWTDecoder;
use GAState\Web\LTI\Util\JWTEncoder               as DefaultJWTEncoder;
use GAState\Web\LTI\Util\JWTEncoderInterface      as JWTEncoder;
use GAState\Web\LTI\Util\KeyPairFactoryInterface  as KeyPairFactory;
use GAState\Web\LTI\Util\OpenSSLKeyPairFactory    as DefaultKeyPairFactory;

return (function () {
    /**
     * Default dependencies from Slim
     *
     * @var array<string,mixed> $slimDeps
     */
    $slimDeps = require(LTIEnv::getString(LTIEnv::SLIM_DIR) . "/Dependencies.php");


    /**
     * App dependencies / Slim overrides
     *
     * @var array<string,mixed> $appDeps
     */
    $appDeps = [
        'baseURI'          => LTIEnv::getString(LTIEnv::BASE_URI),
        'launchPrefix'     => 'launch-',
        'statePrefix'      => 'state-',
        'noncePrefix'      => 'nonce-',
        'issuer'           => LTIEnv::getString(LTIEnv::LTI_ISSUER),
        'clientID'         => LTIEnv::getString(LTIEnv::LTI_CLIENT_ID),
        'deploymentID'     => LTIEnv::getString(LTIEnv::LTI_DEPLOYMENT_ID),
        'toolLaunchURL'    => LTIEnv::getString(LTIEnv::LTI_LAUNCH_URL),
        'jwksCacheName'    => LTIEnv::getString(LTIEnv::JWKS_CACHE_NAME),
        'jwksExpiresAfter' => LTIEnv::getInt(LTIEnv::JWKS_EXPIRES_AFTER),
        'jwksRegenKeyAt'   => LTIEnv::getInt(LTIEnv::JWKS_REGEN_KEY_AT),
        'lmsKeySetURL'     => LTIEnv::getString(LTIEnv::LMS_KEYSET_URL),
        'lmsLoginURL'      => LTIEnv::getString(LTIEnv::LMS_LOGIN_URL),

        SlimApp::class        => \DI\get(LTIApp::class),
        MessageFactory::class => \DI\get(DefaultMessageFactory::class),
        JWKS::class           => \DI\factory([JWKSFactory::class, 'createJWKS']),
        JWKSFactory::class    => \DI\get(DefaultJWKSFactory::class),
        JWTDecoder::class     => \DI\get(DefaultJWTDecoder::class),
        JWTEncoder::class     => \DI\get(DefaultJWTEncoder::class),
        KeyPairFactory::class => \DI\get(DefaultKeyPairFactory::class),
        FirebaseKeySet::class => \DI\autowire()
            ->constructorParameter('jwksUri', \DI\get('lmsKeySetURL'))
            ->constructorParameter('expiresAfter', 360)
            ->constructorParameter('rateLimit', true),
        DefaultJWTEncoder::class => \DI\factory(function (JWKS $jwks) {
            return new DefaultJWTEncoder($jwks->getAvailableKeyPair());
        }),
    ];

    return array_merge($slimDeps, $appDeps);
})();
