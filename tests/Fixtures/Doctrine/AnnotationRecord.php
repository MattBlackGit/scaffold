<?php

declare(strict_types=1);

namespace Ngaje\Scaffold\Tests\Fixtures\Doctrine;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="annotation_record")
 */
final class AnnotationRecord
{
    /** @ORM\Id @ORM\GeneratedValue @ORM\Column(type="integer") */
    public ?int $id = null;

    /** @ORM\Column(type="string") */
    public string $name = '';
}
