<?php
/*
 * Schedule plugin for CraftCMS
 *
 * https://github.com/glue-agency/craft-schedule
 */

namespace GlueAgency\schedule\timers;

use craft\base\MissingComponentTrait;
use GlueAgency\schedule\base\Timer;

/**
 * Class MissingTimer
 *
 * @package GlueAgency\schedule\timers
 * @author Glue Agency <info@glue.be>
 */
class MissingTimer extends Timer
{
    use MissingComponentTrait;
}
