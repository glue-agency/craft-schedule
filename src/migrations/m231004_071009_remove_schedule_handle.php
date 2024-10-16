<?php

namespace GlueAgency\schedule\migrations;

use craft\db\Migration;

/**
 * m231004_071009_remove_schedule_handle migration.
 */
class m231004_071009_remove_schedule_handle extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp(): bool
    {
        // Place migration code here...
        $this->dropColumn('{{%schedules}}', 'handle');
        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown(): bool
    {
        echo "m231004_071009_remove_schedule_handle cannot be reverted.\n";
        return false;
    }
}
