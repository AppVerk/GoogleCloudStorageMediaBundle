<?php
declare(strict_types=1);

namespace AppVerk\GoogleCloudStorageMediaBundle\Service;

use function array_key_exists;

class MediaValidation
{
    public function __construct(
        private readonly ?int $maxSize = null,
        private readonly array $allowedMimeTypes = [],
        private readonly array $groups = []
    ) {
    }

    private function getGroup(string $groupName): ?array
    {
        if (array_key_exists($groupName, $this->groups)) {
            return $this->groups[$groupName];
        }

        return null;
    }

    public function getAllowedMimeTypes(?string $groupName = null): array
    {
        if (null !== $groupName) {
            if ($group = $this->getGroup($groupName)) {
                return $group['allowed_mime_types'];
            }
        }

        return $this->allowedMimeTypes;
    }

    public function getMaxSize(?string $groupName = null): ?int
    {
        if (null !== $groupName) {
            if ($group = $this->getGroup($groupName)) {
                return $group['max_file_size'];
            }
        }

        return $this->maxSize;
    }

    public function getGroupSizes(?string $groupName = null): array
    {
        if (!$groupName) {
            return [];
        }

        $group = $this->getGroup($groupName);

        if (!$group || !isset($group['sizes'])) {
            return [];
        }

        return $group['sizes'];
    }
}
