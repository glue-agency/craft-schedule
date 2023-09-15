<?php
/*
 * Schedule plugin for CraftCMS
 *
 * https://github.com/glue-agency/craft-schedule
 */

namespace GlueAgency\schedule\db;

/**
 * Class Table
 *
 * @package GlueAgency\schedule\db
 * @author Glue Agency <info@glue.be>
 */
abstract class Table
{
    const SCHEDULES = '{{%schedules}}';
    const SCHEDULEGROUPS = '{{%schedulegroups}}';
    const SCHEDULELOGS = '{{%schedulelogs}}';
    const SCHEDULETIMERS = '{{%scheduletimers}}';
}
