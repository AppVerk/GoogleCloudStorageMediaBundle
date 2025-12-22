<?php

declare(strict_types=1);

namespace AppVerk\GoogleCloudStorageMediaBundle\Tests\Service;

use AppVerk\GoogleCloudStorageMediaBundle\Event\FileWasAdded;
use AppVerk\GoogleCloudStorageMediaBundle\Event\FileWasRemoved;
use AppVerk\GoogleCloudStorageMediaBundle\Exception\FilesystemException as AppFileSystemException;
use AppVerk\GoogleCloudStorageMediaBundle\Flysystem\Retriever\UrlRetrieverInterface;
use AppVerk\GoogleCloudStorageMediaBundle\Namer\NamerInterface;
use AppVerk\GoogleCloudStorageMediaBundle\Service\MediaValidation;
use AppVerk\GoogleCloudStorageMediaBundle\Service\v2\StorageService;
use League\Flysystem\FilesystemOperator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

use Override;

class StorageServiceTest extends TestCase
{
    private MediaValidation $mediaValidation;
    private NamerInterface $namer;
    private FilesystemOperator $filesystem;
    private TranslatorInterface $translator;
    private UrlRetrieverInterface $urlRetriever;
    private EventDispatcherInterface $eventDispatcher;
    private StorageService $storageService;

    #[Override]
    protected function setUp(): void
    {
        $this->mediaValidation = $this->createMock(MediaValidation::class);
        $this->namer = $this->createMock(NamerInterface::class);
        $this->filesystem = $this->createMock(FilesystemOperator::class);
        $this->translator = $this->createMock(TranslatorInterface::class);
        $this->urlRetriever = $this->createMock(UrlRetrieverInterface::class);
        $this->eventDispatcher = $this->createMock(EventDispatcherInterface::class);

        $this->storageService = new StorageService(
            $this->mediaValidation,
            $this->namer,
            $this->filesystem,
            $this->translator,
            $this->urlRetriever,
            $this->eventDispatcher
        );
    }

    public function testSaveStoresFileWithCorrectPath(): void
    {
        $file = $this->createMock(UploadedFile::class);
        $file->method('getFilename')->willReturn('test.jpg');
        $file->method('getExtension')->willReturn('jpg');
        $file->method('getMimeType')->willReturn('image/jpeg');
        $file->method('getSize')->willReturn(100);
        $file->method('getContent')->willReturn('file content');

        $fullPath = '2025/12/22/unique_test.jpg';
        $friendlyName = 'unique_test.jpg';
        
        $this->namer->expects($this->once())
            ->method('generate')
            ->willReturn($fullPath);

        $this->mediaValidation->method('getAllowedMimeTypes')->willReturn(['image/jpeg']);
        $this->mediaValidation->method('getMaxSize')->willReturn(1000);
        $this->mediaValidation->method('getGroupSizes')->willReturn([]);

        $this->filesystem->expects($this->once())
            ->method('write')
            ->with($friendlyName, 'file content');

        $this->urlRetriever->expects($this->once())
            ->method('getUrl')
            ->with($fullPath)
            ->willReturn('http://example.com/' . $fullPath);

        $this->eventDispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(FileWasAdded::class));

        $result = $this->storageService->save($file);

        $this->assertEquals($friendlyName, $result->getName());
        $this->assertEquals($fullPath, $result->getFileName());
        $this->assertEquals('http://example.com/' . $fullPath, $result->getUrl());
    }

    public function testRemoveDeletesFile(): void
    {
        $filename = 'path/to/file.jpg';

        $this->filesystem->expects($this->once())
            ->method('fileExists')
            ->with($filename)
            ->willReturn(true);

        $this->filesystem->expects($this->once())
            ->method('delete')
            ->with($filename);

        $this->eventDispatcher->expects($this->once())
            ->method('dispatch')
            ->with($this->isInstanceOf(FileWasRemoved::class));

        $result = $this->storageService->remove($filename);

        $this->assertTrue($result);
    }

    public function testRemoveReturnsFalseIfFileDoesNotExist(): void
    {
        $filename = 'non-existent.jpg';

        $this->filesystem->expects($this->once())
            ->method('fileExists')
            ->with($filename)
            ->willReturn(false);

        $this->filesystem->expects($this->never())
            ->method('delete');

        $result = $this->storageService->remove($filename);

        $this->assertFalse($result);
    }
}
