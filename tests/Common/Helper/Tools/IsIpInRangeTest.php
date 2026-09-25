<?php

namespace Tests\Common\Helper;

use Nails\Common\Helper\Tools;
use PHPUnit\Framework\TestCase;

class IsIpInRangeTest extends TestCase
{
    /**
     * @covers \Nails\Common\Helper\Tools::isIpInRange()
     */
    public function test_exact_ipv4_matches(): void
    {
        $this->assertTrue(Tools::isIpInRange('203.0.113.7', ['203.0.113.7']));
        $this->assertFalse(Tools::isIpInRange('203.0.113.8', ['203.0.113.7']));
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\Tools::isIpInRange()
     */
    public function test_ipv4_cidr_matches(): void
    {
        $this->assertTrue(Tools::isIpInRange('198.51.100.1', ['198.51.100.0/24']));
        $this->assertTrue(Tools::isIpInRange('198.51.100.254', ['198.51.100.0/24']));
        $this->assertFalse(Tools::isIpInRange('198.51.101.1', ['198.51.100.0/24']));
        $this->assertTrue(Tools::isIpInRange('10.1.2.3', ['10.0.0.0/8']));
        $this->assertTrue(Tools::isIpInRange('198.51.100.9', ['198.51.100.8/29']));
        $this->assertFalse(Tools::isIpInRange('198.51.100.16', ['198.51.100.8/29']));
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\Tools::isIpInRange()
     */
    public function test_exact_ipv6_matches_regardless_of_notation(): void
    {
        $this->assertTrue(Tools::isIpInRange('2001:db8::1', ['2001:db8::1']));
        $this->assertTrue(Tools::isIpInRange('2001:DB8:0:0::1', ['2001:db8::1']));
        $this->assertFalse(Tools::isIpInRange('2001:db8::2', ['2001:db8::1']));
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\Tools::isIpInRange()
     */
    public function test_ipv6_cidr_matches(): void
    {
        $this->assertTrue(Tools::isIpInRange('2001:db8:abcd::1', ['2001:db8::/32']));
        $this->assertFalse(Tools::isIpInRange('2001:db9::1', ['2001:db8::/32']));
        $this->assertTrue(Tools::isIpInRange('2001:db8:0:ff::1', ['2001:db8:0:f0::/60']));
        $this->assertFalse(Tools::isIpInRange('2001:db8:0:100::1', ['2001:db8:0:f0::/60']));
    }

    // --------------------------------------------------------------------------

    /**
     * Previously an IPv6 CIDR entry matched every IPv6 address
     *
     * @covers \Nails\Common\Helper\Tools::isIpInRange()
     */
    public function test_ipv6_cidr_does_not_match_unrelated_ipv6(): void
    {
        $this->assertFalse(Tools::isIpInRange('2a00:1450::1', ['2001:db8::/32']));
        $this->assertFalse(Tools::isIpInRange('::1', ['2001:db8::/32']));
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\Tools::isIpInRange()
     */
    public function test_families_do_not_cross_match(): void
    {
        $this->assertFalse(Tools::isIpInRange('203.0.113.7', ['2001:db8::/32']));
        $this->assertFalse(Tools::isIpInRange('2001:db8::1', ['0.0.0.0/0']));
        $this->assertFalse(Tools::isIpInRange('0.0.0.0', ['2001:db8::/32']));
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\Tools::isIpInRange()
     */
    public function test_zero_prefix_matches_whole_family(): void
    {
        $this->assertTrue(Tools::isIpInRange('203.0.113.7', ['0.0.0.0/0']));
        $this->assertTrue(Tools::isIpInRange('2001:db8::1', ['::/0']));
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\Tools::isIpInRange()
     */
    public function test_invalid_input_never_matches(): void
    {
        $this->assertFalse(Tools::isIpInRange('not-an-ip', ['not-an-ip']));
        $this->assertFalse(Tools::isIpInRange('203.0.113.7', ['203.0.113.0/33']));
        $this->assertFalse(Tools::isIpInRange('203.0.113.7', ['203.0.113.0/abc']));
        $this->assertFalse(Tools::isIpInRange('203.0.113.7', ['garbage/24']));
        $this->assertFalse(Tools::isIpInRange('2001:db8::1', ['2001:db8::/129']));
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\Tools::isIpInRange()
     */
    public function test_string_list_is_parsed(): void
    {
        $sRange = "203.0.113.7, 198.51.100.0/24\n2001:db8::/32";

        $this->assertTrue(Tools::isIpInRange('203.0.113.7', $sRange));
        $this->assertTrue(Tools::isIpInRange('198.51.100.20', $sRange));
        $this->assertTrue(Tools::isIpInRange('2001:db8::5', $sRange));
        $this->assertFalse(Tools::isIpInRange('192.0.2.1', $sRange));
        $this->assertFalse(Tools::isIpInRange('2a00:1450::1', $sRange));
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\Tools::isIpInRange()
     */
    public function test_empty_range_never_matches(): void
    {
        $this->assertFalse(Tools::isIpInRange('203.0.113.7', []));
        $this->assertFalse(Tools::isIpInRange('203.0.113.7', ''));
    }
}
