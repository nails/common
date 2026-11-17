<?php

namespace Tests\Common\Helper\ArrayHelper;

use ArrayIterator;
use ArrayObject;
use Nails\Common\Helper\ArrayHelper;
use PHPUnit\Framework\TestCase;
use stdClass;
use TypeError;

/**
 * @covers \Nails\Common\Helper\ArrayHelper::arrayFlattenWithDotNotation()
 */
class ArrayFlattenWithDotNotationTest extends TestCase
{
    /**
     * @covers \Nails\Common\Helper\ArrayHelper::arrayFlattenWithDotNotation()
     */
    public function test_flattens_nested_arrays(): void
    {
        $this->assertSame(
            $this->expectedNested(),
            $this->flatten($this->nestedArray())
        );
    }

    // --------------------------------------------------------------------------

    /**
     * json_decode() of widget data yields stdClass nodes. Nested objects must
     * flatten to the same map as the equivalent array, without a PHP 8.5
     * ArrayIterator deprecation.
     *
     * @covers \Nails\Common\Helper\ArrayHelper::arrayFlattenWithDotNotation()
     */
    public function test_object_input_matches_array_input(): void
    {
        $aArray  = $this->nestedArray();
        $oObject = json_decode(json_encode($aArray));

        $this->assertInstanceOf(stdClass::class, $oObject);
        $this->assertInstanceOf(stdClass::class, $oObject->b);
        $this->assertIsArray($oObject->list);
        $this->assertInstanceOf(stdClass::class, $oObject->list[0]);

        $this->assertSame(
            $this->flatten($aArray),
            $this->flatten($oObject)
        );
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\ArrayHelper::arrayFlattenWithDotNotation()
     */
    public function test_nested_objects_inside_arrays(): void
    {
        $oTop          = new stdClass();
        $oTop->id      = 7;
        $oChild        = new stdClass();
        $oChild->name  = 'widget';
        $oGrand        = new stdClass();
        $oGrand->title = 'Hello';
        $oMeta         = new stdClass();
        $oMeta->x      = 1;
        $oGrand->items = [
            (object) ['label' => 'A'],
            (object) ['label' => 'B', 'meta' => $oMeta],
        ];
        $oChild->data  = $oGrand;
        $oTop->widget  = $oChild;

        $this->assertSame(
            [
                'id'                          => 7,
                'widget.name'                 => 'widget',
                'widget.data.title'           => 'Hello',
                'widget.data.items.0.label'   => 'A',
                'widget.data.items.1.label'   => 'B',
                'widget.data.items.1.meta.x'  => 1,
            ],
            $this->flatten($oTop)
        );
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\ArrayHelper::arrayFlattenWithDotNotation()
     */
    public function test_array_object_and_array_iterator_match_their_storage(): void
    {
        $aStorage = [
            'a' => 1,
            'b' => (object) ['c' => 2],
            'n' => new ArrayObject(['d' => 3]),
        ];
        $aExpected = [
            'a'   => 1,
            'b.c' => 2,
            'n.d' => 3,
        ];

        $this->assertSame($aExpected, $this->flatten(new ArrayObject($aStorage)));
        $this->assertSame($aExpected, $this->flatten(new ArrayIterator($aStorage)));
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\ArrayHelper::arrayFlattenWithDotNotation()
     */
    public function test_only_public_properties_are_read(): void
    {
        $oObject = new class {
            public string $pub = 'public';
            protected string $pro = 'protected';
            private string $pri = 'private';
        };

        $this->assertSame(['pub' => 'public'], $this->flatten($oObject));
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\ArrayHelper::arrayFlattenWithDotNotation()
     */
    public function test_numeric_property_names_match_array_keys(): void
    {
        $oObject        = new stdClass();
        $oObject->{'0'} = 'zero';
        $oObject->{'01'} = 'padded';
        $oObject->{'10'} = 'ten';
        $oObject->name  = 'n';

        $this->assertSame(
            $this->flatten([
                '0'    => 'zero',
                '01'   => 'padded',
                '10'   => 'ten',
                'name' => 'n',
            ]),
            $this->flatten($oObject)
        );
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\ArrayHelper::arrayFlattenWithDotNotation()
     */
    public function test_empty_array_and_object_produce_no_keys(): void
    {
        $this->assertSame([], $this->flatten([]));
        $this->assertSame([], $this->flatten(new stdClass()));
        $this->assertSame([], $this->flatten(new ArrayObject()));
    }

    // --------------------------------------------------------------------------

    /**
     * @covers \Nails\Common\Helper\ArrayHelper::arrayFlattenWithDotNotation()
     */
    public function test_scalar_input_is_rejected(): void
    {
        $this->expectException(TypeError::class);

        ArrayHelper::arrayFlattenWithDotNotation('nope');
    }

    // --------------------------------------------------------------------------

    private function nestedArray(): array
    {
        return [
            'a' => 1,
            'b' => [
                'c'     => 2,
                'd'     => [3, ['e' => 4]],
                'empty' => [],
                'nil'   => null,
                'flag'  => false,
                'zero'  => 0,
                'str'   => '',
            ],
            'list' => [
                ['label' => 'A', 'url' => '/a'],
                ['label' => 'B', 'url' => null],
            ],
            'empty_obj' => (object) [],
        ];
    }

    // --------------------------------------------------------------------------

    private function expectedNested(): array
    {
        return [
            'a'             => 1,
            'b.c'           => 2,
            'b.d.0'         => 3,
            'b.d.1.e'       => 4,
            'b.nil'         => null,
            'b.flag'        => false,
            'b.zero'        => 0,
            'b.str'         => '',
            'list.0.label'  => 'A',
            'list.0.url'    => '/a',
            'list.1.label'  => 'B',
            'list.1.url'    => null,
        ];
    }

    // --------------------------------------------------------------------------

    /**
     * @param mixed $mInput
     */
    private function flatten($mInput): array
    {
        $aWarnings = [];
        set_error_handler(static function (int $iSeverity, string $sMessage) use (&$aWarnings): bool {
            if ($iSeverity === E_DEPRECATED || $iSeverity === E_USER_DEPRECATED) {
                $aWarnings[] = $sMessage;

                return true;
            }

            return false;
        });

        try {
            $aResult = ArrayHelper::arrayFlattenWithDotNotation($mInput);
        } finally {
            restore_error_handler();
        }

        $this->assertSame([], $aWarnings, 'PHP deprecation: ' . implode("\n", $aWarnings));

        return $aResult;
    }
}
