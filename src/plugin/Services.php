<?php
/*
 * Schedule plugin for CraftCMS
 *
 * https://github.com/panlatent/schedule
 */

namespace panlatent\schedule\plugin;

use craft\errors\DeprecationException;
use panlatent\schedule\Builder;
use panlatent\schedule\services\Logs;
use panlatent\schedule\services\Schedules;
use panlatent\schedule\services\Timers;
use yii\base\InvalidConfigException;

/**
 * Trait Services
 *
 * @package panlatent\schedule\plugin
 * @property-read Builder $builder
 * @property-read Schedules $schedules
 * @property-read Logs $logs
 * @author Panlatent <panlatent@gmail.com>
 */
trait Services
{
    /**
     * @since 0.3.2
     * @return Builder
     */
    public function createBuilder(): Builder
    {
        return new Builder();
    }

    /**
     * @return Builder|null
     * @throws InvalidConfigException|DeprecationException
     * @deprecated
     * @see createBuilder()
     */
    public function getBuilder(): Builder|null
    {
        \Craft::$app->getDeprecator()->log('schedule.getBuilder()', 'This method has been deprecated, singleton objects will have bad problems in persistent mode.');
        return $this->get('builder');
    }

    /**
     * @return Schedules
     * @throws InvalidConfigException
     */
    public function getSchedules(): Schedules
    {
        return $this->get('schedules');
    }

    /**
     * @return Timers
     * @throws InvalidConfigException
     */
    public function getTimers(): Timers
    {
        return $this->get('timers');
    }

    /**
     * @return Logs
     * @throws InvalidConfigException
     */
    public function getLogs(): Logs
    {
        return $this->get('logs');
    }

    /**
     * Set service components.
     */
    private function _setComponents(): void
    {
        $this->setComponents([
            'builder' => Builder::class,
            'schedules' => Schedules::class,
            'timers' => Timers::class,
            'logs' => Logs::class,
        ]);
    }
}
