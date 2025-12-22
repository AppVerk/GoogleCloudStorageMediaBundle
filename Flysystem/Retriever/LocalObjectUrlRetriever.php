<?php

declare(strict_types=1);

namespace AppVerk\GoogleCloudStorageMediaBundle\Flysystem\Retriever;

use const DIRECTORY_SEPARATOR;

use Override;

class LocalObjectUrlRetriever implements UrlRetrieverInterface
{
    public function __construct(
        private readonly string $publicPath = '/'
    ) {
    }

    #[Override]
    public function getUrl(string $filename): string
    {
        return $this->publicPath . DIRECTORY_SEPARATOR . $filename;
    }
}
