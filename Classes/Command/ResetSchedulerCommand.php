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
            ->setDescription('Reset scheduler tasks and send info mail on error')
            ->addOption(
                'set-timeout',
                't',
                InputOption::VALUE_OPTIONAL,
                'Send information mail about failed tasks or reset task after this timeout exceeds',
                24 * 60 * 60,
            )
            ->addOption(
                'email',
                'm',
                InputOption::VALUE_OPTIONAL,
                'If set, the command will sent error of failed task via email',
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $groupedTasks = $this->taskRepository->getGroupedTasks();

        $infoMail = (string)$input->getOption('email');
        if ($infoMail === '') {
            $output->writeln(
                '<comment>Mail receiver is empty, so no one will be informed about failing scheduler tasks</comment>'
            );
        }

        if ($this->extConf->getEmailFromAddress() === '') {
            $output->writeln(
                '<comment>Mail FROM is not configured. Please check mailFromAddress in Installtool or reset_scheduler extension settings</comment>'
            );
        }

        $configuration = new ResetSchedulerConfiguration(
            $groupedTasks,
            $infoMail,
            (int)$input->getOption('set-timeout'),
        );

        $output->writeln(sprintf(
            'Found %d tasks to process',
            count($configuration->getTasks())
        ));


        $this->schedulerService->process($configuration);

        $output->writeln('Done');

        return Command::SUCCESS;
    }
}
