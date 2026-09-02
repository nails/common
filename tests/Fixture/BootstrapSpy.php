<?php

namespace Tests\Fixture;

/**
 * Class BootstrapSpy
 *
 * Stands in for \Nails\Bootstrap in tests.
 *
 * Nails\Common\Factory\Redirect::execute() calls the shutdown method statically
 * so it cannot be replaced with a PHPUnit double; this records the calls made to
 * it instead. Call reset() in setUp() so that state does not leak between tests.
 *
 * @package Tests\Fixture
 */
class BootstrapSpy
{
    /**
     * The number of times shutdown() has been called since the last reset()
     *
     * @var int
     */
    public static int $iShutdownCallCount = 0;

    // --------------------------------------------------------------------------

    /**
     * Discards any recorded calls
     *
     * @return void
     */
    public static function reset(): void
    {
        static::$iShutdownCallCount = 0;
    }

    // --------------------------------------------------------------------------

    /**
     * Records a call in place of \Nails\Bootstrap::shutdown()
     *
     * @return void
     */
    public static function shutdown(): void
    {
        static::$iShutdownCallCount++;
    }
}
