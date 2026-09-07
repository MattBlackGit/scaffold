<?php

declare(strict_types=1);

namespace Ngaje\Scaffold\Tests\Doctrine;

use Doctrine\ORM\Tools\SchemaTool;
use Ngaje\Scaffold\Database;
use Ngaje\Scaffold\Tests\Fixtures\Doctrine\AnnotationRecord;
use Ngaje\Scaffold\Tests\Fixtures\Doctrine\AttributeRecord;
use PHPUnit\Framework\TestCase;

final class DatabaseMixedMappingTest extends TestCase
{
    public function testAnnotationAndAttributeEntitiesCanShareOneMappingDirectory(): void
    {
        $path = dirname(__DIR__) . '/Fixtures/Doctrine';
        $database = new Database('sqlite::memory:', null, null, null, null, null, 'Test', $path);
        $entityManager = $database->getDoctrine();

        $metadata = [
            $entityManager->getClassMetadata(AttributeRecord::class),
            $entityManager->getClassMetadata(AnnotationRecord::class),
        ];
        (new SchemaTool($entityManager))->createSchema($metadata);

        $attributeRecord = new AttributeRecord();
        $attributeRecord->name = 'attribute';
        $annotationRecord = new AnnotationRecord();
        $annotationRecord->name = 'annotation';
        $entityManager->persist($attributeRecord);
        $entityManager->persist($annotationRecord);
        $entityManager->flush();
        $entityManager->clear();

        self::assertSame(
            'attribute',
            $entityManager->find(AttributeRecord::class, $attributeRecord->id)?->name
        );
        self::assertSame(
            'annotation',
            $entityManager->find(AnnotationRecord::class, $annotationRecord->id)?->name
        );
    }
}
