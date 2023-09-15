<?php
/*
 * Schedule plugin for CraftCMS
 *
 * https://github.com/glue-agency/craft-schedule
 */

namespace GlueAgency\schedule\migrations;

use craft\db\Migration;

/**
 * m190303_100542_add_schedules_description_column migration.
 */
class m190303_100542_add_schedules_description_column extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->addColumn('{{%schedules}}', 'description', $this->string()->after('handle'));
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $this->dropColumn('{{%schedules}}', 'description');
    }
}
