<?php
/*
 * Schedule plugin for CraftCMS
 *
 * https://github.com/glue-agency/craft-schedule
 */

namespace GlueAgency\schedule\schedules;

use Craft;
use craft\base\MissingComponentTrait;
use GlueAgency\schedule\base\Schedule;
use GlueAgency\schedule\Builder;
use yii\base\NotSupportedException;

/**
 * Class MissingSchedule
 *
 * @package GlueAgency\schedule\schedules
 * @author Glue Agency <info@glue.be>
 */
class MissingSchedule extends Schedule
{
    use MissingComponentTrait;

    /**
     * @inheritdoc
     */
    public function build(Builder $builder): void
    {
        Craft::warning('Missing build a schedule', __METHOD__);
    }

    /**
     * @inheritdoc
     */
    public function execute(int $logId = null): bool
    {
        throw new NotSupportedException();
    }
}
