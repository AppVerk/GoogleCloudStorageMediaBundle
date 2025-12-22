<?php
declare(strict_types=1);

namespace AppVerk\GoogleCloudStorageMediaBundle\Service;

use AppVerk\GoogleCloudStorageMediaBundle\Entity\Media;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class MediaProvider
{
    private ?Request $request;

    public function __construct(
        private readonly RequestStack $requestStack
    ) {
        $this->request = $this->requestStack->getCurrentRequest();
    }

    public function getUrl(Media $media): string
    {
        return $media->getFileName();
    }

    public function getPublicUrl(Media $media): string
    {
        return $this->getUrl($media);
    }
}
