<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reset-scheduler.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\ResetScheduler\Configuration;


use TYPO3\CMS\Core\Utility\MathUtility;

class ResetSchedulerConfiguration
{
    public const DEFAULT_TIMEOUT = 60 * 60;

    private array $failedAndGroupedTasks;

    private string $infoMail;

    private int $executionTimeout;

    private bool $reset;

    public function __construct(
        array $failedAndGroupedTasks,
        string $infoMail,
        int $executionTimeout,
        bool $reset
    ) {
        $this->failedAndGroupedTasks = $failedAndGroupedTasks;
        $this->infoMail = $infoMail;
        $this->executionTimeout = $executionTimeout;
        $this->reset = $reset;
    }

    /**
     * Returns tasks whose classes are invalid or extend wrong scheduler classes or classes can not be found.
     *
     * It does NOT contain tasks which results into error on last scheduler run!!!
     */
    public function getErrorClasses(): array
    {
        return $this->failedAndGroupedTasks['errorClasses'] ?? [];
    }

    /**
     * Returns all tasks grouped, but without any filtering.
     * It also contains deactivated tasks.
     * Keep public. Use some of the other methods in this class.
     */
    private function getGroupedTasks(): array
    {
        return $this->failedAndGroupedTasks['taskGroupsWithTasks'] ?? [];
    }

    /**
     * Returns all active single and recurring tasks
     */
    public function getTasks(): array
    {
        $tasks = [];

        foreach ($this->getGroupedTasks() as $groupWithTasks) {
            foreach ($groupWithTasks['tasks'] as $task) {
                if (($task['disabled'] ?? false) === false) {
                    $tasks[] = $task;
                }
            }
        }

        return $tasks;
    }

    /**
     * Returns all active single and recurring tasks with error
     */
    public function getTasksWithError(): array
    {
        return array_filter($this->getTasks(), static function($task): bool {
            return $task['lastExecutionFailure'] ?? false;
        });
    }

    /**
     * Returns all active single and recurring tasks where execution timeout exceeds
     */
    public function getTasksGreaterExecutionTimeout(): array
    {
        $executionTimeout = $this->getExecutionTimeout();

        return array_filter($this->getTasks(), static function($task) use ($executionTimeout): bool {
            return ($task['isRunning'] ?? false)
                && ($task['lastExecutionTime'] ?? 0)
                && time() > $task['lastExecutionTime'] + $executionTimeout;
        });
    }

    /**
     * Check for any failing tasks
     */
    public function hasFailingTasks(): bool
    {
        return $this->getErrorClasses()
            || $this->getTasksWithError()
            || $this->getTasksGreaterExecutionTimeout();
    }

    public function getInfoMail(): string
    {
        return trim($this->infoMail);
    }

    public function getExecutionTimeout(): int
    {
        return MathUtility::forceIntegerInRange($this->executionTimeout, 1, self::DEFAULT_TIMEOUT);
    }

    public function isReset(): bool
    {
        return $this->reset;
    }
}
