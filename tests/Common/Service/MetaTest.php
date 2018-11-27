<?php

namespace Nails\Common\Service;

use PHPUnit\Framework\TestCase;

class MetaTest extends TestCase
{
    /**
     * @covers \Nails\Common\Meta::getEntries
     */
    public function testGetEntries()
    {
        $oMeta = new Meta();
        $this->assertCount(0, $oMeta->getEntries());
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Meta::addRaw
     */
    public function testAddRaw()
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
     * @covers \Nails\Common\Meta::addRaw
     */
    public function testAddRawDoesRemovesDupliates()
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
     * @covers \Nails\Common\Meta::addRaw
     */
    public function testAddRawIsChainable()
    {
        $oMeta = new Meta();
        $aData = [
            'foo' => 'bar',
        ];

        $this->assertInstanceOf('Nails\Common\Service\Meta', $oMeta->addRaw($aData));
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Meta::removeRaw
     */
    public function testRemoveRaw()
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
     * @covers \Nails\Common\Meta::add
     */
    public function testAdd()
    {
        $oMeta = new Meta();
        $oMeta->add('foo', 'bar');
        $this->assertEquals(1, count($oMeta->getEntries()));
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Meta::addRaw
     */
    public function testAddIsChainable()
    {
        $oMeta = new Meta();
        $this->assertInstanceOf('Nails\Common\Service\Meta', $oMeta->add('foo', 'bar'));
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Meta::remove
     */
    public function testRemove()
    {
        $oMeta = new Meta();
        $oMeta->add('foo', 'bar');
        $oMeta->remove('foo', 'bar');
        $this->assertCount(0, $oMeta->getEntries());
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Meta::outputAr
     */
    public function testOutputAr()
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
     * @covers \Nails\Common\Meta::outputStr
     */
    public function testOutputStr()
    {
        $oMeta = new Meta();

        $oMeta->add('foo', 'bar');
        $oMeta->add('cat', 'dog', 'link');

        $aExpected = '<meta name="foo" content="bar">' . "\n" . '<link name="cat" content="dog">';

        $this->assertEquals($aExpected, $oMeta->outputStr());
    }
}
