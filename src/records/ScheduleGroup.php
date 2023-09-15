<?php
/*
 * Schedule plugin for CraftCMS
 *
 * https://github.com/glue-agency/craft-schedule
 */

namespace GlueAgency\schedule\records;

use craft\db\ActiveRecord;

/**
 * Class ScheduleGroup
 *
 * @package GlueAgency\schedule\records
 * @property int $id
 * @property string $name
 * @author Glue Agency <info@glue.be>
 */
class ScheduleGroup extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%schedulegroups}}';
    }
}
