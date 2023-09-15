<?php
/*
 * Schedule plugin for CraftCMS
 *
 * https://github.com/glue-agency/craft-schedule
 */

namespace GlueAgency\schedule\timers;

use Craft;
use GlueAgency\schedule\base\Timer;

/**
 * Class Custom
 *
 * @package GlueAgency\schedule\timers
 * @author Glue Agency <info@glue.be>
 */
class Custom extends Timer
{
    // Static Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('schedule', 'Custom');
    }

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function getSettingsHtml(): ?string
    {
        return Craft::$app->getView()->renderTemplate('schedule/_components/timers/Custom', [
            'minute' => $this->minute,
            'hour' => $this->hour,
            'day' => $this->day,
            'month' => $this->month,
            'week' => $this->week,
        ]);
    }
}
