<?php
/*
 * Schedule plugin for CraftCMS
 *
 * https://github.com/glue-agency/craft-schedule
 */

namespace GlueAgency\schedule\models;

use Craft;
use craft\base\Model;
use GlueAgency\schedule\base\ScheduleInterface;
use GlueAgency\schedule\Plugin;
use GlueAgency\schedule\records\ScheduleGroup as ScheduleGroupRecord;

/**
 * Class ScheduleGroup
 *
 * @package GlueAgency\schedule\models
 * @author Glue Agency <info@glue.be>
 */
class ScheduleGroup extends Model
{
    // Properties
    // =========================================================================

    /**
     * @var int|null
     */
    public $id;

    /**
     * @var string|null
     */
    public $name;

    /**
     * @var ScheduleInterface[]|null
     */
    private $_schedules;

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
            [['name'], 'string'],
            [['name'], 'unique', 'targetClass' => ScheduleGroupRecord::class, 'targetAttribute' => 'name']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Craft::t('app', 'ID'),
            'name' => Craft::t('app', 'name'),
        ];
    }

    /**
     * @return ScheduleInterface[]
     */
    public function getSchedules(): array
    {
        if ($this->_schedules !== null) {
            return $this->_schedules;
        }

        if (!$this->id) {
            return [];
        }

        return $this->_schedules = Plugin::$plugin->getSchedules()->getSchedulesByGroupId($this->id);
    }
}
