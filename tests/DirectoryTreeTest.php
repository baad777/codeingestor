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
    public function testTreeOnlyDirs()
    {
        // Add root dirs to include
        mkdir($this->testDir . '/SubDir2');
        mkdir($this->testDir . '/SubDir3');
        // add a file in the root dir to exclude
        file_put_contents($this->testDir . '/file.txt', '');

        $config = new ScanConfiguration([
            ScanConfigurationOption::SOURCE_PATH->value => $this->testDir,
            ScanConfigurationOption::ONLY_DIRS->value => ["SubDir2", "SubDir3"]
        ]);
        $scanner = new FileScanner($config);

        $expected = "SubDir2\nSubDir3\n";

        $this->assertEquals($expected, $scanner->generateDirectoryTree());
    }
    public function testTreeOnlyDirsAndOnlyFiles()
    {
        // Add root dirs to include
        mkdir($this->testDir . '/SubDir2');
        file_put_contents($this->testDir . '/SubDir2/file2.txt', '');
        file_put_contents($this->testDir . '/SubDir2/file22.txt', '');

        mkdir($this->testDir . '/SubDir3');
        // add a file in the root dir to exclude
        file_put_contents($this->testDir . '/file.txt', '');
        file_put_contents($this->testDir . '/file2.txt', '');

        $config = new ScanConfiguration([
            ScanConfigurationOption::SOURCE_PATH->value => $this->testDir,
            ScanConfigurationOption::ONLY_DIRS->value => ["SubDir2"],
            ScanConfigurationOption::ONLY_FILES->value => ["file2.txt"]
        ]);
        $scanner = new FileScanner($config);

        $expected = "SubDir2\n    file2.txt\n    file22.txt\nfile2.txt\n";

        $this->assertEquals($expected, $scanner->generateDirectoryTree());
    }
}