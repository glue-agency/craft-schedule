<?php
/*
 * Schedule plugin for CraftCMS
 *
 * https://github.com/glue-agency/craft-schedule
 */

namespace GlueAgency\schedule\plugin;

use Craft;
use craft\errors\DeprecationException;
use GlueAgency\schedule\Builder;
use GlueAgency\schedule\services\Logs;
use GlueAgency\schedule\services\Schedules;
use GlueAgency\schedule\services\Timers;
use yii\base\InvalidConfigException;

/**
 * Trait Services
 *
 * @package GlueAgency\schedule\plugin
 * @property-read Builder $builder
 * @property-read Schedules $schedules
 * @property-read Logs $logs
 * @author Glue Agency <info@glue.be>
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
        Craft::$app->getDeprecator()->log('schedule.getBuilder()', 'This method has been deprecated, singleton objects will have bad problems in persistent mode.');
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
