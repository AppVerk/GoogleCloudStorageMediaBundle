<?php

declare(strict_types=1);

namespace AppVerk\GoogleCloudStorageMediaBundle\Tests\Namer\Strategy;

use AppVerk\GoogleCloudStorageMediaBundle\Namer\Strategy\CurrentDateStrategy;
use PHPUnit\Framework\TestCase;

class CurrentDateStrategyTest extends TestCase
{
    public function testGenerate(): void
    {
        $strategy = new CurrentDateStrategy('Y-m-d/');
        $filename = 'test';
        $extension = 'jpg';
        
        $expectedPrefix = date('Y-m-d/');
        $result = $strategy->generate($filename, $extension, 'pre_', '_suf');
        
        $this->assertEquals($expectedPrefix . 'pre_test_suf.jpg', $result);
    }
}
