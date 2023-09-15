<?php
/*
 * Schedule plugin for CraftCMS
 *
 * https://github.com/glue-agency/craft-schedule
 */

namespace GlueAgency\schedule\web\twig;

use GlueAgency\schedule\Plugin;
use yii\base\Behavior;

/**
 * Class CraftVariableBehavior
 *
 * @author Glue Agency <info@glue.be>
 */
class CraftVariableBehavior extends Behavior
{
    /**
     * @var Plugin
     */
    public Plugin $schedule;

    /**
     * @inheritdoc
     */
    public function init(): void
    {
        parent::init();

        $this->schedule = Plugin::getInstance();
    }
}
