<?php

namespace CodeIngestor\Tests;

use CodeIngestor\DirectoryDeleterTrait;
use CodeIngestor\FileScanner;
use CodeIngestor\ScanConfiguration;
use CodeIngestor\ScanConfigurationOption;
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
        $config = new ScanConfiguration([
            ScanConfigurationOption::SOURCE_PATH->value => $this->testDir
        ]);
        $scanner = new FileScanner($config);

        $files = $scanner->scanFiles();
        $this->assertNotContains('.', $files);
        $this->assertNotContains('..', $files);
    }

    public function testHandlesEmptyDirectory()
    {
        $emptyDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'empty_test_' . uniqid();
        mkdir($emptyDir);

        $config = new ScanConfiguration([
            ScanConfigurationOption::SOURCE_PATH->value => $emptyDir
        ]);
        $scanner = new FileScanner($config);

        $this->assertEmpty($scanner->scanFiles());
        rmdir($emptyDir);
    }

    public function testExcludesHiddenFilesAndDirectories()
    {
        // Add a hidden file and directory to the test directory
        file_put_contents($this->testDir . '/.hidden_file.txt', '');
        mkdir($this->testDir . '/.hidden_dir');
        // add a file in the hidden dir
        file_put_contents($this->testDir . '/.hidden_dir/file_in_hidden_dir.txt', '');

        $config = new ScanConfiguration([
            ScanConfigurationOption::SOURCE_PATH->value => $this->testDir
        ]);
        $scanner = new FileScanner($config);

        $files = $scanner->scanFiles();
        // files must not include the hidden file and directory
        $this->assertNotContains('.hidden_file.txt', $files);
        $this->assertNotContains('.hidden_dir', $files);
        $this->assertNotContains('file_in_hidden_dir.txt', $files);
    }

    public function testIgnoresFilesWithPattern()
    {
        // Add a two log files to the test directory
        file_put_contents($this->testDir . '/file1.log', '');
        file_put_contents($this->testDir . '/src/file2.log', '');

        $config = new ScanConfiguration([
            ScanConfigurationOption::SOURCE_PATH->value => $this->testDir,
            ScanConfigurationOption::IGNORE_FILES->value => ['*.log'] // Ignore files with .log extension
        ]);
        $scanner = new FileScanner($config);

        $files = $scanner->scanFiles();

        $this->assertNotContains('file1.log', $files);
        $this->assertNotContains('src/file2.log', $files);

    }

    public function testIgnoresOutputFileFromConfigIfExists()
    {
        // Add a output file to the test directory
        file_put_contents($this->testDir . '/outputFILE.txt', 'OUTPUT_TEXT_GOES_HERE');

        $config = new ScanConfiguration([
            ScanConfigurationOption::SOURCE_PATH->value => $this->testDir,
            ScanConfigurationOption::OUTPUT->value => $this->testDir . '/outputFILE.txt' // Ignore files with .log extension
        ]);
        $scanner = new FileScanner($config);
        $files = $scanner->scanFiles();

        $this->assertNotContains('outputFILE.txt', $files);
    }
}