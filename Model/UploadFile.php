<?php
declare(strict_types=1);

namespace AppVerk\GoogleCloudStorageMediaBundle\Model;

readonly class UploadFile
{
    public function __construct(
        private string $name,
        private string $fileName,
        private string $url,
        private string $mimetype,
        private int $size,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getMimetype(): string
    {
        return $this->mimetype;
    }

    public function getSize(): int
    {
        return $this->size;
    }
}
