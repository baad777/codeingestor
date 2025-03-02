<?php
namespace CodeIngestor;

use Symfony\Component\Yaml\Yaml;

class ConfigHandler {
    public function loadConfig(string $configPath): array {
        $defaults = [
            ScanConfigurationOption::SOURCE_PATH->value => getcwd(),
            ScanConfigurationOption::OUTPUT->value => 'codeingestor_output.txt',
            ScanConfigurationOption::IGNORE_DIRS->value => ['vendor', 'node_modules', '.git'],
            ScanConfigurationOption::IGNORE_FILES->value => ['.env', '.gitignore', '*.lock']
        ];

        $userConfig = [];

        if (file_exists($configPath)) {
            $userConfig = Yaml::parseFile($configPath);
        }

        return array_merge($defaults, $userConfig);
    }
}