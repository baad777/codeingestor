<?php

namespace CodeIngestor\Tests;

use CodeIngestor\DirectoryDeleterTrait;
use CodeIngestor\FileScanner;
use CodeIngestor\ScanConfiguration;
use PHPUnit\Framework\TestCase;

class FileScannerTest extends TestCase
{
    use DirectoryDeleterTrait;
    private string $testDir;

    protected function setUp(): void
    {
        $this->testDir = sys_get_temp_dir().DIRECTORY_SEPARATOR.'codeingestor_test_'.uniqid();
        mkdir($this->testDir);

        mkdir($this->testDir.'/src');
        file_put_contents($this->testDir.'/src/file1.php', '');
        mkdir($this->testDir.'/src/utils');
        file_put_contents($this->testDir.'/src/utils/file2.php', '');
        mkdir($this->testDir.'/src/vendor'); // Should be ignored
    }

    protected function tearDown(): void
    {
        $this->deleteDirectory($this->testDir);
    }

    public function testIgnoresDotAndDotDotByDefault()
    {
        $config = new ScanConfiguration(
            $this->testDir,
            [], // Empty ignore_dirs (but . and .. are always ignored)
            []
        );
        $scanner = new FileScanner($config);

        $files = $scanner->scanFiles();
        $this->assertNotContains('.', $files);
        $this->assertNotContains('..', $files);
    }

    public function testHandlesEmptyDirectory()
    {
        $emptyDir = sys_get_temp_dir().DIRECTORY_SEPARATOR.'empty_test_'.uniqid();
        mkdir($emptyDir);

        $config = new ScanConfiguration($emptyDir, [], []);
        $scanner = new FileScanner($config);

        $this->assertEmpty($scanner->scanFiles());
        rmdir($emptyDir);
    }
}