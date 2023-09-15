<?php
/*
 * Schedule plugin for CraftCMS
 *
 * https://github.com/glue-agency/craft-schedule
 */

namespace GlueAgency\schedule\models;

use GlueAgency\schedule\base\Schedule;
use yii\base\Model;

/**
 * Class LogCriteria
 *
 * @package GlueAgency\schedule\models
 * @author Glue Agency <info@glue.be>
 */
class LogCriteria extends Model
{
    // Properties
    // =========================================================================

    /**
     * @var int[]|int|null
     */
    public $scheduleId;

    /**
     * @var Schedule|string|null
     */
    public $schedule;

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
