<?php

declare(strict_types=1);

namespace AppVerk\GoogleCloudStorageMediaBundle\Namer;

use AppVerk\GoogleCloudStorageMediaBundle\Namer\Strategy\NamingStrategyInterface;

use Override;

abstract class AbstractNamer implements NamerInterface
{
    public function __construct(
        protected readonly NamingStrategyInterface $strategy
    ) {
    }

    #[Override]
    public function generate(string $filename, string $extension): string
    {
        return $this->strategy->generate($this->sanitize($filename, $extension), $extension);
    }

    protected function sanitize(string $filename, string $extension = ''): string
    {
        $newName = str_ireplace('.' . $extension, '', $filename);
        $newName = preg_replace(['/\s/', '/\.[\.]+/', '/[^\w_\.\-]/'], ['_', '.', ''], $newName);

        return $newName;
    }
}
