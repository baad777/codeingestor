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
        $this->testDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'codeingestor_test_' . uniqid();
        mkdir($this->testDir);

        $srcDir = $this->testDir . '/src';
        mkdir($srcDir);
        file_put_contents($srcDir . '/file1.php', '');
        $utilsDir = $srcDir . '/utils';
        mkdir($utilsDir);
        file_put_contents($utilsDir . '/file2.php', '');

        // Should be ignored
        $vendorDir = $this->testDir . '/src/vendor';
        mkdir($vendorDir);
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

    public function testTreeIncludesFilesAndDirectories()
    {
        // Add a file at the root of the test directory
        file_put_contents($this->testDir . '/root_file.txt', '');

        $config = new ScanConfiguration($this->testDir, [], []);
        $scanner = new FileScanner($config);

        $expected = "src\n    utils\n        file2.php\n    vendor\n    file1.php\nroot_file.txt\n";

        $this->assertEquals($expected, $scanner->generateDirectoryTree());
    }
}