<?php
/*
 * Schedule plugin for CraftCMS
 *
 * https://github.com/glue-agency/craft-schedule
 */



namespace GlueAgency\schedule\events;

use GlueAgency\schedule\base\TimerInterface;
use yii\base\Event;

/**
 * Class TimerEvent
 *
 * @package GlueAgency\schedule\events
 * @author Glue Agency <info@glue.be>
 */
class TimerEvent extends Event
{
    /**
     * @var TimerInterface|null
     */
    public ?TimerInterface $timer;

    /**
     * @var bool
     */
    public bool $isNew = false;
}
