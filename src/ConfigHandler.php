<?php
namespace CodeIngestor;

use Symfony\Component\Yaml\Yaml;
use RuntimeException;

class ConfigHandler {
    public function loadConfig(string $configPath): array {
        $defaults = [
            'source' => getcwd(),
            'output' => 'output.txt',
            'ignore_dirs' => ['vendor', 'node_modules', '.git'],
            'ignore_files' => ['.env', '.gitignore']
        ];

        $userConfig = [];

        if (file_exists($configPath)) {
            $userConfig = Yaml::parseFile($configPath);
        }

        return array_merge($defaults, $userConfig);
    }
}