<?php
declare(strict_types=1);

namespace AppVerk\GoogleCloudStorageMediaBundle\Service;

use Google\Cloud\Storage\Bucket;
use Google\Cloud\Storage\StorageClient;

class Storage
{
    private readonly StorageClient $client;

    /**
     * Storage constructor.
     */
    public function __construct(string $projectId, private readonly string $bucketId, string $keyFilePath)
    {
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
