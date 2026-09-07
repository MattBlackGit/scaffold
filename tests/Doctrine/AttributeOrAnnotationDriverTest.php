<?php

declare(strict_types=1);

namespace Ngaje\Scaffold\Tests\Doctrine;

use Doctrine\Persistence\Mapping\ClassMetadata;
use Doctrine\Persistence\Mapping\Driver\MappingDriver;
use Ngaje\Scaffold\Doctrine\AttributeOrAnnotationDriver;
use PHPUnit\Framework\TestCase;

final class AttributeOrAnnotationDriverTest extends TestCase
{
    public function testAttributeMappingTakesPrecedence(): void
    {
        $attributes = new RecordingDriver(['Example\\AttributedEntity']);
        $annotations = new RecordingDriver(['Example\\AttributedEntity']);
        $driver = new AttributeOrAnnotationDriver($attributes, $annotations);

        $driver->loadMetadataForClass('Example\\AttributedEntity', $this->createStub(ClassMetadata::class));

        self::assertSame(['Example\\AttributedEntity'], $attributes->loadedClasses);
        self::assertSame([], $annotations->loadedClasses);
    }

    public function testAnnotationMappingRemainsAvailableDuringConversion(): void
    {
        $attributes = new RecordingDriver([]);
        $annotations = new RecordingDriver(['Example\\AnnotatedEntity']);
        $driver = new AttributeOrAnnotationDriver($attributes, $annotations);

        $driver->loadMetadataForClass('Example\\AnnotatedEntity', $this->createStub(ClassMetadata::class));

        self::assertSame([], $attributes->loadedClasses);
        self::assertSame(['Example\\AnnotatedEntity'], $annotations->loadedClasses);
    }

    public function testClassInventoryIsDeduplicatedAndTransientStateUsesBothDrivers(): void
    {
        $driver = new AttributeOrAnnotationDriver(
            new RecordingDriver(['Example\\Converted', 'Example\\Shared']),
            new RecordingDriver(['Example\\Shared', 'Example\\Legacy'])
        );

        self::assertSame(
            ['Example\\Converted', 'Example\\Shared', 'Example\\Legacy'],
            $driver->getAllClassNames()
        );
        self::assertFalse($driver->isTransient('Example\\Converted'));
        self::assertFalse($driver->isTransient('Example\\Legacy'));
        self::assertTrue($driver->isTransient('Example\\Unknown'));
    }
}

final class RecordingDriver implements MappingDriver
{
    /** @var list<string> */
    public array $loadedClasses = [];

    /** @param list<string> $classes */
    public function __construct(private array $classes)
    {
    }

    public function loadMetadataForClass($className, ClassMetadata $metadata): void
    {
        $this->loadedClasses[] = $className;
    }

    public function getAllClassNames(): array
    {
        return $this->classes;
    }

    public function isTransient($className): bool
    {
        return !in_array($className, $this->classes, true);
    }
}
