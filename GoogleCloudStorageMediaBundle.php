<?php
declare(strict_types=1);

namespace AppVerk\GoogleCloudStorageMediaBundle;

use AppVerk\GoogleCloudStorageMediaBundle\DependencyInjection\Compiler\AddStorageServiceMappingPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

use Override;

class GoogleCloudStorageMediaBundle extends Bundle
{
    #[Override]
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new AddStorageServiceMappingPass());
    }
}
