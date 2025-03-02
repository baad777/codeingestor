<?php

namespace CodeIngestor;

class FileContentWriter
{
    public function __construct(protected ScanConfiguration $scanConfig)
    {
    }

    public function writeFileContents(): bool|int
    {
        // Scan files
        $fileScanner = new FileScanner($this->scanConfig);
        $files = $fileScanner->scanFiles();
        // Generate directory tree
        $tree = $fileScanner->generateDirectoryTree();

        // Prepare output content
        $outputContent = "Directory Tree:\n{$tree}\n\n";
        $outputContent .= "File Contents:\n";

        $extractor = new ContentExtractor();
        foreach ($files as $file) {
            $absolutePath = $this
                    ->scanConfig
                    ->getOption(ScanConfigurationOption::SOURCE_PATH->value) . DIRECTORY_SEPARATOR . $file;

            $outputContent .= "\n================================================\n";
            $outputContent .= "File: {$file}\n";
            $outputContent .= "================================================\n";
            $outputContent .= $extractor->extract($absolutePath) . "\n";
        }

        // Write to output file
        $outputPath = $this->scanConfig->getOption(ScanConfigurationOption::OUTPUT->value);
        if (!is_dir(dirname($outputPath))) {
            mkdir(dirname($outputPath), 0755, true);
        }

        // overwrite if exists
        return file_put_contents($outputPath, $outputContent);
    }
}