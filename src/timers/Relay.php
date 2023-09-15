<?php
/*
 * Schedule plugin for CraftCMS
 *
 * https://github.com/glue-agency/craft-schedule
 */

namespace GlueAgency\schedule\timers;

use Craft;
use DateInterval;
use DateTime;
use GlueAgency\schedule\base\Schedule;
use GlueAgency\schedule\base\Timer;

/**
 * Class Relay
 *
 * @package GlueAgency\schedule\timers
 * @author Glue Agency <info@glue.be>
 */
class Relay extends Timer
{
    // Static Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('schedule', 'Relay');
    }

    // Properties
    // =========================================================================

    /**
     * @var int Wait time (minute)
     */
    public $wait = 1;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        $rules = parent::rules();
        $rules[] = [['wait'], 'integer', 'min' => 1];

        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $attributeLabels = parent::attributeLabels();
        $attributeLabels['wait'] = Craft::t('schedule', 'Wait Time');

        return $attributeLabels;
    }

    /**
     * @inheritdoc
     */
    public function getCronExpression(): string
    {
        /** @var Schedule $schedule */
        $schedule = $this->getSchedule();

        if (!$schedule->getLastFinishedDate()) {
            return '* * * * * *';
        }

        $date = $schedule->getLastFinishedDate()->add(new DateInterval("PT{$this->wait}M"));
        if ($date->format('YmdHi') < date('YmdHi')) {
            $date = new DateTime('now');
        }

        return $date->format('i H d m * *');
    }

    /**
     * @inheritdoc
     */
    public function getCronDescription(): string
    {
        return Craft::t('schedule', 'Wait {wait} minutes after last executed', [
            'wait' => $this->wait,
        ]);
    }

    /**
     * @inheritdoc
     */
    public function getSettingsHtml(): ?string
    {
        return Craft::$app->getView()->renderTemplate('schedule/_components/timers/Relay', [
            'timer' => $this,
        ]);
    }
}
