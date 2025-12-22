<?php
declare(strict_types=1);

namespace AppVerk\GoogleCloudStorageMediaBundle\Twig;

use AppVerk\GoogleCloudStorageMediaBundle\Entity\Media;
use AppVerk\GoogleCloudStorageMediaBundle\Service\MediaProvider;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

use Override;

class MediaExtension extends AbstractExtension
{
    public function __construct(
        private readonly MediaProvider $mediaProvider
    ) {
    }

    #[Override]
    public function getFilters(): array
    {
        return [
            new TwigFilter('media', [$this, 'mediaFilter']),
            new TwigFilter('json_media', [$this, 'jsonMediaFilter'], [
                'is_safe' => ['html'],
            ]),
            new TwigFilter('array_media', [$this, 'arrayMediaFilter'], [
                'is_safe' => ['html'],
            ]),
        ];
    }

    public function jsonMediaFilter($media): string
    {
        $data = [];

        if ($media instanceof Media) {
            $data = [
                'name'      => $media->getName(),
                'url'       => $this->mediaProvider->getUrl($media),
                'mime_type' => $media->getMimeType(),
                'size'      => $media->getSize(),
            ];
        }

        return (string) json_encode($data);
    }

    public function arrayMediaFilter($media): array
    {
        $data = [];

        if ($media instanceof Media) {
            $data = [
                'name'      => $media->getName(),
                'url'       => $this->mediaProvider->getUrl($media),
                'mime_type' => $media->getMimeType(),
                'size'      => $media->getSize(),
            ];
        }

        return $data;
    }

    public function mediaFilter(Media $media): string
    {
        return $this->mediaProvider->getUrl($media);
    }
}
