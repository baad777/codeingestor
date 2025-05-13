<?php

namespace CodeIngestor\Tests;

use CodeIngestor\ScanConfiguration;
use CodeIngestor\ScanConfigurationOption;
use PHPUnit\Framework\TestCase;

class ScanConfigurationTest extends TestCase
{
    // scenario for basic scan configuration
    public function testNewScanConfigurationWithDefaultOptions()
    {
        $scanConfig = new \CodeIngestor\ScanConfiguration([
            'sourcePath' => $sourcePath = '/some/path',
            'output' => $outputFile = 'some_output_file.txt',
            'ignoreDirs' => $ignoreDirs = ['dir1', 'dir2'],
            'ignoreFiles' => $ignoreFiles =  ['file1.txt', 'file2.txt'],
            'onlyDirs' => $onlyDirs = ['app', 'src'],
            'onlyFiles' => $onlyFiles = ['composer.json', 'phpunit.xml'],
        ]);

        $this->assertEquals($sourcePath, $scanConfig->getOption('sourcePath'));
        $this->assertEquals($outputFile, $scanConfig->getOption('output'));
        $this->assertEquals($ignoreDirs, $scanConfig->getOption('ignoreDirs'));
        $this->assertEquals($ignoreFiles, $scanConfig->getOption('ignoreFiles'));
        $this->assertEquals($onlyDirs, $scanConfig->getOption('onlyDirs'));
        $this->assertEquals($onlyFiles, $scanConfig->getOption('onlyFiles'));
        // non existent option should return null
        $this->assertNull($scanConfig->getOption('nonExistentOption'));
    }

    public function testScanConfigurationSourcePathAndOutputMustNeverBeNull()
    {
        $scanConfig = new ScanConfiguration([]);

        $this->assertNotNull($sourcePath = $scanConfig->getOption(ScanConfigurationOption::SOURCE_PATH->value));
        $this->assertEquals(getcwd(), $sourcePath);

        $this->assertNotNull($outputFile = $scanConfig->getOption(ScanConfigurationOption::OUTPUT->value));
        $this->assertEquals("codeingestor_output.txt", $outputFile);

    }
}