<?php
/*
 * Schedule plugin for CraftCMS
 *
 * https://github.com/glue-agency/craft-schedule
 */

namespace GlueAgency\schedule\controllers;

use Craft;
use craft\errors\MissingComponentException;
use craft\helpers\Json;
use craft\helpers\UrlHelper;
use craft\web\Controller;
use GlueAgency\schedule\base\Schedule;
use GlueAgency\schedule\base\Timer;
use GlueAgency\schedule\base\TimerInterface;
use GlueAgency\schedule\errors\TimerException;
use GlueAgency\schedule\Plugin;
use GlueAgency\schedule\timers\Custom;
use Throwable;
use yii\base\InvalidConfigException;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * Class TimersController
 *
 * @package GlueAgency\schedule\controllers
 * @author Glue Agency <info@glue.be>
 */
class TimersController extends Controller
{
    // Public Methods
    // =========================================================================

    /**
     * @param int|null $scheduleId
     * @param int|null $timerId
     * @param TimerInterface|null $timer
     * @return Response
     * @throws InvalidConfigException
     */
    public function actionEditTimer(int $scheduleId = null, int $timerId = null, TimerInterface $timer = null): Response
    {
        /** @var Timer $timer */
        /** @var Schedule $schedule */
        $timers = Plugin::$plugin->getTimers();

        if ($timer === null) {
            $schedule = Plugin::$plugin->getSchedules()->getScheduleById($scheduleId);
            if (!$schedule) {
                throw new NotFoundHttpException();
            }

            if ($timerId !== null) {
                $timer = $timers->getTimerById($timerId);
                if (!$timer) {
                    throw new NotFoundHttpException();
                }
            } else {
                $timer = $timers->createTimer([
                    'type' => Custom::class,
                    'scheduleId' => $scheduleId,
                ]);
            }
        } else {
            $schedule = $timer->getSchedule();
        }

        $allTimerTypes = $timers->getAllTimerTypes();

        $timerInstances = [];
        $timerTypeOptions = [];
        foreach ($allTimerTypes as $class) {
            $timerInstances[$class] = $class === get_class($timer) ? $timer : new $class();
            $timerTypeOptions[] = [
                'label' => $class::displayName(),
                'value' => $class,
            ];
        }

        $isNewTimer = !$timer->id;

        $crumbs = [
            ['label' => Craft::t('schedule', 'Schedule'), 'url' => UrlHelper::cpUrl('schedule')],
        ];

        if ($schedule->group) {
            $crumbs[] = ['label' => (string)$schedule->group, 'url' => UrlHelper::cpUrl('schedule/groups/' . $schedule->group->id)];
        }
        $crumbs[] = ['label' => (string)$schedule, 'url' => UrlHelper::cpUrl('schedule/' . $schedule->id)];
        $crumbs[] = ['label' => Craft::t('schedule', 'Timers'), 'url' => UrlHelper::cpUrl('schedule/'. $schedule->id . '/timers')];


        return $this->renderTemplate('schedule/timers/_edit', [
            'isNewTimer' => $isNewTimer,
            'schedule' => $schedule,
            'timer' => $timer,
            'timerInstances' => $timerInstances,
            'timerTypes' => $allTimerTypes,
            'timerTypeOptions' => $timerTypeOptions,
            'title' => $isNewTimer ? Craft::t('schedule', 'Create a timer') : (string)$timer,
            'crumbs' => $crumbs,
        ]);
    }

    /**
     * Save a timer.
     *
     * @return Response|null
     * @throws BadRequestHttpException
     * @throws InvalidConfigException
     * @throws Throwable
     * @throws TimerException
     * @throws MissingComponentException
     */
    public function actionSaveTimer(): ?Response
    {
        $this->requirePostRequest();

        $timers = Plugin::$plugin->getTimers();
        $request = Craft::$app->getRequest();

        $type = $request->getBodyParam('type');

        $timer = $timers->createTimer([
            'id' => $request->getBodyParam('timerId'),
            'type' => $type,
            'scheduleId' => $request->getBodyParam('scheduleId'),
            'minute' => $request->getBodyParam('minute'),
            'hour' => $request->getBodyParam('hour'),
            'day' => $request->getBodyParam('day'),
            'month' => $request->getBodyParam('month'),
            'week' => $request->getBodyParam('week'),
            'enabled' => (bool)$request->getBodyParam('enabled'),
            'settings' => $request->getBodyParam('types.' . $type)
        ]);

        if (!$timers->saveTimer($timer)) {
            Craft::$app->getSession()->setError(Craft::t('schedule', 'Couldn’t save timer.'));

            Craft::$app->getUrlManager()->setRouteParams([
                'timer' => $timer,
            ]);

            return null;
        }

        Craft::$app->getSession()->setNotice(Craft::t('schedule', 'Timer saved.'));

        return $this->redirectToPostedUrl();
    }

    /**
     * Delete a timer.
     *
     * @return Response
     * @throws BadRequestHttpException
     * @throws InvalidConfigException
     * @throws NotFoundHttpException
     * @throws Throwable
     */
    public function actionDeleteTimer(): Response
    {
        $this->requirePostRequest();
        $this->requireAcceptsJson();

        $timers = Plugin::$plugin->getTimers();

        $timerId = Craft::$app->getRequest()->getBodyParam('id');
        $timer = $timers->getTimerById($timerId);
        if (!$timer) {
            throw new NotFoundHttpException();
        }

        if (!$timers->deleteTimer($timer)) {
            return $this->asJson([
                'success' => false,
            ]);
        }

        return $this->asJson([
            'success' => true,
        ]);
    }

    /**
     * @return Response
     * @throws BadRequestHttpException
     * @throws Throwable
     */
    public function actionToggleTimer(): Response
    {
        $this->requirePostRequest();
        $this->requireAcceptsJson();

        $timers = Plugin::$plugin->getTimers();
        $request = Craft::$app->getRequest();

        $timer = $timers->getTimerById($request->getBodyParam('id'));
        if (!$timer) {
            return $this->asJson(['success' => false]);
        }
        /** @var Schedule $schedule */
        $timer->enabled = (bool)$request->getBodyParam('enabled');

        if (!$timers->saveTimer($timer)) {
            var_dump($timer->getErrors());
            return $this->asJson(['success' => false]);
        }

        return $this->asJson(['success' => true]);
    }

    /**
     * Reorder all schedule timers.
     *
     * @return Response
     * @throws BadRequestHttpException
     * @throws InvalidConfigException
     * @throws Throwable
     */
    public function actionReorderTimers(): Response
    {
        $this->requirePostRequest();

        $ids = Craft::$app->getRequest()->getBodyParam('ids');
        $ids = Json::decodeIfJson($ids);

        return $this->asJson([
            'success' => Plugin::$plugin->getTimers()->reorderTimers($ids)
        ]);
    }
}
