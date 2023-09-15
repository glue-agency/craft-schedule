<?php
/*
 * Schedule plugin for CraftCMS
 *
 * https://github.com/glue-agency/craft-schedule
 */

namespace GlueAgency\schedule\controllers;

use Craft;
use craft\web\Controller;
use GlueAgency\schedule\Plugin;
use yii\web\Response;

/**
 * Class LogsController
 *
 * @package GlueAgency\schedule\controllers
 * @author Glue Agency <info@glue.be>
 */
class LogsController extends Controller
{
    // Public Methods
    // =========================================================================

    /**
     * @return Response
     */
    public function actionDeleteAllLogs(): Response
    {
        $this->requirePostRequest();
        $this->requireAdmin();

        if (!Plugin::getInstance()->getLogs()->deleteAllLogs()) {
            return $this->asJson([
                'success' => false
            ]);
        }

        return $this->asJson([
            'success' => true
        ]);
    }

    /**
     * @return Response
     */
    public function actionDeleteLogsByScheduleId(): Response
    {
        $this->requirePostRequest();

        $schedules = Plugin::getInstance()->getSchedules();

        $scheduleId = Craft::$app->getRequest()->getBodyParam('scheduleId');
        $schedule = $schedules->getScheduleById($scheduleId);
        if (!$schedule) {
            return $this->asJson([
                'success' => false
            ]);
        }

        if (!Plugin::getInstance()->getLogs()->deleteLogsByScheduleId($scheduleId)) {
            return $this->asJson([
                'success' => false
            ]);
        }

        return $this->asJson([
            'success' => true
        ]);
    }
}
