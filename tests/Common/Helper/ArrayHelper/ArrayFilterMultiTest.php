<?php

namespace Tests\Commnon\Helper\ArrayHelper;

use Nails\Common\Exception\NailsException;
use Nails\Common\Helper\ArrayHelper;
use PHPUnit\Framework\TestCase;
use stdClass;

class ArrayFilterMultiTest extends TestCase
{
    /**
     * Test data 1
     *
     * @var array
     */
    private $aTestItem1 = [
        'key_1' => 'cat',
        'key_2' => 'dog',
    ];

    /**
     * Test data 2
     *
     * @var array
     */
    private $aTestItem2 = [
        'key_1' => 'mouse',
        'key_2' => '',
    ];

    /**
     * Test data (array)
     *
     * @var array[]
     */
    private $aTestArrayArray = [];

    /**
     * Test data (object)
     *
     * @var stdClass[]
     */
    private $aTestArrayObject = [];

    // --------------------------------------------------------------------------

    /**
     * ArrayFilterMultiTest constructor.
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->aTestArrayArray = [
            $this->aTestItem1,
            $this->aTestItem2,
        ];

        $this->aTestArrayObject = [
            (object) $this->aTestItem1,
            (object) $this->aTestItem2,
        ];
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\ArrayHelper::arrayFilterMulti()
     */
    public function test_removes_where_key_is_empty(): void
    {
        $aOutput = ArrayHelper::arrayFilterMulti('key_2', $this->aTestArrayArray);
        $this->assertCount(1, $aOutput);

        $aOutput = ArrayHelper::arrayFilterMulti('key_2', $this->aTestArrayObject);
        $this->assertCount(1, $aOutput);
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\ArrayHelper::arrayFilterMulti()
     */
    public function test_leaves_where_key_is_not_empty(): void
    {
        $aOutput = ArrayHelper::arrayFilterMulti('key_1', $this->aTestArrayArray);
        $this->assertCount(2, $aOutput);

        $aOutput = ArrayHelper::arrayFilterMulti('key_1', $this->aTestArrayObject);
        $this->assertCount(2, $aOutput);
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\ArrayHelper::arrayFilterMulti()
     */
    public function test_exception_thrown_when_key_doeS_not_exist(): void
    {
        $this->expectException(NailsException::class);
        ArrayHelper::arrayFilterMulti('does_not_exist', $this->aTestArrayArray);
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\ArrayHelper::arrayFilterMulti()
     */
    public function test_custom_filter_function(): void
    {
        $aOutput = ArrayHelper::arrayFilterMulti(
            'key_1',
            $this->aTestArrayArray,
            function ($sValue) {
                return $sValue === 'lizard';
            }
        );
        $this->assertCount(0, $aOutput);

        $aOutput = ArrayHelper::arrayFilterMulti(
            'key_1',
            $this->aTestArrayArray,
            function ($sValue) {
                return $sValue === 'cat';
            }
        );
        $this->assertCount(1, $aOutput);
    }
}
