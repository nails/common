<?php

namespace Tests\Common\Factory\Redirect;

use Nails\Common\Exception\Redirect\InvalidDestinationException;
use Nails\Common\Exception\Redirect\InvalidLocationHttpResponseCodeException;
use Nails\Common\Exception\Redirect\InvalidMethodException;
use Nails\Common\Factory\Redirect;
use Nails\Common\Service\UserFeedback;
use Nails\Config;
use PHPUnit\Framework\TestCase;

class RedirectTest extends TestCase
{
    private function getInstance(
        string $sUrl = null,
        string $sMethod = null,
        int $iLocationHttpResponseCode = null,
        bool $bAllowExternal = null,
        $oUserFeedbackMock = null,
        $sBootstrapMock = null
    ): Redirect {
        return new Redirect(
            $sUrl ?? '',
            $sMethod ?? Redirect::METHOD_LOCATION,
            $iLocationHttpResponseCode ?? Redirect::HTTP_CODE_TEMPORARY,
            $bAllowExternal ?? false,
            $oUserFeedbackMock ?? $this->createUserFeedbackMock(),
            $sBootstrapMock ?? get_class($this->createBootstrapMock())
        );
    }

    // --------------------------------------------------------------------------

    private function createUserFeedbackMock()
    {
        return $this->createMock(UserFeedback::class);
    }

    // --------------------------------------------------------------------------

    private function createBootstrapMock()
    {
        $sClassName = 'Bootstrap_' . md5(microtime(true));
        return eval("
            class $sClassName {
                public static \$bTestShutdown = false;
                public static function shutdown()
                {
                    if (self::\$bTestShutdown) {
                        throw new \RuntimeException(__METHOD__ . ' was called');
                    }
                }
            }
            
            return new $sClassName();
        ");
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Factory\Redirect::__constructor
     */
    public function test_can_set_url_via_constructor(): void
    {
        $sUrl      = 'https://example.com';
        $oRedirect = $this->getInstance($sUrl);
        $this->assertEquals($sUrl, $oRedirect->getUrl());
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Factory\Redirect::__constructor
     */
    public function test_can_set_method_via_constructor(): void
    {
        $sMethod   = Redirect::METHOD_REFRESH;
        $oRedirect = $this->getInstance(null, $sMethod);
        $this->assertEquals($sMethod, $oRedirect->getMethod());
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Factory\Redirect::__constructor
     */
    public function test_can_set_http_code_via_constructor(): void
    {
        $iCode     = Redirect::HTTP_CODE_PERMANENT;
        $oRedirect = $this->getInstance(null, null, $iCode);
        $this->assertEquals($iCode, $oRedirect->getLocationHttpResponseCode());
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Factory\Redirect::__constructor
     */
    public function test_can_set_external_via_constructor(): void
    {
        $bAllowExternal = true;
        $oRedirect      = $this->getInstance(null, null, null, $bAllowExternal);
        $this->assertEquals($bAllowExternal, $oRedirect->isAllowExternal());
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Factory\Redirect::setLocalHost
     * @covers \Nails\Common\Factory\Redirect::getLocalHost
     */
    public function test_can_set_and_get_local_host(): void
    {
        $sUrl      = 'https://localhost.com';
        $oRedirect = $this->getInstance();
        $oRedirect->setLocalHost($sUrl);
        $this->assertEquals($sUrl, $oRedirect->getLocalHost());
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Factory\Redirect::setUrl
     * @covers \Nails\Common\Factory\Redirect::getUrl
     */
    public function test_can_set_and_get_url(): void
    {
        $sUrl      = 'https://example.com';
        $oRedirect = $this->getInstance();
        $oRedirect->setUrl($sUrl);
        $this->assertEquals($sUrl, $oRedirect->getUrl());
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Factory\Redirect::setMethod()
     * @covers \Nails\Common\Factory\Redirect::getMethod
     */
    public function test_can_set_and_get_method(): void
    {
        $sMethod   = Redirect::METHOD_LOCATION;
        $oRedirect = $this->getInstance();
        $oRedirect->setMethod($sMethod);
        $this->assertEquals($sMethod, $oRedirect->getMethod());
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Factory\Redirect::setMethod()
     */
    public function test_set_method_throws_exception_for_invalid_method(): void
    {
        $sMethod   = 'Invalid method';
        $oRedirect = $this->getInstance();

        $this->expectException(InvalidMethodException::class);
        $oRedirect->setMethod($sMethod);
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Factory\Redirect::setMethod()
     * @covers \Nails\Common\Factory\Redirect::getMethod
     */
    public function test_can_set_and_get_location_http_code(): void
    {
        $iCode     = Redirect::HTTP_CODE_PERMANENT;
        $oRedirect = $this->getInstance();
        $oRedirect->setLocationHttpResponseCode($iCode);
        $this->assertEquals($iCode, $oRedirect->getLocationHttpResponseCode());
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Factory\Redirect::setMethod()
     */
    public function test_set_method_throws_exception_for_invalid_location_http_code(): void
    {
        $iCode     = 999;
        $oRedirect = $this->getInstance();

        $this->expectException(InvalidLocationHttpResponseCodeException::class);
        $oRedirect->setLocationHttpResponseCode($iCode);
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Factory\Redirect::setUrl
     * @covers \Nails\Common\Factory\Redirect::getUrl
     */
    public function test_can_set_and_get_allow_external(): void
    {
        $oRedirect = $this->getInstance();
        $this->assertEquals(false, $oRedirect->isAllowExternal());

        $oRedirect->allowExternal();
        $this->assertEquals(true, $oRedirect->isAllowExternal());
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Factory\Redirect::getMethods()
     */
    public function test_set_static_get_methods_returns_array_of_strings(): void
    {
        $oRedirect = $this->getInstance();
        $aMethods  = $oRedirect::getMethods();
        $this->assertIsArray($aMethods);

        foreach ($aMethods as $sMethod) {
            $this->assertIsString($sMethod);
        }
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Factory\Redirect::getHttpCodes()
     */
    public function test_set_static_get_http_codes_returns_array_of_ints(): void
    {
        $oRedirect = $this->getInstance();
        $aCodes    = $oRedirect::getHttpCodes();
        $this->assertIsArray($aCodes);

        foreach ($aCodes as $iCode) {
            $this->assertIsInt($iCode);
        }
    }

    // --------------------------------------------------------------------------

    public function test_execute_sends_redirect_header()
    {
        $oRedirect = $this->getInstance();
        $oRedirect
            ->setLocalHost('https://localhost.com')
            ->setUrl('/foo/bar')
            ->execute(function ($sHeader) {
                $this->assertEquals('Location: https://localhost.com/foo/bar', $sHeader);
            });
    }

    // --------------------------------------------------------------------------

    public function test_user_feedback_data_is_persisted()
    {
        $oUserFeedback = $this->createUserFeedbackMock();

        $oUserFeedback
            ->expects($this->once())
            ->method('persist');

        $oRedirect = $this->getInstance(null, null, null, null, $oUserFeedback);
        $oRedirect
            ->setLocalHost('https://localhost.com')
            ->setUrl('/foo/bar')
            ->execute(function ($sHeader) {
                // closure prevents redirect from sending headers or exiting
            });
    }

    // --------------------------------------------------------------------------

    public function test_bootstrap_shutdown_method_is_called()
    {
        $oBootstrap = $this->createBootstrapMock();

        $oBootstrap::$bTestShutdown = true;

        $this->expectException(\RuntimeException::class);

        $oRedirect = $this->getInstance(null, null, null, null, null, get_class($oBootstrap));
        $oRedirect
            ->setLocalHost('https://localhost.com')
            ->setUrl('/foo/bar')
            ->execute(function ($sHeader) {
                // closure prevents redirect from sending headers or exiting
            });
    }

    // --------------------------------------------------------------------------

    public function test_external_host_throws_exception()
    {
        $this->expectException(InvalidDestinationException::class);

        $oRedirect = $this->getInstance();
        $oRedirect
            ->setLocalHost('https://localhost.com')
            ->setUrl('https://remotehost.com/foo/bar"')
            ->execute();
    }

    // --------------------------------------------------------------------------

    public function test_external_host_sends_header_when_allow_external_is_set()
    {
        $oRedirect = $this->getInstance();
        $oRedirect
            ->setLocalHost('https://localhost.com')
            ->setUrl('https://remotehost.com/foo/bar')
            ->allowExternal()
            ->execute(function ($sHeader) {
                $this->assertEquals('Location: https://remotehost.com/foo/bar', $sHeader);
            });
    }
}
