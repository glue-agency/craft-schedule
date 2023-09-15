<?php
/*
 * Schedule plugin for CraftCMS
 *
 * https://github.com/glue-agency/craft-schedule
 */

namespace GlueAgency\schedule;

use GlueAgency\schedule\base\ScheduleInterface;
use GlueAgency\schedule\events\ScheduleBuildEvent;
use omnilight\scheduling\Event;
use omnilight\scheduling\Schedule;
use yii\base\InvalidConfigException;

/**
 * Class Builder
 *
 * @package GlueAgency\schedule
 * @method  Event call(callable $callback, array $parameters = [])
 * @author Glue Agency <info@glue.be>
 */
class Builder extends Schedule
{
    // Constants
    // =========================================================================

    /**
     * @event ScheduleBuildEvent
     */
    const EVENT_BEFORE_BUILD = 'beforeBuild';

    /**
     * @event ScheduleBuildEvent
     */
    const EVENT_AFTER_BUILD = 'afterBuild';

    /**
     * @inheritdoc
     */
    public $cliScriptName = 'craft';

    /**
     * @param ScheduleInterface $schedule
     */
    public function schedule(ScheduleInterface $schedule)
    {
        $schedule->build($this);
    }

    /**
     * Build schedules.
     * @throws InvalidConfigException
     * @throws InvalidConfigException
     */
    public function build(bool $force = false)
    {
        if (!$this->beforeBuild()) {
            return $this;
        }

        $schedules = Plugin::$plugin->getSchedules();
        $schedules->force = $force;
        foreach ($schedules->getActiveSchedules() as $schedule) {
            $schedule->build($this);
        }

        $this->afterBuild();

        return $this;
    }

    /**
     * Before build.
     *
     * @return bool
     */
    public function beforeBuild(): bool
    {
        $event = new ScheduleBuildEvent([
            'builder' => $this,
            'events' => $this->_events,
        ]);

        if ($this->hasEventHandlers(static::EVENT_BEFORE_BUILD)) {
            $this->trigger(static::EVENT_BEFORE_BUILD, $event);
            $this->_events = $event->events;

            return $event->isValid;
        }

        return true;
    }

    /**
     * After build
     */
    public function afterBuild()
    {
        $event = new ScheduleBuildEvent([
            'builder' => $this,
            'events' => $this->_events,
        ]);

        if ($this->hasEventHandlers(static::EVENT_AFTER_BUILD)) {
            $this->trigger(static::EVENT_AFTER_BUILD, $event);
            $this->_events = $event->events;
        }
    }
}
