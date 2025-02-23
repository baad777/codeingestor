<?php

namespace CodeIngestor;

use Symfony\Component\Yaml\Yaml;

class ConfigHandler {
    public function loadConfig(string $configPath): array {
        // Default configuration (fallback)
        $defaults = [
            'source' => './',
            'output' => 'output.txt',
            'ignore_dirs' => ['vendor', 'node_modules', '.git'],
            'ignore_files' => ['.env', '.gitignore']
        ];

        if (file_exists($configPath)) {
            $userConfig = Yaml::parseFile($configPath);
            return array_merge($defaults, $userConfig);
        }

        return $defaults;
    }
}