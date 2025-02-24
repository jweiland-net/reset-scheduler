<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/reset-scheduler.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\ResetScheduler\Command;

use JWeiland\ResetScheduler\Configuration\ExtConf;
use JWeiland\ResetScheduler\Configuration\ResetSchedulerConfiguration;
use JWeiland\ResetScheduler\Service\SchedulerService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Scheduler\Domain\Repository\SchedulerTaskRepository;

/**
 * A command to check failed/broken scheduler task and send info mail, if error detected
 */
class ResetSchedulerCommand extends Command
{
    private SchedulerTaskRepository $taskRepository;

    private SchedulerService $schedulerService;

    private ExtConf $extConf;

    public function __construct(
        SchedulerTaskRepository $schedulerTaskRepository,
        SchedulerService $schedulerService,
        ExtConf $extConf,
        ?string $name = null
    ) {
        $this->taskRepository = $schedulerTaskRepository;
        $this->schedulerService = $schedulerService;
        $this->extConf = $extConf;

        parent::__construct($name);
    }

    public function configure(): void
    {
        $this
            ->setDescription('Reset scheduler tasks and/or send info mail on error')
            ->addOption(
                'email',
                'm',
                InputOption::VALUE_OPTIONAL,
                'If set, the command will sent error of failed task via email',
            )
            ->addOption(
                'execution-timeout',
                't',
                InputOption::VALUE_OPTIONAL,
                'Set timeout for tasks which are currently running and exceeds the given timeout',
                ResetSchedulerConfiguration::DEFAULT_TIMEOUT,
            )
            ->addOption(
                'reset',
                'r',
                InputOption::VALUE_NONE,
                'Reset scheduler tasks to re-run them on next cron job call',
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $isReset = (bool)$input->getOption('reset');
            $infoMail = (string)$input->getOption('email');

            // Early return, if either mail system is configured nor reset-option is active
            if (!$this->isMailSystemConfigured($infoMail, $output) && !$isReset) {
                $output->writeln('<error>Either mail nor reset is activated. Nothing to do. End.</error>');
                return Command::SUCCESS;
            }

            $configuration = new ResetSchedulerConfiguration(
                $this->taskRepository->getGroupedTasks(),
                $infoMail,
                (int)$input->getOption('execution-timeout'),
                $isReset,
            );

            if (!$configuration->hasFailingTasks()) {
                $output->writeln('No failing tasks found');
                return Command::SUCCESS;
            }

            $output->writeln('Start processing failing tasks');
            $this->schedulerService->process($configuration);

            if ($isReset) {
                $output->writeln('Failing tasks were reset');
            }

            if ($this->isMailSystemConfigured($infoMail, $output)) {
                $output->writeln('A mail about failing tasks was send');
            }
        } catch (\Exception|\Throwable $exception) {
            $output->writeln('Error: ' . $exception->getMessage());
        }

        // This task must work in any case.
        // Try to catch whatever error messages, log them, mail them, but do not return values greater than 0,
        // as this task will also be stopped then.
        return Command::SUCCESS;
    }

    private function isMailSystemConfigured(string $infoMail, OutputInterface $output): bool
    {
        if ($infoMail === '') {
            $output->writeln(
                '<comment>Mail receiver is empty, so no one will be informed about failing scheduler tasks</comment>'
            );
            return false;
        }

        if ($this->extConf->getEmailFromAddress() === '') {
            $output->writeln(
                '<comment>Mail FROM is not configured. Please check mailFromAddress in Installtool or reset_scheduler extension settings</comment>'
            );
            return false;
        }

        return true;
    }
}
