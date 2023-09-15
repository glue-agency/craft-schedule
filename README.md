Schedule
========

Manage your CraftCMS application schedules in Cp. Run a command, trigger an event, 
push a queue task, or send HTTP requests at a specified time. You can flexibly customize
the trigger time and even design your schedule type.

Requirements
------------

This plugin requires Craft CMS 3.1 or later.

Installation
------------

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require glue-agency/craft-schedule

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for Schedule.

4. Add a record to system crontab:
    
        * * * * * php /path/to/craft schedules/run 1>> /dev/null 2>&1

   Or use built-in `schedules/listen` command:

   ```shell
   $ ./craft schedules/listen
   ```

   If you use nitro:

   ```shell
   $ nitro craft schedules/listen
   ```

License
-------
The Schedule is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).
