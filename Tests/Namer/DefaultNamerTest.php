<?php

declare(strict_types=1);

namespace AppVerk\GoogleCloudStorageMediaBundle\Tests\Namer;

use AppVerk\GoogleCloudStorageMediaBundle\Namer\DefaultNamer;
use AppVerk\GoogleCloudStorageMediaBundle\Namer\Strategy\NamingStrategyInterface;
use PHPUnit\Framework\TestCase;

class DefaultNamerTest extends TestCase
{
    public function testGenerate(): void
    {
        $strategy = $this->createMock(NamingStrategyInterface::class);
        $strategy->expects($this->once())
            ->method('generate')
            ->with('my_file', 'jpg', '', $this->callback(fn($suffix) => str_starts_with($suffix, '_')))
            ->willReturn('generated_path/my_file_unique.jpg');

        $namer = new DefaultNamer($strategy);
        $result = $namer->generate('my file.jpg', 'jpg');

        $this->assertEquals('generated_path/my_file_unique.jpg', $result);
    }

    public function testSanitize(): void
    {
        $strategy = $this->createMock(NamingStrategyInterface::class);
        $strategy->method('generate')->willReturnArgument(0);

        $namer = new DefaultNamer($strategy);
        
        // Test sanitization via a proxy or reflection if needed, 
        // but here we can test it through the strategy's input
        
        $strategy->expects($this->atLeastOnce())
            ->method('generate')
            ->with('file_name', 'png')
            ->willReturn('file_name');

        $namer->generate('file name.png', 'png');
    }
}
