<?php

namespace Nails\Common\Helper\Model\Expand;

use Nails\Common\Helper\Model\Expand;
use Nails\Common\Exception\NailsException;

/**
 * Class Group
 *
 * @package Nails\Common\Helper\Model\Expand
 */
class Group
{
    /**
     * The collection of triggers
     *
     * @var self[]|Expand[]
     */
    private $aTriggers = [];

    // --------------------------------------------------------------------------

    /**
     * Group constructor.
     *
     * @param self[]|Expand[] $aTriggers The triggers in the group
     *
     * @throws NailsException
     */
    public function __construct(array $aTriggers)
    {
        foreach ($aTriggers as $mTrigger) {
            if (!$mTrigger instanceof self && !$mTrigger instanceof Expand) {
                throw new NailsException(
                    sprintf(
                        '%s only accepts instances of %s and %s, %s provided',
                        self::class,
                        self::class,
                        Expand::class,
                        gettype($mTrigger)
                    )
                );
            }
        }
        $this->aTriggers = $aTriggers;
    }

    // --------------------------------------------------------------------------

    /**
     * Compile the expansions in the group
     *
     * @return array
     */
    public function compile()
    {
        $aOut = [];
        foreach ($this->aTriggers as $mTrigger) {
            if ($mTrigger instanceof self) {
                $aOut = array_merge($aOut, $mTrigger->compile());
            } else {
                $aOut[] = $mTrigger->compile();
            }
        }

        return $aOut;
    }
}
