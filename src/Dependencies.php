<?php

declare(strict_types=1);

namespace GAState\Web\LTI\Slim;

use Firebase\JWT\CachedKeySet                     as FirebaseKeySet;
use GAState\Web\LTI\Action\LaunchAction           as LaunchAction;
use GAState\Web\LTI\Action\LoginAction            as LoginAction;
use GAState\Web\LTI\Controller\LaunchController   as LaunchController;
use GAState\Web\LTI\Controller\LoginController    as LoginController;
use GAState\Web\LTI\Model\MessageFactory          as DefaultMessageFactory;
use GAState\Web\LTI\Model\MessageFactoryInterface as MessageFactory;
use GAState\Web\LTI\Slim\App                      as LTIBaseApp;
use GAState\Web\LTI\Slim\Env                      as Env;
use GAState\Web\LTI\Util\JWKS                     as JWKS;
use GAState\Web\LTI\Util\JWKSFactory              as DefaultJWKSFactory;
use GAState\Web\LTI\Util\JWKSFactoryInterface     as JWKSFactory;
use GAState\Web\LTI\Util\JWTDecoder               as DefaultJWTDecoder;
use GAState\Web\LTI\Util\JWTDecoderInterface      as JWTDecoder;
use GAState\Web\LTI\Util\JWTEncoder               as DefaultJWTEncoder;
use GAState\Web\LTI\Util\JWTEncoderInterface      as JWTEncoder;
use GAState\Web\LTI\Util\KeyPairFactoryInterface  as KeyPairFactory;
use GAState\Web\LTI\Util\OpenSSLKeyPairFactory    as DefaultKeyPairFactory;
use GAState\Web\Slim\App                          as BaseApp;

return (function () {
    /**
     * Default dependencies
     *
     * @var array<string,mixed> $defaultDeps
     */
    $defaultDeps = require(Env::getString(Env::SLIM_DIR) . "/Dependencies.php");

    /**
     * Environment variables
     *
     * @var array<string,mixed> $envDeps
     */
    $envDeps = [
        'launchPrefix' => Env::getString(Env::LTI_LAUNCH_PREFIX),
        'statePrefix' => Env::getString(Env::LTI_STATE_PREFIX),
        'noncePrefix' => Env::getString(Env::LTI_NONCE_PREFIX),
        'issuer' => Env::getString(Env::LTI_ISSUER),
        'clientID' => Env::getString(Env::LTI_CLIENT_ID),
        'deploymentID' => Env::getString(Env::LTI_DEPLOYMENT_ID),
        'toolLaunchURL' => Env::getString(Env::LTI_LAUNCH_URL),
        'jwksCacheName' => Env::getString(Env::JWKS_CACHE_NAME),
        'jwksExpiresAfter' => Env::getInt(Env::JWKS_EXPIRES_AFTER),
        'jwksRegenKeyAt' => Env::getInt(Env::JWKS_REGEN_KEY_AT),
        'lmsKeySetURL' => Env::getString(Env::LMS_KEYSET_URL),
        'lmsLoginURL' => Env::getString(Env::LMS_LOGIN_URL),
    ];

    /**
     * App dependencies
     *
     * @var array<string,mixed> $appDeps
     */
    $appDeps = [
        BaseApp::class => \DI\get(LTIBaseApp::class),
        FirebaseKeySet::class => \DI\autowire()
            ->constructorParameter('jwksUri', \DI\get('lmsKeySetURL'))
            ->constructorParameter('expiresAfter', 360)
            ->constructorParameter('rateLimit', true),
        JWKS::class => \DI\factory([JWKSFactory::class, 'createJWKS']),
        JWKSFactory::class => \DI\autowire(DefaultJWKSFactory::class)
            ->constructorParameter('jwksExpiresAfter', \DI\get('jwksExpiresAfter'))
            ->constructorParameter('jwksRegenKeyAt', \DI\get('jwksRegenKeyAt'))
            ->constructorParameter('jwksCacheName', \DI\get('jwksCacheName')),
        JWTDecoder::class => \DI\get(DefaultJWTDecoder::class),
        JWTEncoder::class => \DI\factory(function (JWKS $jwks) {
            return new DefaultJWTEncoder($jwks->getAvailableKeyPair());
        }),
        KeyPairFactory::class => \DI\get(DefaultKeyPairFactory::class),
        LaunchAction::class => \DI\autowire()
            ->constructorParameter('issuer', \DI\get('issuer'))
            ->constructorParameter('clientID', \DI\get('clientID'))
            ->constructorParameter('deploymentID', \DI\get('deploymentID')),
        LaunchController::class => \DI\autowire()
            ->constructorParameter('baseURI', \DI\get('baseURI'))
            ->constructorParameter('launchPrefix', \DI\get('launchPrefix'))
            ->constructorParameter('statePrefix', \DI\get('statePrefix'))
            ->constructorParameter('noncePrefix', \DI\get('noncePrefix')),
        LoginAction::class => \DI\autowire()
            ->constructorParameter('lmsLoginURL', \DI\get('lmsLoginURL'))
            ->constructorParameter('issuer', \DI\get('issuer'))
            ->constructorParameter('clientID', \DI\get('clientID'))
            ->constructorParameter('deploymentID', \DI\get('deploymentID'))
            ->constructorParameter('toolLaunchURL', \DI\get('toolLaunchURL')),
        LoginController::class => \DI\autowire()
            ->constructorParameter('baseURI', \DI\get('baseURI'))
            ->constructorParameter('statePrefix', \DI\get('statePrefix'))
            ->constructorParameter('noncePrefix', \DI\get('noncePrefix')),
        MessageFactory::class => \DI\get(DefaultMessageFactory::class),
    ];

    return array_merge($defaultDeps, $envDeps, $appDeps);
})();
