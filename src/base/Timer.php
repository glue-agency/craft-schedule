<?php
/*
 * Schedule plugin for CraftCMS
 *
 * https://github.com/glue-agency/craft-schedule
 */

namespace GlueAgency\schedule\base;

use Craft;
use craft\base\SavableComponent;
use GlueAgency\schedule\helpers\CronHelper;
use GlueAgency\schedule\Plugin;
use yii\base\InvalidConfigException;

/**
 * Class Timer
 *
 * @package GlueAgency\schedule\base
 * @property ScheduleInterface $schedule
 * @author Glue Agency <info@glue.be>
 */
abstract class Timer extends SavableComponent implements TimerInterface
{
    // Traits
    // =========================================================================

    use TimerTrait;

    // Properties
    // =========================================================================

    /**
     * @var ScheduleInterface|null
     */
    private ?ScheduleInterface $_schedule;

    // Public Methods
    // =========================================================================

    /**
     * @return string
     */
    public function __toString()
    {
        return Craft::t('schedule', '# {order}' , [
            'order' => (int)$this->sortOrder
        ]);
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        $rules = parent::rules();
        $rules[] = [['scheduleId', 'enabled'], 'required'];
        $rules[] = [['scheduleId', 'sortOrder'], 'integer'];
        $rules[] = [['minute', 'hour', 'day', 'month', 'week'], 'string'];
        $rules[] = [['enabled'], 'boolean'];
        $rules[] = [['minute', 'hour', 'day', 'month', 'week'], function($property) {
            if ($this->$property === null || $this->$property === '') {
                $this->$property = '*';
            }
        }];

        return $rules;
    }

    /**
     * @inheritdoc
     * @throws InvalidConfigException
     */
    public function isValid(): bool
    {
        return $this->getSchedule()->isValid() && $this->enabled;
    }

    /**
     * @inheritdoc
     */
    public function getCronExpression(): string
    {
        return sprintf('%s %s %s %s %s *', $this->minute, $this->hour, $this->day, $this->month, $this->week);
    }

    /**
     * @return ScheduleInterface
     * @throws InvalidConfigException
     */
    public function getSchedule(): ScheduleInterface
    {
        if ($this->_schedule !== null) {
            return $this->_schedule;
        }

        if ($this->scheduleId === null) {
            throw new InvalidConfigException('The timer missing its schedule ID');
        }

        $this->_schedule = Plugin::getInstance()->getSchedules()->getScheduleById($this->scheduleId);
        if ($this->_schedule === null) {
            throw new InvalidConfigException('Invalid schedule ID: ' . $this->scheduleId);
        }

        return $this->_schedule;
    }

    /**
     * @param ScheduleInterface $schedule
     */
    public function setSchedule(ScheduleInterface $schedule): void
    {
        $this->_schedule = $schedule;
    }

    /**
     * @return string
     */
    public function getCronDescription(): string
    {
        return CronHelper::toDescription($this->getCronExpression());
    }

    /**
     * @param string $cron
     */
    public function setCronExpression(string $cron): void
    {
        list($this->minute, $this->hour, $this->day, $this->month, $this->week, ) = explode(' ', $cron);
    }
}
