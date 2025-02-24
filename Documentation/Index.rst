..  _start:

========================
jweiland/reset-scheduler
========================

In certain scenarios, a TYPO3 scheduler task may continuously run without
errors. This can occur if the task exceeds the configured
:php:max_execution_time or consumes more memory than the defined
:php:memory_limit. As a result, the task is unable to complete execution
properly and cannot provide any feedback on whether it was successfully
executed.

Tasks that can no longer provide feedback will not be executed again by the
TYPO3 scheduler. This is where the TYPO3 extension reset_scheduler comes into
play. As a scheduler task itself, it detects indefinitely running tasks and
notifies you via email about failures or timeouts. Additionally, it can reset
failing tasks, ensuring they are executed again in the next scheduled run.

..  toctree::
    :glob:
    :titlesonly:
    :hidden:

    */Index
    Installation
    Configuration
    GetHelp
    *

..  card-grid::
    :columns: 1
    :columns-md: 2
    :gap: 4
    :class: pb-4
    :card-height: 100

    ..  card:: :ref:`Installation <installation>`

        Explains how to install this extension in Composer-based and Classic
        TYPO3 installations.

    ..  card:: :ref:`Configuration <configuration>`

                Learn how to configure this extension.

    ..  card:: :ref:`Frequently Asked Questions (FAQ) <faq>`

        These questions have been frequently asked.

    ..  card:: :ref:`How to get help <help>`

        Learn where to get help and how to report issues you found.
