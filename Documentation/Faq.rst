:navigation-title: FAQ

..  _faq:

================================
Frequently Asked Questions (FAQ)
================================

..  accordion::
    :name: faq

    ..  accordion-item:: Which kind of failing tasks are supported?
        :name: task-failor-types
        :header-level: 2
        :show:

        Currently 3 kinds of task errors are supported:

        #.  Invalid tasks: Tasks which cannot be unserialized from
            tx_scheduler_task record or tasks which do not extend the scheduler
            :php:`AbstractTask` class or tasks containing invalid information
            about execution times or task classis not registered in
            task repository.
        #.  Running tasks exceeding the defined execution-timeout option
        #.  Failing tasks: Tasks which are throwing an exception. Also tasks
            which results in a return value greater than `0` results in an
            exception internally in scheduler.

    ..  accordion-item:: I don't get any mail
        :name: no-mail
        :header-level: 2

        You have to setup the mail FROM and NAME in extension settings of
        `reset_scheduler`. If not defined the values will be retrieved from
        `$GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromAddress']` and
        `$GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromName']` in
        TYPO3 installtool. Please make sure you have configured these values.
        Please test mail system in TYPO3 installtool. Further you have to
        activate mailing with :ref:`Option: email <confval-options-email>`.

        ..  note
            You will only get email, if there are any failing tasks.

    ..  accordion-item:: I still have tasks with errors in BE of scheduler
        :name: still-having-errors
        :header-level: 2

        The :ref:`Option: reset <confval-options-reset>` will only reset running
        scheduler tasks after
        :ref:`Option: execution timeout <confval-options-execution-timeout>`
        has been exceeded.

        ..  note
            If a scheduler tasks was processed regardless if successfully or
            not, the task will be tried to processed with next scheduler run
            automatically. So no need to reset them.

    ..  accordion-item:: What if reset_scheduler task fails?
        :name: reset-scheduler-fails
        :header-level: 2

        All return values of our command are set to `0` (SUCCESS). So, it can
        not fail. Further we have wrapped all API calls into try-catch clauses
        to prevent suddendy failors. Sure, also our task may result into
        PHP limitations. To be really sure that our task is running we prefer
        executing our command `scheduler:reset` via an additional cronjob
        from shell.

    ..  accordion-item:: Which tasks will be reset?
        :name: which-tasks-reset
        :header-level: 2

        All running tasks whicn exceeds the defined
        :ref:`Option: execution timeout <confval-options-execution-timeout>`

        Failing tasks will not be reset.

    ..  accordion-item:: How to show failing tests on CLI?
        :name: show failing-tasks-cli
        :header-level: 2

        Currently, this is not possible. Failing or exceeding tasks are only
        available via mail.
