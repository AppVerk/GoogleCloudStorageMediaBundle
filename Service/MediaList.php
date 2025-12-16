<?php

namespace AppVerk\GoogleCloudStorageMediaBundle\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

class MediaList
{
    protected Storage $storage;

    public function __construct(
        Storage $storage
    ) {
        $this->storage = $storage;
    }

    public function listAllFiles(): iterable
    {
        $bucket = $this->storage->bucket();

        foreach ($bucket->objects() as $object) {
            yield $object->name();
        }
    }
}
