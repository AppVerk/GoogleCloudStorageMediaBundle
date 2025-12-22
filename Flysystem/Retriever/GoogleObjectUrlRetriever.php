<?php

declare(strict_types=1);

namespace AppVerk\GoogleCloudStorageMediaBundle\Flysystem\Retriever;

use Google\Cloud\Storage\Bucket;
use Google\Cloud\Storage\StorageClient;
use Google\Cloud\Storage\StorageObject;

use Override;

class GoogleObjectUrlRetriever implements UrlRetrieverInterface
{
    private StorageClient $client;

    public function __construct(
        string $projectId,
        private readonly string $bucketId,
        string $keyFilePath
    ) {
        $this->client = new StorageClient(
            [
                'projectId'   => $projectId,
                'keyFilePath' => $keyFilePath,
            ],
        );
    }

    public function bucket(): Bucket
    {
        return $this->client->bucket($this->bucketId);
    }

    public function getObject($filename): StorageObject
    {
        return $this->bucket()->object($filename);
    }

    public function getInfo(StorageObject $storageObject): array
    {
        return $storageObject->info();
    }

    #[Override]
    public function getUrl(string $filename): string
    {
        $info = $this->getInfo($this->getObject($filename));

        return $info['mediaLink'];
    }
}
