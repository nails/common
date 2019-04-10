<?php

namespace Nails\Common\Factory;

/**
 * Class Pagination
 *
 * @package Nails\Common\Factory
 */
class Pagination
{
    /**
     * The Pagination library
     *
     * @var \NAILS_Pagination
     */
    protected $oCiLibrary;

    // --------------------------------------------------------------------------

    /**
     * Pagination constructor.
     *
     * @param array $aConfig
     */
    public function __construct(array $aConfig = [])
    {
        get_instance()->load->library('pagination');
        $this->oCiLibrary = get_instance()->pagination;
    }

    // --------------------------------------------------------------------------

    /**
     * Initialize the pagination object
     *
     * @param array $aConfig The config array to initialize with
     *
     * @return $this
     */
    public function initialize(array $aConfig): self
    {
        $this->oCiLibrary->initialize($aConfig);
        return $this;
    }

    // --------------------------------------------------------------------------

    /**
     * Generate the pagination makup
     *
     * @return string
     */
    public function generate(): string
    {
        return $this->oCiLibrary->create_links();
    }
}
