<?php

namespace Nails\Common\Validation\Rule;

use Nails\Common\Exception\FactoryException;
use Nails\Common\Exception\NailsException;
use Nails\Common\Validation\AbstractRule;
use Nails\Common\Validation\Context;
use Nails\Factory;

/**
 * Rule: `is_id[Model.provider]` — the value must be the ID of an existing item
 * in the given model (provider defaults to `app`)
 */
class IsId extends AbstractRule
{
    public const NAME            = 'is_id';
    public const DEFAULT_MESSAGE = 'The {field} field must be a valid ID';

    public function apply(mixed $mValue, Context $oContext): bool
    {
        //  If blank, then assume the ID is not required
        if (empty($mValue)) {
            return true;
        }

        [$sModel, $sProvider] = array_pad($oContext->getParams('.'), 2, 'app');

        try {
            $oModel = Factory::model($sModel, $sProvider);
        } catch (FactoryException $e) {
            throw new NailsException(
                sprintf(
                    'Failed to load model %s::%s when processing rule is_id; %s',
                    $sModel,
                    $sProvider,
                    $e->getMessage()
                ),
                $e->getCode(),
                $e
            );
        }

        return $oModel->getById($mValue) !== null;
    }
}
