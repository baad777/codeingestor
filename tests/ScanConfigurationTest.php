<?php

namespace CodeIngestor\Tests;

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
            'ignoreFiles' => $ignoreFiles =  ['file1.txt', 'file2.txt']
        ]);

        $this->assertEquals($sourcePath, $scanConfig->getOption('sourcePath'));
        $this->assertEquals($outputFile, $scanConfig->getOption('output'));
        $this->assertEquals($ignoreDirs, $scanConfig->getOption('ignoreDirs'));
        $this->assertEquals($ignoreFiles, $scanConfig->getOption('ignoreFiles'));
        // non existent option should return null
        $this->assertNull($scanConfig->getOption('nonExistentOption'));
    }
}