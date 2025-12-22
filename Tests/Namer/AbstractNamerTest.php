<?php

declare(strict_types=1);

namespace AppVerk\GoogleCloudStorageMediaBundle\Tests\Namer;

use AppVerk\GoogleCloudStorageMediaBundle\Namer\AbstractNamer;
use AppVerk\GoogleCloudStorageMediaBundle\Namer\Strategy\NamingStrategyInterface;
use PHPUnit\Framework\TestCase;

class AbstractNamerTest extends TestCase
{
    public function testSanitize(): void
    {
        $strategy = $this->createMock(NamingStrategyInterface::class);
        $strategy->method('generate')->willReturnArgument(0);

        $namer = new class($strategy) extends AbstractNamer {
            public function testSanitize(string $filename, string $extension): string
            {
                return $this->sanitize($filename, $extension);
            }
        };

        $this->assertEquals('file_name', $namer->testSanitize('file name.jpg', 'jpg'));
        $this->assertEquals('file-name', $namer->testSanitize('file-name.png', 'png'));
        $this->assertEquals('file.name', $namer->testSanitize('file..name.png', 'png'));
        $this->assertEquals('special_chars', $namer->testSanitize('spec!@#$%^&*()ial chars.jpg', 'jpg'));
    }
}
