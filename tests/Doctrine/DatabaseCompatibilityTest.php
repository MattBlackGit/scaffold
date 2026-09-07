<?php

declare(strict_types=1);

namespace Ngaje\Scaffold\Tests\Doctrine;

use Doctrine\ORM\Query\Filter\SQLFilter;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\Persistence\Mapping\ClassMetadata;
use Ngaje\Scaffold\Database;
use Ngaje\Scaffold\Tests\Fixtures\Doctrine\AnnotationRecord;
use PHPUnit\Framework\TestCase;

final class DatabaseCompatibilityTest extends TestCase
{
    public function testAnnotationBootstrapPersistsUsingTheSharedPdoConnection(): void
    {
        $database = $this->database();
        $entityManager = $database->getDoctrine();

        self::assertSame($database, $entityManager->getConnection()->getWrappedConnection());
        (new SchemaTool($entityManager))->createSchema([
            $entityManager->getClassMetadata(AnnotationRecord::class),
        ]);

        $record = new AnnotationRecord();
        $record->name = 'legacy annotation';
        $entityManager->persist($record);
        $entityManager->flush();
        $entityManager->clear();

        self::assertSame(
            'legacy annotation',
            $entityManager->find(AnnotationRecord::class, $record->id)?->name
        );
    }

    public function testRestartDoctrineRestoresEnabledFilterAndParameter(): void
    {
        $database = $this->database();
        $entityManager = $database->getDoctrine();
        $entityManager->getConfiguration()->addFilter('company', CompanyFilter::class);
        $filter = $entityManager->getFilters()->enable('company');
        $filter->setParameter('company', 42);
        $expectedParameter = $filter->getParameter('company');

        $database->restartDoctrine();

        $restored = $database->getDoctrine()->getFilters()->getFilter('company');
        self::assertInstanceOf(CompanyFilter::class, $restored);
        self::assertSame($expectedParameter, $restored->getParameter('company'));
    }

    private function database(): Database
    {
        return new Database(
            'sqlite::memory:',
            null,
            null,
            null,
            null,
            null,
            'ScaffoldTest',
            dirname(__DIR__) . '/Fixtures/Doctrine'
        );
    }
}

final class CompanyFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias): string
    {
        return '1 = 1';
    }
}
