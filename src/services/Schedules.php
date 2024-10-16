<?php
/*
 * Schedule plugin for CraftCMS
 *
 * https://github.com/glue-agency/craft-schedule
 */

namespace GlueAgency\schedule\services;

use Craft;
use craft\errors\MissingComponentException;
use craft\events\RegisterComponentTypesEvent;
use craft\helpers\Component as ComponentHelper;
use craft\helpers\Db;
use craft\helpers\Json;
use craft\web\Request;
use GlueAgency\schedule\base\Schedule;
use GlueAgency\schedule\base\ScheduleInterface;
use GlueAgency\schedule\db\Table;
use GlueAgency\schedule\errors\ScheduleException;
use GlueAgency\schedule\errors\ScheduleGroupException;
use GlueAgency\schedule\events\ScheduleEvent;
use GlueAgency\schedule\events\ScheduleGroupEvent;
use GlueAgency\schedule\models\ScheduleCriteria;
use GlueAgency\schedule\models\ScheduleGroup;
use GlueAgency\schedule\records\Schedule as ScheduleRecord;
use GlueAgency\schedule\records\ScheduleGroup as ScheduleGroupRecord;
use GlueAgency\schedule\schedules\Console;
use GlueAgency\schedule\schedules\Event;
use GlueAgency\schedule\schedules\HttpRequest;
use GlueAgency\schedule\schedules\MissingSchedule;
use GlueAgency\schedule\schedules\Queue;
use Throwable;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\db\Exception;
use yii\db\Expression;
use yii\db\Query;

/**
 * Class Schedules
 *
 * @package GlueAgency\schedule\services
 * @author Glue Agency <info@glue.be>
 */
class Schedules extends Component
{
    // Constants
    // =========================================================================

    /**
     * @event RegisterComponentTypesEvent
     */
    const EVENT_REGISTER_ALL_SCHEDULE_TYPES = 'registerAllScheduleTypes';

    /**
     * @event ScheduleGroupEvent The event that is triggered before a tag group is saved.
     */
    const EVENT_BEFORE_SAVE_GROUP = 'beforeSaveGroup';

    /**
     * @event ScheduleGroupEvent The event that is triggered after a tag group is saved.
     */
    const EVENT_AFTER_SAVE_GROUP = 'afterSaveGroup';

    /**
     * @event ScheduleGroupEvent The event that is triggered before a tag group is deleted.
     */
    const EVENT_BEFORE_DELETE_GROUP = 'beforeDeleteGroup';

    /**
     * @event ScheduleGroupEvent The event that is triggered after a tag group is deleted.
     */
    const EVENT_AFTER_DELETE_GROUP = 'afterDeleteGroup';

    /**
     * @event ScheduleEvent
     */
    const EVENT_BEFORE_SAVE_SCHEDULE = 'beforeSaveSchedule';

    /**
     * @event ScheduleEvent
     */
    const EVENT_AFTER_SAVE_SCHEDULE = 'afterSaveSchedule';

    /**
     * @event ScheduleEvent
     */
    const EVENT_BEFORE_DELETE_SCHEDULE = 'beforeDeleteSchedule';

    /**
     * @event ScheduleEvent
     */
    const EVENT_AFTER_DELETE_SCHEDULE = 'afterDeleteSchedule';

    // Properties
    // =========================================================================

    /**
     * @var bool Force fetch groups or schedules. (Not cache)
     */
    public $force = false;

    /**
     * @var bool
     */
    private $_fetchedAllGroups = false;

    /**
     * @var ScheduleGroup[]|null
     */
    private $_groupsById;

    /**
     * @var ScheduleGroup[]|null
     */
    private $_groupsByName;

    /**
     * @var bool
     */
    private $_fetchedAllSchedules = false;

    /**
     * @var ScheduleInterface[]|null
     */
    private $_schedulesById;

    /**
     * Returns all category groups.
     *
     * @return ScheduleGroup[]
     */
    public function getAllGroups(): array
    {
        if ($this->_fetchedAllGroups) {
            return array_values($this->_groupsById);
        }

        $this->_groupsById = [];

        $results = $this->_createGroupQuery()->all();

        foreach ($results as $result) {
            $group = $this->createGroup($result);
            $this->_groupsById[$group->id] = $group;
            $this->_groupsByName[$group->name] = $group;
        }

        if (!$this->force) {
            $this->_fetchedAllGroups = true;
        }

        return array_values($this->_groupsById);
    }

    /**
     * Returns a group by its ID.
     *
     * @param int $groupId
     * @return ScheduleGroup|null
     */
    public function getGroupById(int $groupId): ?ScheduleGroup
    {
        if ($this->_groupsById && array_key_exists($groupId, $this->_groupsById)) {
            return $this->_groupsById[$groupId];
        }

        if ($this->_fetchedAllGroups) {
            return null;
        }

        $result = $this->_createGroupQuery()
            ->where(['id' => $groupId])
            ->one();

        return $this->_groupsById[$groupId] = $result ? $this->createGroup($result) : null;
    }

    /**
     * Returns a group by its name.
     *
     * @param string $name
     * @return ScheduleGroup|null
     */
    public function getGroupByHandle(string $name): ?ScheduleGroup
    {
        if ($this->_groupsByName && array_key_exists($name, $this->_groupsByName)) {
            return $this->_groupsByName[$name];
        }

        if ($this->_fetchedAllGroups) {
            return null;
        }

        $result = $this->_createGroupQuery()
            ->where(['handle' => $name])
            ->one();

        return $this->_groupsByName[$name] = $result ? $this->createGroup($result) : null;
    }

    /**
     * Create a group.
     *
     * @param mixed $config
     * @return ScheduleGroup
     */
    public function createGroup(mixed $config): ScheduleGroup
    {
        return new ScheduleGroup($config);
    }

    /**
     * Save a group.
     *
     * @param ScheduleGroup $group
     * @param bool $runValidation
     * @return bool
     * @throws ScheduleGroupException
     */
    public function saveGroup(ScheduleGroup $group, bool $runValidation = true): bool
    {
        $isNewGroup = !$group->id;

        if ($this->hasEventHandlers(self::EVENT_BEFORE_SAVE_GROUP)) {
            $this->trigger(self::EVENT_BEFORE_SAVE_GROUP, new ScheduleGroupEvent([
                'group' => $group,
                'isNew' => $isNewGroup,
            ]));
        }

        if ($runValidation && !$group->validate()) {
            Craft::info('Schedule group not saved due to validation error.', __METHOD__);
            return false;
        }

        if (!$isNewGroup) {
            $groupRecord = ScheduleGroupRecord::findOne(['id' => $group->id]);
            if (!$groupRecord) {
                throw new ScheduleGroupException("No group exists with the ID “{$group->id}“");
            }
        } else {
            $groupRecord = new ScheduleGroupRecord();
        }

        $groupRecord->name = $group->name;
        $groupRecord->save(false);

        if ($isNewGroup) {
            $group->id = $groupRecord->id;
        }

        if ($this->hasEventHandlers(self::EVENT_AFTER_SAVE_GROUP)) {
            $this->trigger(self::EVENT_AFTER_SAVE_GROUP, new ScheduleGroupEvent([
                'group' => $group,
                'isNew' => $isNewGroup,
            ]));
        }

        return true;
    }

    /**
     * Delete a group.
     *
     * @param ScheduleGroup $group
     * @return bool
     * @throws Exception
     */
    public function deleteGroup(ScheduleGroup $group): bool
    {
        if ($this->hasEventHandlers(self::EVENT_BEFORE_DELETE_GROUP)) {
            $this->trigger(self::EVENT_BEFORE_DELETE_GROUP, new ScheduleGroupEvent([
                'group' => $group,
            ]));
        }

        Craft::$app->getDb()->createCommand()->delete('{{%schedulegroups}}', [
            'id' => $group->id,
        ])->execute();

        if ($this->hasEventHandlers(self::EVENT_AFTER_DELETE_GROUP)) {
            $this->trigger(self::EVENT_AFTER_DELETE_GROUP, new ScheduleGroupEvent([
                'group' => $group,
            ]));
        }

        return true;
    }

    // Schedules
    // =========================================================================

    /**
     * @return string[]
     */
    public function getAllScheduleTypes(): array
    {
        $types = [
            HttpRequest::class,
            Console::class,
            Event::class,
            Queue::class,
        ];

        $event = new RegisterComponentTypesEvent([
            'types' => $types,
        ]);

        $this->trigger(static::EVENT_REGISTER_ALL_SCHEDULE_TYPES, $event);

        return $event->types;
    }

    /**
     * @return ScheduleInterface[]
     * @throws InvalidConfigException
     */
    public function getAllSchedules(): array
    {
        if ($this->_fetchedAllSchedules) {
            return array_values($this->_schedulesById);
        }

        $this->_schedulesById = [];

        $results = $this->_createScheduleQuery()->all();
        foreach ($results as $result) {
            /** @var Schedule $schedule */
            $schedule = $this->createSchedule($result);
            $this->_schedulesById[$schedule->id] = $schedule;
        }

        if (!$this->force) {
            $this->_fetchedAllSchedules = true;
        }

        return array_values($this->_schedulesById);
    }

    /**
     * @return ScheduleInterface[]
     * @throws InvalidConfigException
     */
    public function getActiveSchedules(): array
    {
        return array_filter($this->getAllSchedules(), function(ScheduleInterface $schedule) {
            return $schedule->isValid();
        });
    }

    /**
     * @param int|string|null $groupId
     * @return ScheduleInterface[]
     * @throws InvalidConfigException
     */
    public function getSchedulesByGroupId(int|string $groupId = null): array
    {
        $schedules = [];

        if ($groupId === null) {
            $results = $this->_createScheduleQuery()
                ->orderBy('name')
                ->all();

        } else {
            if ($groupId === 'ungrouped') $groupId = null;
            $results = $this->_createScheduleQuery()
                ->where(['groupId' => $groupId])
                ->all();
        }

        foreach ($results as $result) {
            /** @var Schedule $schedule */
            $schedule = $this->createSchedule($result);
            $this->_schedulesById[$schedule->id] = $schedule;
            $schedules[] = $schedule;
        }

        return $schedules;
    }

    /**
     * @param int $scheduleId
     * @return ScheduleInterface|null
     * @throws InvalidConfigException
     */
    public function getScheduleById(int $scheduleId): ?ScheduleInterface
    {
        if ($this->_schedulesById && array_key_exists($scheduleId, $this->_schedulesById)) {
            return $this->_schedulesById[$scheduleId];
        }

        if ($this->_fetchedAllSchedules) {
            return null;
        }

        $result = $this->_createScheduleQuery()
            ->where(['id' => $scheduleId])
            ->one();

        return $this->_schedulesById[$scheduleId] = $result ? $this->createSchedule($result) : null;
    }

    /**
     * @param array|ScheduleCriteria $criteria
     * @return ScheduleInterface[]
     * @throws InvalidConfigException
     */
    public function findSchedules(array|ScheduleCriteria $criteria): array
    {
        if (!$criteria instanceof ScheduleCriteria) {
            $criteria = new ScheduleCriteria($criteria);
        }

        $query = $this->_createScheduleQuery()
            ->orderBy($criteria->sortOrder)
            ->offset($criteria->offset)
            ->limit($criteria->limit);

        $this->_applyConditions($query, $criteria);

        $schedules = [];
        $results = $query->all();
        foreach ($results as $result) {
            $schedules[] = $this->createSchedule($result);
        }

        return $schedules;
    }

    /**
     * @param array|ScheduleCriteria $criteria
     * @return ScheduleInterface|null
     * @throws InvalidConfigException
     */
    public function findSchedule(array|ScheduleCriteria $criteria): ?ScheduleInterface
    {
        if (!$criteria instanceof ScheduleCriteria) {
            $criteria = new ScheduleCriteria($criteria);
        }

        $criteria->limit = 1;

        $results = $this->findSchedules($criteria);
        if (!$results) {
            return null;
        }

        return array_pop($results);
    }

    /**
     * @param array|ScheduleCriteria $criteria
     * @return int
     */
    public function getTotalSchedules(array|ScheduleCriteria $criteria = []): int
    {
        if (!$criteria instanceof ScheduleCriteria) {
            $criteria = new ScheduleCriteria($criteria);
        }
        $query = $this->_createScheduleQuery();
        $this->_applyConditions($query, $criteria);

        return $query->count('[[schedules.id]]');
    }

    /**
     * @param Request|null $request
     * @return ScheduleInterface
     * @throws InvalidConfigException
     */
    public function createScheduleFromRequest(Request $request = null): ScheduleInterface
    {
        if ($request === null) {
            $request = Craft::$app->getRequest();
        }

        $type = $request->getBodyParam('type');

        return $this->createSchedule([
            'id' => $request->getBodyParam('scheduleId'),
            'groupId' => $request->getBodyParam('groupId'),
            'name' => $request->getBodyParam('name'),
            'description' => $request->getBodyParam('description'),
            'type' => $type,
            'settings' => $request->getBodyParam('types.' . $type, []),
            'enabled' => (bool)$request->getBodyParam('enabled'),
            'enabledLog' => $request->getBodyParam('enabledLog'),
        ]);
    }

    /**
     * @param mixed $config
     * @return ScheduleInterface
     * @throws InvalidConfigException
     */
    public function createSchedule(mixed $config): ScheduleInterface
    {
        try {
            $schedule = ComponentHelper::createComponent($config, ScheduleInterface::class);
        } catch (MissingComponentException $exception) {
            unset($config['type']);
            $schedule = new MissingSchedule($config);
        }

        return $schedule;
    }

    /**
     * @param ScheduleInterface $schedule
     * @param bool $runValidation
     * @return bool
     * @throws Exception
     * @throws ScheduleException
     * @throws Throwable
     */
    public function saveSchedule(ScheduleInterface $schedule, bool $runValidation = true): bool
    {
        /** @var Schedule $schedule */
        $isNewSchedule = $schedule->getIsNew();

        if ($this->hasEventHandlers(self::EVENT_BEFORE_SAVE_SCHEDULE)) {
            $this->trigger(self::EVENT_BEFORE_SAVE_SCHEDULE, new ScheduleEvent([
                'schedule' => $schedule,
                'isNew' => $isNewSchedule,
            ]));
        }

        if (!$schedule->beforeSave($isNewSchedule)) {
            return false;
        }

        if ($runValidation && !$schedule->validate()) {
            Craft::info("Schedule not saved due to validation error.", __METHOD__);
            return false;
        }

        $transaction = Craft::$app->getDb()->beginTransaction();
        try {
            if (!$isNewSchedule) {
                $record = ScheduleRecord::findOne(['id' => $schedule->id]);
                if (!$record) {
                    throw new ScheduleException("No schedule exists with the ID: “{$schedule->id}“.");
                }
            } else {
                $record = new ScheduleRecord();
            }

            $record->groupId = $schedule->groupId;
            $record->name = $schedule->name;
            $record->description = $schedule->description;
            $record->type = get_class($schedule);
            $record->user = $schedule->user;
            $record->settings = Json::encode($schedule->getSettings());
            $record->enabled = (bool)$schedule->enabled;
            $record->enabledLog = (bool)$schedule->enabledLog;

            $record->save(false);

            if ($isNewSchedule) {
                $schedule->id = $record->id;
            }

            $transaction->commit();
        } catch (Throwable $exception) {
            $transaction->rollBack();

            throw $exception;
        }

        $this->_schedulesById[$schedule->id] = $schedule;

        $schedule->afterSave($isNewSchedule);

        if ($this->hasEventHandlers(self::EVENT_AFTER_SAVE_SCHEDULE)) {
            $this->trigger(self::EVENT_AFTER_SAVE_SCHEDULE, new ScheduleEvent([
                'schedule' => $schedule,
                'isNew' => $isNewSchedule,
            ]));
        }

        return true;
    }

    /**
     * Reorders schedules.
     *
     * @param array $scheduleIds
     * @return bool
     */
    public function reorderSchedules(array $scheduleIds): bool
    {
        $db = Craft::$app->getDb();

        $transaction = $db->beginTransaction();
        try {
            foreach ($scheduleIds as $scheduleOrder => $scheduleId) {
                $db->createCommand()->update('{{%schedules}}', [
                    'sortOrder' => $scheduleOrder,
                ], [
                    'id' => $scheduleId,
                ])->execute();
            }
            $transaction->commit();
        } catch (Throwable $e) {
            $transaction->rollBack();

            throw $e;
        }

        return true;
    }

    /**
     * Delete a schedule.
     *
     * @param ScheduleInterface $schedule
     * @return bool
     */
    public function deleteSchedule(ScheduleInterface $schedule): bool
    {
        /** @var Schedule $schedule */
        if ($this->hasEventHandlers(self::EVENT_BEFORE_DELETE_SCHEDULE)) {
            $this->trigger(self::EVENT_BEFORE_DELETE_SCHEDULE, new ScheduleEvent([
                'schedule' => $schedule,
            ]));
        }

        $schedule->beforeDelete();

        $db = Craft::$app->getDb();

        $transaction = $db->beginTransaction();
        try {
            $db->createCommand()->delete('{{%schedules}}', [
                'id' => $schedule->id,
            ])->execute();

            $transaction->commit();

            $schedule->afterDelete();
        } catch (Throwable $exception) {
            $transaction->rollBack();

            throw $exception;
        }

        if ($this->hasEventHandlers(self::EVENT_AFTER_DELETE_SCHEDULE)) {
            $this->trigger(self::EVENT_AFTER_DELETE_SCHEDULE, new ScheduleEvent([
                'schedule' => $schedule,
            ]));
        }

        return true;
    }

    // Private Methods
    // =========================================================================

    /**
     * @return Query
     */
    private function _createGroupQuery(): Query
    {
        return (new Query())
            ->select(['id', 'name'])
            ->from('{{%schedulegroups}}');
    }

    /**
     * @return Query
     */
    private function _createScheduleQuery(): Query
    {
        return (new Query())
            ->select([
                'schedules.id',
                'schedules.groupId',
                'schedules.name',
                'schedules.description',
                'schedules.type',
                'schedules.user',
                'schedules.settings',
                'schedules.enabled',
                'schedules.enabledLog',
                'schedules.lastStartedTime',
                'schedules.lastFinishedTime',
                'schedules.lastStatus',
                'schedules.dateCreated',
                'schedules.dateUpdated',
                'schedules.uid',
            ])
            ->from('{{%schedules}} schedules')
            ->orderBy('schedules.sortOrder');
    }

    /**
     * @param Query $query
     * @param ScheduleCriteria $criteria
     */
    private function _applyConditions(Query $query, ScheduleCriteria $criteria): void
    {
        if ($criteria->enabledLog !== null) {
            $query->andWhere(Db::parseParam('schedules.enabledLog', $criteria->enabledLog));
        }

        if ($criteria->hasLogs !== null) {
            $query->andWhere([
                '=',
                'schedules.id',
                (new Query())
                    ->select('logs.scheduleId')
                    ->from(Table::SCHEDULELOGS . ' logs')
                    ->where([
                        'logs.scheduleId' => new Expression('schedules.id')
                    ])
                    ->limit(1),
            ]);
        }

        if ($criteria->search) {
            $query->andWhere(['like', 'schedules.name',  $criteria->search]);
        }
    }
}
