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
                    ->getOption(ScanConfigurationOption::SOURCE_PATH->value)
            );
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

            $path            = $dir . DIRECTORY_SEPARATOR . $entry;
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
        $tree    = '';
        $entries = scandir($dir);
        if ($entries === false) {
            return $tree;
        }

        // Separate directories and files, ignoring "." and ".."
        $dirs  = [];
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
        $entryPath = realpath($dir . DIRECTORY_SEPARATOR . $entry);

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

        $ignoreDirs  = $this->config->getOption(ScanConfigurationOption::IGNORE_DIRS->value) ?? [];
        $ignoreFiles = $this->config->getOption(ScanConfigurationOption::IGNORE_FILES->value) ?? [];

        // Skip ignored directories
        foreach ($ignoreDirs as $dir) {
            if (str_starts_with($entry, $dir)) {
                return true;
            }
        }

        // Skip ignored files
        foreach ($ignoreFiles as $pattern) {
            if (fnmatch($pattern, $entry)) {
                return true;
            }
        }

        // Skip output file from configuration
        $outputFile = realpath($this->config->getOption(ScanConfigurationOption::OUTPUT->value));

        if ($outputFile === $entryPath) {
            return true;
        }

        // if isset config onlyDirs, and is dir, and it is in that array, return false
        $onlyDirs   = $this->config->getOption(ScanConfigurationOption::ONLY_DIRS->value) ?? [];
        $onlyFiles  = $this->config->getOption(ScanConfigurationOption::ONLY_FILES->value) ?? [];
        $sourcePath = $this->config->getOption(ScanConfigurationOption::SOURCE_PATH->value);

        if (count($onlyFiles) && !is_dir($entryPath)) {
            // if $entryPath is in sourcePath folder
            $realEntryPath  = realpath($entryPath);
            $realSourcePath = realpath($sourcePath);
            $realEntryPath  = str_replace($realSourcePath, '', $realEntryPath);
            // if $realEntryPath is directly under / without any subfolder
            if (count($exp = explode(DIRECTORY_SEPARATOR, $realEntryPath)) == 2) {
                return !in_array($exp[1], $onlyFiles);
            }
        }

        if (count($onlyDirs)) {
            // get path of $entryPath relative to $sourcePath (use realpath)
            $realEntryPath  = realpath($entryPath);
            $realSourcePath = realpath($sourcePath);
            $realEntryPath  = str_replace($realSourcePath, '', $realEntryPath);
            // if the base folder of $entryPath does not exist in $onlyDirs return true
            $baseDir = explode(DIRECTORY_SEPARATOR, $realEntryPath)[1] ?? '';
            if (!in_array($baseDir, $onlyDirs)) {
                return true;
            }
        }

        return false;
    }
}