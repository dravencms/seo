<?php declare(strict_types=1);

namespace Dravencms\Seo\Console;

use Dravencms\Seo\Setup\DefaultDataInstaller;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\{InputInterface, InputOption};
use Symfony\Component\Console\Output\OutputInterface;

final class SetupCommand extends Command
{
    public function __construct(private DefaultDataInstaller $installer) { parent::__construct('seo:setup'); }

    protected function configure(): void
    {
        $this->setDescription('Add missing seo defaults without replacing existing data');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->installer->install();
        $output->writeln('Missing seo defaults installed; existing records retained.');
        return Command::SUCCESS;
    }
}
