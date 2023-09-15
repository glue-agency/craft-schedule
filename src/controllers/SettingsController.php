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
 * Class SettingsController
 *
 * @package GlueAgency\schedule\controllers
 * @author Glue Agency <info@glue.be>
 */
class SettingsController extends Controller
{
    /**
     * @inheritdoc
     */
    public function init(): void
    {
        parent::init();

        $this->requireAdmin();
    }

    /**
     * @return Response|null
     */
    public function actionSaveGeneral()
    {
        $this->requirePostRequest();

        $request = Craft::$app->getRequest();
        $settings = Plugin::$plugin->getSettings();
        $settings->load($request->getBodyParams(), '');

        if (!Craft::$app->getPlugins()->savePluginSettings(Plugin::$plugin, $settings->toArray())) {
            Craft::$app->getSession()->setError(Craft::t('schedule', 'Couldn’t save settings.'));

            return null;
        }

        Craft::$app->getSession()->setNotice(Craft::t('schedule', 'Settings saved.'));

        return $this->redirectToPostedUrl();
    }
}
