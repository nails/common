<?php

namespace Nails\Common\Service;

use Mustache\Engine;

/**
 * Class Mustache
 *
 * @package Nails\Common\Service
 */
class Mustache extends Engine
{
    public function render($template, $context = [])
    {
        //  Works around an issue in PHP8 where the template is not a string
        //  strlen(): Passing null to parameter #1 ($string) of type string is deprecated
        return parent::render((string) $template, $context);
    }
}
