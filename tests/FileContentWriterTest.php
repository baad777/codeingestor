<?php

namespace CodeIngestor\Tests;

use CodeIngestor\DirectoryDeleterTrait;
use CodeIngestor\FileContentWriter;
use CodeIngestor\ScanConfiguration;
use CodeIngestor\ScanConfigurationOption;
use JetBrains\PhpStorm\NoReturn;
use PHPUnit\Framework\TestCase;

class FileContentWriterTest extends TestCase
{
    use DirectoryDeleterTrait;

    private string $testDir;

    protected function setUp(): void
    {
        $this->testDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'codeingestor_test_' . uniqid();
        mkdir($this->testDir);

        $srcDir = $this->testDir . '/src';
        mkdir($srcDir);
        file_put_contents($srcDir . '/file1.php', '');
        $utilsDir = $srcDir . '/utils';
        mkdir($utilsDir);
        file_put_contents($utilsDir . '/file2.php', '');
    }

    protected function tearDown(): void
    {
        $this->deleteDirectory($this->testDir);
    }

    #[NoReturn] public function testWriteFileContentsTwiceWillOverwriteExistingFile()
    {
        $fileContentWriter = new FileContentWriter(new ScanConfiguration([
            ScanConfigurationOption::SOURCE_PATH->value => $this->testDir,
            ScanConfigurationOption::OUTPUT->value => $filePath = $this->testDir . '/output.txt'
        ]));
        // Call the method under test
        $fileContentWriter->writeFileContents();
        // get file size
        $fileSize = filesize($filePath);
        // Write to the same file again
        $fileContentWriter->writeFileContents();
        // Check if the file size has changed
        $newFileSize = filesize($filePath);
        $this->assertEquals($fileSize, $newFileSize);
    }
}