<?php

declare(strict_types=1);

namespace AppVerk\GoogleCloudStorageMediaBundle\Namer;

use Override;

class DefaultNamer extends AbstractNamer
{
    #[Override]
    public function generate(string $filename, string $extension): string
    {
        return $this->strategy->generate($this->sanitize($filename, $extension), $extension, '', uniqid('_'));
    }
}
