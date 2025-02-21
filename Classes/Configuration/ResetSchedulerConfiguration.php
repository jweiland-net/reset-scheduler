<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reset-scheduler.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\ResetScheduler\Configuration;


class ResetSchedulerConfiguration
{
    private array $failedAndGroupedTasks;

    private string $infoMail;

    private int $timeout;

    public function __construct(array $failedAndGroupedTasks, string $infoMail, int $timeout)
    {
        $this->failedAndGroupedTasks = $failedAndGroupedTasks;
        $this->infoMail = $infoMail;
        $this->timeout = $timeout;
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

    public function getGroupedTasks(): array
    {
        return $this->failedAndGroupedTasks['taskGroupsWithTasks'] ?? [];
    }

    public function getTasks(): array
    {
        $tasks = [];

        foreach ($this->getGroupedTasks() as $groupWithTasks) {
            foreach ($groupWithTasks['tasks'] as $task) {
                $tasks[] = $task;
            }
        }

        return $tasks;
    }

    public function getTasksWithError(): array
    {
        return array_filter($this->getTasks(), static function($task): bool {
            return $task['lastExecutionFailure'] ?? false;
        });
    }

    public function getInfoMail(): string
    {
        return $this->infoMail;
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }
}
