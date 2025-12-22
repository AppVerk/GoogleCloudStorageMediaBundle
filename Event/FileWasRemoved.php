<?php

declare(strict_types=1);

namespace AppVerk\GoogleCloudStorageMediaBundle\Event;

readonly class FileWasRemoved
{
    public function __construct(
        private string $fileName
    ) {
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }
}
