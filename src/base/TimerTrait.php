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
    public ?int $scheduleId;

    /**
     * @var string|null
     */
    public ?string $minute;

    /**
     * @var string|null
     */
    public ?string $hour;

    /**
     * @var string|null
     */
    public ?string $day;

    /**
     * @var string|null
     */
    public ?string $month;

    /**
     * @var string|null
     */
    public ?string $week;

    /**
     * @var bool|null
     */
    public ?bool $enabled = true;

    /**
     * @var int|null
     */
    public ?int $sortOrder;
}
