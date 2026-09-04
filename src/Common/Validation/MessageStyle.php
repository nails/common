<?php

namespace Nails\Common\Validation;

/**
 * Which flavour of default message to prefer
 *
 * PLAIN prefers `fv_<rule>` ("This field is required."), WITH_FIELD prefers
 * `fv_<rule>_field` ("The {field} field is required.").
 */
enum MessageStyle: string
{
    case PLAIN      = 'PLAIN';
    case WITH_FIELD = 'WITH_FIELD';
}
