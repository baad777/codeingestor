<?php

namespace CodeIngestor;
enum ScanConfigurationOption: string
{
    case SOURCE_PATH = 'sourcePath';
    case OUTPUT = 'outputFile';
    case IGNORE_DIRS = 'ignoreDirs';
    case IGNORE_FILES = 'ignoreFiles';
}
