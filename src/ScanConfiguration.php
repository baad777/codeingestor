<?php

namespace CodeIngestor;

readonly class ScanConfiguration
{
    public function __construct(
        private string $sourcePath,
        private array $ignoreDirs,
        private array $ignoreFiles
    ) {
    }

    public function getSourcePath(): string
    {
        return $this->sourcePath;
    }

    public function getIgnoreDirs(): array
    {
        return $this->ignoreDirs;
    }

    public function getIgnoreFiles(): array
    {
        return $this->ignoreFiles;
    }
}