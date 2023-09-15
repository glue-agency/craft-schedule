<?php
/*
 * Schedule plugin for CraftCMS
 *
 * https://github.com/glue-agency/craft-schedule
 */

namespace GlueAgency\schedule\events;

use GlueAgency\schedule\models\ScheduleGroup;
use yii\base\Event;

/**
 * Class ScheduleGroupEvent
 *
 * @package GlueAgency\schedule\events
 * @author Glue Agency <info@glue.be>
 */
class ScheduleGroupEvent extends Event
{
    /**
     * @var ScheduleGroup|null
     */
    public ?ScheduleGroup $group;

    /**
     * @var bool
     */
    public bool $isNew = false;
}
