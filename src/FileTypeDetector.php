<?php

namespace CodeIngestor;

class FileTypeDetector
{
    /**
     * Checks if a file is binary.
     */
    public function isBinary(string $filePath): bool
    {
        // 1. Check MIME type first
        $mimeType = $this->getMimeType($filePath);
        if (str_starts_with($mimeType, 'text/')) {
            return false; // Likely ASCII
        }

        // 2. Fallback: Check file content for binary patterns
        return $this->hasBinaryContent($filePath);
    }

    /**
     * Get MIME type using the Fileinfo extension.
     */
    private function getMimeType(string $filePath): string
    {
        if (!extension_loaded('fileinfo')) {
            throw new \RuntimeException('Fileinfo extension is required.');
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $filePath);
        finfo_close($finfo);

        return $mimeType;
    }

    /**
     * Check file content for binary patterns (non-printable characters).
     */
    private function hasBinaryContent(string $filePath): bool
    {
        $content = file_get_contents($filePath, false, null, 0, 1024);
        if ($content === false) {
            return true; // Treat unreadable files as binary
        }

        // Regex to detect non-printable ASCII or control characters
        return (bool) preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F]/', $content);
    }
}