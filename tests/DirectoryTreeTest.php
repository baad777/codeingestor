<?php
namespace CodeIngestor\Tests;

use CodeIngestor\FileScanner;
use CodeIngestor\ScanConfiguration;
use CodeIngestor\ScanConfigurationOption;
use PHPUnit\Framework\TestCase;

class DirectoryTreeTest extends TestCase
{
    private string $testDir;

    protected function setUp(): void
    {
        $this->testDir = sys_get_temp_dir() . '/tree_test_' . uniqid();
        mkdir($this->testDir);

        mkdir($this->testDir . '/src');
        mkdir($this->testDir . '/src/SubDir');
        file_put_contents($this->testDir . '/src/SubDir/file.txt', '');
    }

    public function testGeneratesCorrectTreeStructure()
    {
        $config = new ScanConfiguration([
            'sourcePath' => $this->testDir
        ]);
        $scanner = new FileScanner($config);

        $expected = "src\n    SubDir\n        file.txt\n";
        $this->assertEquals($expected, $scanner->generateDirectoryTree());
    }

    public function testTreeIncludesFilesAndDirectories()
    {
        // Add a file at the root of the test directory
        file_put_contents($this->testDir . '/root_file.txt', '');

        $config = new ScanConfiguration([
            ScanConfigurationOption::SOURCE_PATH->value => $this->testDir
        ]);
        $scanner = new FileScanner($config);

        $expected = "src\n    SubDir\n        file.txt\nroot_file.txt\n";

        $this->assertEquals($expected, $scanner->generateDirectoryTree());
    }
}