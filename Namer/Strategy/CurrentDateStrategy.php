<?php

declare(strict_types=1);

namespace AppVerk\GoogleCloudStorageMediaBundle\Namer\Strategy;

class CurrentDateStrategy implements NamingStrategyInterface
{
    public function __construct(
        private readonly string $format = 'Y/m/d/'
    ) {
    }

    public function generate(string $filename, string $extension, string $prefix = '', string $suffix = ''): string
    {
        return date($this->format) . $prefix . $filename . $suffix . '.' . $extension;
    }
}
