<?php
declare(strict_types=1);

namespace AppVerk\GoogleCloudStorageMediaBundle\Controller;

use AppVerk\GoogleCloudStorageMediaBundle\Doctrine\MediaManager;
use AppVerk\GoogleCloudStorageMediaBundle\Service\MediaUploader;
use Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/media')]
class MediaController
{
    #[Route(path: '/upload/{group}', name: 'upload_media', methods: ['POST'])]
    public function uploadAction(Request $request, MediaUploader $mediaUploader, MediaManager $mediaManager, ?string $group = null): Response
    {
        $file = $request->files->get('file');
        if ($file instanceof UploadedFile) {
            try {
                [$fileName, $size] = $mediaUploader->upload($file, $group);
                $media = $mediaManager->createMedia($file, $fileName, $size);

                $output['fileName'] = $media->getFileName();
                $output['id'] = $media->getId();

                return new JsonResponse($output);
            } catch (Exception $e) {
                return new JsonResponse($e->getMessage(), Response::HTTP_BAD_REQUEST);
            }
        }

        return new Response('', Response::HTTP_BAD_REQUEST);
    }
}
