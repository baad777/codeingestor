<?php

namespace CodeIngestor\Command;

use CodeIngestor\ConfigHandler;
use CodeIngestor\Exception\ValidationException;
use CodeIngestor\Validation\SourceValidator;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class IngestCommand extends Command
{
    protected static $defaultName = 'ingest';

    protected function configure(): void
    {
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

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $configPath = $input->getOption('config');
            $config = (new ConfigHandler())->loadConfig($configPath);

            // Validate source directory
            $validator = new SourceValidator();
            $resolvedSource = $validator->validate($config['source']);
            $output->writeln("<info>Validated source: {$resolvedSource}</info>");
            // TODO: Next step (scan files)
            $output->writeln('Done!');
            return Command::SUCCESS;
        } catch (ValidationException $e) {
            $output->writeln("<error>Validation Error: {$e->getMessage()}</error>");
            return Command::FAILURE;
        } catch (RuntimeException $e) {
            $output->writeln("<error>Error: {$e->getMessage()}</error>");
            return Command::FAILURE;
        }
    }
}