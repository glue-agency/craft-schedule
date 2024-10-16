<?php
/*
 * Schedule plugin for CraftCMS
 *
 * https://github.com/glue-agency/craft-schedule
 */

namespace GlueAgency\schedule\base;

use Craft;
use craft\base\SavableComponent;
use craft\db\mysql\Schema as MysqlSchema;
use craft\helpers\ArrayHelper;
use DateTime;
use GlueAgency\schedule\Builder;
use GlueAgency\schedule\db\Table;
use GlueAgency\schedule\events\ScheduleEvent;
use GlueAgency\schedule\helpers\PrecisionDateTimeHelper;
use GlueAgency\schedule\models\ScheduleGroup;
use GlueAgency\schedule\models\ScheduleLog;
use GlueAgency\schedule\Plugin;
use Throwable;
use yii\base\InvalidConfigException;
use yii\db\Query;


/**
 * Class Schedule
 *
 * @package GlueAgency\schedule\base
 * @property ScheduleGroup $group
 * @property TimerInterface[] $timers
 * @property-read ScheduleLog[] $logs
 * @property-read int $totalLogs
 * @property-read string $cronExpression
 * @property-read string $cronDescription
 * @property-read DateTime $lastFinishedDate
 * @property-read int $lastDuration
 * @author Glue Agency <info@glue.be>
 */
abstract class Schedule extends SavableComponent implements ScheduleInterface
{
    // Traits
    // =========================================================================

    use ScheduleTrait;

    // Events
    // =========================================================================

    /**
     * @event ScheduleEvent
     */
    const EVENT_BEFORE_RUN = 'beforeRun';

    /**
     * @event ScheduleEvent
     */
    const EVENT_AFTER_RUN = 'afterRun';

    // Statuses
    // =========================================================================

    const STATUS_PREPARING = 'preparing';
    const STATUS_PROCESSING = 'processing';
    const STATUS_SUCCESSFUL = 'successful';
    const STATUS_FAILED = 'failed';

    // Static Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function isRunnable(): bool
    {
        return false;
    }

    // Properties
    // =========================================================================

    /**
     * @var ScheduleGroup|null
     */
    private $_group;

    /**
     * @var TimerInterface[]|null
     */
    private $_timers;

    /**
     * @var DateTime|null
     */
    private $_lastFinishedDate;

    // Public Methods
    // =========================================================================

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->name;
    }

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        return [
            [['name'], 'required'],
            [['id', 'groupId', 'lastStartedTime', 'lastFinishedTime'], 'integer'],
            [['name', 'description', 'user'], 'string'],
            [['enabledLog', 'lastStatus'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributes(): array
    {
        $attributes = parent::attributes();
        $attributes[] = 'lastFinishedDate';
        $attributes[] = 'lastDuration';

        return $attributes;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels(): array
    {
        return [
            'name' => Craft::t('app', 'Name'),
            'groupId' => Craft::t('app', 'Group'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function isValid(): bool
    {
        return $this->enabled;
    }

    /**
     * @return ScheduleGroup|null
     * @throws InvalidConfigException
     */
    public function getGroup(): ?ScheduleGroup
    {
        if ($this->_group !== null) {
            return $this->_group;
        }

        if (!$this->groupId) {
            return null;
        }

        return $this->_group = Plugin::$plugin->getSchedules()->getGroupById($this->groupId);
    }

    /**
     * @return TimerInterface[]
     * @throws InvalidConfigException
     */
    public function getTimers(): array
    {
        if ($this->_timers !== null) {
            return $this->_timers;
        }

        if (!$this->id) {
            return [];
        }

        $this->_timers = Plugin::getInstance()->getTimers()->getTimersByScheduleId($this->id);

        return $this->_timers;
    }

    /**
     * @param TimerInterface[]|null $timers
     */
    public function setTimers(array $timers): void
    {
        $this->_timers = $timers;
    }

    /**
     * @return TimerInterface[]
     */
    public function getActiveTimers(): array
    {
        return ArrayHelper::where($this->getTimers(), 'enabled');
    }

    /**
     * @return ScheduleLog[]
     */
    public function getLogs(): array
    {
        if (!$this->id) {
            return [];
        }

        return Plugin::$plugin->getLogs()
            ->findLogs(['scheduleId' => $this->id]);
    }

    /**
     * @return int
     */
    public function getTotalLogs(): int
    {
        if (!$this->id) {
            return 0;
        }

        return Plugin::$plugin->getLogs()->getTotalLogs(['scheduleId' => $this->id]);
    }

    /**
     * @return DateTime|null
     */
    public function getLastFinishedDate(): ?DateTime
    {
        if ($this->_lastFinishedDate !== null) {
            return $this->_lastFinishedDate;
        }

        if (!$this->lastFinishedTime) {
            return null;
        }

        $this->_lastFinishedDate = PrecisionDateTimeHelper::toDateTime($this->lastFinishedTime);

        return $this->_lastFinishedDate;
    }

    /**
     * @return int
     */
    public function getLastDuration(): int
    {
        if (!$this->lastStartedTime || !$this->lastFinishedTime) {
            return 0;
        }

        return $this->lastFinishedTime - $this->lastStartedTime;
    }

    /**
     * @inheritdoc
     */
    public function build(Builder $builder): void
    {
        if (static::isRunnable()) {
            foreach ($this->getActiveTimers() as $timer) {
                $builder->call([$this, 'run'])
                    ->cron($timer->getCronExpression());
            }

        }
    }

    /**
     * @inheritdoc
     */
    public function renderLogContent(ScheduleLog $log): string
    {
        return $log->output;
    }

    /**
     * @return bool
     */
    public function run(): bool
    {
        if (!$this->beforeRun()) {
            return false;
        }

        $id = null;
        if ($this->enabledLog) {
            $id = $this->beginLog();
        }

        try {
            $successful = $this->execute($id);
        } catch (Throwable $exception) {
            $reason = $exception->getMessage();
            Craft::error($reason, __METHOD__);

            $successful = false;
        }

        if ($this->enabledLog) {
            $this->endLog($id, $successful ? self::STATUS_SUCCESSFUL : self::STATUS_FAILED, $reason ?? null);
        }

        $this->afterRun($successful);

        return true;
    }

    public function beforeRun(): bool
    {
        if ($this->hasEventHandlers(self::EVENT_BEFORE_RUN)) {
            $this->trigger(self::EVENT_BEFORE_RUN, new ScheduleEvent([
                'schedule' => $this,
            ]));
        }

        Craft::$app->getDb()->createCommand()
            ->update(Table::SCHEDULES, [
                'lastStartedTime' => round(microtime(true) * 1000),
            ], [
                'id' => $this->id,
            ])
            ->execute();

        return true;
    }

    public function afterRun(bool $successful): void
    {
        if ($this->hasEventHandlers(self::EVENT_AFTER_RUN)) {
            $this->trigger(self::EVENT_AFTER_RUN, new ScheduleEvent([
                'schedule' => $this,
            ]));
        }

        Craft::$app->getDb()->createCommand()
            ->update(Table::SCHEDULES, [
                'lastFinishedTime' => PrecisionDateTimeHelper::time(),
                'lastStatus' => $successful,
            ], [
                'id' => $this->id,
            ])
            ->execute();
    }

    // Protected Methods
    // =========================================================================

    /**
     * Execute somethings.
     *
     * @param int|null $logId
     * @return bool
     */
    protected function execute(int $logId = null): bool
    {
        if ($this->enabledLog) {
            Craft::info(sprintf("Schedule #%d running with log ID: #%d", $this->id, $logId), __METHOD__);
        } else {
            Craft::info(sprintf("Schedule #%d running without log", $this->id), __METHOD__);
        }

        return true;
    }

    /**
     * @return int Log ID
     */
    protected function beginLog(): int
    {
        $db = Craft::$app->getDb();

        $sortOrderQuery = (new Query())
            ->select(['[[sortOrder]] + 1 AS sortOrder'])
            ->from(Table::SCHEDULELOGS)
            ->where([
                'scheduleId' => $this->id,
            ])
            ->orderBy('sortOrder DESC')
            ->limit(1);

        $db->createCommand()
            ->insert(Table::SCHEDULELOGS, [
                'scheduleId' => $this->id,
                'status' => self::STATUS_PREPARING,
                'startTime' => PrecisionDateTimeHelper::time(),
                'output' => '',
                'sortOrder' => $db->getSchema() instanceof MysqlSchema ?  // MySQL not support same table sub query on insert.
                    $sortOrderQuery->scalar() :
                    $sortOrderQuery,
            ])
            ->execute();

        return $db->getLastInsertID();
    }

    /**
     * @param int $id Log ID
     * @param string $status
     * @param string|null $reason
     */
    protected function endLog(int $id, string $status, string $reason = null)
    {
        Craft::$app->getDb()->createCommand()
            ->update(Table::SCHEDULELOGS, [
                'status' => $status,
                'reason' => $reason,
                'endTime' => PrecisionDateTimeHelper::time(),
            ], [
                'id' => $id,
            ])
            ->execute();
    }
}
