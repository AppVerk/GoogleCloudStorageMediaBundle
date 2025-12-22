<?php
declare(strict_types=1);

namespace AppVerk\GoogleCloudStorageMediaBundle\Service;

use Google\Cloud\Storage\Bucket;
use Google\Cloud\Storage\StorageClient;

readonly class Storage
{
    private StorageClient $client;

    public function __construct(
        string $projectId,
        private string $bucketId,
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
}
