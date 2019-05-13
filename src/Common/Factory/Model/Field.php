<?php

namespace Nails\Common\Factory\Model;

class Field
{
    /**
     * The field's key
     *
     * @var string
     */
    public $key;

    /**
     * The field's label
     *
     * @var string
     */
    public $label;

    /**
     * The field's type
     *
     * @var string
     */
    public $type = 'text';

    /**
     * Whether the field can be null
     *
     * @var bool
     */
    public $allow_null;

    /**
     * The field's validation rules
     *
     * @var array
     */
    public $validation = [];

    /**
     * The field's default value
     *
     * @var string
     */
    public $default;

    /**
     * The field's options (applicable to dropdowns only)
     *
     * @var array
     */
    public $options = [];

    /**
     * The field's maximum length
     *
     * @var int
     */
    public $max_length;

    // --------------------------------------------------------------------------

    //  @todo (Pablo - 2019-05-09) - Deprecate/move the following. These are all admin specific so should probably not be here

    /**
     * The field's class
     *
     * @var string
     */
    public $class;

    /**
     * The field's info
     *
     * @var string
     */
    public $info;

    /**
     * The field's fieldset
     *
     * @var string
     */
    public $fieldset = 'Details';

    /**
     * The field's data
     *
     * @var array
     */
    public $data = [];
}
