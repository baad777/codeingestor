<?php
namespace CodeIngestor\Tests;

use CodeIngestor\FileScanner;
use CodeIngestor\ScanConfiguration;
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
        $config = new ScanConfiguration($this->testDir, [], []);
        $scanner = new FileScanner($config);

        $expected = "src\n    SubDir\n        file.txt\n";
        $this->assertEquals($expected, $scanner->generateDirectoryTree());
    }
}