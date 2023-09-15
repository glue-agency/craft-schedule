<?php
/*
 * Schedule plugin for CraftCMS
 *
 * https://github.com/glue-agency/craft-schedule
 */

namespace GlueAgency\schedule\models;

use craft\base\Model;
use craft\helpers\App;
use GlueAgency\schedule\validators\CarbonStringIntervalValidator;
use GlueAgency\schedule\validators\PhpBinaryValidator;

/**
 * Class Settings
 *
 * @package GlueAgency\schedule\models
 * @author Glue Agency <info@glue.be>
 */
class Settings extends Model
{
    // Properties
    // =========================================================================

    /**
     * @var string PHP binary path.
     */
    public $cliPath = 'php';

    /**
     * @var string|null
     */
    public $customName;

    /**
     * @var string|null
     */
    public $customCpNavName;

    /**
     * @var string|null
     */
    public $logExpireAfter;

    /**
     * @deprecated
     * @var bool
     */
    public $modifyPluginName = false;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['cliPath', 'customName', 'customCpNavName', 'logExpireAfter'], 'string'],
            [['modifyPluginName'], 'boolean'],
            [['cliPath'], PhpBinaryValidator::class, 'minVersion' => '7.1', 'allowParseEnv' => true],
            [['logExpireAfter'], CarbonStringIntervalValidator::class],
        ];
    }

    /**
     * @return string
     */
    public function getCliPath(): string
    {
        return App::parseEnv($this->cliPath) ?: 'php';
    }
}
