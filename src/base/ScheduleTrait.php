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
    public ?int $groupId;

    /**
     * @var string|null
     */
    public ?string $name;

    /**
     * @var string|null
     */
    public ?string $description;

    /**
     * @var string|null
     */
    public ?string $user;

    /**
     * @var bool
     */
    public bool $enabled = true;

    /**
     * @var bool|null
     */
    public ?bool $enabledLog;

    /**
     * @var int|null
     */
    public ?int $lastStartedTime;

    /**
     * @var int|null
     */
    public ?int $lastFinishedTime;

    /**
     * @var string|null
     */
    public ?string $lastStatus;

    /**
     * @var int|null
     */
    public ?int $sortOrder;

    /**
     * @var string|null
     */
    public ?string $uid;
}
