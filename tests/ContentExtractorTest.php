<?php

namespace CodeIngestor\Tests;

use CodeIngestor\ContentExtractor;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class ContentExtractorTest extends TestCase
{
    private string $testFile;

    protected function setUp(): void
    {
        $this->testFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'test_file.txt';
        file_put_contents($this->testFile, 'Hello, world!');
    }

    protected function tearDown(): void
    {
        if (file_exists($this->testFile)) {
            unlink($this->testFile);
        }
    }

    public function testExtractsContentSuccessfully()
    {
        $extractor = new ContentExtractor();
        $this->assertEquals('Hello, world!', $extractor->extract($this->testFile));
    }

    public function testThrowsExceptionForMissingFile()
    {
        $this->expectException(RuntimeException::class);
        $extractor = new ContentExtractor();
        $extractor->extract('/invalid/path');
    }
}