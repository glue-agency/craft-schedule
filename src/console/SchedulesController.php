<?php
/*
 * Schedule plugin for CraftCMS
 *
 * https://github.com/glue-agency/craft-schedule
 */

namespace GlueAgency\schedule\console;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use Craft;
use GlueAgency\schedule\base\Schedule;
use GlueAgency\schedule\Plugin;
use GlueAgency\schedule\validators\CarbonStringIntervalValidator;
use omnilight\scheduling\Event;
use yii\base\InvalidConfigException;
use yii\console\Controller;
use yii\helpers\BaseConsole;
use yii\helpers\Console;

/**
 * Class ScheduleController
 *
 * @package GlueAgency\schedule\console
 * @author Glue Agency <info@glue.be>
 */
class SchedulesController extends Controller
{
    // Properties
    // =========================================================================

    /**
     * @inheritdoc
     */
    public $defaultAction = 'list';

    /**
     * @var bool|null Force flush schedule repository.
     */
    public $force;

    /**
     * @var bool|null Clear all logs.
     */
    public $all;

    /**
     * @var string|null Expiry offset for log clearing.
     */
    public $expire;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function options($actionID): array
    {
        $options = parent::options($actionID);
        switch ($actionID) {
            case 'run': // no break
            case 'listen':
                $options[] = 'force';
                break;
            case 'clear-logs':
                $options[] = 'all';
                $options[] = 'expire';
                break;
        }

        return $options;
    }

    /**
     * List all schedules.
     */
    public function actionList(): void
    {
        $schedules = Plugin::$plugin->getSchedules();

        $i = 0;
        if ($ungroupedSchedules = $schedules->getSchedulesByGroupId()) {
            $this->stdout(Craft::t('schedule', 'Ungrouped') . ": \n", BaseConsole::FG_YELLOW);
            foreach ($ungroupedSchedules as $schedule) {
                /** @var Schedule $schedule */
                $this->stdout(Console::renderColoredString("    > #$i %c$schedule->name\n"));
                ++$i;
            }
        }

        foreach ($schedules->getAllGroups() as $group) {
            $this->stdout("$group->name: \n", BaseConsole::FG_YELLOW);
            foreach ($group->getSchedules() as $schedule) {
                // @var Schedule $schedule

                $this->stdout(Console::renderColoredString("    > #$i %c$schedule->name\n"));
                ++$i;
            }
        }
    }

    /**
     * Run all schedules.
     *
     * @param Event[]|null $events
     * @throws InvalidConfigException
     */
    public function actionRun(array $events = null): void
    {
        if ($events === null) {
            $events = Plugin::$plugin->createBuilder()
                ->build($this->force ?? false)
                ->dueEvents(Craft::$app);
        }

        if (empty($events)) {
            $this->stdout("No scheduled commands are ready to run.\n");
            return;
        }

        foreach ($events as $event) {
            $command = $event->getSummaryForDisplay();
            $this->stdout("Running scheduled command: $command\n");

            $event->run(Craft::$app);

            Craft::info("Running scheduled command: $command", __METHOD__);
        }

        Craft::info("Running scheduled event total: " . count($events), __METHOD__);
    }

    /**
     * Run a permanent command to call crons run command every minute
     *
     * @return void
     */
    public function actionListen(): void
    {
        if ($this->force === null) {
            $this->force = true;
        }

        if (!$this->force) {
            $this->stdout("(!) Notice: Force option is disable, all schedules updates will not be synchronized.\n");
        }

        $waitSeconds = $this->nextMinute();
        $this->stdout("Waiting $waitSeconds seconds for next run of scheduler\n");
        sleep($waitSeconds);
        $this->triggerCronCall();
    }

    /**
     * Clear schedules logs with an optional time offset.
     *
     * @return void
     */
    public function actionClearLogs(): void
    {
        if ($this->all) {
            Plugin::$plugin->getLogs()->deleteAllLogs();
            $this->stdout("Deleted all logs \n", BaseConsole::FG_GREEN);

            return;
        }

        if (Plugin::getInstance()->getSettings()->logExpireAfter || $this->expire) {
            $expire    = $this->expire ?: Plugin::getInstance()->getSettings()->logExpireAfter;
            $validator = new CarbonStringIntervalValidator;

            if ($validator->validate($expire, $error)) {
                Plugin::$plugin->getLogs()->deleteLogsByDateCreated(
                    Carbon::now()->subtract($expire)
                );

                $interval = CarbonInterval::make($expire);
                $this->stdout("Deleted all logs older than {$interval->forHumans()} \n", BaseConsole::FG_GREEN);

                return;
            }

            $this->stderr($error . ".\n", BaseConsole::FG_RED);

            return;
        }

        $this->stdout("Provide the expire or all option to use this command. \n", BaseConsole::FG_YELLOW);
    }

    protected function triggerCronCall(array $events = null): void
    {
        $this->stdout("Running scheduler \n");
        $this->actionRun($events);
        $this->stdout("completed, sleeping... \n");

        $sec = $this->nextMinute();
        if ($sec >= 5) {
            // Use free time to get events.
            $events = Plugin::$plugin->createBuilder()
                ->build($this->force ?? false)
                ->dueEvents(Craft::$app);
        } else {
            $events = null;
        }

        sleep($sec < 5 ? $sec : $this->nextMinute());
        $this->triggerCronCall($events);
    }

    /**
     * @return int
     */
    protected function nextMinute(): int
    {
        $current = Carbon::now();
        return 60 - $current->second;
    }
}
