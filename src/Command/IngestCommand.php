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
            // option to init a default yaml config file
            ->addOption(
                'init',
                'i',
                InputOption::VALUE_NONE,
                'Initialize a default config file'
            )
            ->addArgument(
                "sourcePath",
                InputArgument::OPTIONAL,
                'Path to the folder for ingesting content'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Load config and validate
        $configPath    = $input->getOption('config') ?? "codeingestor.yaml";
        $configHandler = new ConfigHandler();
        $configArray   = $configHandler->loadConfig($configPath);

        if ($commandResult = $this->handleInit($input, $output, $configPath)) {
            return $commandResult;
        }

        $sourcePath = $input->getArgument('sourcePath') ?? null;
        if ($sourcePath != null) {
            // check if the folder exists
            if (!is_dir($sourcePath)) {
                throw new RuntimeException("Source path does not exist");
            }
        }

        try {
            $sourcePath                                               = is_null(
                $sourcePath
            ) ? $configArray[ScanConfigurationOption::SOURCE_PATH->value] : $sourcePath;
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

    private function handleInit(
        InputInterface $input,
        OutputInterface $output,
        string $configPath
    ) {
        // if init option then init config file
        if ($input->getOption('init')) {
            if (file_exists($configPath)) {
                throw new RuntimeException("Config file already exists");
            }
            // copy codeingestor.yaml.dist to current path
            $sourcePath = realpath(
                __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR . "codeingestor.yaml.dist"
            );
            $destPath   = realpath(getcwd()) . DIRECTORY_SEPARATOR . "codeingestor.yaml";

            if ($sourcePath === false || $destPath === false || !copy($sourcePath, $destPath)) {
                throw new RuntimeException("Failed to create config file");
            }

            $output->writeln("<info>Default config file created at: {$configPath}</info>");

            return Command::SUCCESS;
        }

        return null;
    }
}