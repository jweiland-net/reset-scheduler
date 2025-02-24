<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reset-scheduler.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\ResetScheduler\Service;

use JWeiland\ResetScheduler\Configuration\ExtConf;
use JWeiland\ResetScheduler\Configuration\ResetSchedulerConfiguration;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Address;
use TYPO3\CMS\Core\Mail\FluidEmail;
use TYPO3\CMS\Core\Mail\MailerInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Scheduler\Domain\Repository\SchedulerTaskRepository;

class SchedulerService
{
    private SchedulerTaskRepository $taskRepository;

    private ExtConf $extConf;

    private LoggerInterface $logger;

    public function __construct(
        SchedulerTaskRepository $schedulerTaskRepository,
        ExtConf $extConf,
        LoggerInterface $logger
    ) {
        $this->taskRepository = $schedulerTaskRepository;
        $this->extConf = $extConf;
        $this->logger = $logger;
    }

    public function process(ResetSchedulerConfiguration $configuration): void
    {
        if ($configuration->isReset()) {
            $this->resetFailingTasks($configuration->getTasksGreaterExecutionTimeout());
        }

        if ($configuration->getInfoMail() === '' || $this->extConf->getEmailFromAddress() === '') {
            $this->logger->warning('EXT:reset_scheduler can not send info mail as mail FROM is not configured');
            return;
        }

        try {
            $fluidMail = $this->getFluidMail($configuration->getInfoMail());
            $this->processErrorClasses($configuration->getErrorClasses(), $fluidMail);
            $this->processExecutionTimeoutTasks($configuration->getTasksGreaterExecutionTimeout(), $fluidMail);
            $this->processTasksWithError($configuration->getTasksWithError(), $fluidMail);
            GeneralUtility::makeInstance(MailerInterface::class)->send($fluidMail);
        } catch (TransportExceptionInterface $exception) {
            $this->logger->warning('EXT:reset_scheduler mail error: ' . $exception->getMessage());
        }
    }

    private function resetFailingTasks(array $failedTasks): void
    {
        foreach ($failedTasks as $failedTask) {
            try {
                $task = $this->taskRepository->findByUid((int)$failedTask['uid']);
                if ($this->taskRepository->isTaskMarkedAsRunning($task)) {
                    $this->taskRepository->removeAllRegisteredExecutionsForTask($task);
                }
            } catch (\OutOfBoundsException|\UnexpectedValueException $exception) {
                $this->logger->error('EXT:reset_scheduler task error: ' . $exception->getMessage());
            }
        }
    }

    private function processErrorClasses(array $errorClasses, FluidEmail $fluidEmail): void
    {
        $fluidEmail->assign('errorClasses', $errorClasses);
    }

    private function processExecutionTimeoutTasks(array $tasksGreaterExecutionTimeout, FluidEmail $fluidEmail): void
    {
        $fluidEmail->assign('executionTimeoutTasks', $tasksGreaterExecutionTimeout);
    }

    private function processTasksWithError(array $tasksWithError, FluidEmail $fluidEmail): void
    {
        $fluidEmail->assign('tasksWithError', $tasksWithError);
    }

    private function getFluidMail(string $infoMail): FluidEmail
    {
        $fluidMail = new FluidEmail();

        return $fluidMail
            ->to($infoMail)
            ->from(new Address($this->extConf->getEmailFromAddress(), $this->extConf->getEmailFromName()))
            ->subject('Error report about failed scheduler tasks')
            ->format(FluidEmail::FORMAT_HTML)
            ->setTemplate('ResetSchedulerErrorReport');
    }
}
