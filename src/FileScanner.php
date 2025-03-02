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

        $this
            ->scanDirectory(
                $this
                    ->config
                    ->getOption(ScanConfigurationOption::SOURCE_PATH->value),
                $files
            );

        return $files;
    }

    public function generateDirectoryTree(): string
    {
        return $this
            ->buildTree(
                $this
                    ->config
                    ->getOption(ScanConfigurationOption::SOURCE_PATH->value));
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

            $path = $dir . DIRECTORY_SEPARATOR . $entry;
            $newRelativePath = $relativePath !== '' ? $relativePath . DIRECTORY_SEPARATOR . $entry : $entry;

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

        // Separate directories and files, ignoring "." and ".."
        $dirs = [];
        $files = [];
        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }
            $path = $dir . DIRECTORY_SEPARATOR . $entry;
            if (is_dir($path)) {
                $dirs[] = $entry;
            } else {
                $files[] = $entry;
            }
        }

        // Sort entries alphabetically for consistent output
        sort($dirs);
        sort($files);

        // Process directories first
        foreach ($dirs as $entry) {
            if ($this->shouldIgnore($entry, $dir)) {
                continue;
            }
            $path = $dir . DIRECTORY_SEPARATOR . $entry;
            $tree .= $prefix . $entry . "\n";
            $tree .= $this->buildTree($path, $prefix . '    ');
        }

        // Process files next
        foreach ($files as $entry) {
            if ($this->shouldIgnore($entry, $dir)) {
                continue;
            }
            $tree .= $prefix . $entry . "\n";
        }

        return $tree;
    }

    private function shouldIgnore(string $entry, string $dir): bool
    {
        // Always ignore '.' and '..' to prevent infinite loops
        if ($entry === '.' || $entry === '..') {
            return true;
        }

        // Check for hidden files on Unix-like systems
        if (str_starts_with($entry, '.') && PHP_OS_FAMILY !== 'Windows') {
            return true;
        }

        // Check for hidden files on Windows
        if (str_starts_with($entry, '~')) {
            return true;
        }

        $ignoreDirs = $this->config->getOption(ScanConfigurationOption::IGNORE_DIRS->value) ?? [];
        $ignoreFiles = $this->config->getOption(ScanConfigurationOption::IGNORE_FILES->value) ?? [];

        // Skip ignored directories
        if (is_dir($dir . DIRECTORY_SEPARATOR . $entry)) {
            return in_array($entry, $ignoreDirs, true);
        }

        // Skip ignored files
        foreach ($ignoreFiles as $pattern) {
            if (fnmatch($pattern, $entry)) {
                return true;
            }
        }

        return false;
    }
}