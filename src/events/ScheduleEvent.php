<?php
/*
 * Schedule plugin for CraftCMS
 *
 * https://github.com/glue-agency/craft-schedule
 */

namespace GlueAgency\schedule\events;

use GlueAgency\schedule\base\ScheduleInterface;
use yii\base\Event;

/**
 * Class ScheduleEvent
 *
 * @package GlueAgency\schedule\events
 * @author Glue Agency <info@glue.be>
 */
class ScheduleEvent extends Event
{
    /**
     * @var ScheduleInterface
     */
    public ScheduleInterface $schedule;

    /**
     * @var bool
     */
    public bool $isNew = false;
}
