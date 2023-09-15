<?php

namespace GlueAgency\schedule\migrations;

use craft\db\Migration;
use craft\db\Query;
use GlueAgency\schedule\records\Schedule;
use GlueAgency\schedule\records\Timer;

/**
 * m230915_072628_change_schedule_type_name migration.
 */
class m230915_072628_change_schedule_type_name extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp(): bool
    {
        $schedules = (new Query())
            ->select(['id', 'type', 'name'])
            ->from('{{%schedules}}')
            ->all();

        $timers = (new Query())
            ->select(['id', 'type'])
            ->from('{{%scheduletimers}}')
            ->all();

        foreach ($schedules as $result) {
            $name = $result['name'];
            $oldType = $result['type'];
            $newType = str_replace('panlatent', 'GlueAgency', $oldType);

            $schedule = Schedule::findOne($result['id']);
            $schedule->setAttribute('type', $newType);
            $schedule->save(false);
            echo "    > Updated schedule: $name\n";
        }

        foreach ($timers as $result) {
            $oldType = $result['type'];
            $newType = str_replace('panlatent', 'GlueAgency', $oldType);

            $timer = Timer::findOne($result['id']);
            $timer->setAttribute('type', $newType);
            $timer->save(false);
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown(): bool
    {
        echo "m230915_072628_change_schedule_type_name cannot be reverted.\n";
        return false;
    }
}
