<?php
/*
 * Schedule plugin for CraftCMS
 *
 * https://github.com/glue-agency/craft-schedule
 */

namespace GlueAgency\schedule\base;

use craft\base\SavableComponentInterface;
use GlueAgency\schedule\Builder;
use GlueAgency\schedule\models\ScheduleLog;

/**
 * Interface ScheduleInterface
 *
 * @package GlueAgency\schedule\base
 * @author Glue Agency <info@glue.be>
 */
interface ScheduleInterface extends SavableComponentInterface
{
    /**
     * @return bool whether can execute run method
     */
    public static function isRunnable(): bool;

    /**
     * @return bool whether to run the schedule
     */
    public function isValid(): bool;

    /**
     * @param Builder $builder
     */
    public function build(Builder $builder);

    /**
     * @return bool
     */
    public function run(): bool;

    /**
     * @param ScheduleLog $log
     * @return string
     */
    public function renderLogContent(ScheduleLog $log): string;
}
