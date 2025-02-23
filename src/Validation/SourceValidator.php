<?php
namespace CodeIngestor\Validation;

use CodeIngestor\Exception\ValidationException;

class SourceValidator
{
    public function validate(string $sourcePath): string
    {
        $realPath = realpath($sourcePath);

        if ($realPath === false) {
            throw new ValidationException("Source directory does not exist: {$sourcePath}");
        }

        if (!is_dir($realPath)) {
            throw new ValidationException("Path is not a directory: {$realPath}");
        }

        if (!is_readable($realPath)) {
            throw new ValidationException("Directory is not readable: {$realPath}");
        }

        return $realPath; // Return resolved absolute path
    }
}