<?php

namespace CodeIngestor;

interface FileScannerInterface
{
    /**
     * @return string[] Array of relative file paths
     */
    public function scanFiles(): array;

    /**
     * @return string Directory tree structure
     */
    public function generateDirectoryTree(): string;
}