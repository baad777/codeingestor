<?php

namespace CodeIngestor;

readonly class ScanConfiguration
{
    public function __construct(
        private array $config = []
    ) {
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