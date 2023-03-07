<?php

declare(strict_types=1);

namespace GAState\Web\LTI\Slim;

use GAState\Web\Slim\Env as SlimEnv;

class Env extends SlimEnv
{
    public const LTI_DIR            = 'LTI_DIR';
    public const LTI_ISSUER         = 'LTI_ISSUER';
    public const LTI_CLIENT_ID      = 'LTI_CLIENT_ID';
    public const LTI_DEPLOYMENT_ID  = 'LTI_DEPLOYMENT_ID';
    public const LTI_LAUNCH_URL     = 'LTI_LAUNCH_URL';
    public const LTI_LAUNCH_PREFIX  = 'LTI_LAUNCH_PREFIX';
    public const LTI_STATE_PREFIX   = 'LTI_STATE_PREFIX';
    public const LTI_NONCE_PREFIX   = 'LTI_NONCE_PREFIX';
    public const JWKS_CACHE_NAME    = 'JWKS_CACHE_NAME';
    public const JWKS_EXPIRES_AFTER = 'JWKS_EXPIRES_AFTER';
    public const JWKS_REGEN_KEY_AT  = 'JWKS_REGEN_KEY_AT';
    public const LMS_KEYSET_URL     = 'LMS_KEYSET_URL';
    public const LMS_LOGIN_URL      = 'LMS_LOGIN_URL';


    /**
     * @return void
     */
    protected static function setDefaults(): void
    {
        parent::setDefaults();

        $baseURI = static::getString(static::BASE_URI);
        static::setString(static::LTI_DIR, __DIR__);
        static::setString(static::LTI_ISSUER);
        static::setString(static::LTI_CLIENT_ID);
        static::setString(static::LTI_DEPLOYMENT_ID);
        static::setString(static::LTI_LAUNCH_URL, "{$baseURI}/lti/launch");
        static::setString(static::LTI_LAUNCH_PREFIX, "launch-");
        static::setString(static::LTI_STATE_PREFIX, "state-");
        static::setString(static::LTI_NONCE_PREFIX, "nonce-");
        static::setString(static::JWKS_CACHE_NAME, "lti-1.3-jwks");
        static::setInt(static::JWKS_EXPIRES_AFTER, 3600);
        static::setInt(static::JWKS_REGEN_KEY_AT, 360);
        static::setString(static::LMS_KEYSET_URL);
        static::setString(static::LMS_LOGIN_URL);
    }
}
