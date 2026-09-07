<?php

declare(strict_types=1);

namespace Ngaje\Scaffold\Doctrine;

use Doctrine\Persistence\Mapping\ClassMetadata;
use Doctrine\Persistence\Mapping\Driver\MappingDriver;

/**
 * Selects native PHP attributes when a class provides them and otherwise falls
 * back to the legacy annotation mapping. This permits incremental conversion of
 * entities that share one namespace and mapping directory.
 */
final class AttributeOrAnnotationDriver implements MappingDriver
{
    public function __construct(
        private MappingDriver $attributeDriver,
        private MappingDriver $annotationDriver
    ) {
    }

    public function loadMetadataForClass($className, ClassMetadata $metadata): void
    {
        $this->driverFor($className)->loadMetadataForClass($className, $metadata);
    }

    public function getAllClassNames(): array
    {
        return array_values(array_unique(array_merge(
            $this->attributeDriver->getAllClassNames(),
            $this->annotationDriver->getAllClassNames()
        )));
    }

    public function isTransient($className): bool
    {
        return $this->attributeDriver->isTransient($className)
            && $this->annotationDriver->isTransient($className);
    }

    private function driverFor(string $className): MappingDriver
    {
        return $this->attributeDriver->isTransient($className)
            ? $this->annotationDriver
            : $this->attributeDriver;
    }
}
