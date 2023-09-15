<?php
/*
 * Schedule plugin for CraftCMS
 *
 * https://github.com/glue-agency/craft-schedule
 */

namespace GlueAgency\schedule\validators;

use Carbon\CarbonInterval;
use Carbon\Exceptions\InvalidIntervalException;
use yii\validators\Validator;


/**
 * Class CarbonStringIntervalValidator
 *
 * @package GlueAgency\schedule\validators
 * @author Glue Agency <info@glue.be>
 */
class CarbonStringIntervalValidator extends Validator
{
    // Properties
    // =========================================================================

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function validateValue($value)
    {
        try {
            if(CarbonInterval::make($value) === null) {
                return ['Not a valid Carbon interval "{value}"', ['value' => $value]];
            }

            return null;
        } catch(InvalidIntervalException $e) {
            return ['Not a valid Carbon interval "{value}"', ['value' => $value]];
        }
    }
}
