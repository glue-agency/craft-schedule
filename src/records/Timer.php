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
 * Class Timer
 *
 * @package GlueAgency\schedule\records
 * @property int $id
 * @property int $scheduleId
 * @property string $type
 * @property string $minute
 * @property string $hour
 * @property string $day
 * @property string $month
 * @property string $week
 * @property string $settings
 * @property bool $enabled
 * @property int $sortOrder
 * @author Glue Agency <info@glue.be>
 */
class Timer extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return Table::SCHEDULETIMERS;
    }
}
