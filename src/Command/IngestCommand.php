<?php

namespace CodeIngestor\Command;

use CodeIngestor\ConfigHandler;
use CodeIngestor\ContentExtractor;
use CodeIngestor\Exception\ValidationException;
use CodeIngestor\FileScanner;
use CodeIngestor\ScanConfiguration;
use CodeIngestor\ScanConfigurationOption;
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
                InputOption::VALUE_OPTIONAL,
                'Path to config file',
                'codeingestor.yaml'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            // Load config and validate
            $configPath = $input->getOption('config') ?? "codeingestor.yaml";
            $configArray = (new ConfigHandler())->loadConfig($configPath);
            $configArray[ScanConfigurationOption::SOURCE_PATH->value] = (new SourceValidator())->validate($configArray[ScanConfigurationOption::SOURCE_PATH->value]);

            // Create scan configuration
            $scanConfig = new ScanConfiguration($configArray);

            // Scan files
            $fileScanner = new FileScanner($scanConfig);
            $files = $fileScanner->scanFiles();
            // Generate directory tree
            $tree = $fileScanner->generateDirectoryTree();

            // Prepare output content
            $outputContent = "Directory Tree:\n{$tree}\n\n";
            $outputContent .= "File Contents:\n";

            $extractor = new ContentExtractor();
            foreach ($files as $file) {
                $absolutePath = $scanConfig->getOption(ScanConfigurationOption::SOURCE_PATH->value) . DIRECTORY_SEPARATOR . $file;
                $outputContent .= "\n================================================\n";
                $outputContent .= "File: {$file}\n";
                $outputContent .= "================================================\n";
                $outputContent .= $extractor->extract($absolutePath) . "\n";
            }

            // Write to output file
            $outputPath = $scanConfig->getOption(ScanConfigurationOption::OUTPUT->value);
            if (!is_dir(dirname($outputPath))) {
                mkdir(dirname($outputPath), 0755, true);
            }
            file_put_contents($outputPath, $outputContent);

            $output->writeln("<info>Output written to: {$outputPath}</info>");

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