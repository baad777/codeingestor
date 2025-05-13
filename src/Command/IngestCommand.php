<?php

namespace CodeIngestor\Command;

use CodeIngestor\ConfigHandler;
use CodeIngestor\Exception\ValidationException;
use CodeIngestor\FileContentWriter;
use CodeIngestor\ScanConfiguration;
use CodeIngestor\ScanConfigurationOption;
use CodeIngestor\Validation\SourceValidator;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'codeingestor')]
class IngestCommand extends Command
{
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
            )
            ->addArgument(
                "sourcePath",
                InputArgument::OPTIONAL,
                'Path to the folder for ingesting content'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $sourcePath = $input->getArgument('sourcePath') ?? null;
        if ($sourcePath != null) {
            // check if folder exists
            if (!is_dir($sourcePath)) {
                throw new RuntimeException("Source path does not exist");
            }
        }

        // Load config and validate
        $configPath = $input->getOption('config') ?? "codeingestor.yaml";

        try {
            $configArray = (new ConfigHandler())->loadConfig($configPath);
            $sourcePath = is_null($sourcePath) ? $configArray[ScanConfigurationOption::SOURCE_PATH->value] : $sourcePath;
            $configArray[ScanConfigurationOption::SOURCE_PATH->value] = (new SourceValidator())->validate($sourcePath);

            $fileContentWriter = new FileContentWriter(new ScanConfiguration($configArray));
            $fileContentWriter->writeFileContents();

            $output->writeln("<info>Output written to: {$configArray[ScanConfigurationOption::OUTPUT->value]}</info>");

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