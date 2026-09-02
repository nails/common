<?php

namespace Tests\Common\Service;

use Nails\Common\Service\EnvironmentProtection;
use Nails\Config;
use Nails\Environment;
use PHPUnit\Framework\TestCase;

class EnvironmentProtectionTest extends TestCase
{
    const PASSWORD = 'correct-horse';
    const KEY      = 'test-private-key';

    // --------------------------------------------------------------------------

    /**
     * Returns a service configured with a single known credential
     *
     * @param array|null  $aCredentials Overrides the default credentials
     * @param string|null $sEnvironment Overrides the default environment
     * @param int|null    $iTtl         Overrides the default TTL
     * @param string|null $sKey         Overrides the default signing key
     */
    private function service(
        ?array $aCredentials = null,
        ?string $sEnvironment = null,
        ?int $iTtl = null,
        ?string $sKey = null
    ): EnvironmentProtection {
        return new EnvironmentProtection(
            $aCredentials ?? ['alice' => hash('sha256', self::PASSWORD)],
            [],
            $sEnvironment ?? Environment::ENV_STAGE,
            $iTtl ?? EnvironmentProtection::DEFAULT_TTL,
            $sKey ?? self::KEY
        );
    }

    // --------------------------------------------------------------------------

    /**
     * @covers EnvironmentProtection::isProtected
     */
    public function testIsProtectedIsFalseWithoutCredentials(): void
    {
        $this->assertFalse($this->service([])->isProtected());
    }

    // --------------------------------------------------------------------------

    /**
     * @covers EnvironmentProtection::isProtected
     */
    public function testIsProtectedIsTrueWithCredentials(): void
    {
        $this->assertTrue($this->service()->isProtected());
    }

    // --------------------------------------------------------------------------

    /**
     * @covers EnvironmentProtection::verifyCredentials
     */
    public function testVerifyCredentialsAcceptsLegacySha256Hash(): void
    {
        $this->assertTrue($this->service()->verifyCredentials('alice', self::PASSWORD));
    }

    // --------------------------------------------------------------------------

    /**
     * @covers EnvironmentProtection::verifyCredentials
     */
    public function testVerifyCredentialsAcceptsPasswordHash(): void
    {
        $oService = $this->service(['alice' => password_hash(self::PASSWORD, PASSWORD_DEFAULT)]);
        $this->assertTrue($oService->verifyCredentials('alice', self::PASSWORD));
    }

    // --------------------------------------------------------------------------

    /**
     * @covers EnvironmentProtection::verifyCredentials
     */
    public function testVerifyCredentialsRejectsBadPassword(): void
    {
        $this->assertFalse($this->service()->verifyCredentials('alice', 'wrong'));
    }

    // --------------------------------------------------------------------------

    /**
     * @covers EnvironmentProtection::verifyCredentials
     */
    public function testVerifyCredentialsRejectsUnknownUser(): void
    {
        $this->assertFalse($this->service()->verifyCredentials('bob', self::PASSWORD));
    }

    // --------------------------------------------------------------------------

    /**
     * @covers EnvironmentProtection::verifyCredentials
     */
    public function testVerifyCredentialsRejectsNulls(): void
    {
        $this->assertFalse($this->service()->verifyCredentials(null, null));
    }

    // --------------------------------------------------------------------------

    /**
     * @covers EnvironmentProtection::generateToken
     * @covers EnvironmentProtection::validateToken
     */
    public function testTokenRoundTrip(): void
    {
        $oService = $this->service();
        $this->assertSame('alice', $oService->validateToken($oService->generateToken('alice')));
    }

    // --------------------------------------------------------------------------

    /**
     * @covers EnvironmentProtection::validateToken
     */
    public function testValidateTokenRejectsExpiredToken(): void
    {
        $oService = $this->service(null, null, 3600);
        $sToken   = $oService->generateToken('alice', time() - 7200);

        $this->assertNull($oService->validateToken($sToken));
    }

    // --------------------------------------------------------------------------

    /**
     * @covers EnvironmentProtection::validateToken
     */
    public function testValidateTokenRejectsTamperedPayload(): void
    {
        $oService = $this->service(['alice' => hash('sha256', self::PASSWORD), 'bob' => 'nope']);

        [$sPayload, $sSignature] = explode('.', $oService->generateToken('alice'));

        $aPayload      = json_decode(base64_decode(strtr($sPayload, '-_', '+/')), true);
        $aPayload['u'] = 'bob';

        $sTampered = rtrim(strtr(base64_encode(json_encode($aPayload)), '+/', '-_'), '=');

        $this->assertNull($oService->validateToken($sTampered . '.' . $sSignature));
    }

    // --------------------------------------------------------------------------

    /**
     * @covers EnvironmentProtection::validateToken
     */
    public function testValidateTokenRejectsTokenSignedWithAnotherKey(): void
    {
        $sToken = $this->service(null, null, null, 'another-key')->generateToken('alice');

        $this->assertNull($this->service()->validateToken($sToken));
    }

    // --------------------------------------------------------------------------

    /**
     * @covers EnvironmentProtection::validateToken
     */
    public function testValidateTokenRejectsTokenFromAnotherEnvironment(): void
    {
        $sToken = $this->service(null, Environment::ENV_DEV)->generateToken('alice');

        $this->assertNull($this->service(null, Environment::ENV_STAGE)->validateToken($sToken));
    }

    // --------------------------------------------------------------------------

    /**
     * @covers EnvironmentProtection::validateToken
     */
    public function testValidateTokenRejectsTokenWhenPasswordChanges(): void
    {
        $sToken = $this->service()->generateToken('alice');

        $oService = $this->service(['alice' => hash('sha256', 'a-new-password')]);

        $this->assertNull($oService->validateToken($sToken));
    }

    // --------------------------------------------------------------------------

    /**
     * @covers EnvironmentProtection::validateToken
     */
    public function testValidateTokenRejectsTokenWhenUserIsRemoved(): void
    {
        $sToken = $this->service()->generateToken('alice');

        $this->assertNull($this->service(['bob' => 'nope'])->validateToken($sToken));
    }

    // --------------------------------------------------------------------------

    /**
     * @covers EnvironmentProtection::validateToken
     */
    public function testValidateTokenRejectsMalformedTokens(): void
    {
        $oService = $this->service();

        $this->assertNull($oService->validateToken(null));
        $this->assertNull($oService->validateToken(''));
        $this->assertNull($oService->validateToken('not-a-token'));
        $this->assertNull($oService->validateToken('too.many.parts'));
        $this->assertNull($oService->validateToken('.'));
    }

    // --------------------------------------------------------------------------

    /**
     * @covers EnvironmentProtection::shouldRenewToken
     */
    public function testShouldRenewTokenIsFalseForFreshToken(): void
    {
        $oService = $this->service();

        $this->assertFalse($oService->shouldRenewToken($oService->generateToken('alice')));
    }

    // --------------------------------------------------------------------------

    /**
     * @covers EnvironmentProtection::shouldRenewToken
     */
    public function testShouldRenewTokenIsTruePastHalfLife(): void
    {
        $oService = $this->service(null, null, 3600);
        $sToken   = $oService->generateToken('alice', time() - 2400);

        $this->assertTrue($oService->shouldRenewToken($sToken));
        $this->assertSame('alice', $oService->validateToken($sToken));
    }

    // --------------------------------------------------------------------------

    /**
     * @covers EnvironmentProtection::shouldRenewToken
     */
    public function testShouldRenewTokenIsFalseForInvalidToken(): void
    {
        $this->assertFalse($this->service()->shouldRenewToken('not-a-token'));
    }

    // --------------------------------------------------------------------------

    /**
     * @covers EnvironmentProtection::generateToken
     */
    public function testTokenIsCookieSafe(): void
    {
        $sToken = $this->service()->generateToken('alice');

        $this->assertMatchesRegularExpression('/^[A-Za-z0-9\-_]+\.[A-Za-z0-9\-_]+$/', $sToken);
    }

    // --------------------------------------------------------------------------

    /**
     * @covers EnvironmentProtection::getCredentials
     */
    public function testCredentialsAreResolvedFromConfig(): void
    {
        Config::set(EnvironmentProtection::CONFIG_CREDENTIALS . '_' . Environment::ENV_STAGE, ['alice' => 'a-hash']);

        $oService = new EnvironmentProtection(null, null, Environment::ENV_STAGE);

        $this->assertSame(['alice' => 'a-hash'], $oService->getCredentials());
    }

    // --------------------------------------------------------------------------

    /**
     * @covers EnvironmentProtection::getCredentials
     */
    public function testCredentialsResolvedFromConfigAreCastToArray(): void
    {
        //  Mirrors the object returned when decoding a protect.*.users.json file
        Config::set(EnvironmentProtection::CONFIG_CREDENTIALS . '_' . Environment::ENV_HTTP_TEST, json_decode('{"alice":"a-hash"}'));

        $oService = new EnvironmentProtection(null, null, Environment::ENV_HTTP_TEST);

        $this->assertSame(['alice' => 'a-hash'], $oService->getCredentials());
    }

    // --------------------------------------------------------------------------

    /**
     * @covers EnvironmentProtection::getIpWhitelist
     */
    public function testIpWhitelistIsResolvedFromConfig(): void
    {
        Config::set(EnvironmentProtection::CONFIG_WHITELIST . '_' . Environment::ENV_STAGE, ['127.0.0.1']);

        $oService = new EnvironmentProtection(null, null, Environment::ENV_STAGE);

        $this->assertSame(['127.0.0.1'], $oService->getIpWhitelist());
    }

    // --------------------------------------------------------------------------

    /**
     * @covers EnvironmentProtection::getTtl
     */
    public function testTtlFallsBackToDefault(): void
    {
        $oService = new EnvironmentProtection(null, null, Environment::ENV_STAGE);

        $this->assertSame(EnvironmentProtection::DEFAULT_TTL, $oService->getTtl());
    }

    // --------------------------------------------------------------------------

    /**
     * @covers EnvironmentProtection::getTtl
     */
    public function testTtlIsResolvedFromConfig(): void
    {
        Config::set(EnvironmentProtection::CONFIG_TTL . '_' . Environment::ENV_DEV, 600);

        $oService = new EnvironmentProtection(null, null, Environment::ENV_DEV);

        $this->assertSame(600, $oService->getTtl());
    }

    // --------------------------------------------------------------------------

    /**
     * @covers EnvironmentProtection::getCookieName
     */
    public function testCookieNameIsConfigurable(): void
    {
        $oService = $this->service();

        $this->assertSame(EnvironmentProtection::DEFAULT_COOKIE_NAME, $oService->getCookieName());

        Config::set(EnvironmentProtection::CONFIG_COOKIE_NAME, 'customenvauth');

        $this->assertSame('customenvauth', $oService->getCookieName());
    }

    // --------------------------------------------------------------------------

    /**
     * @covers EnvironmentProtection::getCookieSameSite
     */
    public function testCookieSameSiteIsConfigurable(): void
    {
        $oService = $this->service();

        $this->assertSame(EnvironmentProtection::DEFAULT_COOKIE_SAME_SITE, $oService->getCookieSameSite());

        Config::set(EnvironmentProtection::CONFIG_COOKIE_SAME_SITE, 'Strict');

        $this->assertSame('Strict', $oService->getCookieSameSite());
    }

    // --------------------------------------------------------------------------

    /**
     * @covers EnvironmentProtection::getCookieDomain
     */
    public function testCookieDomainIsConfigurable(): void
    {
        $oService = $this->service();

        $this->assertSame('', $oService->getCookieDomain());

        Config::set(EnvironmentProtection::CONFIG_COOKIE_DOMAIN, '.example.com');

        $this->assertSame('.example.com', $oService->getCookieDomain());
    }

    // --------------------------------------------------------------------------

    /**
     * @covers EnvironmentProtection::getFieldUser
     * @covers EnvironmentProtection::getFieldPass
     */
    public function testFieldNamesAreConfigurable(): void
    {
        $oService = $this->service();

        $this->assertSame(EnvironmentProtection::DEFAULT_FIELD_USER, $oService->getFieldUser());
        $this->assertSame(EnvironmentProtection::DEFAULT_FIELD_PASS, $oService->getFieldPass());

        Config::set(EnvironmentProtection::CONFIG_FIELD_USER, 'username');
        Config::set(EnvironmentProtection::CONFIG_FIELD_PASS, 'password');

        $this->assertSame('username', $oService->getFieldUser());
        $this->assertSame('password', $oService->getFieldPass());
    }

    // --------------------------------------------------------------------------

    /**
     * @covers EnvironmentProtection::getHeaderUser
     * @covers EnvironmentProtection::getHeaderPass
     */
    public function testHeaderNamesAreConfigurable(): void
    {
        $oService = $this->service();

        $this->assertSame(EnvironmentProtection::DEFAULT_HEADER_USER, $oService->getHeaderUser());
        $this->assertSame(EnvironmentProtection::DEFAULT_HEADER_PASS, $oService->getHeaderPass());

        Config::set(EnvironmentProtection::CONFIG_HEADER_USER, 'X-Env-User');
        Config::set(EnvironmentProtection::CONFIG_HEADER_PASS, 'X-Env-Password');

        $this->assertSame('X-Env-User', $oService->getHeaderUser());
        $this->assertSame('X-Env-Password', $oService->getHeaderPass());
    }

    // --------------------------------------------------------------------------

    /**
     * @covers EnvironmentProtection::getKeySalt
     */
    public function testChangingTheKeySaltInvalidatesIssuedTokens(): void
    {
        $oService = $this->service();
        $sToken   = $oService->generateToken('alice');

        $this->assertSame('alice', $oService->validateToken($sToken));

        Config::set(EnvironmentProtection::CONFIG_KEY_SALT, '/rotated');

        try {
            $this->assertNull($oService->validateToken($sToken));
            $this->assertSame('alice', $oService->validateToken($oService->generateToken('alice')));

        } finally {
            Config::set(EnvironmentProtection::CONFIG_KEY_SALT, EnvironmentProtection::DEFAULT_KEY_SALT);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * @covers EnvironmentProtection::setCredentials
     * @covers EnvironmentProtection::setIpWhitelist
     */
    public function testSettersOverrideResolvedValues(): void
    {
        $oService = $this->service();

        $oService
            ->setCredentials(['bob' => 'nope'])
            ->setIpWhitelist(['127.0.0.1']);

        $this->assertSame(['bob' => 'nope'], $oService->getCredentials());
        $this->assertSame(['127.0.0.1'], $oService->getIpWhitelist());
    }
}
