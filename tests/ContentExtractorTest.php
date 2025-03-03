<?php

namespace CodeIngestor\Tests;

use CodeIngestor\ContentExtractor;
use Faker\Factory;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class ContentExtractorTest extends TestCase
{
    use GeneratesImageTrait;
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

    public function testBinaryFileContentsWillNotBeExtracted()
    {
        // Define the path to save the PNG file in the tmp folder
        $tmpFilePath = sys_get_temp_dir() . '/test_image.png';
        $this->generateImage($tmpFilePath);

        $extractor = new ContentExtractor();
        $result = $extractor->extract($tmpFilePath);

        $this->assertEquals("***Binary file contents will not be extracted***", $result);
        unlink($tmpFilePath); // Clean up the temporary file after testing
    }
}