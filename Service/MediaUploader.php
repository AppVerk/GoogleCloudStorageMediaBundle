<?php
declare(strict_types=1);

namespace AppVerk\GoogleCloudStorageMediaBundle\Service;

use AppVerk\GoogleCloudStorageMediaBundle\Exception\InvalidSizeException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Contracts\Translation\TranslatorInterface;

use function in_array;

class MediaUploader
{
    public function __construct(
        private readonly MediaValidation $mediaValidation,
        private readonly TranslatorInterface $translator,
        private readonly Storage $storage,
    ) {
    }

    public function upload(UploadedFile $file, ?string $groupName = null): array
    {
        $this->validate($file, $groupName);
        $this->validateSize($file, $groupName);

        $fileObject = fopen($file->getRealPath(), 'r');
        $fileMime = !empty($file->getClientMimeType()) ? $file->getClientMimeType() : $file->getMimeType();

        $object = $this->storage->bucket()
            ->upload(
                $fileObject,
                [
                    'name'          => md5(uniqid()) . '.' . $file->guessExtension(),
                    'metadata'      => ['contentType' => $fileMime],
                    'predefinedAcl' => 'publicRead',
                ],
            );

        $fileData = $object->info();

        return [$fileData['mediaLink'], $fileData['size']];
    }

    public function uploadWithName(UploadedFile $file, ?string $groupName = null): array
    {
        $this->validate($file, $groupName);
        $this->validateSize($file, $groupName);

        $fileObject = fopen($file->getRealPath(), 'r');
        $fileMime = !empty($file->getClientMimeType()) ? $file->getClientMimeType() : $file->getMimeType();

        $object = $this->storage->bucket()
            ->upload(
                $fileObject,
                [
                    'name'          => preg_replace('/\s/', '', $file->getClientOriginalName()),
                    'metadata'      => ['contentType' => $fileMime, 'filename' => $file->getClientOriginalName()],
                    'predefinedAcl' => 'publicRead',
                ],
            );

        $fileData = $object->info();

        return [$fileData['mediaLink'], $fileData['size']];
    }

    private function validate(UploadedFile $file, ?string $groupName = null): void
    {
        $allowedMimeTypes = $this->mediaValidation->getAllowedMimeTypes($groupName);
        if (!empty($allowedMimeTypes) && !in_array($file->getMimeType(), $allowedMimeTypes)) {
            throw new BadRequestHttpException($this->translator->trans('media.validation.image_type', ['%type%' => $file->getMimeType()]));
        }

        $maxSize = $this->mediaValidation->getMaxSize($groupName);
        if ($maxSize) {
            if (!($fileSize = $file->getSize())) {
                throw new InvalidSizeException('media.validation.file_not_found', null, 404);
            }

            if ($fileSize > $maxSize) {
                throw new BadRequestHttpException($this->translator->trans('media.validation.image_size', ['%max_size%' => $maxSize]));
            }
        }
    }

    private function validateSize(UploadedFile $file, ?string $groupName = null): void
    {
        $sizes = $this->mediaValidation->getGroupSizes($groupName);
        if (empty($sizes)) {
            return;
        }

        [$imageWidth, $imageHeight] = getimagesize($file->getPathname());

        $minWidth = $sizes['min_width'];
        $maxWidth = $sizes['max_width'];
        $minHeight = $sizes['min_height'];
        $maxHeight = $sizes['max_height'];

        if ($imageWidth < $minWidth || $imageWidth > $maxWidth || $imageHeight < $minHeight || $imageHeight > $maxHeight) {
            throw new BadRequestHttpException($this->translator->trans('media.validation.image_dimension', ['%max_width%' => $maxWidth, '%min_width%' => $minWidth, '%max_height%' => $maxHeight, '%min_height%' => $minHeight]));
        }
    }
}
