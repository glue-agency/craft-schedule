<?php
/*
 * Schedule plugin for CraftCMS
 *
 * https://github.com/glue-agency/craft-schedule
 */

namespace GlueAgency\schedule\plugin;

use craft\events\RegisterUrlRulesEvent;
use craft\web\UrlManager;
use yii\base\Event;

/**
 * Trait Routes
 *
 * @package GlueAgency\schedule\plugin
 * @author Glue Agency <info@glue.be>
 */
trait Routes
{
    /**
     * Register Cp URL rule.
     */
    public function _registerCpRoutes(): void
    {
        Event::on(UrlManager::class, UrlManager::EVENT_REGISTER_CP_URL_RULES, function (RegisterUrlRulesEvent $event) {
            $event->rules = array_merge($event->rules, [
                'schedule/groups/<groupId>' => ['template' => 'schedule'],
                'schedule/new' => 'schedule/schedules/edit-schedule',
                'schedule/<scheduleId:\d+>' => 'schedule/schedules/edit-schedule',
                'schedule/<scheduleId:\d+>/timers' => ['template' => 'schedule/timers'],
                'schedule/<scheduleId:\d+>/timers/new' => 'schedule/timers/edit-timer',
                'schedule/<scheduleId:\d+>/timers/<timerId:\d+>' => 'schedule/timers/edit-timer',
                'schedule/<scheduleId:\d+>/logs' => ['template' => 'schedule/_logs'],
                'schedule/<scheduleId:\d+>/logs/<logId:\d+>' => ['template' => 'schedule/logs/_view'],
            ]);
        });
    }
}
