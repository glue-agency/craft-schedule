<?php
/*
 * Schedule plugin for CraftCMS
 *
 * https://github.com/glue-agency/craft-schedule
 */

namespace GlueAgency\schedule\records;

use craft\db\ActiveRecord;
use GlueAgency\schedule\db\Table;

/**
 * Class Schedule
 *
 * @package GlueAgency\schedule\records
 * @property int $id
 * @property int $groupId
 * @property string $name
 * @property string $description
 * @property string $type
 * @property string $user
 * @property string $settings
 * @property bool $enabled
 * @property bool $enabledLog
 * @property int $lastStartedTime
 * @property int $lastFinishedTime
 * @property bool $lastStatus
 * @property int $sortOrder
 * @author Glue Agency <info@glue.be>
 */
class Schedule extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return Table::SCHEDULES;
    }
}
