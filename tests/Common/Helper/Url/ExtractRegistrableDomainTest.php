<?php

namespace Tests\Common\Helper\Url;

use Nails\Common\Helper\Url;
use PHPUnit\Framework\TestCase;

class ExtractRegistrableDomainTest extends TestCase
{
    /**
     * @covers \Nails\Common\Helper\Url::extractRegistrableDomain()
     */
    public function test_accepts_string_as_first_arg(): void
    {
        $this->expectNotToPerformAssertions();
        Url::extractRegistrableDomain('example.com');
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\Url::extractRegistrableDomain()
     */
    public function test_accepts_null_as_first_arg(): void
    {
        $this->expectNotToPerformAssertions();
        Url::extractRegistrableDomain(null);
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\Url::extractRegistrableDomain()
     */
    public function test_test_arg_is_required(): void
    {
        $this->expectException(\TypeError::class);
        Url::extractRegistrableDomain();
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\Url::extractRegistrableDomain()
     */
    public function test_does_not_accepts_other_data_type_as_first_arg(): void
    {
        $this->expectException(\TypeError::class);
        Url::extractRegistrableDomain((object) []);
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\Url::extractRegistrableDomain()
     */
    public function test_returns_correct_domain(): void
    {
        $aTests = [
            'my-domain.com',
            'my-domain.co.uk',
            'my-domain.io',
        ];

        foreach ($aTests as $sExpected) {

            $this->assertEquals($sExpected, Url::extractRegistrableDomain($sExpected));
            $this->assertEquals($sExpected, Url::extractRegistrableDomain($sExpected));

            $this->assertEquals($sExpected, Url::extractRegistrableDomain('http://' . $sExpected));
            $this->assertEquals($sExpected, Url::extractRegistrableDomain('https://' . $sExpected));

            $this->assertEquals($sExpected, Url::extractRegistrableDomain('http://subdomain.' . $sExpected));
            $this->assertEquals($sExpected, Url::extractRegistrableDomain('https://subdomain.' . $sExpected));

            $this->assertEquals($sExpected, Url::extractRegistrableDomain('http://deep.subdomain.' . $sExpected));
            $this->assertEquals($sExpected, Url::extractRegistrableDomain('https://deep.subdomain.' . $sExpected));
        }
    }
}
