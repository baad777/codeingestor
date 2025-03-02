<?php

namespace CodeIngestor;

class ScanConfiguration
{
    public function __construct(
        private array $config = []
    )
    {
        if (!array_key_exists($key = ScanConfigurationOption::SOURCE_PATH->value, $this->config)) {
            $this->config[$key] = getcwd();
        }

        if (!array_key_exists($key = ScanConfigurationOption::OUTPUT->value, $this->config)) {
            $this->config[$key] = "codeingestor_output.txt";
        }
    }

    public function getOption(string $option): mixed
    {
        if (isset($this->config[$option])) {
            return $this->config[$option];
        } else {
            return null;
        }
    }
}