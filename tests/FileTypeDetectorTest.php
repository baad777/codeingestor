<?php

namespace CodeIngestor\Tests;

use CodeIngestor\FileTypeDetector;
use PHPUnit\Framework\TestCase;

class FileTypeDetectorTest extends TestCase
{
    use GeneratesImageTrait;
    private string $textFile;
    private string $binaryFile;

    protected function setUp(): void
    {
        $this->textFile = sys_get_temp_dir() . '/test.txt';
        file_put_contents($this->textFile, 'ASCII content');

        // Define the path to save the PNG file in the tmp folder
        $this->binaryFile = sys_get_temp_dir() . '/test_image.png';
        $this->generateImage($this->binaryFile);
    }

    protected function tearDown(): void
    {
        unlink($this->textFile);
        unlink($this->binaryFile);
    }

    public function testDetectsTextFile()
    {
        $detector = new FileTypeDetector();
        $this->assertFalse($detector->isBinary($this->textFile));
    }

    public function testDetectsBinaryFile()
    {
        $detector = new FileTypeDetector();
        $this->assertTrue($detector->isBinary($this->binaryFile));
    }
}