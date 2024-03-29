<?php

declare(strict_types = 1);

namespace AppVerk\GoogleCloudStorageMediaBundle\Event;

class FileWasRemoved
{
    private string $fileName;

    public function __construct(string $fileName) {
        $this->fileName = $fileName;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }
}
