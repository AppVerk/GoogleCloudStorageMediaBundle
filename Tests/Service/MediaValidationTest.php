<?php

declare(strict_types=1);

namespace AppVerk\GoogleCloudStorageMediaBundle\Tests\Service;

use AppVerk\GoogleCloudStorageMediaBundle\Service\MediaValidation;
use PHPUnit\Framework\TestCase;

class MediaValidationTest extends TestCase
{
    private MediaValidation $mediaValidation;

    protected function setUp(): void
    {
        $this->mediaValidation = new MediaValidation(
            1000,
            ['image/jpeg'],
            [
                'group1' => [
                    'allowed_mime_types' => ['application/pdf'],
                    'max_file_size' => 2000,
                    'sizes' => [
                        'min_width' => 100,
                        'max_width' => 200,
                    ]
                ]
            ]
        );
    }

    public function testGetAllowedMimeTypesDefault(): void
    {
        $this->assertEquals(['image/jpeg'], $this->mediaValidation->getAllowedMimeTypes());
    }

    public function testGetAllowedMimeTypesGroup(): void
    {
        $this->assertEquals(['application/pdf'], $this->mediaValidation->getAllowedMimeTypes('group1'));
    }

    public function testGetAllowedMimeTypesNonExistentGroup(): void
    {
        $this->assertEquals(['image/jpeg'], $this->mediaValidation->getAllowedMimeTypes('non-existent'));
    }

    public function testGetMaxSizeDefault(): void
    {
        $this->assertEquals(1000, $this->mediaValidation->getMaxSize());
    }

    public function testGetMaxSizeGroup(): void
    {
        $this->assertEquals(2000, $this->mediaValidation->getMaxSize('group1'));
    }

    public function testGetGroupSizes(): void
    {
        $this->assertEquals(['min_width' => 100, 'max_width' => 200], $this->mediaValidation->getGroupSizes('group1'));
    }

    public function testGetGroupSizesEmpty(): void
    {
        $this->assertEquals([], $this->mediaValidation->getGroupSizes());
        $this->assertEquals([], $this->mediaValidation->getGroupSizes('non-existent'));
    }
}
