<?php

namespace CodeIngestor;

use RuntimeException;

class ContentExtractor
{
    protected FileTypeDetector $fileTypeDetector;

    public function __construct()
    {
        $this->fileTypeDetector = new FileTypeDetector();
    }

    public function extract(string $filePath): string
    {
        if (!file_exists($filePath)) {
            throw new RuntimeException("File not found: {$filePath}");
        }

        if (!is_readable($filePath)) {
            throw new RuntimeException("File is not readable: {$filePath}");
        }

        // if file is not text, then return string
        if($this->fileTypeDetector->isBinary($filePath)) {
            return "*** This is a binary file. Content cannot be displayed. ***";
        }

        $content = file_get_contents($filePath);
        if ($content === false) {
            throw new RuntimeException("Failed to read file: {$filePath}");
        }

        return $content;
    }
}