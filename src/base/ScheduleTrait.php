<?php
/*
 * Schedule plugin for CraftCMS
 *
 * https://github.com/glue-agency/craft-schedule
 */

namespace GlueAgency\schedule\base;

/**
 * Trait ScheduleTrait
 *
 * @package GlueAgency\schedule\base
 * @author Glue Agency <info@glue.be>
 */
trait ScheduleTrait
{
    /**
     * @var int|null
     */
    public $groupId;

    /**
     * @var string|null
     */
    public $name;

    /**
     * @var string|null
     */
    public $handle;

    /**
     * @var string|null
     */
    public $description;

    /**
     * @var string|null
     */
    public $user;

    /**
     * @var bool
     */
    public $enabled = true;

    /**
     * @var bool|null
     */
    public $enabledLog;

    /**
     * @var int|null
     */
    public $lastStartedTime;

    /**
     * @var int|null
     */
    public $lastFinishedTime;

    /**
     * @var string|null
     */
    public $lastStatus;

    /**
     * @var int|null
     */
    public $sortOrder;

    /**
     * @var string|null
     */
    public $uid;
}
