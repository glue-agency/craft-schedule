<?php
/*
 * Schedule plugin for CraftCMS
 *
 * https://github.com/glue-agency/craft-schedule
 */

namespace GlueAgency\schedule\base;

/**
 * Trait TimerTrait
 *
 * @package GlueAgency\schedule\base
 * @author Glue Agency <info@glue.be>
 */
trait TimerTrait
{
    // Properties
    // =========================================================================

    /**
     * @var int|null
     */
    public $scheduleId;

    /**
     * @var string|null
     */
    public $minute;

    /**
     * @var string|null
     */
    public $hour;

    /**
     * @var string|null
     */
    public $day;

    /**
     * @var string|null
     */
    public $month;

    /**
     * @var string|null
     */
    public $week;

    /**
     * @var bool|null
     */
    public $enabled = true;

    /**
     * @var int|null
     */
    public $sortOrder;
}
