<?php
/*
 * Schedule plugin for CraftCMS
 *
 * https://github.com/glue-agency/craft-schedule
 */

namespace GlueAgency\schedule\web\assets\timers;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;
use craft\web\assets\vue\VueAsset;
use craft\web\View;

/**
 * Class ScheduleAsset
 *
 * @package GlueAgency\schedule\assets
 * @author Glue Agency <info@glue.be>
 */
class TimersAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public $sourcePath = '@schedule/web/assets/timers/dist';

    /**
     * @inheritdoc
     */
    public $depends = [
        CpAsset::class,
        VueAsset::class,
    ];

    /**
     * @inheritdoc
     */
    public $js = [
        'timer.js',
    ];

    /**
     * @inheritdoc
     */
    public function registerAssetFiles($view)
    {
        parent::registerAssetFiles($view);

        if ($view instanceof View) {
            $view->registerTranslations('schedule', [
                'Timer enabled.',
                'Timer disabled.',
            ]);
        }
    }
}
