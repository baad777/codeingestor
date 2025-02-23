<?php

namespace CodeIngestor;

readonly class FileScanner implements FileScannerInterface
{
    public function __construct(private ScanConfiguration $config)
    {
    }

    public function scanFiles(): array
    {
        $files = [];
        $this->scanDirectory($this->config->getSourcePath(), $files);
        return $files;
    }

    public function generateDirectoryTree(): string
    {
        return $this->buildTree($this->config->getSourcePath());
    }

    private function scanDirectory(string $dir, array &$files, string $relativePath = ''): void
    {
        $entries = scandir($dir);
        if ($entries === false) {
            return;
        }

        foreach ($entries as $entry) {
            if ($this->shouldIgnore($entry, $dir)) {
                continue;
            }

            $path = $dir.DIRECTORY_SEPARATOR.$entry;
            $newRelativePath = $relativePath !== '' ? $relativePath.DIRECTORY_SEPARATOR.$entry : $entry;

            if (is_dir($path)) {
                $this->scanDirectory($path, $files, $newRelativePath);
            } else {
                $files[] = $newRelativePath;
            }
        }
    }

    private function buildTree(string $dir, string $prefix = ''): string
    {
        $tree = '';
        $entries = scandir($dir);
        if ($entries === false) {
            return $tree;
        }

        foreach ($entries as $entry) {
            if ($this->shouldIgnore($entry, $dir)) {
                continue;
            }

            $path = $dir.DIRECTORY_SEPARATOR.$entry;
            if (is_dir($path)) {
                $tree .= $prefix.$entry."\n";
                $tree .= $this->buildTree($path, $prefix.'    ');
            }
        }

        return $tree;
    }

    private function shouldIgnore(string $entry, string $dir): bool
    {
        // Always ignore '.' and '..' to prevent infinite loops
        if ($entry === '.' || $entry === '..') {
            return true;
        }

        $ignoreDirs = $this->config->getIgnoreDirs();
        $ignoreFiles = $this->config->getIgnoreFiles();

        // Skip ignored directories
        if (is_dir($dir.DIRECTORY_SEPARATOR.$entry)) {
            return in_array($entry, $ignoreDirs, true);
        }

        // Skip ignored files
        foreach ($ignoreFiles as $pattern) {
            if (str_contains($entry, $pattern)) {
                return true;
            }
        }

        return false;
    }
}