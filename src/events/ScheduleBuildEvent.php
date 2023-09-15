<?php
/*
 * Schedule plugin for CraftCMS
 *
 * https://github.com/glue-agency/craft-schedule
 */

namespace GlueAgency\schedule\events;

use GlueAgency\schedule\Builder;
use yii\base\Event;

/**
 * Class ScheduleBuildEvent
 *
 * @package GlueAgency\schedule\events
 * @author Glue Agency <info@glue.be>
 */
class ScheduleBuildEvent extends Event
{
    /**
     * @var Builder|null
     */
    public ?Builder $builder;

    /**
     * @var array[]|null
     */
    public ?array $events;

    /**
     * @var bool
     */
    public bool $isValid = true;
}
