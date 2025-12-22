<?php

declare(strict_types=1);

namespace AppVerk\GoogleCloudStorageMediaBundle\Event;

use AppVerk\GoogleCloudStorageMediaBundle\Model\UploadFile;

readonly class FileWasAdded
{
    public function __construct(
        private UploadFile $file
    ) {
    }

    public function getFile(): UploadFile
    {
        return $this->file;
    }
}
