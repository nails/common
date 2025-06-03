<?php

namespace Nails\Common\Factory\Model\Field;

use Nails\Common\Helper\Form;
use Nails\Common\Factory\Model\Field;

class WysiwygBasic extends Wysiwyg
{
    /** @var string */
    public $type = Form::FIELD_WYSIWYG_BASIC;
}
