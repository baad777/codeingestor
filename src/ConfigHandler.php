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

        if (!file_exists($configPath)) {
            throw new RuntimeException("Config file not found: {$configPath}");
        }

        $userConfig = Yaml::parseFile($configPath);
        return array_merge($defaults, $userConfig);
    }
}