<?php

namespace CodeIngestor\Command;

use CodeIngestor\ConfigHandler;
use CodeIngestor\Exception\ValidationException;
use CodeIngestor\FileScanner;
use CodeIngestor\ScanConfiguration;
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
            // Load config and validate
            $configPath = $input->getOption('config');
            $configArray = (new ConfigHandler())->loadConfig($configPath);
            $resolvedSource = (new SourceValidator())->validate($configArray['source']);

            // Create scan configuration
            $scanConfig = new ScanConfiguration(
                $resolvedSource,
                $configArray['ignore_dirs'],
                $configArray['ignore_files']
            );

            // Scan files
            $fileScanner = new FileScanner($scanConfig);
            $files = $fileScanner->scanFiles();
            $output->writeln("Scanned ".count($files)." files.");

            // Generate directory tree
            $tree = $fileScanner->generateDirectoryTree();
            $output->writeln("Directory tree:\n".$tree);

            // TODO: Write to output file (next step)
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