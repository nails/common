<?php

namespace Nails\Common\Traits\Database\Seeder;

/**
 * Trait Collection
 *
 * @package Nails\Common\Traits\Database\Seeder
 */
trait Collection
{
    /**
     * Returns a random element from a supplied array
     *
     * @param array $aItems
     *
     * @return mixed
     */
    protected function randomItem($aItems = [])
    {
        return $aItems[array_rand($aItems)];
    }

    // --------------------------------------------------------------------------

    /**
     * Returns a random selection from a supplied array
     *
     * @param array $aItems      The array to select from
     * @param int   $iNum        The number of items to return
     * @param bool  $bAllowEmpty Allow an empty result to be returned
     *
     * @return array
     */
    protected function randomItems(array $aItems, $iNum = null, $bAllowEmpty = true): array
    {
        if ($iNum === null) {
            $iNum = rand(
                ($bAllowEmpty ? 0 : 1),
                count($aItems)
            );
        }

        if (empty($iNum)) {
            return [];
        }

        $aKeys = (array) array_rand($aItems, $iNum);

        return array_values(
            array_filter(
                $aItems,
                function ($mKey) use ($aKeys) {
                    return in_array($mKey, $aKeys);
                },
                ARRAY_FILTER_USE_KEY
            )
        );
    }
}
