<?php
/*
 * Schedule plugin for CraftCMS
 *
 * https://github.com/glue-agency/craft-schedule
 */

namespace GlueAgency\schedule\timers;

use Craft;
use craft\helpers\DateTimeHelper;
use DateTimeZone;
use Exception;
use GlueAgency\schedule\base\Timer;
use yii\base\InvalidConfigException;

/**
 * Class DateTime
 *
 * @package GlueAgency\schedule\timers
 * @author Glue Agency <info@glue.be>
 */
class DateTime extends Timer
{
    // Static Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('schedule', 'DateTime');
    }

    // Properties
    // =========================================================================

    /**
     * @var int|null
     */
    public ?int $year;

    /**
     * @var string|null
     */
    public ?string $timezone;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function rules(): array
    {
        $rules = parent::rules();
        $rules[] = [['datetime'], 'required'];
        $rules[] = [['datetime'], function($attribute) {
            if ($this->getDatetime()->getTimestamp() < time()) {
                $this->addError($attribute, Craft::t('schedule', 'Can not set a previous time'));
            }
        }];

        return $rules;
    }

    /**
     * @inheritdoc
     */
    public function attributes(): array
    {
        $attributes = parent::attributes();
        $attributes[] = 'datetime';

        return $attributes;
    }

    /**
     * @inheritdoc
     */
    public function isValid(): bool
    {
        return parent::isValid() && $this->year >= date('Y');
    }

    /**
     * @return \DateTime|null
     * @throws Exception
     */
    public function getDatetime(): ?\DateTime
    {
        if (empty($this->year) || empty($this->timezone)) {
            return null;
        }

        $datetime = sprintf("%d-%d-%d %d:%d", $this->year, $this->month, $this->day, $this->hour, $this->minute);
        return new \DateTime($datetime, new DateTimeZone($this->timezone));
    }

    /**
     * @param mixed $datetime
     * @throws Exception
     */
    public function setDateTime(mixed $datetime): void
    {
        $datetime = DateTimeHelper::toDateTime($datetime);
        $this->timezone = $datetime->getTimezone()->getName();
        $this->year = $datetime->format('Y');
        $this->month = $datetime->format('m');
        $this->day = $datetime->format('d');
        $this->hour = $datetime->format('H');
        $this->minute = $datetime->format('m');
    }

    /**
     * @return string
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function getCronDescription(): string
    {
        if (extension_loaded('intl')) {
            $format = Craft::$app->getLocale()->getDateTimeFormat('short');
            $datetime = Craft::$app->getFormatter()->asDatetime($this->getDatetime(), $format);
        } else {
            $datetime = $this->getDatetime()->format('Y-m-d H:i');
        }

        return Craft::t('schedule', 'At {datetime}', ['datetime' => $datetime]);
    }

    /**
     * @inheritdoc
     */
    public function getSettingsHtml(): ?string
    {
        return Craft::$app->getView()->renderTemplate('schedule/_components/timers/DateTime', [
            'timer' => $this,
        ]);
    }
}
