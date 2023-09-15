<?php
/*
 * Schedule plugin for CraftCMS
 *
 * https://github.com/glue-agency/craft-schedule
 */

namespace GlueAgency\schedule\models;

use yii\base\Model;

/**
 * Class ScheduleCriteria
 *
 * @package GlueAgency\schedule\models
 * @author Glue Agency <info@glue.be>
 */
class ScheduleCriteria extends Model
{
    /**
     * @var string|null
     */
    public $search;

    /**
     * @var bool|null
     */
    public $hasLogs;

    /**
     * @var bool|null
     */
    public $enabledLog;

    /**
     * @var string|null
     */
    public $sortOrder;

    /**
     * @var int|null
     */
    public $offset;

    /**
     * @var int|null
     */
    public $limit;
}
