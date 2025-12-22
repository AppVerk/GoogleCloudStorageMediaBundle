<?php
declare(strict_types=1);

namespace AppVerk\GoogleCloudStorageMediaBundle\Form\DataTransformer;

use AppVerk\Components\Doctrine\EntityInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

use function sprintf;

class MediaTransformer implements DataTransformerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var string
     */
    private $className;

    public function __construct(EntityManagerInterface $entityManager, string $className)
    {
        $this->entityManager = $entityManager;
        $this->className = $className;
    }

    public function transform($value): mixed
    {
        if (!$value instanceof EntityInterface) {
            return '';
        }

        return $value;
    }

    public function reverseTransform($value): mixed
    {
        if (!$value) {
            return null;
        }

        $entity = $this->entityManager
            ->getRepository($this->className)
            ->find($value);

        if (!$entity) {
            throw new TransformationFailedException(sprintf('An entity with ID "%s" does not exist!', $value));
        }

        return $entity;
    }
}
