:navigation-title: Configuration
..  _configuration:

=============
Configuration
=============

reset_scheduler comes with a schedulable command :bash:`scheduler:reset`. That
way you can execute it via CLI (cron) and as a TYPO3 scheduler task.

Scheduler Task
==============

Create a new scheduler task of type: `Execute console command`.

Choose schedulable command: `scheduler:reset`.

Define a fequency

Press `Save` to reload the form with new options.

Set the :ref:`options <options>` to your needs.

CLI Command
===========

Composer
--------

..  code-block:: bash

    vendor/bin/typo3 scheduler:reset [OPTIONS]

DDEV
----

..  code-block:: bash

    ddev typo3 scheduler:reset [OPTIONS]

Classic
-------

..  code-block:: bash

    typo3/sysext/core/bin/typo3 scheduler:reset [OPTIONS]

..  _options

Options
=======

..  confval-menu::
    :name: confval-options
    :display: table
    :type:
    :default:

..  confval:: info (-i)
    :name: options-info
    :type: bool
    :default: false

    Using the `info` option, you can first check for any failing tasks that
    need to be reset or reported via email. This helps prevent an excessive
    number of notification emails. Using this option will not process any
    other options and will not reset any task nor send any mail.

    ..  code-block:: bash

        vendor/bin/typo3 scheduler:reset -i

..  confval:: email (-m)
    :name: options-email
    :type: string
    :default:

    Specify the recipient's email address here. Notifications will only be sent
    if any failing tasks are detected. The task also verifies whether the
    TYPO3 mail system is correctly configured. If any issues are found, they
    will be reported via the CLI.

    ..  code-block:: bash

        vendor/bin/typo3 scheduler:reset -m admin@example.com

..  confval:: execution-timeout (-t)
    :name: options-execution-timeout
    :type: int
    :default: 3600

    Specify a timeout (in seconds) after which indefinitely running tasks
    should be reset. Using the `execution-timeout` option without the `reset`
    or `email` option will show an error.

    ..  code-block:: bash

        vendor/bin/typo3 scheduler:reset -t 900 -r

..  confval:: reset (-r)
    :name: options-reset
    :type: bool
    :default: false

    Resets all tasks that have exceeded the defined execution timeout
    (default: 3600 seconds). After the reset, the task will be executed again
    in the next scheduler run.

    ..  note
        Resetting a running task in the scheduler does not terminate the
        underlying script running on the CLI!

    ..  code-block:: bash

        vendor/bin/typo3 scheduler:reset -t 1800 -r

Reset and Mail
--------------

It is also possible to sent info mail and reset failing tasks in one run:

..  code-block:: bash

    vendor/bin/typo3 scheduler:reset -t 1800 -r -m admin@example.com
