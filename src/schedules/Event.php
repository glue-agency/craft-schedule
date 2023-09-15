<?php
/*
 * Schedule plugin for CraftCMS
 *
 * https://github.com/glue-agency/craft-schedule
 */

namespace GlueAgency\schedule\schedules;

use Craft;
use GlueAgency\schedule\base\Schedule;
use GlueAgency\schedule\helpers\ClassHelper;
use ReflectionClass;
use yii\base\Component;
use yii\base\Event as BaseEvent;

/**
 * Class Event
 *
 * @package GlueAgency\schedule\schedules
 * @author Glue Agency <info@glue.be>
 */
class Event extends Schedule
{
    // Static Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function displayName(): string
    {
        return Craft::t('schedule', 'Event');
    }

    /**
     * @inheritdoc
     */
    public static function isRunnable(): bool
    {
        return true;
    }

    // Properties
    // =========================================================================

    /**
     * @var string|null
     */
    public $className;

    /**
     * @var string|null
     */
    public $eventName;

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function getSettingsHtml(): ?string
    {
        $classSuggestions = [
            [
                'label' => '',
                'data' => $this->_getClassSuggestions(),
            ]
        ];

        return Craft::$app->getView()->renderTemplate('schedule/_components/schedules/Event/settings', [
            'schedule' => $this,
            'classSuggestions' => $classSuggestions,
        ]);
    }

    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function execute(int $logId = null): bool
    {
        Craft::info("Event Schedule trigger event: $this->className::$this->eventName", __METHOD__);

        BaseEvent::trigger($this->className, $this->eventName);

        return true;
    }

    // Private Methods
    // =========================================================================

    /**
     * @return array
     */
    private function _getClassSuggestions(): array
    {
        $suggestions = [];
        foreach (ClassHelper::findClasses() as $class) {
            if (is_subclass_of($class, Component::class)) {
                $suggestions[] = [
                    'name' => $class,
                    'hint' => ClassHelper::getPhpDocSummary((new ReflectionClass($class))->getDocComment()),
                ];
            }
        }

        return $suggestions;
    }
}
