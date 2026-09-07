<?php

declare(strict_types=1);

namespace Ngaje\Scaffold\Tests\Fixtures\Doctrine;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

/**
 * @Entity
 * @Table(name="annotation_record")
 */
final class AnnotationRecord
{
    /** @Id @GeneratedValue @Column(type="integer") */
    public ?int $id = null;

    /** @Column(type="string") */
    public string $name = '';
}
