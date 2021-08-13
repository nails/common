<?php

namespace Nails\Common\Helper\Model;

/**
 * Class Paginate
 *
 * @package Nails\Common\Helper\Model
 */
class Paginate
{
    private string $iPerPage;
    private string $iPage;

    // --------------------------------------------------------------------------

    /**
     * Paginate constructor.
     *
     * @param int $iPerPage
     * @param int $iPage
     */
    public function __construct(int $iPerPage, int $iPage = null)
    {
        $this->iPerPage = $iPerPage;
        $this->iPage    = $iPage ?? 0;
    }

    // --------------------------------------------------------------------------

    /**
     * Compile the statement
     *
     * @return array|string
     */
    public function compile()
    {
        return [$this->iPage, $this->iPerPage];
    }
}
