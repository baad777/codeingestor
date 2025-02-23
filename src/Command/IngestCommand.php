<?php
namespace CodeIngestor\Command;

use CodeIngestor\ConfigHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class IngestCommand extends Command {
    protected static $defaultName = 'ingest';

    protected function configure(): void {
        $this
            ->setDescription('Parse files within a directory structure for LLM ingestion')
            ->addOption(
                'config',
                'c',
                InputOption::VALUE_REQUIRED,
                'Path to config file',
                'codeingestor.yaml'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int {
        $configPath = $input->getOption('config');
        $config = (new ConfigHandler())->loadConfig($configPath);

        $output->writeln('Starting ingestion...');
        $output->writeln('Done!');

        return Command::SUCCESS;
    }
}