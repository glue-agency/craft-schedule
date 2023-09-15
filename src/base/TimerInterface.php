<?php
/*
 * Schedule plugin for CraftCMS
 *
 * https://github.com/glue-agency/craft-schedule
 */

namespace GlueAgency\schedule\base;

/**
 * Interface TimerInterface
 *
 * @package GlueAgency\schedule\base
 * @author Glue Agency <info@glue.be>
 */
interface TimerInterface
{
    /**
     * @see \GlueAgency\schedule\services\Timers::getAllTimers()
     *
     * @return bool whether to run the timer.
     */
    public function isValid(): bool;

    /**
     * Returns cron expression.
     *
     * @return string
     */
    public function getCronExpression(): string;
}
