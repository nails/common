<?php

namespace Tests\Common\Service;

use PHPUnit\Framework\TestCase;
use Nails\Common\Service\Meta;

class MetaTest extends TestCase
{
    /**
     * @covers Meta::getEntries
     */
    public function testGetEntries(): void
    {
        $oMeta = new Meta();
        $this->assertCount(0, $oMeta->getEntries());
    }

    // --------------------------------------------------------------------------

    /**
     * @covers Meta::addRaw
     */
    public function testAddRaw(): void
    {
        $oMeta = new Meta();
        $aData = [
            'foo' => 'bar',
        ];

        $oMeta->addRaw($aData);
        $this->assertCount(1, $oMeta->getEntries());
    }

    // --------------------------------------------------------------------------

    /**
     * @covers Meta::addRaw
     */
    public function testAddRawDoesRemovesDuplicates(): void
    {
        $oMeta = new Meta();
        $aData = [
            'foo' => 'bar',
        ];

        $oMeta->addRaw($aData);
        $oMeta->addRaw($aData);
        $this->assertCount(1, $oMeta->getEntries());
    }

    // --------------------------------------------------------------------------

    /**
     * @covers Meta::addRaw
     */
    public function testAddRawIsChainable(): void
    {
        $oMeta = new Meta();
        $aData = [
            'foo' => 'bar',
        ];

        $this->assertInstanceOf('Nails\Common\Service\Meta', $oMeta->addRaw($aData));
    }

    // --------------------------------------------------------------------------

    /**
     * @covers Meta::removeRaw
     */
    public function testRemoveRaw(): void
    {
        $oMeta = new Meta();
        $aData = [
            'foo' => 'bar',
        ];

        $oMeta->addRaw($aData);
        $oMeta->removeRaw($aData);
        $this->assertCount(0, $oMeta->getEntries());
    }

    // --------------------------------------------------------------------------

    /**
     * @covers Meta::add
     */
    public function testAdd(): void
    {
        $oMeta = new Meta();
        $oMeta->add('foo', 'bar');
        $this->assertEquals(1, count($oMeta->getEntries()));
    }

    // --------------------------------------------------------------------------

    /**
     * @covers Meta::addRaw
     */
    public function testAddIsChainable(): void
    {
        $oMeta = new Meta();
        $this->assertInstanceOf('Nails\Common\Service\Meta', $oMeta->add('foo', 'bar'));
    }

    // --------------------------------------------------------------------------

    /**
     * @covers Meta::remove
     */
    public function testRemove(): void
    {
        $oMeta = new Meta();
        $oMeta->add('foo', 'bar');
        $oMeta->remove('foo', 'bar');
        $this->assertCount(0, $oMeta->getEntries());
    }

    // --------------------------------------------------------------------------

    /**
     * @covers Meta::outputAr
     */
    public function testOutputAr(): void
    {
        $oMeta = new Meta();

        $oMeta->add('foo', 'bar');
        $oMeta->add('cat', 'dog', 'link');

        $aExpected = [
            '<meta name="foo" content="bar">',
            '<link name="cat" content="dog">',
        ];

        $this->assertEquals($aExpected, $oMeta->outputAr());
    }

    // --------------------------------------------------------------------------

    /**
     * @covers Meta::outputStr
     */
    public function testOutputStr(): void
    {
        $oMeta = new Meta();

        $oMeta->add('foo', 'bar');
        $oMeta->add('cat', 'dog', 'link');

        $aExpected = '<meta name="foo" content="bar">' . "\n" . '<link name="cat" content="dog">';

        $this->assertEquals($aExpected, $oMeta->outputStr());
    }
}
